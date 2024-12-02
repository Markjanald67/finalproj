<?php
session_start();

// Check if the user is already logged in
if (isset($_SESSION['user_name'])) {
    header("Location: main.php");
    exit();
}

// Initialize notification variables
$notification = '';
$notificationType = '';

// Include database connection
include 'db_connection.php';

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['login_email'];
    $password = $_POST['login_password'];

    $stmt = $conn->prepare("SELECT USER_ID, USER_NAME, PASSWORD, city FROM users WHERE EMAIL = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['PASSWORD'])) {
            $_SESSION['user_id'] = $user['USER_ID'];
            $_SESSION['user_name'] = $user['USER_NAME'];
            $_SESSION['user_city'] = $user['city'];
            $_SESSION['login_message'] = "Login successful! Welcome back.";
            header("Location: main.php");
            exit();
        } else {
            $notification = "Invalid password.";
            $notificationType = 'error';
        }
    } else {
        $notification = "No user found with that email.";
        $notificationType = 'error';
    }

    // Redirect back to login page to display the error
    if (isset($notification)) {
        $_SESSION['login_error'] = $notification;
        header("Location: login.php");
        exit();
    }
}

// Check for login messages, errors, and sign-up success
if (isset($_SESSION['login_message'])) {
    $notification = $_SESSION['login_message'];
    $notificationType = 'success';
    unset($_SESSION['login_message']);
} elseif (isset($_SESSION['login_error'])) {
    $notification = $_SESSION['login_error'];
    $notificationType = 'error';
    unset($_SESSION['login_error']);
} elseif (isset($_SESSION['signup_success'])) {
    $notification = $_SESSION['signup_success'];
    $notificationType = 'success';
    unset($_SESSION['signup_success']);
}

// Prepare notification HTML
$notificationHtml = '';
if ($notification) {
    $notificationHtml = "<div class='notification {$notificationType}' id='notification'>{$notification}</div>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - QPAL</title>
    <link rel="stylesheet" type="text/css" href="./style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        .login-container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 400px;
            max-width: 100%;
            animation: fadeIn 0.5s ease-out, float 6s ease-in-out infinite;
            position: relative;
        }
        .login-container::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background-color: transparent;
            border: 2px solid black;
            z-index: -1;
            filter: blur(20px);
            animation: glowing 10s linear infinite;
            opacity: 0.7;
            border-radius: 16px;
        }
        .login-header {
            background-color: black;
            color: white;
            padding: 20px;
            text-align: center;
            font-weight: 600;
        }
        .login-form {
            padding: 30px;
        }
        .form-control {
            border: 2px solid #e1e1e1;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        .btn-login {
            background-color: transparent;
            border: 1px solid black;
            border-radius: 5px;
            color: black;
            font-weight: 600;
            padding: 10px;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            background-color: black;
            color: white;
            transform: translateY(-2px);
        }
        .signup-link {
            text-align: center;
            margin-top: 20px;
        }
        .notification {
            position: fixed;
            top: 12%;
            left: 50%;
            transform: translateX(-50%);
            padding: 15px 25px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            opacity: 0;
            transition: top 0.5s ease, opacity 0.5s ease;
            margin-top: 100px;
        }
        .notification.show {
            top: 20px;
            opacity: 1;
        }
        .notification.success {
            background-color: #28a745;
        }
        .notification.error {
            background-color: #dc3545;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        @keyframes glowing {
            0% { background-position: 0 0; }
            50% { background-position: 400% 0; }
            100% { background-position: 0 0; }
        }
        .form-floating {
            margin-bottom: 15px;
        }
        .form-floating label {
            transition: all 0.3s ease;
        }
        .form-floating input:focus + label,
        .form-floating input:not(:placeholder-shown) + label {
            transform: translateY(-1.5rem) scale(0.85);
            color: #007bff;
        }
        @media (max-width: 768px) {
            .notification {
                width: 90%;
                max-width: 300px;
            }
        }
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translate(-50%, -20px);
            }
            to {
                opacity: 1;
                transform: translate(-50%, 0);
            }
        }
        .notification.show {
            animation: fadeInDown 0.5s ease forwards;
        }
        .error-message {
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body>
    <!-- Warning Banner -->
    <div class="warning-banner" data-aos="fade-down" data-aos-delay="700" style="position: fixed; top: 0; left: 0; right: 0; z-index: 1001;">
        WARNING: This product contains nicotine. Nicotine is an addictive chemical.
    </div>

    <!-- Header -->
    <header data-aos="fade-down" data-aos-delay="500" style="position: fixed; top: 0px; left: 0; right: 0; z-index: 1000; ">
        <nav class="navbar navbar-expand-lg " style="  background-color: #edf1f8 !important; border-bottom: 2px solid grey; padding: 0 !important;">
            <div class="container">
                <a class="navbar-brand fw-bold text-dark" href="#" ><img src="./logo.svg" alt="" style="width: 100%;"></a>
                <button class="navbar-toggler" type="button" aria-label="Toggle navigation" >
                    <div class="burger-icon" >
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </button>
                <div class="collapse navbar-collapse justify-content-between  " id="navbarNav" style="  background-color: #edf1f8 !important; ">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item mx-4">
                            <a class="nav-link text-dark" href="./main.php"> Home</a>
                        </li>
                        <li class="nav-item mx-4">
                            <a class="nav-link text-dark" href="./aboutpage.php">About</a>
                        </li>
                        <li class="nav-item mx-4">
                            <a class="nav-link text-dark" href="./product.php">Products</a>
                        </li>
                    </ul>
                    <div class="d-flex">
                    <?php if (isset($_SESSION['user_name'])): // Check if user is logged in ?>
                        <div class="dropdown" >
                            <button class="btn btn-outline-success dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false" >
                                <?= htmlspecialchars($_SESSION['user_name']) ?> <!-- Display user name -->
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="userDropdown" style=" margin: 0px  -50px;">
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li> <!-- Link to logout -->
                                <li>
                                <?php if (isset($_SESSION['user_name'])): ?>
                                <a href="./account-dashboard.php" class="dropdown-item">Account Settings</a>
                                    <?php endif; ?>
                                </li>
                                <li><a class="dropdown-item" href="order.php">My Orders</a></li> <!-- Link to orderslip -->
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="./login.php"><button class="btn btn-outline-success" type="button" id="loginBtn">Login</button></a>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <div class="side-menu " style="background-color: #edf1f8 !important;">
        <button type="button" class="btn-close" aria-label="Close"></button>
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="#">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#about">About</a>
            </li>
            <li class="nav-item ">
                <a class="nav-link " href="./product.php" id="productDropdown" role="button" aria-expanded="false">
                    Products
                </a>
            </li>
        </ul>
        <div class="mt-4">
        <?php if (isset($_SESSION['user_name'])): // Check if user is logged in ?>
            <div class="dropdown">
                <button class="btn btn-outline-success dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <?= htmlspecialchars($_SESSION['user_name']) ?> <!-- Display user name -->
                </button>
                <ul class="dropdown-menu" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="logout.php">Logout</a></li> <!-- Link to logout -->
                    <li><a class="dropdown-item" href="order.php">My Orders</a></li> <!-- Link to logout -->
                </ul>
            </div>
        <?php else: ?>
            <a href="signup.php"><button class="btn btn-outline-success" type="button" id="signUpBtn">Sign Up</button></a>
        <?php endif; ?>
        </div>
    </div>

    <!-- Spacer to prevent content from being hidden under fixed elements -->
    <div style="height: 50px;"></div>

    <!-- Updated Notification Area -->
    <?php echo $notificationHtml; ?>

    <!-- Login Form -->
    <div class="login-container">
        <div class="login-header">
            <h2>Welcome to QPAL</h2>
        </div>
        <div class="login-form">
            <form id="loginForm" method="post" action="login.php" novalidate>
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="login_email" name="login_email" placeholder="name@example.com" required>
                    <label for="login_email">Email address</label>
                    <div class="error-message" id="email-error"></div>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="login_password" name="login_password" placeholder="Password" required>
                    <label for="login_password">Password</label>
                    <div class="error-message" id="password-error"></div>
                </div>
                <button type="submit" class="btn btn-login w-100">Login</button>
            </form>
            <div class="signup-link">
                <p>Don't have an account? <a href="signup.php">Sign up here</a>.</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const notification = document.getElementById('notification');
            if (notification) {
                notification.classList.add('show');
                setTimeout(() => {
                    notification.style.opacity = '0';
                    notification.style.top = '-100px';
                    setTimeout(() => {
                        notification.remove();
                    }, 500);
                }, 5000);
            }

            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.addEventListener('focus', () => {
                    input.parentElement.style.transform = 'translateY(-5px)';
                });
                input.addEventListener('blur', () => {
                    input.parentElement.style.transform = 'translateY(0)';
                });
            });

            // Client-side form validation
            const form = document.getElementById('loginForm');
            const emailInput = document.getElementById('login_email');
            const passwordInput = document.getElementById('login_password');
            const emailError = document.getElementById('email-error');
            const passwordError = document.getElementById('password-error');

            form.addEventListener('submit', function(event) {
                let isValid = true;

                // Validate email
                if (!emailInput.value || !isValidEmail(emailInput.value)) {
                    emailError.textContent = 'Please enter a valid email address.';
                    isValid = false;
                } else {
                    emailError.textContent = '';
                }

                // Validate password
                if (!passwordInput.value || passwordInput.value.length < 8) {
                    passwordError.textContent = 'Password must be at least 8 characters long.';
                    isValid = false;
                } else {
                    passwordError.textContent = '';
                }

                if (!isValid) {
                    event.preventDefault();
                }
            });

            function isValidEmail(email) {
                const re = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                return re.test(String(email).toLowerCase());
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./main.js"></script>
</body>
</html>