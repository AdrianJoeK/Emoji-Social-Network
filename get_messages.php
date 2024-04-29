<?php
session_start();
require 'db.php';

$friend_id = $_GET['friend_id'];
$user_id = $_SESSION['user_id'];

$messageQuery = "SELECT m.message, m.timestamp, m.from_user_id, m.to_user_id, u.username, u.tag
                 FROM messages m
                 JOIN users u ON u.id = m.from_user_id
                 WHERE (m.from_user_id = ? AND m.to_user_id = ?) OR (m.from_user_id = ? AND m.to_user_id = ?)
                 ORDER BY m.timestamp ASC";
$messageStmt = $conn->prepare($messageQuery);
$messageStmt->bind_param("iiii", $user_id, $friend_id, $friend_id, $user_id);
$messageStmt->execute();
$resultMessages = $messageStmt->get_result();

if ($resultMessages->num_rows > 0) {
    while ($message = $resultMessages->fetch_assoc()) {
        $isSentByUser = $message['from_user_id'] == $user_id;
        echo "<p>" . ($isSentByUser ? "You: " : htmlspecialchars($message['username']) . ": ") . htmlspecialchars($message['message']) . " (" . $message['timestamp'] . ")</p>";
    }
} else {
    echo "<p>No messages from this friend.</p>";
}

$messageStmt->close();
$conn->close();
?>
