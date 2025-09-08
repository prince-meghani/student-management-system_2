<?php
session_start();
include("inc/db.php");

if (isset($_SESSION['email']) && isset($_SESSION['role'])) {
    header("Location: index.php");
    exit;
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM user WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
       

        if (password_verify($password, $row['password'])) {
            $_SESSION['email'] = $row['email'];
            $_SESSION['role'] = $row['role'];
            header("Location: index.php");
            exit;
        } else {
            $error = "❌ Invalid email or password!";
        }
    } else {
        $error = "❌ No account found!";
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Login - SMS</title>
</head>

<body>
    <h2>Login</h2>
    <p style="color:red;"><?php echo $error; ?></p>
    <form method="POST">
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Login</button>
    </form>
    <br>
    <a href="forgot_password.php">Forgot Password?</a>
</body>

</html>