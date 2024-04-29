<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $tag = $_POST['tag'];
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ? AND tag = ?");
    $stmt->bind_param("ss", $user, $tag);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashed_password);
    if ($stmt->fetch() && password_verify($pass, $hashed_password)) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $user;
        $_SESSION['user_id'] = $id;
        $_SESSION['user_tag'] = $tag;

        header("Location: dashboard.php");
        exit;
    } else {
        header("Location: index.html?message=Invalid+username+or+password");
        exit;
    }

    $stmt->close();
}
$conn->close();
?>
