<?php
require_once "../db.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST["add"])) {
    $name = trim($_POST["name"]);
    if ($name !== "") {
        $stmt = $conn->prepare("INSERT INTO categories(name) VALUES (?)");
        $stmt->bind_param("s", $name);
        $stmt->execute();
    }
}

if (isset($_GET["delete"])) {
    $id = (int)$_GET["delete"];
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: categories.php");
    exit;
}

if (isset($_POST["update"])) {
    $id = (int)$_POST["id"];
    $name = trim($_POST["name"]);
    $stmt = $conn->prepare("UPDATE categories SET name = ? WHERE id = ?");
    $stmt->bind_param("si", $name, $id);
    $stmt->execute();
}

$result = $conn->query("SELECT * FROM categories ORDER BY id");
?>
<h2>Manage Categories</h2>

<form method="post">
    <input name="name" placeholder="Category name" required>
    <button name="add">Add Category</button>
</form>

<?php while ($row = $result->fetch_assoc()): ?>
    <form method="post">
        <input type="hidden" name="id"
               value="<?= $row['id'] ?>">
        <input name="name"
               value="<?= htmlspecialchars($row['name']) ?>" required>
        <button name="update">Update</button>
        <a href="?delete=<?= $row['id'] ?>"
           onclick="return confirm('Delete category?')">Delete</a>
    </form>
<?php endwhile; ?>

<a href="dashboard.php">Back</a>