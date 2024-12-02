<?php


include 'header.php';


if (isset($_SESSION['error'])) {
    echo "<div class='alert alert-danger'>" . $_SESSION['error'] . "</div>";
    unset($_SESSION['error']); // Clear error after displaying
}

// Database connection
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "quickpuff"; 

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Get user ID
$userId = $_SESSION['user_id']; // Assuming user_id is stored in session

// Fetch cart items for the user
$sql = "SELECT c.cart_id, p.PRODUCT_NAME, p.PRICE, c.quantity 
        FROM cart c 
        JOIN products p ON c.product_id = p.PRODUCT_ID 
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link rel="stylesheet" href="./style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
                   <!--fONT AWESOME-->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<style>
table {
    width: 100%;
    margin-top: 80px;
    background-color: transparent !important;
    border: 3px solid black !important;
   
}
.btn-success {
    border-radius: 10px !important;
    padding: 12px 30px;
    background-color: transparent;
    border: 2px solid black;
    color: black;
}
.btn-success:hover {
    background-color: black;
    color: white;
    border: 2px solid black;
}
.product-checkbox {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }
</style>
<body>

   
	
   <div style="height:100px;"></div>

   <div class="container mt-5" style="height: 100vh;">
        <h1 class="text-center mb-4" style="color: black; margin-top: 50px">Your Cart</h1>
        <form id="cartForm" method="POST" action="update_cart.php">
            <table class="table">
                <thead style="border: 2px solid black !important;">
                    <tr>
                        <th>Select</th>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalAmount = 0;
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $total = $row['PRICE'] * $row['quantity'];
                            $totalAmount += $total;
                            echo "<tr data-cart-id='{$row['cart_id']}'>
                                    <td><input type='checkbox' name='selected_products[]' value='{$row['cart_id']}' class='product-checkbox'></td>
                                    <td>{$row['PRODUCT_NAME']}</td>
                                    <td>₱" . number_format($row['PRICE'], 2) . "</td>
                                    <td>
                                        <button type='button' class='btn btn-sm btn-secondary decrease-quantity'>-</button>
                                        <span class='quantity'>{$row['quantity']}</span>
                                        <button type='button' class='btn btn-sm btn-secondary increase-quantity'>+</button>
                                    </td>
                                    <td class='item-total'>₱" . number_format($total, 2) . "</td>
                                    <td>
                                        <a href='remove_from_cart.php?cart_id={$row['cart_id']}' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to remove this item from your cart?\");'>Remove</a>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center'>Your cart is empty.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            
            <h3>Total Amount: ₱<span id="total-amount"><?php echo number_format($totalAmount, 2); ?></span></h3>
            <?php if ($result->num_rows > 0): ?>
        
                <button type="button" class="btn btn-success" id="checkout-button" disabled>Proceed to Checkout</button>
            <?php else: ?>
                <button class="btn btn-success" disabled>Proceed to Checkout</button>
                <p class="text-danger mt-2">Your cart is empty. Add items to your cart before checking out.</p>
            <?php endif; ?>
        </form>
    </div>


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
    <script>
   $(document).ready(function() {
        $('.increase-quantity, .decrease-quantity').click(function() {
            var $row = $(this).closest('tr');
            var cartId = $row.data('cart-id');
            var $quantitySpan = $row.find('.quantity');
            var currentQuantity = parseInt($quantitySpan.text());
            var newQuantity = $(this).hasClass('increase-quantity') ? currentQuantity + 1 : Math.max(1, currentQuantity - 1);

            updateCartItem(cartId, newQuantity);
        });

        function updateCartItem(cartId, newQuantity) {
            $.ajax({
                url: 'update_cart.php',
                method: 'POST',
                data: { cart_id: cartId, quantity: newQuantity },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Failed to update cart: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while updating the cart.');
                }
            });
        }

        // Add event listener for checkbox changes
        $('.product-checkbox').change(function() {
            updateTotalAmount();
        });

        function updateTotalAmount() {
            var totalAmount = 0;
            $('.product-checkbox:checked').each(function() {
                var $row = $(this).closest('tr');
                var itemTotal = parseFloat($row.find('.item-total').text().replace('₱', '').replace(',', ''));
                totalAmount += itemTotal;
            });
            $('#total-amount').text(totalAmount.toFixed(2));
        }

        // Enable/disable checkout button based on checkbox selection
        $('.product-checkbox').change(function() {
            var anyChecked = $('.product-checkbox:checked').length > 0;
            $('#checkout-button').prop('disabled', !anyChecked);
        });

        $('#checkout-button').click(function() {
            if ($('.product-checkbox:checked').length === 0) {
                alert('Please select at least one product before proceeding to checkout.');
            } else {
                window.location.href = 'checkout.php'; // Redirect to checkout
            }
        });
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./main.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
