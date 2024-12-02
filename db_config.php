<?php
// Database configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // Replace with your MySQL username
define('DB_PASSWORD', ''); // Replace with your MySQL password
define('DB_NAME', 'quickpuff');


// Attempt to connect to MySQL database
try {
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set the character set (optional, but recommended)
    if (!$conn->set_charset("utf8mb4")) {
        throw new Exception("Error setting charset: " . $conn->error);
    }
} catch(Exception $e) {
    // Log the error
    error_log("Database connection error: " . $e->getMessage());
    
    // Show a generic error message to the user
    die("Something went wrong. Please try again later.");
}
?>
