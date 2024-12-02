<?php
session_start();
require_once 'db_config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("User not logged in");
}

// Validate the cart ID
$cartId = filter_input(INPUT_GET, 'cart_id', FILTER_VALIDATE_INT);
if ($cartId === false) {
    die("Invalid cart ID");
}

try {
    // Start a transaction
    $conn->begin_transaction();

    // Fetch the cart item
    $stmt = $conn->prepare("SELECT product_id, quantity FROM cart WHERE cart_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cartId, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $productId = $row['product_id'];
        $quantityToRemove = $row['quantity'];

        // Update stock in the products table
        $stmt = $conn->prepare("UPDATE products SET STOCK = STOCK + ? WHERE PRODUCT_ID = ?");
        $stmt->bind_param("ii", $quantityToRemove, $productId);
        if (!$stmt->execute()) {
            throw new Exception("");
        }

        // Remove the cart item
        $stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cartId, $_SESSION['user_id']); // Ensure user_id is included for security
        if (!$stmt->execute()) {
            throw new Exception("");
        }

        // Commit the transaction
        $conn->commit();
        $_SESSION['message'] = "";
    } else {
        throw new Exception("");
    }
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = "Error: " . $e->getMessage();
} finally {
    // Close the statement if it was created
    if (isset($stmt) && $stmt) {
        $stmt->close();
    }
    // Close the connection
    $conn->close();
}

// Redirect back to the cart page with a success or error message
header("Location: cart.php");
exit();
?>
