<?php
require_once "db.php";

$username = "admin";
$password = "password";
$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    "UPDATE admins SET password = ? WHERE username = ?"
);
$stmt->bind_param("ss", $hash, $username);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Admin password updated successfully.";
} else {
    $stmt = $conn->prepare(
        "SELECT id FROM admins WHERE username = ?"
    );
    $stmt->bind_param("s", $username);
    $stmt->execute();

    if ($stmt->get_result()->num_rows === 0) {
        $hash = password_hash("password", PASSWORD_DEFAULT);
        $stmt = $conn->prepare(
            "INSERT INTO admins (username, password) VALUES (?, ?)"
        );
        $stmt->bind_param("ss", $username, $hash);
        $stmt->execute();
        echo "Admin account created successfully.";
    } else {
        echo "Admin account exists; password may already be correct.";
    }
}
?>