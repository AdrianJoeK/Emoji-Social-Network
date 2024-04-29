<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.html");
    exit;
}

require 'db.php';

$chat_id = $_POST['chat_id'];
$friend_id = $_POST['friend_id'];

$checkStmt = $conn->prepare("SELECT * FROM group_chat_members WHERE chat_id = ? AND user_id = ?");
$checkStmt->bind_param("ii", $chat_id, $friend_id);
$checkStmt->execute();
if ($checkStmt->get_result()->num_rows > 0) {
    $_SESSION['message'] = "This user is already in the group.";
} else {
    $addStmt = $conn->prepare("INSERT INTO group_chat_members (chat_id, user_id) VALUES (?, ?)");
    $addStmt->bind_param("ii", $chat_id, $friend_id);
    if ($addStmt->execute()) {
        $_SESSION['message'] = "User added to group successfully!";
    } else {
        $_SESSION['message'] = "Error adding user to group: " . $addStmt->error;
    }
    $addStmt->close();
}
$checkStmt->close();
$conn->close();
header("Location: dashboard.php");
exit;
?>
