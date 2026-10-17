<?php
require_once "db.php";

unset($_SESSION["user_id"], $_SESSION["cart"]);
header("Location: login.php");
exit;
?>