<?php
$servername = "localhost";
$username = "databaseUsername";
$password = "databasePassword";
$dbname = "databaseName";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if (!$conn->set_charset("utf8mb4")) {
  printf("Error loading character set utf8mb4: %s\n", $conn->error);
  exit();
}
?>
