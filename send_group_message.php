<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.html");
    exit;
}

require 'db.php';

$chat_id = $_POST['recipient_id'];
$message = $_POST['message'];
$user_id = $_SESSION['user_id'];

if (empty($message)) {
    $_SESSION['message'] = "Message cannot be empty.";
    header("Location: dashboard.php?group_id=" . $chat_id);
    exit;
} 

$stmt = $conn->prepare("INSERT INTO group_chat_messages (chat_id, from_user_id, message) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $chat_id, $user_id, $message);
if ($stmt->execute()) {
    $_SESSION['message'] = "Message sent successfully!";
} else {
    $_SESSION['message'] = "Error sending message: " . $stmt->error;
}
$stmt->close();
$conn->close();
header("Location: dashboard.php?group_id=" . $chat_id);
exit;
?>
