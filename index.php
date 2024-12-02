<?php
session_start();

// Check for existing cookie
if (isset($_COOKIE['age_verified']) && $_COOKIE['age_verified'] === 'true') {
  $_SESSION['age_verified'] = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['age']) && $_POST['age'] === '21+') {
      $_SESSION['age_verified'] = true;
      // Set a cookie that expires in 30 days
      setcookie('age_verified', 'true', time() + (86400 * 30), "/");
      header('Location: main.php');
      exit;
  }
}

if (isset($_SESSION['age_verified']) && $_SESSION['age_verified'] === true) {
  header('Location: main.php');
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Age Verification</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Inter', sans-serif;
            background-color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            background: #ffffff;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            color: #333333;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s ease-out forwards;
        }
        h1 {
            margin-bottom: 20px;
            font-weight: 600;
            color: #1a1a1a;
        }
        p {
            margin-bottom: 30px;
            font-weight: 300;
            color: #4a4a4a;
        }
        .btn {
            background: #f0f0f0;
            color: #333333;
            border: none;
            padding: 12px 24px;
            margin: 10px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateY(20px);
        }
        .btn:hover {
            background: #333333;
            color: #ffffff;
        }
        .btn-yes {
            animation: fadeInBtn 0.5s ease-out 0.3s forwards;
        }
        .btn-no {
            animation: fadeInBtn 0.5s ease-out 0.5s forwards;
        }
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes fadeInBtn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .warning {
            font-size: 14px;
            margin-top: 20px;
            color: #777777;
        }
        .logo {
            width: 80px;
            height: 80px;
            background-color: #333333;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 24px;
            color: #ffffff;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">21+</div>
        <h1>Age Verification</h1>
        <p>You must be 21 years or older to enter this website.</p>
        <form method="post" id="ageForm">
            <input type="hidden" name="age" id="ageInput">
            <button type="button" class="btn btn-yes" onclick="submitAge('21+')">I am 21 or older</button>
            <button type="button" class="btn btn-no" onclick="submitAge('under21')">I am under 21</button>
        </form>
        <p class="warning">WARNING: This product contains nicotine. Nicotine is an addictive chemical.</p>
    </div>

    <script>
        function submitAge(age) {
            if (age === 'under21') {
                alert("We're sorry, but you must be 21 or older to access this website.");
                return;
            }
            document.getElementById('ageInput').value = age;
            document.getElementById('ageForm').submit();
        }
    </script>
</body>
</html>