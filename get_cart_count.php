<?php
session_start(); // Start the session

// Database connection
require_once 'db_connection.php';


$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $sql = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ? AND is_archived = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    echo json_encode(['total' => $row['total'] ? $row['total'] : 0]);
    $stmt->close();
} else {
    echo json_encode(['total' => 0]);
}

$conn->close();
?>
