<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['cart_id']) || !isset($_POST['quantity'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$userId = $_SESSION['user_id'];
$cartId = $_POST['cart_id'];
$newQuantity = intval($_POST['quantity']);

if ($newQuantity < 1) {
    echo json_encode(['success' => false, 'message' => 'Quantity must be at least 1']);
    exit;
}

// Start a transaction
$conn->begin_transaction();

try {
    // Get the current cart item details
    $fetchSql = "SELECT c.product_id, c.quantity, p.STOCK FROM cart c JOIN products p ON c.product_id = p.PRODUCT_ID WHERE c.cart_id = ? AND c.user_id = ?";
    $fetchStmt = $conn->prepare($fetchSql);
    $fetchStmt->bind_param("ii", $cartId, $userId);
    $fetchStmt->execute();
    $fetchResult = $fetchStmt->get_result();
    $item = $fetchResult->fetch_assoc();

    if (!$item) {
        throw new Exception('Cart item not found');
    }

    $productId = $item['product_id'];
    $currentQuantity = $item['quantity'];
    $currentStock = $item['STOCK'];

    // Calculate the stock change
    $stockChange = $currentQuantity - $newQuantity;

    // Check if there's enough stock
    if ($currentStock + $stockChange < 0) {
        throw new Exception('Not enough stock available');
    }

    // Update the cart item quantity
    $updateCartSql = "UPDATE cart SET quantity = ? WHERE cart_id = ? AND user_id = ?";
    $updateCartStmt = $conn->prepare($updateCartSql);
    $updateCartStmt->bind_param("iii", $newQuantity, $cartId, $userId);
    $updateCartStmt->execute();

    // Update the product stock
    $updateStockSql = "UPDATE products SET STOCK = STOCK + ? WHERE PRODUCT_ID = ?";
    $updateStockStmt = $conn->prepare($updateStockSql);
    $updateStockStmt->bind_param("ii", $stockChange, $productId);
    $updateStockStmt->execute();

    // Fetch the updated cart item details
    $fetchUpdatedSql = "SELECT c.quantity, p.PRICE, p.STOCK FROM cart c JOIN products p ON c.product_id = p.PRODUCT_ID WHERE c.cart_id = ?";
    $fetchUpdatedStmt = $conn->prepare($fetchUpdatedSql);
    $fetchUpdatedStmt->bind_param("i", $cartId);
    $fetchUpdatedStmt->execute();
    $fetchUpdatedResult = $fetchUpdatedStmt->get_result();
    $updatedItem = $fetchUpdatedResult->fetch_assoc();

    // Calculate the new item total
    $itemTotal = $updatedItem['quantity'] * $updatedItem['PRICE'];

    // Fetch the new total amount for all cart items
    $totalSql = "SELECT SUM(c.quantity * p.PRICE) as total FROM cart c JOIN products p ON c.product_id = p.PRODUCT_ID WHERE c.user_id = ?";
    $totalStmt = $conn->prepare($totalSql);
    $totalStmt->bind_param("i", $userId);
    $totalStmt->execute();
    $totalResult = $totalStmt->get_result();
    $totalRow = $totalResult->fetch_assoc();
    $totalAmount = $totalRow['total'];

    // Commit the transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'itemTotal' => $itemTotal,
        'totalAmount' => $totalAmount,
        'newStock' => $updatedItem['STOCK']
    ]);
} catch (Exception $e) {
    // Rollback the transaction in case of any error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>