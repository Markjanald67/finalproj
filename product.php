<?php

require_once 'db_connection.php';

include 'header.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch products from the database
$sql = "SELECT PRODUCT_ID, PRODUCT_NAME, image_path, PRICE, STOCK, FLAVOURS, EXP_DATE FROM products";
$result = $conn->query($sql);


?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Product List</title>
        <link rel="stylesheet" href="./style.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
                <!--fONT AWESOME-->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <style>
            body {
                background-color: #edf1f8 !important;
                color: rgb(0, 0, 0) !important;
            
            }
            .card {
                transition: transform 0.2s;
                border: 1px solid black;
            }
            .card:hover {
                transform: scale(1.05);
            }
            .card-img-top {
                width: 100%;
                height: auto;
                max-width: 200px;
                object-fit: contain;
                margin: 0 auto;
            }
           
            .btn {
                background-color: grey;
                border: none;
                padding: 10px 20px;
            }
            .btn:hover{
                background-color: black;
                border: none;
                padding: 10px 20px;

            }
            .btn:disabled {
                background-color: #ccc;
                cursor: not-allowed;
            }
            .btn1{
                background-color: transparent;
                border: 1px solid black;
                border-radius: 5px;
                color: black;
                font-weight: 600;
                padding: 10px;
            }
        </style>
    </head>
    <body>

  	

   

    <div class="container mt-5">
        <h1 class="text-center mb-4">Product List</h1>
        <div class="row" id="product-list">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $productId = $row["PRODUCT_ID"];
                    $imagePath = $row["image_path"] ? 'ADMIN/views/' . $row["image_path"] : 'images/default.webp';
                    $isOutOfStock = $row["STOCK"] <= 0;
                    echo '<div class="col-md-4 col-sm-6 mb-4">';
                    echo '<div class="card">';
                    echo '<img src="' . htmlspecialchars($imagePath) . '" class="card-img-top" alt="' . htmlspecialchars($row["PRODUCT_NAME"]) . '">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . htmlspecialchars($row["PRODUCT_NAME"]) . '</h5>';
                    echo '<p class="card-text">Price: ₱' . number_format($row["PRICE"], 2) . '</p>';
                    echo '<p class="card-text stock" data-stock="' . $row["STOCK"] . '">Stock: ' . $row["STOCK"] . '</p>';
                    echo '<p class="card-text">Variety/Flavor: ' . htmlspecialchars($row["FLAVOURS"]) . '</p>';
                    echo '<p class="card-text">Expiration Date: ' . date("M d, Y", strtotime($row["EXP_DATE"])) . '</p>';
                    echo '<input type="number" class="form-control quantity-input" style="width: 80px; display: inline-block; margin-right: 10px; border: 1px solid black;" min="1" max="' . $row["STOCK"] . '" value="1" ' . ($isOutOfStock ? 'disabled' : '') . '>';
                    echo '<button class="btn btn-primary add-to-cart" data-name="' . htmlspecialchars($row["PRODUCT_NAME"]) . '" data-price="' . $row["PRICE"] . '" data-id="' . $productId . '" ' . ($isOutOfStock ? 'disabled' : '') . '>' . ($isOutOfStock ? 'Out of Stock' : 'Add to Cart') . '</button>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>No products found.</p>';
            }
            ?>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./main.js"></script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    const cartCountElement = document.querySelector('.cart-count');

    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            const productName = this.getAttribute('data-name');
            const quantityInput = this.previousElementSibling;
            const quantity = parseInt(quantityInput.value);
            const stockElement = this.closest('.card-body').querySelector('.stock');
            const currentStock = parseInt(stockElement.getAttribute('data-stock'));

            <?php if (!isset($_SESSION['user_id'])): ?>
                alert("You need to login to add this product to your cart.");
                window.location.href = "login.php";
                return;
            <?php else: ?>
                if (currentStock <= 0) {
                    alert("This product is out of stock.");
                    return;
                }

                if (quantity > currentStock) {
                    alert("Not enough stock available. Current stock: " + currentStock);
                    return;
                }

                fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `product_id=${productId}&quantity=${quantity}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(`${productName} has been added to your cart.`);
                        
                        // Update stock display
                        const newStock = currentStock - quantity;
                        stockElement.textContent = `Stock: ${newStock}`;
                        stockElement.setAttribute('data-stock', newStock);
                        
                        // Update cart count immediately
                        cartCountElement.textContent = data.cartCount;
                        
                        // Reset quantity input
                        quantityInput.value = 1;
                        quantityInput.max = newStock;

                        // Disable button if out of stock
                        if (newStock <= 0) {
                            this.disabled = true;
                            this.textContent = 'Out of Stock';
                            quantityInput.disabled = true;
                        }
                    } else {
                        alert('Failed to add product: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while adding the product to the cart.');
                });
            <?php endif; ?>
        });
    });
});
</script>




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


	

    </body>
    </html>

    <?php
    $conn->close();
    ?>