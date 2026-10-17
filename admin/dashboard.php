<?php
require_once "../db.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}
?>
<h2>Admin Dashboard</h2>
<a href="categories.php">Manage Categories</a><br><br>
<a href="products.php">Manage Products</a><br><br>
<a href="logout.php">Logout</a>