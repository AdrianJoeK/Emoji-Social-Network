<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.html");
    exit;
}

require 'db.php';

$chat_name = $_POST['chat_name'];

$stmt = $conn->prepare("INSERT INTO group_chats (name) VALUES (?)");
$stmt->bind_param("s", $chat_name);
if ($stmt->execute()) {
    $chat_id = $stmt->insert_id;
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("INSERT INTO group_chat_members (chat_id, user_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $chat_id, $user_id);
    $stmt->execute();
    $_SESSION['message'] = "Group chat created successfully!";
} else {
    $_SESSION['message'] = "Error creating group chat: " . $stmt->error;
}
$stmt->close();
$conn->close();
header("Location: dashboard.php");
exit;
?>
