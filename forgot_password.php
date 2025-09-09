<?php
include("inc/db.php");

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Check if email exists in database
    $result = mysqli_query($conn, "SELECT * FROM `user` WHERE email='$email' LIMIT 1");
    if (mysqli_num_rows($result) == 1) {
        // Generate token and expiry
        $token = bin2hex(random_bytes(16)); 
        $expiry = date("Y-m-d H:i:s", strtotime('+2 hours')); 

        // Update token in database
        $update = "UPDATE `user` 
                   SET reset_token='$token', reset_token_expiry='$expiry' 
                   WHERE email='$email'";

        if (mysqli_query($conn, $update)) {
            $resetLink = "http://localhost/sms-simple/reset_password.php?token=$token";
            $successMessage = "Password reset link generated! <br>
                               <a href='$resetLink'>Click here to reset your password</a>";
        } else {
            $errorMessage = "Error updating token: " . mysqli_error($conn);
        }
    } else {
        $errorMessage = "No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            padding: 20px;
        }

        .container {
            max-width: 400px;
            margin: 60px auto;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        input[type="email"] {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        button {
            background: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        button:hover {
            background: #0056b3;
        }

        .message {
            margin-top: 15px;
            padding: 10px;
            font-size: 14px;
            border-radius: 4px;
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
        <h2>Forgot Password</h2>

        <!-- Display success or error messages -->
        <?php if (!empty($successMessage)): ?>
            <div class="message success"><?php echo $successMessage; ?></div>
        <?php elseif (!empty($errorMessage)): ?>
            <div class="message error"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <!-- Forgot password form -->
        <form method="POST">
            <input type="email" name="email" placeholder="Enter your registered email" required>
            <br>
            <button type="submit">Send Reset Link</button>
        </form>

        <a href="login.php" class="back-link">Back to Login</a>
    </div>
</body>
</html>
