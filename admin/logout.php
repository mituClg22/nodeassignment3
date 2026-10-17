<?php
require_once "../db.php";

unset($_SESSION["admin_id"]);
header("Location: login.php");
exit;
?>