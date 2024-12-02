<?php

include 'header.php';
require_once 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle order cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order'])) {
    $orderId = $_POST['order_id'];
    $reason = $_POST['cancel_reason'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Update order status
        $updateOrderSql = "UPDATE orders SET ORDER_STATUS = 'Cancelled' WHERE ORDER_ID = ? AND USER_ID = ? AND ORDER_STATUS = 'Pending'";
        $updateOrderStmt = $conn->prepare($updateOrderSql);
        $updateOrderStmt->bind_param("ii", $orderId, $userId);
        $updateOrderStmt->execute();

        if ($updateOrderStmt->affected_rows === 0) {
            throw new Exception('Order not found or already cancelled');
        }

        // Insert cancellation reason
        $insertReasonSql = "INSERT INTO order_cancellations (ORDER_ID, REASON) VALUES (?, ?)";
        $insertReasonStmt = $conn->prepare($insertReasonSql);
        $insertReasonStmt->bind_param("is", $orderId, $reason);
        $insertReasonStmt->execute();

        $conn->commit();
        $_SESSION['message'] = "Order #$orderId has been cancelled successfully.";
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Error cancelling order: " . $e->getMessage();
    }
}

// Determine if we're showing all orders or just recent ones
$showAllOrders = isset($_GET['show_all']) && $_GET['show_all'] == 1;

// Fetch orders for the user
$orderSql = "SELECT o.ORDER_ID, o.CREATED_AT, o.TOTAL_AMOUNT, o.ORDER_STATUS,
             t.DV_ADDRESS, t.MODE_OF_PAYMENT, t.AMOUNT_TENDERED, t.CHANGE_AMOUNT, t.GCASH_REF_NUMBER,
             u.city
             FROM orders o
             JOIN transactions t ON o.ORDER_ID = t.ORDER_ID
             JOIN users u ON o.USER_ID = u.USER_ID
             WHERE o.USER_ID = ?
             ORDER BY o.CREATED_AT DESC";

if (!$showAllOrders) {
    $orderSql .= " LIMIT 3";
}

// Handle order status update (new code)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderId = $_POST['order_id'];
    $newStatus = $_POST['new_status'];

    $updateStatusSql = "UPDATE orders SET ORDER_STATUS = ? WHERE ORDER_ID = ? AND USER_ID = ?";
    $updateStatusStmt = $conn->prepare($updateStatusSql);
    $updateStatusStmt->bind_param("sii", $newStatus, $orderId, $userId);
    $updateStatusStmt->execute();

    if ($updateStatusStmt->affected_rows > 0) {
        $_SESSION['message'] = "Order #$orderId status updated to $newStatus.";
    } else {
        $_SESSION['error'] = "Error updating order status.";
    }
}

$orderStmt = $conn->prepare($orderSql);
$orderStmt->bind_param("i", $userId);
$orderStmt->execute();
$orderResult = $orderStmt->get_result();

// Fetch order items
$itemSql = "SELECT oi.ORDER_ID, p.PRODUCT_NAME, oi.QUANTITY, oi.PRICE
            FROM order_items oi
            JOIN products p ON oi.PRODUCT_ID = p.PRODUCT_ID
            WHERE oi.ORDER_ID IN (SELECT ORDER_ID FROM orders WHERE USER_ID = ?)
            ORDER BY oi.ORDER_ID";
$itemStmt = $conn->prepare($itemSql);
$itemStmt->bind_param("i", $userId);
$itemStmt->execute();
$itemResult = $itemStmt->get_result();

// Group items by order
$orderItems = [];
while ($item = $itemResult->fetch_assoc()) {
    $orderItems[$item['ORDER_ID']][] = $item;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Summary</title>
    <link rel="stylesheet" href="./style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #edf1f8;
        }
        .order-container {
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 50px;
            margin-bottom: 50px;
        }
        .btn-received {
            background-color: #28a745;
            color: white;
            border: 2px solid #28a745;
        }
        .btn-received:hover{
            background-color: transparent;
            border: 2px solid #28a745;
        }
        .btn-danger{
            background-color: red;
            border: 2px solid red;
        }
        .btn-danger:hover{
            background-color: transparent;
            border: 2px solid red;
            color: black;
        }
    </style>
</head>
<body>
<div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 order-container">
                <h1 class="text-center mb-4">Order Summary</h1>

                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>

                <?php if ($orderResult->num_rows > 0): ?>
                    <?php while ($order = $orderResult->fetch_assoc()): ?>
                        <div class="mb-4 p-4 border rounded">
                            <h2>Order #<?= $order['ORDER_ID'] ?></h2>
                            <?php
                                $orderDateTime = new DateTime($order['CREATED_AT']);
                                $formattedDate = $orderDateTime->format('F j, Y, g:i A');
                            ?>
                            <p><strong>Order Date:</strong> <?= $formattedDate ?></p>
                            <p><strong>Order Status:</strong> <?= $order['ORDER_STATUS'] ?></p>
                            <p><strong>Delivery Address:</strong> <?= htmlspecialchars($order['DV_ADDRESS'] . ', ' . $order['city']) ?></p>
                            <p><strong>Payment Method:</strong> <?= $order['MODE_OF_PAYMENT'] ?></p>
                            <?php if ($order['MODE_OF_PAYMENT'] == 'gcash' && !empty($order['GCASH_REF_NUMBER'])): ?>
                                <p><strong>GCash Reference Number:</strong> <?= htmlspecialchars($order['GCASH_REF_NUMBER']) ?></p>
                            <?php endif; ?>

                            <h3>Order Items</h3>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orderItems[$order['ORDER_ID']] as $item): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item['PRODUCT_NAME']) ?></td>
                                            <td><?= $item['QUANTITY'] ?></td>
                                            <td>₱<?= number_format($item['PRICE'], 2) ?></td>
                                            <td>₱<?= number_format($item['PRICE'] * $item['QUANTITY'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">Total Amount:</th>
                                        <th>₱<?= number_format($order['TOTAL_AMOUNT'], 2) ?></th>
                                    </tr>
                                    <?php if ($order['MODE_OF_PAYMENT'] != 'cash'): ?>
                                        <tr>
                                            <th colspan="3" class="text-end">Amount Tendered:</th>
                                            <th>₱<?= number_format($order['AMOUNT_TENDERED'], 2) ?></th>
                                        </tr>
                                        <tr>
                                            <th colspan="3" class="text-end">Change:</th>
                                            <th>₱<?= number_format($order['CHANGE_AMOUNT'], 2) ?></th>
                                        </tr>
                                    <?php endif; ?>
                                </tfoot>
                            </table>
                            <div class="mt-3">
                                <?php if ($order['ORDER_STATUS'] === 'Pending'): ?>
                                    <button class="btn btn-danger" onclick="showCancelModal(<?= $order['ORDER_ID'] ?>)">Cancel Order</button>
                                <?php elseif ($order['ORDER_STATUS'] === 'Shipped'): ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="order_id" value="<?= $order['ORDER_ID'] ?>">
                                        <input type="hidden" name="new_status" value="Received">
                                        <button type="submit" name="update_status" class="btn btn-received">Order Received</button>
                                    </form>
                                    <button class="btn btn-danger" disabled>Cancel Order</button>
                                <?php elseif ($order['ORDER_STATUS'] === 'Received'): ?>
                                    <span class="badge bg-success">Order Received</span>
                                <?php elseif ($order['ORDER_STATUS'] === 'Cancelled'): ?>
                                    <span class="badge bg-danger">Order Cancelled</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No order details available.</p>
                <?php endif; ?>

                <div class="text-center mt-4">
                    <?php if (!$showAllOrders): ?>
                        <a href="?show_all=1" class="btn btn-primary">View All Orders</a>
                    <?php else: ?>
                        <a href="?" class="btn btn-secondary">Show Recent Orders</a>
                    <?php endif; ?>
                    <a href="main.php" class="btn btn-primary">Back to Home</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Order Modal -->
    <div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelOrderModalLabel">Cancel Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="order_id" id="cancelOrderId">
                        <div class="mb-3">
                            <label for="cancelReason" class="form-label">Reason for Cancellation</label>
                            <select class="form-select" id="cancelReason" name="cancel_reason" required>
                                <option value="">Select a reason</option>
                                <option value="Changed my mind">Changed my mind</option>
                                <option value="Found a better price">Found a better price</option>
                                <option value="Delivery is too slow">Delivery is too slow</option>
                                <option value="Ordered by mistake">Ordered by mistake</option>
                                <option value="Payment issues">Payment issues</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="cancel_order" class="btn btn-primary">Submit Cancellation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script>
        function showCancelModal(orderId) {
            document.getElementById('cancelOrderId').value = orderId;
            var modal = new bootstrap.Modal(document.getElementById('cancelOrderModal'));
            modal.show();
        }
    </script>
</body>
</html>