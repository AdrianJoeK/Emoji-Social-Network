<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.html");
    exit;
}

require 'db.php';

$sender_id = $_SESSION['user_id'];
$recipient_id = $_POST['recipient_id'];
$message = $_POST['message'];

if (empty($message)) {
    $_SESSION['message'] = "Message cannot be empty.";
} else {
    $stmt = $conn->prepare("INSERT INTO messages (from_user_id, to_user_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $sender_id, $recipient_id, $message);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Message sent successfully!";
    } else {
        $_SESSION['message'] = "Error sending message: " . $stmt->error;
    }
    $stmt->close();
}
$conn->close();
header("Location: dashboard.php?friend_id=" . $recipient_id);
exit;
?>
