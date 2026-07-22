<?php
$servername = "localhost";
$username = "admin_user";
$password = "12345";
$database = "stayhub_db";
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
$conn->set_charset('utf8mb4');
?>