<?php
require_once "db.php";

$category = filter_input(INPUT_GET, "category", FILTER_VALIDATE_INT);
$page = filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT);

$category = max(1, $category ?: 1);
$page = max(1, $page ?: 1);
$limit = 4;

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM products WHERE category_id=?");
$stmt->bind_param("i", $category);
$stmt->execute();
$total = (int)$stmt->get_result()->fetch_assoc()["total"];

$pages = max(1, (int)ceil($total / $limit));
$page = min($page, $pages);
$offset = ($page - 1) * $limit;

$stmt = $conn->prepare(
    "SELECT id,pname,price,picture
     FROM products WHERE category_id=?
     ORDER BY id DESC LIMIT ? OFFSET ?"
);
$stmt->bind_param("iii", $category, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

while ($p = $result->fetch_assoc()):
?>
<div class="product">
    <?php if ($p["picture"]): ?>
        <img src="uploads/<?= rawurlencode($p["picture"]) ?>"
             alt="<?= htmlspecialchars($p["pname"]) ?>">
    <?php endif; ?>

    <h4><?= htmlspecialchars($p["pname"]) ?></h4>
    <p>Price: ₹<?= number_format($p["price"], 2) ?></p>

    <form action="add_to_cart.php" method="post">
        <input type="hidden" name="id" value="<?= $p['id'] ?>">
        <button type="submit">Add to Cart</button>
    </form>
</div>
<?php endwhile; ?>

<div style="clear:both"></div>

<button onclick="loadProducts(<?= $category ?>, <?= $page - 1 ?>)"
    <?= $page <= 1 ? "disabled" : "" ?>>Previous</button>

Page <?= $page ?> of <?= $pages ?>

<button onclick="loadProducts(<?= $category ?>, <?= $page + 1 ?>)"
    <?= $page >= $pages ? "disabled" : "" ?>>Next</button>