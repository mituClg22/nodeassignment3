<?php
require_once "db.php";

if (!isset($_SESSION["captcha_a"], $_SESSION["captcha_b"]) ||
    $_SERVER["REQUEST_METHOD"] !== "POST") {
    $_SESSION["captcha_a"] = random_int(1, 9);
    $_SESSION["captcha_b"] = random_int(1, 9);
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $answer = $_POST["captcha"] ?? "";

    $expected = $_SESSION["captcha_a"] + $_SESSION["captcha_b"];

    if (!ctype_digit($answer) || (int)$answer !== $expected) {
        $error = "Incorrect CAPTCHA.";
    } elseif ($name === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)
              || strlen($password) < 8) {
        $error = "Enter valid details. Password must be at least 8 characters.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare(
            "INSERT INTO users(name,email,password) VALUES (?,?,?)"
        );
        $stmt->bind_param("sss", $name, $email, $hash);

        if ($stmt->execute()) {
            unset($_SESSION["captcha_a"], $_SESSION["captcha_b"]);
            header("Location: login.php");
            exit;
        } else {
            $error = "Registration failed. Email may already exist.";
        }
    }

    $_SESSION["captcha_a"] = random_int(1, 9);
    $_SESSION["captcha_b"] = random_int(1, 9);
}
?>
<h2>User Registration</h2>
<p><?= htmlspecialchars($error) ?></p>

<form method="post">
    Name: <input name="name" required><br><br>
    Email: <input type="email" name="email" required><br><br>
    Password: <input type="password" name="password"
                      minlength="8" required><br><br>

    Solve: <?= $_SESSION["captcha_a"] ?> +
           <?= $_SESSION["captcha_b"] ?> =
    <input type="number" name="captcha" required><br><br>

    <button type="submit">Register</button>
</form>