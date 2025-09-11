<?php
session_start();
include("inc/db.php");

if (!isset($_GET['token']) || empty($_GET['token'])) {
    die("<p style='color:red; text-align:center;'>Invalid token provided in URL.</p>");
}

$token = mysqli_real_escape_string($conn, $_GET['token']);

$sql = "SELECT * FROM `user` WHERE reset_token = '$token' AND reset_token_expiry > NOW()";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("<p style='color:red; text-align:center;'>SQL ERROR: " . mysqli_error($conn) . "</p>");
}

if (mysqli_num_rows($result) == 0) {
    die("<p style='color:red; text-align:center;'>Token expired or invalid.</p>");
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newPassword = $_POST['password'];

    if (strlen($newPassword) < 4) {
        $message = "<div class='alert error'>Password must be at least 4 characters long.</div>";
    } else {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        
        $update = "UPDATE `user` 
                   SET password='$hashedPassword', 
                       reset_token=NULL, 
                       reset_token_expiry=NULL 
                   WHERE reset_token='$token'";

        if (mysqli_query($conn, $update)) {
            $message = "<div class='alert success'>Password reset successful! 
                        <a href='login.php'>Login here</a>.</div>";
        } else {
            $message = "<div class='alert error'>Error updating password: " . mysqli_error($conn) . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 320px;
            text-align: center;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        label {
            font-size: 14px;
            color: #555;
            display: block;
            text-align: left;
            margin-bottom: 5px;
        }

        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0 15px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        button {
            width: 100%;
            background: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        button:hover {
            background: #0056b3;
        }

        .alert {
            margin-top: 15px;
            padding: 10px;
            border-radius: 4px;
            font-size: 14px;
        }

        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .back-link {
            display: block;
            margin-top: 15px;
            font-size: 14px;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>

        <?php if (!empty($message)) echo $message; ?>

        <form method="POST">
            <label for="password">New Password</label>
            <input type="password" name="password" id="password" placeholder="Enter new password" required>
            <button type="submit">Reset Password</button>
        </form>

        <a href="login.php" class="back-link">Back to Login</a>
    </div>
</body>
</html>
