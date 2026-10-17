<?php
require_once "../db.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

function resizeImage($tmp, $destination, $type) {
    if ($type === IMAGETYPE_JPEG) {
        $src = @imagecreatefromjpeg($tmp);
    } elseif ($type === IMAGETYPE_PNG) {
        $src = @imagecreatefrompng($tmp);
    } elseif ($type === IMAGETYPE_WEBP && function_exists("imagecreatefromwebp")) {
        $src = @imagecreatefromwebp($tmp);
    } else {
        return false;
    }

    if (!$src) return false;

    $w = imagesx($src);
    $h = imagesy($src);
    $scale = min(1, 500 / max($w, $h));
    $nw = max(1, (int)($w * $scale));
    $nh = max(1, (int)($h * $scale));

    $dst = imagecreatetruecolor($nw, $nh);
    imagealphablending($dst, false);
    imagesavealpha($dst, true);
    $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
    imagefill($dst, 0, 0, $transparent);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);

    if ($type === IMAGETYPE_JPEG) {
        $ok = imagejpeg($dst, $destination, 85);
    } elseif ($type === IMAGETYPE_PNG) {
        $ok = imagepng($dst, $destination);
    } else {
        $ok = imagewebp($dst, $destination, 85);
    }

    imagedestroy($src);
    imagedestroy($dst);
    return $ok;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["pname"]);
    $price = (float)$_POST["price"];
    $category = (int)$_POST["category_id"];
    $picture = null;

    if ($name !== "" && $price >= 0 && $category > 0) {
        if (isset($_FILES["picture"]) &&
            $_FILES["picture"]["error"] === UPLOAD_ERR_OK) {

            if ($_FILES["picture"]["size"] > 5 * 1024 * 1024) {
                die("Image must be 5 MB or smaller.");
            }

            $info = getimagesize($_FILES["picture"]["tmp_name"]);
            $allowed = [
                IMAGETYPE_JPEG => "jpg",
                IMAGETYPE_PNG => "png",
                IMAGETYPE_WEBP => "webp"
            ];

            if ($info && isset($allowed[$info[2]])) {
                $picture = bin2hex(random_bytes(16)) . "." . $allowed[$info[2]];
                $destination = "../uploads/" . $picture;

                if (!resizeImage(
                    $_FILES["picture"]["tmp_name"],
                    $destination,
                    $info[2]
                )) {
                    $picture = null;
                }
            }
        }

        if ($picture !== null) {
            $stmt = $conn->prepare(
                "INSERT INTO products(category_id,pname,price,picture)
                 VALUES (?,?,?,?)"
            );
            $stmt->bind_param("isds", $category, $name, $price, $picture);
            $stmt->execute();
        }
    }

    header("Location: products.php");
    exit;
}

if (isset($_GET["delete"])) {
    $id = (int)$_GET["delete"];
    $stmt = $conn->prepare("SELECT picture FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $old = $stmt->get_result()->fetch_assoc();

    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($old && $old["picture"]) {
        $path = "../uploads/" . basename($old["picture"]);
        if (is_file($path)) unlink($path);
    }

    header("Location: products.php");
    exit;
}

$categories = $conn->query("SELECT * FROM categories");
$products = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>
<h2>Manage Products</h2>

<form method="post" enctype="multipart/form-data">
    Product Name:
    <input name="pname" required><br><br>

    Price:
    <input type="number" name="price" min="0"
           step="0.01" required><br><br>

    Category:
    <select name="category_id" required>
        <?php while ($c = $categories->fetch_assoc()): ?>
            <option value="<?= $c['id'] ?>">
                <?= htmlspecialchars($c['name']) ?>
            </option>
        <?php endwhile; ?>
    </select><br><br>

    Picture:
    <input type="file" name="picture"
           accept="image/jpeg,image/png,image/webp" required><br><br>

    <button type="submit">Add Product</button>
</form>

<hr>

<?php while ($p = $products->fetch_assoc()): ?>
    <p>
        <?= htmlspecialchars($p["pname"]) ?>
        - ₹<?= number_format($p["price"], 2) ?>

        <?php if ($p["picture"]): ?>
            <img src="../uploads/<?= rawurlencode($p["picture"]) ?>"
                 width="80" alt="Product">
        <?php endif; ?>

        <a href="?delete=<?= $p['id'] ?>"
           onclick="return confirm('Delete product?')">Delete</a>
    </p>
<?php endwhile; ?>

<a href="dashboard.php">Back to Dashboard</a>