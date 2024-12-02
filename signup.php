    <?php
    session_start();
    include 'db_connection.php';

    // Initialize notification variables
    $notification = '';
    $notificationType = '';

    // Check for sign-up messages and errors
    if (isset($_SESSION['signup_message'])) {
        $notification = $_SESSION['signup_message'];
        $notificationType = 'success';
        unset($_SESSION['signup_message']);
    } elseif (isset($_SESSION['signup_error'])) {
        $notification = $_SESSION['signup_error'];
        $notificationType = 'error';
        unset($_SESSION['signup_error']);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];
        $contact_no = $_POST['contact_no'];
        $city = $_POST['city'];
        $age = $_POST['age'];
    
        // Validate password
        if (strlen($password) < 8 || !preg_match('/[!@#$%^&*]/', $password)) {
            $_SESSION['signup_error'] = "Password must be at least 8 characters long and include a special character.";
            header("Location: signup.php");
            exit();
        }
    
        // Check if passwords match
        if ($password !== $confirmPassword) {
            $_SESSION['signup_error'] = "Passwords do not match.";
            header("Location: signup.php");
            exit();
        }
    
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
        // Start transaction
        $conn->begin_transaction();
    
        try {
            // Check if user already exists
            $stmt = $conn->prepare("SELECT * FROM users WHERE EMAIL = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                throw new Exception("User with this email already exists.");
            }
    
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (USER_NAME, EMAIL, PASSWORD, CONTACT_NO, city, AGE, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("sssssi", $username, $email, $hashedPassword, $contact_no, $city, $age);
            $stmt->execute();
    
            // Get the inserted user's ID
            $user_id = $conn->insert_id;
    
            // Update city_demographics
            $stmt = $conn->prepare("INSERT INTO city_demographics (city_name, user_count, is_cavite) 
                                    VALUES (?, 1, ?) 
                                    ON DUPLICATE KEY UPDATE user_count = user_count + 1");
            $is_cavite = in_array($city, ['Cavite City', 'Bacoor', 'Imus', 'Dasmariñas', 'Kawit', 'Noveleta', 'Rosario', 'General Trias', 'Tanza', 'Trece Martires', 'Silang', 'Tagaytay', 'Carmona', 'Maragondon', 'Ternate', 'Naic', 'Indang', 'Alfonso', 'General Emilio Aguinaldo', 'Mendez', 'Amadeo', 'Magallanes']) ? 1 : 0;
            $stmt->bind_param("si", $city, $is_cavite);
            $stmt->execute();
    
            // Commit transaction
            $conn->commit();
    
            $_SESSION['signup_success'] = "Your account has been successfully created. Please log in.";
            header("Location: login.php");
            exit();
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            $_SESSION['signup_error'] = "Error: " . $e->getMessage();
            header("Location: signup.php");
            exit();
        }
    
        $stmt->close();
    }
    

    // Fetch cities from city_demographics table
    $cityQuery = "SELECT city_name FROM city_demographics ORDER BY is_cavite DESC, city_name ASC";
    $cityResult = $conn->query($cityQuery);
    $cityOptions = "";
    while ($cityRow = $cityResult->fetch_assoc()) {
        $cityOptions .= "<option value='" . htmlspecialchars($cityRow['city_name']) . "'>" . htmlspecialchars($cityRow['city_name']) . "</option>";
    }

    $conn->close();

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
        <title>Sign Up - QPAL</title>
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
            .signup-container {
                background-color: white;
                border-radius: 15px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                overflow: hidden;
                width: 800px;
                max-width: 100%;
                animation: fadeIn 0.5s ease-out, float 6s ease-in-out infinite;
                position: relative;
                margin-top: 3rem;
            }
            .signup-container::before {
                content: '';
                position: absolute;
                top: -2px;
                left: -2px;
                right: -2px;
                bottom: -2px;
                background: transparent;
                z-index: -1;
                filter: blur(10px);
                animation: glowing 10s linear infinite;
                opacity: 0.7;
                border-radius: 16px;
            }
            .signup-header {
                background-color: black;
                color: white;
                padding: 20px;
                text-align: center;
                font-weight: 600;
            }
            .signup-form {
                padding: 30px;
            }
            .form-control, .form-select {
                border: 2px solid #e1e1e1;
                border-radius: 5px;
                transition: all 0.3s ease;
            }
            .form-control:focus, .form-select:focus {
                border-color: #007bff;
                box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            }
            .btn-signup {
                background-color: transparent;
                border: 1px solid black;
                border-radius: 5px;
                color: black;
                font-weight: 600;
                padding: 10px;
                transition: all 0.3s ease; 
            }
            .btn-signup:hover {
                background-color: black;
                color: white;
                transform: translateY(-2px);
            }
            .login-link {
                text-align: center;
                margin-top: 20px;
            }
            .notification {
                position: fixed;
                top: -100px;
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
            .form-floating input:not(:placeholder-shown) + label,
            .form-floating select:focus + label,
            .form-floating select:not(:placeholder-shown) + label {
                transform: translateY(-1.5rem) scale(0.85);
                color: #007bff;
            }
            @media (max-width: 768px) {
                .notification {
                    width: 90%;
                    max-width: 300px;
                }
                .signup-container {
                    width: 100%;
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
            .password-toggle {
                position: absolute;
                right: 10px;
                top: 50%;
                transform: translateY(-50%);
                cursor: pointer;
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
            <nav class="navbar navbar-expand-lg " style="background-color: #edf1f8 !important; border-bottom: 2px solid grey; padding: 0 !important;">
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
                    <div class="collapse navbar-collapse justify-content-between" id="navbarNav" style="background-color: #edf1f8 !important;">
                        <ul class="navbar-nav mx-auto">
                            <li class="nav-item mx-4">
                                <a class="nav-link text-dark" href="#"> Home</a>
                            </li>
                            <li class="nav-item mx-4">
                                <a class="nav-link text-dark" href="./aboutpage.php">About</a>
                            </li>
                            <li class="nav-item mx-4">
                                <a class="nav-link text-dark" href="./product.php">Products</a>
                            </li>
                        </ul>
                        <div class="d-flex">
                            <?php if (isset($_SESSION['user_name'])): ?>
                                <div class="dropdown">
                                    <button class="btn btn-outline-success dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <?= htmlspecialchars($_SESSION['user_name']) ?>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="userDropdown" style="margin: 0px -50px;">
                                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                                        <li><a class="dropdown-item" href="./account-dashboard.php">Account Settings</a></li>
                                        <li><a class="dropdown-item" href="order.php">My Orders</a></li>
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
    <!-- Updated Notification Area -->
    <?php echo $notificationHtml; ?>

    <!-- Sign Up Form -->
    <div class="signup-container">
        <div class="signup-header">
            <h2>Account Registration</h2>
        </div>
        <div class="signup-form">
            <form id="signUpForm" method="post" action="signup.php">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                            <label for="username">Username</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                            <label for="email">Email address</label>
                        </div>
                        <div class="form-floating mb-3 position-relative">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                            <label for="password">Password</label>
                            <span class="password-toggle" onclick="togglePassword('password')">👁️</span>
                        </div>
                        <div class="form-floating mb-3 position-relative">
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                            <label for="confirm_password">Confirm Password</label>
                            <span class="password-toggle" onclick="togglePassword('confirm_password')">👁️</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="contact_no" name="contact_no" placeholder="Contact No." required>
                            <label for="contact_no">Contact No.</label>
                        </div>
                        <div class="form-floating mb-3">
                            <select class="form-select" id="city" name="city" required>
                                <option value="" disabled selected>Select your city</option>
                                <?php echo $cityOptions; ?>
                            </select>
                            <label for="city">City</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="number" class="form-control" id="age" name="age" placeholder="Age" required>
                            <label for="age">Age</label>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-signup w-100">Sign up</button>
            </form>
            <div class="login-link">
                <p>Already have an account? <a href="login.php">Log in here</a>.</p>
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

            const form = document.getElementById('signUpForm');
            form.addEventListener('submit', function(event) {
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirm_password').value;

                if (password.length < 8 || !/[!@#$%^&*]/.test(password)) {
                    event.preventDefault();
                    alert('Password must be at least 8 characters long and include a special character.');
                } else if (password !== confirmPassword) {
                    event.preventDefault();
                    alert('Passwords do not match.');
                }
            });

            const citySelect = document.getElementById('city');
            citySelect.addEventListener('change', function() {
                this.classList.add('has-value');
            });
        });

        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
            } else {
                input.type = 'password';
            }
        }
    </script>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="./main.js"></script>
    </body>
    </html>