<?php
session_start(); // Start the session

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to main page or login page
header("Location: main.php"); // Redirect to main page
exit();
?>

