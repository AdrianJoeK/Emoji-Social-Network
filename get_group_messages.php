<?php
session_start();
require 'db.php';

$group_id = $_GET['group_id'];
$user_id = $_SESSION['user_id'];

$messageQuery = "SELECT gcm.message, gcm.timestamp, u.username, u.tag
                 FROM group_chat_messages gcm
                 JOIN users u ON u.id = gcm.from_user_id
                 WHERE gcm.chat_id = ?
                 ORDER BY gcm.timestamp ASC";
$messageStmt = $conn->prepare($messageQuery);
$messageStmt->bind_param("i", $group_id);
$messageStmt->execute();
$resultMessages = $messageStmt->get_result();

if ($resultMessages->num_rows > 0) {
    while ($message = $resultMessages->fetch_assoc()) {
        echo "<p>" . htmlspecialchars($message['username']) . ": " . htmlspecialchars($message['message']) . " (" . $message['timestamp'] . ")</p>";
    }
} else {
    echo "<p>No messages in this group chat.</p>";
}

$messageStmt->close();
$conn->close();
?>
