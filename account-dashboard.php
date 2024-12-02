<?php
include 'header.php';   
require_once 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user information
$stmt = $conn->prepare("SELECT USER_NAME, EMAIL, CONTACT_NO, city, AGE, profile_picture FROM users WHERE USER_ID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Fetch city data
$city_stmt = $conn->prepare("SELECT city_id, city_name FROM city_demographics ORDER BY city_name");
$city_stmt->execute();
$city_result = $city_stmt->get_result();
$cities = $city_result->fetch_all(MYSQLI_ASSOC);

$success_message = $error_message = '';

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $target_dir = "uploads/";
    $file_extension = pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION);
    $file_name = "profile_" . $user_id . "." . $file_extension;
    $target_file = $target_dir . $file_name;
    $uploadOk = 1;
    $imageFileType = strtolower($file_extension);

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        $error_message = "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["profile_picture"]["size"] > 500000) {
        $error_message = "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        $error_message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // If everything is ok, try to upload file
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            // Update database with new profile picture path
            $update_stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE USER_ID = ?");
            $update_stmt->bind_param("si", $target_file, $user_id);
            $update_stmt->execute();
            $update_stmt->close();

            // Refresh user data
            $user['profile_picture'] = $target_file;
            $success_message = "Profile picture updated successfully.";
        } else {
            $error_message = "Sorry, there was an error uploading your file.";
        }
    }

}
// Handle form submission for updating user information
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_info'])) {
    $user_name = $_POST['user_name'];
    $email = $_POST['email'];
    $contact_no = $_POST['contact_no'];
    $city = $_POST['city'];
    $age = $_POST['age'];

    // Check if any information has changed
    $changes_made = false;
    if ($user_name != $user['USER_NAME'] || $email != $user['EMAIL'] || 
        $contact_no != $user['CONTACT_NO'] || $city != $user['city'] || 
        $age != $user['AGE']) {
        $changes_made = true;
    }

    if ($changes_made) {
        // Start transaction
        $conn->begin_transaction();

        try {
            // Update user information
            $update_stmt = $conn->prepare("UPDATE users SET USER_NAME = ?, EMAIL = ?, CONTACT_NO = ?, city = ?, AGE = ? WHERE USER_ID = ?");
            $update_stmt->bind_param("ssssii", $user_name, $email, $contact_no, $city, $age, $user_id);
            $update_stmt->execute();

            // Update city demographics
            $decrement_stmt = $conn->prepare("UPDATE city_demographics SET user_count = user_count - 1 WHERE city_name = ?");
            $decrement_stmt->bind_param("s", $user['city']);
            $decrement_stmt->execute();

            $increment_stmt = $conn->prepare("UPDATE city_demographics SET user_count = user_count + 1 WHERE city_name = ?");
            $increment_stmt->bind_param("s", $city);
            $increment_stmt->execute();

            // Commit transaction
            $conn->commit();

            $success_message = "Information updated successfully!";
            
            // Refresh user data
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            $error_message = "Error updating profile: " . $e->getMessage();
        }
    }
}

$stmt->close();
$city_stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>QPAL</title>
	<link rel="stylesheet" type="text/css" href="./style.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	
	<!--for arrow-->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />



  <!--AOS-->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

	<!-- font -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

     <!--fONT AWESOME-->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding-top: 80px;
        }
        .account {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        .profile-picture {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 20px;
        }
        .btn-edit {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        .city-options {
        max-height: 200px;
        overflow-y: auto;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        padding: 0.5rem;
    }
    .city-options .form-check {
        margin-bottom: 0.5rem;
    }
    .city-options .form-check-label {
        cursor: pointer;
    }
    .city-options .form-check-input:checked + .form-check-label {
        color: #0d6efd;
        font-weight: bold;
    }
    </style>
</head>
<body style="  background-color: #edf1f8 !important;">

	


     <!-- Account Dashboard Section -->
     <div class="container account">
        <h1 class="mb-4">Welcome, <?php echo htmlspecialchars($user['USER_NAME']); ?>!</h1>
        <p>Manage your account settings and preferences.</p>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4 text-center">
                <img src="<?php echo $user['profile_picture'] ? htmlspecialchars($user['profile_picture']) : 'https://via.placeholder.com/150'; ?>" alt="Profile Picture" class="profile-picture">
                <form action="" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                    <div class="mb-3">
                        <input type="file" class="form-control" name="profile_picture" id="profile_picture">
                    </div>
                    <button type="submit" class="btn" style="border: 2px solid black; background-color: white;color: black">Update Picture</button>
                </form>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">User Information</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong style="margin-right: 20px;">Name:</strong> <?php echo htmlspecialchars($user['USER_NAME']); ?></li>
                            <li class="list-group-item"><strong style="margin-right: 23px;">Email:</strong> <?php echo htmlspecialchars($user['EMAIL']); ?></li>
                            <li class="list-group-item"><strong style="margin-right: 16px;">Phone:</strong> <?php echo htmlspecialchars($user['CONTACT_NO']); ?></li>
                            <li class="list-group-item"><strong style="margin-right: 40px;">City:</strong> <?php echo htmlspecialchars($user['city']); ?></li>
                            <li class="list-group-item"><strong style="margin-right: 39px;">Age:</strong> <?php echo htmlspecialchars($user['AGE']); ?></li>
                        </ul>
                    </div>
                </div>
                <button type="button" class="btn mt-3" data-bs-toggle="modal" data-bs-target="#editProfileModal" style="border: 2px solid black; background-color: white;color: black">
                    Edit Information
                </button>
            </div>
        </div>
    </div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="">
                    <div class="mb-3">
                        <label for="user_name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="user_name" name="user_name" value="<?php echo htmlspecialchars($user['USER_NAME']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['EMAIL']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="contact_no" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="contact_no" name="contact_no" value="<?php echo htmlspecialchars($user['CONTACT_NO']); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">City</label>
                        <div class="city-options">
                            <?php foreach ($cities as $city): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="city" id="city_<?php echo $city['city_id']; ?>" value="<?php echo htmlspecialchars($city['city_name']); ?>" <?php echo ($user['city'] == $city['city_name']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label <?php echo ($user['city'] == $city['city_name']) ? 'text-primary fw-bold' : ''; ?>" for="city_<?php echo $city['city_id']; ?>">
                                        <?php echo htmlspecialchars($city['city_name']); ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="age" class="form-label">Age</label>
                        <input type="number" class="form-control" id="age" name="age" value="<?php echo htmlspecialchars($user['AGE']); ?>">
                    </div>
                    <input type="hidden" name="update_info" value="1">
                    <button type="submit" class="btn btn-primary">Update Information</button>
                </form>
            </div>
        </div>
    </div>
</div>

                                   



    <script>
        function validateForm() {
            var fileInput = document.getElementById('profile_picture');
            if (fileInput.files.length === 0) {
                alert('Please select an image first.');
                return false;
            }
            return true;
        }
        document.addEventListener('DOMContentLoaded', function() {
        const cityOptions = document.querySelectorAll('.city-options .form-check-input');
        cityOptions.forEach(option => {
            option.addEventListener('change', function() {
                cityOptions.forEach(opt => {
                    opt.nextElementSibling.classList.remove('text-primary', 'fw-bold');
                });
                if (this.checked) {
                    this.nextElementSibling.classList.add('text-primary', 'fw-bold');
                }
            });
        });
    });
    </script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./main.js"></script>
    <script src="./restrict.js"></script>
    <script>
        AOS.init();
    </script>
</body>
</html>