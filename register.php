<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $tag = $_POST['tag'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $checkStmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND tag = ?");
    $checkStmt->bind_param("ss", $user, $tag);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows > 0) {
        header("Location: index.html?message=Username+and+Tag+combination+already+exists");
        exit;
    }
    $checkStmt->close();

    $stmt = $conn->prepare("INSERT INTO users (username, tag, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $user, $tag, $pass);

    if ($stmt->execute()) {
        header("Location: index.html?message=Registration+Successful");
        exit;
    } else {
        header("Location: index.html?message=Error+Registration+Failed");
        exit;
    }

    $stmt->close();
}
$conn->close();
?>
