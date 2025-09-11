<?php
session_start();
include("inc/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != "Teacher") {
    die("Access denied. Only teachers can add students.");
}

$flash = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $classroom = mysqli_real_escape_string($conn, $_POST['classroom']);
    $parent_email = mysqli_real_escape_string($conn, $_POST['parent_email']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']); 
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

   
    $res = mysqli_query($conn, "SELECT pid FROM parent WHERE email='$parent_email'");
    if ($row = mysqli_fetch_assoc($res)) {
        $parent_id = $row['pid'];

        
        $last = mysqli_query($conn, "SELECT sid FROM student ORDER BY sid DESC LIMIT 1");
        $last_row = mysqli_fetch_assoc($last);
        $last_id = $last_row ? intval(substr($last_row['sid'], 3)) : 0;
        $sid = "STU" . str_pad($last_id + 1, 5, "0", STR_PAD_LEFT);

       
        $sql_student = "INSERT INTO student (sid, fname, lname, classroom, parent, email) 
                        VALUES ('$sid', '$fname', '$lname', '$classroom', '$parent_id', '$email')";

        if (mysqli_query($conn, $sql_student)) {
            
            $sql_user = "INSERT INTO user (email, password, role) 
                         VALUES ('$email', '$hashed_password', 'Student')";
            mysqli_query($conn, $sql_user);

            $_SESSION['flash'] = ['type' => 'ok', 'msg' => "Student $fname $lname added successfully!"];
            header("Location: add_student.php");
            exit;
        } else {
            $_SESSION['flash'] = ['type' => 'bad', 'msg' => "Error: " . mysqli_error($conn)];
            header("Location: add_student.php");
            exit;
        }
    } else {
        $_SESSION['flash'] = ['type' => 'bad', 'msg' => "Parent with email $parent not found."];
        header("Location: add_student.php");
        exit;
    }
}


if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Student</title>
<style>
    body { font-family: Arial, sans-serif; background: #f4f6f9; margin:0; padding:0; }
    .container { max-width: 600px; margin:50px auto; padding:20px; }
    h1 { text-align:center; color:#007bff; margin-bottom:20px; }
    .flash { padding:12px; margin-bottom:20px; border-radius:5px; font-weight:bold; text-align:center; }
    .flash.ok { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
    .flash.bad { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }
    .card { background:#fff; padding:25px; border-radius:10px; box-shadow:0 4px 15px rgba(0,0,0,0.1); }
    form { display:flex; flex-direction:column; }
    label { font-weight:bold; margin-top:15px; margin-bottom:5px; }
    input[type="text"], input[type="email"], input[type="password"] { padding:10px; border:1px solid #ccc; border-radius:6px; font-size:14px; }
    input:focus { border-color:#007bff; outline:none; box-shadow:0 0 5px rgba(0,123,255,0.3); }
    .btn-primary, .btn-back { margin-top:20px; padding:12px; border:none; border-radius:6px; font-size:16px; cursor:pointer; text-align:center; text-decoration:none; display:inline-block; }
    .btn-primary { background:#007bff; color:#fff; transition:0.3s; }
    .btn-primary:hover { background:#0056b3; }
    .btn-back { background:#6c757d; color:#fff; margin-bottom:20px; }
    .btn-back:hover { background:#5a6268; }
</style>
</head>
<body>
<div class="container">
    <h1>Add New Student</h1>

    <a href="index.php" class="btn-back">&larr; Back</a>

    <?php if ($flash): ?>
        <div class="flash <?= $flash['type'] ?>"><?= htmlspecialchars($flash['msg']) ?></div>
    <?php endif; ?>

    <div class="card">
        <form method="POST">
            <label>First Name</label>
            <input type="text" name="fname" required placeholder="Enter first name">

            <label>Last Name</label>
            <input type="text" name="lname" required placeholder="Enter last name">

            <label>Classroom</label>
            <input type="text" name="classroom" required placeholder="e.g., 4-A">

            <label>Parent Email</label>
            <input type="email" name="parent_email" required placeholder="parent@example.com">

            <label>Student Email</label>
            <input type="email" name="email" required placeholder="student@example.com">

            <label>Password</label>
            <input type="password" name="password" required placeholder="Enter password">

            <button class="btn-primary" type="submit">Add Student</button>
        </form>
    </div>
</div>
</body>
</html>
