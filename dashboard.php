<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.html");
    exit;
}

require 'db.php';

$user_id = $_SESSION['user_id'];

$profileQuery = $conn->prepare("SELECT profile_image FROM users WHERE id = ?");
$profileQuery->bind_param("i", $user_id);
$profileQuery->execute();
$profileResult = $profileQuery->get_result();
$userProfile = $profileResult->fetch_assoc();
$profileImagePath = $userProfile['profile_image'] ?? 'profilepictures/default.png';

$friendsQuery = "SELECT u.id, u.username, u.tag, u.profile_image FROM users u JOIN friends f ON u.id = f.friend_id WHERE f.user_id = ?";
$friendsStmt = $conn->prepare($friendsQuery);
$friendsStmt->bind_param("i", $user_id);
$friendsStmt->execute();
$resultFriends = $friendsStmt->get_result();

$messageQuery = "SELECT m.message, m.timestamp, u.username, u.tag, u.profile_image FROM messages m JOIN users u ON u.id = m.from_user_id WHERE m.to_user_id = ? ORDER BY m.timestamp DESC LIMIT 5";
$messageStmt = $conn->prepare($messageQuery);
$messageStmt->execute();

$groupChatsQuery = "SELECT gc.id, gc.name FROM group_chats gc JOIN group_chat_members gcm ON gc.id = gcm.chat_id WHERE gcm.user_id = ?";
$groupChatsStmt = $conn->prepare($groupChatsQuery);
$groupChatsStmt->bind_param("i", $user_id);
$groupChatsStmt->execute();
$resultGroupChats = $groupChatsStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <script>
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const friendId = urlParams.get('friend_id');
    if (friendId) {
        const friendButton = document.querySelector('button[data-friend-id="' + friendId + '"]');
        if (friendButton) {
            friendButton.click();
        }
    }
});

function loadMessages(id, name, isGroup = false) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("messages").innerHTML = this.responseText;
            document.getElementById("messageHeader").innerHTML = 'Messages with ' + name;
            document.getElementById("sendMessageForm").style.display = 'block';
            document.getElementById("recipient_id").value = id;
            document.getElementById("sendMessageForm").action = isGroup ? 'send_group_message.php' : 'send_message.php';
            if(isGroup) {
                document.getElementById("addGroupMember").style.display = 'block';
                document.getElementById("chat_id").value = id;
            } else {
                document.getElementById("addGroupMember").style.display = 'none';
            }
        }
    };
    const url = isGroup ? "get_group_messages.php?group_id=" + id : "get_messages.php?friend_id=" + id;
    xhttp.open("GET", url, true);
    xhttp.send();
}
    </script>
</head>
<body>
    <div class="sidebar">
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION["username"]) . '#' . htmlspecialchars($_SESSION["user_tag"]); ?></h1>
        <img src="<?php echo htmlspecialchars($profileImagePath); ?>" alt="Profile Image" style="width:100px; height:100px; border-radius:50%;">
        <form action="upload_profile_picture.php" method="post" enctype="multipart/form-data">
            Select image to upload:
            <input type="file" name="fileToUpload" id="fileToUpload">
            <input type="submit" value="Upload Image" name="submit">
        </form>
        <form action="logout.php" method="post">
            <button type="submit">Logout</button>
        </form>
        <h2>Add a Friend</h2>
        <form action="add_friend.php" method="post" accept-charset="UTF-8">
            <label for="friend">Enter Friend's Username and Tag (e.g., Adrian#0000):</label>
            <input type="text" id="friend" name="friend" pattern="^[^\#]+#[0-9]{4}$" title="Username#Tag" required><br>
            <input type="submit" value="Add Friend">
        </form>
        <h2>Create New Group Chat</h2>
        <form action="create_group_chat.php" method="post" accept-charset="UTF-8">
            <label for="chat_name">Group Chat Name:</label>
            <input type="text" id="chat_name" name="chat_name" required><br>
            <input type="submit" value="Create Group">
        </form>
        <h2>Your Friends</h2>
        <?php while ($row = $resultFriends->fetch_assoc()): ?>
            <button class="friend-button" data-friend-id="<?php echo $row['id']; ?>" onclick="loadMessages('<?php echo $row['id']; ?>', '<?php echo htmlspecialchars($row['username']) . '#' . htmlspecialchars($row['tag']); ?>')">
                <img src="profilepictures/<?php echo $row['id']; ?>.png" alt="Friend" class="friend-img" onerror="this.src='profilepictures/default.png';">
                <?php echo htmlspecialchars($row['username']) . '#' . htmlspecialchars($row['tag']); ?>
            </button>
        <?php endwhile; ?>
        <h2>Group Chats</h2>
        <?php while ($row = $resultGroupChats->fetch_assoc()): ?>
            <button onclick="loadMessages('<?php echo $row['id']; ?>', '<?php echo htmlspecialchars($row['name']); ?>', true)">
                <?php echo htmlspecialchars($row['name']); ?>
            </button>
        <?php endwhile; ?>
    </div>
    <div class="main">
        <h2 id="messageHeader">Select a friend or group to view messages</h2>
        <div id="messages"></div>
        <form id="sendMessageForm" action="send_message.php" method="post" style="display:none;">
            <input type="hidden" id="recipient_id" name="recipient_id">
            <textarea name="message" required></textarea><br>
            <input type="submit" value="Send">
        </form>
        <form id="addGroupMember" action="add_to_group.php" method="post" style="display:none;">
            <input type="hidden" id="chat_id" name="chat_id">
            <select name="friend_id">
                <?php foreach($resultFriends as $friend): ?>
                    <option value="<?php echo $friend['id']; ?>"><?php echo htmlspecialchars($friend['username']) . '#' . htmlspecialchars($friend['tag']); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="submit" value="Add to Group">
        </form>
    </div>
</body>
</html>

<?php
$friendsStmt->close();
$messageStmt->close();
$conn->close();
?>
