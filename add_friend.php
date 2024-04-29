<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.html");
    exit;
}

require 'db.php';

if (isset($_POST['friend']) && preg_match("/^(\X+)#(\d{4})$/u", $_POST['friend'], $matches)) {
    $friend_username = $matches[1];
    $friend_tag = $matches[2];

    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND tag = ?");
    $stmt->bind_param("ss", $friend_username, $friend_tag);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $friend_id = $row['id'];

        $checkStmt = $conn->prepare("SELECT * FROM friends WHERE user_id = ? AND friend_id = ?");
        $checkStmt->bind_param("ii", $_SESSION['user_id'], $friend_id);
        $checkStmt->execute();

        if ($checkStmt->get_result()->num_rows > 0) {
            $_SESSION['message'] = "You are already friends.";
        } else {
            $addStmt = $conn->prepare("INSERT INTO friends (user_id, friend_id) VALUES (?, ?)");
            $addStmt->bind_param("ii", $_SESSION['user_id'], $friend_id);
            if ($addStmt->execute()) {
                $_SESSION['message'] = "Friend added successfully!";
            } else {
                $_SESSION['message'] = "Error adding friend: " . $addStmt->error;
            }
            $addStmt->close();
        }
        $checkStmt->close();
    } else {
        $_SESSION['message'] = "No user found with that username and tag.";
    }
    $stmt->close();
} else {
    $_SESSION['message'] = "Invalid input format.";
}
$conn->close();
header("Location: dashboard.php");
exit;
?>
