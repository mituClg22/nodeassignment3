<?php
require_once "db.php";

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: index.php");
    exit;
}

$stmt = $conn->prepare("SELECT id FROM products WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

if ($stmt->get_result()->num_rows === 1) {
    if (!isset($_SESSION["cart"])) {
        $_SESSION["cart"] = [];
    }

    $_SESSION["cart"][$id] = min(
        99,
        ($_SESSION["cart"][$id] ?? 0) + 1
    );
}

header("Location: cart.php");
exit;
?>