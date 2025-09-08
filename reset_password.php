<?php
session_start();
include("inc/db.php");

// ---------------------
// 1. Validate token from URL
// ---------------------
if (!isset($_GET['token']) || empty($_GET['token'])) {
    die("❌ Invalid token provided in URL.");
}

$token = mysqli_real_escape_string($conn, $_GET['token']);





$sql = "SELECT * FROM `user` WHERE reset_token = '$token' AND reset_token_expiry > NOW()";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("SQL ERROR: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) == 0) {
    die("❌ Token expired or invalid");
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newPassword = $_POST['password'];

    if (strlen($newPassword) < 4) {
        echo "<p style='color:red;'>Password must be at least 4 characters long.</p>";
    } else {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $update = "UPDATE `user` 
                   SET password='$hashedPassword', 
                       reset_token=NULL, 
                       reset_token_expiry=NULL 
                   WHERE reset_token='$token'";

        if (mysqli_query($conn, $update)) {
            echo "<p style='color:green;'>✅ Password reset successful! <a href='login.php'>Login</a></p>";
            exit;
        } else {
            echo "<p style='color:red;'>❌ Error updating password: " . mysqli_error($conn) . "</p>";
        }
    }
}
?>

<!-- ---------------------
5. Reset Password Form
--------------------- -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: #fff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 320px;
        }
        h2 {
            text-align: center;
        }
        input[type="password"], button {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        <form method="POST">
            <label>New Password:</label>
            <input type="password" name="password" required>
            <button type="submit">Reset Password</button>
        </form>
    </div>
</body>
</html>
