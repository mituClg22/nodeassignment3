<?php
require_once "db.php";

if (isset($_POST["remove"])) {
    $id = (int)$_POST["remove"];
    unset($_SESSION["cart"][$id]);
}

if (isset($_POST["update"]) && isset($_POST["qty"])) {
    foreach ($_POST["qty"] as $id => $qty) {
        $id = (int)$id;
        $qty = filter_var($qty, FILTER_VALIDATE_INT);

        if ($qty === false || $qty < 1) {
            unset($_SESSION["cart"][$id]);
        } elseif (isset($_SESSION["cart"][$id])) {
            $_SESSION["cart"][$id] = min(99, $qty);
        }
    }
}

$cart = $_SESSION["cart"] ?? [];
$total = 0;
?>
<h2>Shopping Cart</h2>

<form method="post">
<table border="1" cellpadding="10">
<tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th>Action</th></tr>

<?php foreach ($cart as $id => $qty):
    $stmt = $conn->prepare("SELECT pname,price FROM products WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $p = $stmt->get_result()->fetch_assoc();

    if (!$p) continue;

    $subtotal = $p["price"] * $qty;
    $total += $subtotal;
?>
<tr>
    <td><?= htmlspecialchars($p["pname"]) ?></td>
    <td>₹<?= number_format($p["price"], 2) ?></td>
    <td><input type="number" name="qty[<?= $id ?>]"
               value="<?= $qty ?>" min="1" max="99"></td>
    <td>₹<?= number_format($subtotal, 2) ?></td>
    <td><button name="remove" value="<?= $id ?>">Remove</button></td>
</tr>
<?php endforeach; ?>

<tr><td colspan="3">Total</td><td colspan="2">₹<?= number_format($total, 2) ?></td></tr>
</table>

<br><button name="update" value="1">Update Cart</button>
</form>

<a href="index.php">Continue Shopping</a>
<a href="logout.php">Logout</a>