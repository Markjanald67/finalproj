<?php
session_start();

// Database connection
require_once 'db_connection.php';
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_name'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

// Get user ID
$userId = $_SESSION['user_id'];

// Get product details from POST request
$productId = intval($_POST['product_id']);
$quantity = intval($_POST['quantity']);

// Check available stock for the product
$stockSql = "SELECT STOCK FROM products WHERE PRODUCT_ID = ?";
$stmt = $conn->prepare($stockSql);
$stmt->bind_param("i", $productId);
$stmt->execute();
$stockResult = $stmt->get_result();

if ($stockResult->num_rows == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Product not found']);
    exit();
}

$stockRow = $stockResult->fetch_assoc();
$currentStock = $stockRow['STOCK'];

// Check if enough stock is available
if ($currentStock < $quantity) {
    echo json_encode(['status' => 'error', 'message' => 'Insufficient stock available']);
    exit();
}

// Check if the cart is empty
$sql = "SELECT COUNT(*) as count FROM cart WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    // If the cart is empty, insert the new product with cart_id starting from 1
    $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $userId, $productId, $quantity);
} else {
    // Check if the product already exists in the cart
    $sql = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update quantity if product already exists in the cart
        $sql = "UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $quantity, $userId, $productId);
    } else {
        // Insert new product into the cart
        $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $userId, $productId, $quantity);
    }
}

// Execute the cart update
if ($stmt->execute()) {
    // Deduct the stock after successful addition to the cart
    $newStock = $currentStock - $quantity;
    $updateStockSql = "UPDATE products SET STOCK = ? WHERE PRODUCT_ID = ?";
    $updateStmt = $conn->prepare($updateStockSql);
    $updateStmt->bind_param("ii", $newStock, $productId);
    $updateStmt->execute();
    $updateStmt->close();

    // Get the updated cart count
    $cartCountSql = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
    $cartCountStmt = $conn->prepare($cartCountSql);
    $cartCountStmt->bind_param("i", $userId);
    $cartCountStmt->execute();
    $cartCountResult = $cartCountStmt->get_result();
    $cartCountRow = $cartCountResult->fetch_assoc();
    $updatedCartCount = $cartCountRow['total'] ?? 0;

    echo json_encode(['status' => 'success', 'message' => 'Product added to cart', 'cartCount' => $updatedCartCount]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to add product to cart']);
}

$stmt->close();
$conn->close();
?>

