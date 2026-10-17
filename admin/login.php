<?php
require_once "../db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare(
        "SELECT id, password FROM admins WHERE username = ?"
    );
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($admin = $result->fetch_assoc()) {
        if (password_verify($password, $admin["password"])) {
            session_regenerate_id(true);
            $_SESSION["admin_id"] = $admin["id"];
            header("Location: dashboard.php");
            exit;
        }
    }

    $error = "Invalid username or password";
}
?>
<h2>Admin Login</h2>
<p><?= htmlspecialchars($error) ?></p>
<form method="post">
    Username: <input name="username" required><br><br>
    Password: <input type="password" name="password" required><br><br>
    <button type="submit">Login</button>
</form>