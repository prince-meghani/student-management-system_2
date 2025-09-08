<?php
include("inc/db.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $result = mysqli_query($conn, "SELECT * FROM `user` WHERE email='$email' LIMIT 1");
    if (mysqli_num_rows($result) == 1) {
        $token = bin2hex(random_bytes(16)); // secure token
        $expiry = date("Y-m-d H:i:s", strtotime('+2 hours')); // token valid for 2 hours

        $update = "UPDATE `user` 
                   SET reset_token='$token', reset_token_expiry='$expiry' 
                   WHERE email='$email'";

        if (mysqli_query($conn, $update)) {
            echo "<p>✅ Password reset link (simulated email):</p>";
            echo "<a href='reset_password.php?token=$token'>Click Here to Reset Password</a>";
        } else {
            echo "❌ Error updating token: " . mysqli_error($conn);
        }
    } else {
        echo "❌ No account found with that email.";
    }
}
?>

<h2>Forgot Password</h2>
<form method="POST">
    <input type="email" name="email" placeholder="Enter your email" required>
    <button type="submit">Send Reset Link</button>
</form>
