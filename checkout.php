<?php
// Start output buffering at the very beginning of the script
ob_start();

include 'header.php';   
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
require_once 'db_connection.php';

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch user information
$userSql = "SELECT USER_NAME, EMAIL, CONTACT_NO, city, AGE FROM users WHERE USER_ID = ?";
$userStmt = $conn->prepare($userSql);
$userStmt->bind_param("i", $userId);
$userStmt->execute();
$userResult = $userStmt->get_result();
$userInfo = $userResult->fetch_assoc();

// Fetch cart items
$cartSql = "SELECT c.cart_id, c.product_id, p.PRODUCT_NAME, p.PRICE, c.quantity 
            FROM cart c 
            JOIN products p ON c.product_id = p.PRODUCT_ID 
            WHERE c.user_id = ?";
$cartStmt = $conn->prepare($cartSql);
$cartStmt->bind_param("i", $userId);
$cartStmt->execute();
$cartResult = $cartStmt->get_result();

// Calculate total amount
$totalAmount = 0;
$cartItems = [];
while ($item = $cartResult->fetch_assoc()) {
    $totalAmount += $item['PRICE'] * $item['quantity'];
    $cartItems[] = $item;
}

// Fetch city names
$citySql = "SELECT city_name FROM city_demographics";
$cityResult = $conn->query($citySql);
$cities = [];
if ($cityResult->num_rows > 0) {
    while ($row = $cityResult->fetch_assoc()) {
        $cities[] = $row['city_name'];
    }
}

// Function to generate a unique reference number
function generateReferenceNumber() {
    return 'GCASH' . time() . rand(1000, 9999);
}

// Function to simulate GCash payment
function processGCashPayment($amount, $gcashNumber) {
    // In a real-world scenario, you would integrate with the GCash API here
    // For this example, we'll simulate the process
    $success = (rand(0, 10) > 1); // 90% success rate for simulation
    return [
        'success' => $success,
        'reference_number' => generateReferenceNumber(),
        'message' => $success ? 'Payment successful' : 'Payment failed'
    ];
}

$redirect = false;
$redirectUrl = '';

// Process checkout
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dvAddress = trim($_POST['dv_address']); // Trim to remove whitespace
    $paymentMethod = $_POST['payment_method'];
    $newCity = $_POST['city'];
    $newUserName = $_POST['name'];
    $newContactNo = $_POST['contact'];

    // Check if delivery address is empty
    if (empty($dvAddress)) {
        $error = "Delivery address is required. Please fill it out before placing your order.";
    } else {
        // Update user's city, name, and contact number in the database
        $updateUserSql = "UPDATE users SET city = ?, USER_NAME = ?, CONTACT_NO = ? WHERE USER_ID = ?";
        $updateUserStmt = $conn->prepare($updateUserSql);
        $updateUserStmt->bind_param("sssi", $newCity, $newUserName, $newContactNo, $userId);
        $updateUserStmt->execute();
        $updateUserStmt->close();

        // Initialize total quantity and amount
        $totalQuantity = 0;
        $totalAmount = 0;

        foreach ($cartItems as $item) {
            $totalQuantity += $item['quantity'];
            $totalAmount += $item['PRICE'] * $item['quantity'];
        }

        // Check if total quantity is greater than 0
        if ($totalQuantity <= 0) {
            $error = "No items in the cart to checkout.";
        } else {
            // Start a transaction
            $conn->begin_transaction();

            try {
                // Insert into orders table
                $orderSql = "INSERT INTO orders (USER_ID, TOTAL_AMOUNT, QUANTITY, ORDER_STATUS, CREATED_AT) 
                             VALUES (?, ?, ?, 'Pending', NOW())";
                $orderStmt = $conn->prepare($orderSql);
                $orderStmt->bind_param("idi", $userId, $totalAmount, $totalQuantity);
                if (!$orderStmt->execute()) {
                    throw new Exception("Order insertion failed: " . $orderStmt->error);
                }
                $orderId = $conn->insert_id;

                // Insert into order_items table for each product
                $orderItemSql = "INSERT INTO order_items (ORDER_ID, PRODUCT_ID, QUANTITY, PRICE) 
                                 VALUES (?, ?, ?, ?)";
                $orderItemStmt = $conn->prepare($orderItemSql);

                foreach ($cartItems as $item) {
                    if ($item['quantity'] > 0) {
                        $orderItemStmt->bind_param("iiid", $orderId, $item['product_id'], $item['quantity'], $item['PRICE']);
                        if (!$orderItemStmt->execute()) {
                            throw new Exception("Order item insertion failed: " . $orderItemStmt->error);
                        }
                    }
                }

                $orderItemStmt->close();

               // Process payment
                $changeAmount = 0;
                $amountTendered = 0;
                $gcashRefNumber = null;

                if ($paymentMethod == 'gcash') {
                    $gcashNumber = $_POST['gcash_number'];
                    $amountTendered = isset($_POST['amount_tendered']) ? floatval($_POST['amount_tendered']) : 0;

                    // Check if amount tendered is less than total amount
                    if ($amountTendered < $totalAmount) {
                        throw new Exception("Insufficient amount tendered for GCash payment.");
                    }
                    
                    // Simulate GCash payment processing
                    $paymentResult = processGCashPayment($totalAmount, $gcashNumber);
                
                    if (!$paymentResult['success']) {
                        throw new Exception("GCash payment failed. Please try again.");
                    }
                
                    // Store GCash reference number
                    $gcashRefNumber = $paymentResult['reference_number'];
                    $changeAmount = $amountTendered - $totalAmount;
                } elseif ($paymentMethod == 'cash') {
                    // For cash on delivery, set amount tendered to 0 as it will be collected upon delivery
                    $amountTendered = 0;
                    $changeAmount = 0;
                } else {
                    throw new Exception("Invalid payment method selected.");
                }
                
                // Update the transactions table insertion
                $transactionSql = "INSERT INTO transactions (ORDER_ID, USER_ID, DV_ADDRESS, MODE_OF_PAYMENT, TOTAL_AMOUNT, CHANGE_AMOUNT, AMOUNT_TENDERED, GCASH_REF_NUMBER) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $transactionStmt = $conn->prepare($transactionSql);
                $transactionStmt->bind_param("iissddds", $orderId, $userId, $dvAddress, $paymentMethod, $totalAmount, $changeAmount, $amountTendered, $gcashRefNumber);

                if (!$transactionStmt->execute()) {
                    throw new Exception("Transaction insertion failed: " . $transactionStmt->error);
                }

                // Clear the cart
                $clearCartSql = "DELETE FROM cart WHERE user_id = ?";
                $clearCartStmt = $conn->prepare($clearCartSql);
                $clearCartStmt->bind_param("i", $userId);
                $clearCartStmt->execute();
                $clearCartStmt->close();

                // Commit the transaction
                $conn->commit();

                $_SESSION['message'] = "Checkout successful! Your order has been placed.";
                if ($paymentMethod == 'gcash') {
                    $_SESSION['message'] .= " GCash Reference Number: " . $gcashRefNumber;
                }
                $redirect = true;
                $redirectUrl = "order.php?order_id=" . $orderId;
            } catch (Exception $e) {
                // An error occurred, rollback the transaction
                $conn->rollback();
                $error = "Error processing your order. Please try again. " . $e->getMessage();
            }
        }
    }
}

// HTML part starts here
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="./style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
                   <!--fONT AWESOME-->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #edf1f8;
        }
        .checkout-container {
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 50px;
        }
        .btn-custom {
            background-color: #000;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            background-color: #333;
            color: #fff;
        }
    </style>
</head>
<body>


    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 checkout-container">
                <h2 class="text-center mb-4">Checkout</h2>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <form method="POST" action="" id="checkoutForm">
                    <div class="row">
                        <!-- Left Column: User Information and Payment -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($userInfo['USER_NAME']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($userInfo['EMAIL']); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="contact" class="form-label">Contact Number</label>
                                <input type="text" class="form-control" id="contact" name="contact" value="<?php echo htmlspecialchars($userInfo['CONTACT_NO']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="city" class="form-label">City</label>
                                <select class="form-select" id="city" name="city" required>
                                    <option value="">Select your city</option>
                                    <?php foreach ($cities as $city): ?>
                                        <option value="<?php echo htmlspecialchars($city); ?>" <?php echo ($userInfo['city'] == $city) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($city); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="dv_address" class="form-label">Delivery Address</label>
                                <textarea class="form-control" id="dv_address" name="dv_address" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select class="form-select" id="payment_method" name="payment_method" required onchange="togglePaymentFields()">
                                    <option value="">Select payment method</option>
                                    <option value="cash">Cash on Delivery</option>
                                    <option value="gcash">GCash</option>
                                </select>
                            </div>
                            <div id="gcash_fields" style="display: none;">
                                <div class="mb-3">
                                    <label for="gcash_number" class="form-label">GCash Number</label>
                                    <input type="text" class="form-control" id="gcash_number" name="gcash_number" placeholder="Enter your GCash number">
                                </div>
                                <div class="mb-3">
                                    <button type="button" class="btn btn-primary" onclick="simulateGCashPayment()">Connect to Gcash</button>
                                </div>
                                <div id="gcash_amount_tendered" style="display: none;">
                                    <div class="mb-3">
                                        <label for="amount_tendered" class="form-label">Amount Tendered</label>
                                        <input type="number" step="0.01" class="form-control" id="amount_tendered" name="amount_tendered" placeholder="Enter the amount tendered">
                                        <div id="amountError" class="text-danger" style="display: none;"></div>
                                    </div>
                                </div>
                                <div id="gcash_confirmation" style="display: none;"></div>
                            </div>
                        </div>
                        
                        <!-- Right Column: Order Summary -->
                        <div class="col-md-6">
                            <h4 class="mb-3">Order Summary</h4>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($cartItems as $item): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['PRODUCT_NAME']); ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td>₱<?php echo number_format($item['PRICE'], 2); ?></td>
                                            <td>₱<?php echo number_format($item['PRICE'] * $item['quantity'], 2); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3" class="text-end">Total Amount:</th>
                                            <th>₱<?php echo number_format($totalAmount, 2); ?></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-custom" id="placeOrderButton">Place Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div style="height: 50px;"></div>

      <!---FOOOTERR---->
<footer class="site-footer">
		<div class="container">
			<div class="footer-content">
				<div class="footer-section about">
					<h3 class="logo-text">QPAL</h3>
					<p style="color: white;">
						QPAL is dedicated to providing high-quality vaping products with a focus on compatibility and user experience.
					</p>
					<div class="contact" style="color: #edf1f8;">
						<span><i class="fas fa-phone"></i> &nbsp; 09935367760</span>
						<span><i class="fas fa-envelope"></i> &nbsp; qpal@gmail.com</span>
					</div>
				</div>
				<div class="footer-section links">
					<h3>Quick Links</h3>
					<ul>
						<li><a href="./main.php">Home</a></li>
						<li><a href="./product.php">Products</a></li>
						<li><a href="./aboutpage.php">About Us</a></li>						
						
					</ul>
				</div>
				<div class="footer-section follow-us">
					<h3>Follow Us</h3>
					<div class="socials">
						<a href="#" style="margin-right: 15px; color:aqua;"><i class="fab fa-facebook fa-3x"></i></a>
						<a href="#" style="color:aqua;"><i class="fab fa-instagram fa-3x"></i></a>
						
					</div>
				</div>
			</div>
			<div class="footer-bottom" style="color: #edf1f8;">
			<a href="http://sunnaj.wuaze.com/?i=1" style="color:white;">	&copy; 2024 QPAL | Designed by Sunnaj | All rights reserved</a>
			</div>
		</div>
	</footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <script src="./main.js"></script>
   
   <script>
    function togglePaymentFields() {
    var paymentMethod = document.getElementById('payment_method').value;
    var gcashFields = document.getElementById('gcash_fields');
    var submitButton = document.getElementById('placeOrderButton');
    var gcashAmountTendered = document.getElementById('gcash_amount_tendered');
    
    if (paymentMethod === 'gcash') {
        gcashFields.style.display = 'block';
        gcashAmountTendered.style.display = 'none';
        submitButton.disabled = true;
    } else {
        gcashFields.style.display = 'none';
        gcashAmountTendered.style.display = 'none';
        submitButton.disabled = false;
    }
    }
    function simulateGCashPayment() {
    var gcashNumber = document.getElementById('gcash_number').value;
    var gcashConfirmation = document.getElementById('gcash_confirmation');
    var submitButton = document.getElementById('placeOrderButton');
    var gcashAmountTendered = document.getElementById('gcash_amount_tendered');
    
    if (gcashNumber.length === 11 && gcashNumber.startsWith('09')) {
        // Show loading state
        gcashConfirmation.innerHTML = '<div class="alert alert-info">Connecting to Gcash...</div>';
        gcashConfirmation.style.display = 'block';

        // Simulate API call delay
        setTimeout(function() {
            // Always succeed for testing purposes
            gcashConfirmation.innerHTML = '<div class="alert alert-success">GCash successfully connected. You can now proceed to enter the payment amount.</div>';
            gcashAmountTendered.style.display = 'block';
            submitButton.disabled = false;
        }, 2000); // 2 second delay to simulate processing
    } else {
        gcashConfirmation.innerHTML = '<div class="alert alert-danger">Please enter a valid GCash number (11 digits starting with 09)</div>';
        gcashConfirmation.style.display = 'block';
        gcashAmountTendered.style.display = 'none';
        submitButton.disabled = true;
    }
}

document.getElementById('placeOrderButton').addEventListener('click', function(e) {
    var paymentMethod = document.getElementById('payment_method').value;
    var totalAmount = <?php echo json_encode($totalAmount); ?>;
    var amountTendered = parseFloat(document.getElementById('amount_tendered').value) || 0;
    var amountError = document.getElementById('amountError');
    var dvAddress = document.getElementById('dv_address').value.trim();

    // Clear previous error messages
    amountError.style.display = 'none';
    amountError.innerText = '';

    // Check if delivery address is empty
    if (dvAddress === '') {
        e.preventDefault(); // Prevent form submission
        alert('Please enter a delivery address before placing your order.');
        return;
    }

    if (paymentMethod === 'gcash') {
        // Check if amount tendered is less than total amount for GCash
        if (amountTendered < totalAmount) {
            var neededAmount = totalAmount - amountTendered;
            amountError.innerText = "You need an additional ₱" + neededAmount.toFixed(2) + " to complete the payment.";
            amountError.style.display = 'block';
            return; // Prevent form submission
        }
    }

    // If all validations pass, submit the form
    document.getElementById('checkoutForm').submit();
});

        <?php if ($redirect): ?>
        window.location.href = '<?php echo $redirectUrl; ?>';
        <?php endif; ?>
</script>

</body>
</html>

<?php
$userStmt->close();
$cartStmt->close();
$conn->close();
?>