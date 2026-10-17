<?php
require_once "db.php";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    
    $stmt = $conn->prepare("SELECT id,password FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user["password"])) {
        session_regenerate_id(true);
        $_SESSION["user_id"] = $user["id"];
        header("Location: index.php");
        exit;
    }

    $error = "Invalid email or password.";
}
?>
<h2>User Login</h2>
<p><?= htmlspecialchars($error) ?></p>

<form method="post">
    Email: <input type="email" name="email" required><br><br>
    Password: <input type="password" name="password" required><br><br>
    <button type="submit">Login</button>
</form>

<a href="register.php">Register</a>