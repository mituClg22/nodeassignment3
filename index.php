<?php
require_once "db.php";
$categories = $conn->query("SELECT * FROM categories ORDER BY name");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Shopping Cart</title>
    <style>
        body { font-family: Arial; margin: 30px; }
        a { margin-right: 12px; }
        .product { display: inline-block; width: 180px; margin: 15px; }
        img { width: 150px; height: 140px; object-fit: contain; }
    </style>
</head>
<body>
<h1>My Shopping Store</h1>

<a href="index.php">Home</a>
<a href="register.php">Register</a>
<a href="login.php">Login</a>
<a href="cart.php">Cart</a>

<h3>Categories</h3>
<?php while ($c = $categories->fetch_assoc()): ?>
    <a href="#" onclick="loadProducts(<?= $c['id'] ?>); return false;">
        <?= htmlspecialchars($c['name']) ?>
    </a>
<?php endwhile; ?>

<hr>
<div id="products">Select a category to view products.</div>

<script>
function loadProducts(category, page = 1) {
    fetch("products.php?category=" + category + "&page=" + page)
        .then(response => response.text())
        .then(html => {
            document.getElementById("products").innerHTML = html;
        })
        .catch(() => {
            document.getElementById("products").textContent =
                "Unable to load products.";
        });
}
</script>
</body>
</html>