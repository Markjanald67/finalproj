<?php
session_start();
require_once 'db_connection.php';

// Function to get cart count
function getCartCount($conn, $userId) {
    $sql = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total'] ?? 0;
}

// Get cart count if user is logged in
$cartCount = 0;
$isLoggedIn = isset($_SESSION['user_id']);
if ($isLoggedIn) {
    $cartCount = getCartCount($conn, $_SESSION['user_id']);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'QPAL'; ?></title>
    <link rel="stylesheet" href="./style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        /* Add your custom styles here */
        body {
            background-color: #edf1f8 !important;
            color: rgb(0, 0, 0) !important;
        }
        .cart-icon {
            position: relative;
            cursor: pointer;
            z-index: 1000;
        }
        .cart-count {
            position: absolute;
            top: -5px;
            right: -10px;
            background: grey;
            color: white;
            border-radius: 45%;
            padding: 3px 7px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <!-- Warning Banner -->
    <div class="warning-banner" data-aos="fade-down" data-aos-delay="700" style="position: fixed; top: 0; left: 0; right: 0; z-index: 1001;">
        WARNING: This product contains nicotine. Nicotine is an addictive chemical.
    </div>

    <!-- Header -->
    <header data-aos="fade-down" data-aos-delay="500" style="position: fixed; top: 0px; left: 0; right: 0; z-index: 1000;">
        <nav class="navbar navbar-expand-lg" style="background-color: #edf1f8 !important; border-bottom: 2px solid grey; padding: 0 !important;">
            <div class="container">
                <a class="navbar-brand fw-bold text-dark" href="#"><img src="./logo.svg" alt="" style="width: 100%;"></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <div class="burger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </button>
                <div class="collapse navbar-collapse justify-content-between" id="navbarNav" style="background-color: #edf1f8 !important;">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item mx-4">
                            <a class="nav-link text-dark" href="./main.php">Home</a>
                        </li>
                        <li class="nav-item mx-4">
                            <a class="nav-link text-dark" href="./aboutpage.php">About</a>
                        </li>
                        <li class="nav-item mx-4">
                            <a class="nav-link text-dark" href="./product.php">Products</a>
                        </li>
                    </ul>
                    <!-- Cart Icon -->
                    <a href="#" class="cart-icon me-3" onclick="checkLoginBeforeCart(event)">
                        <i class="fas fa-shopping-cart" style="color: black; font-size: 1.5rem;"></i>
                        <span class="cart-count"><?= $cartCount ?></span>
                    </a>
                    <div class="d-flex align-items-center">
                        <?php if ($isLoggedIn): // Check if user is logged in ?>
                            <div class="dropdown me-3">
                                <button class="btn btn-outline-success dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <?= htmlspecialchars($_SESSION['user_name']) ?> <!-- Display user name -->
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="userDropdown">
                                    <li><a class="dropdown-item" href="./account-dashboard.php"><i class="fas fa-user-cog"></i> Account Settings</a></li>
                                    <li><a class="dropdown-item" href="order.php"><i class="fas fa-box"></i> My Orders</a></li>
                                    <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <a href="./login.php" class="me-3"><button class="btn btn-outline-success" type="button" id="loginBtn">Login</button></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <div class="side-menu" style="background-color: #edf1f8 !important;">
        <button type="button" class="btn-close" aria-label="Close"></button>
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="./main.php">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="./aboutpage.php">About</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="./product.php" id="productDropdown" role="button" aria-expanded="false">
                    Products
                </a>
            </li>
        </ul>
        <!-- Cart Icon -->
        <a href="#" class="cart-icon me-3" onclick="checkLoginBeforeCart(event)">
            <i class="fas fa-shopping-cart" style="color: black; font-size: 1.5rem;"></i>
            <span class="cart-count"><?= $cartCount ?></span>
        </a>
        <div class="d-flex">
            <?php if ($isLoggedIn): // Check if user is logged in ?>
                <div class="dropdown">
                    <button class="btn btn-outline-success dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <?= htmlspecialchars($_SESSION['user_name']) ?> <!-- Display user name -->
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="userDropdown" style="margin: 0px -50px;">
                        <li>
                            <a href="./account-dashboard.php" class="dropdown-item">
                                <i class="fas fa-user-cog"></i> Account Settings
                            </a>
                        </li>
                        <li><a class="dropdown-item" href="order.php">
                            <i class="fas fa-box"></i> My Orders
                        </a></li>
                        <li><a class="dropdown-item" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a></li>
                    </ul>
                </div>
            <?php else: ?>
                <a href="./login.php"><button class="btn btn-outline-success" type="button" id="loginBtn">Login</button></a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Spacer to prevent content from being hidden under fixed elements -->
    <div style="height: 80px;"></div>

    <!-- Content of the page will be inserted here -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function checkLoginBeforeCart(event) {
            event.preventDefault();
            <?php if (!$isLoggedIn): ?>
                alert("You need to login to view your cart.");
                window.location.href = 'login.php';
            <?php else: ?>
                window.location.href = 'cart.php';
            <?php endif; ?>
        }
    </script>
    <script src="./main.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>