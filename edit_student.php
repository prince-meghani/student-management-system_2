<?php
session_start();
include("inc/db.php");

// Only allow teachers
if (!isset($_SESSION['role']) || $_SESSION['role'] != "Teacher") {
    die("Access denied. Only teachers can edit students.");
}

// Check if SID is provided
if (!isset($_GET['sid']) || empty($_GET['sid'])) {
    die("No student ID provided.");
}

$sid = mysqli_real_escape_string($conn, $_GET['sid']);

// Fetch student info
$student_res = mysqli_query($conn, "SELECT * FROM student WHERE sid='$sid'");
if (mysqli_num_rows($student_res) == 0) {
    die("Student not found.");
}
$student = mysqli_fetch_assoc($student_res);

// Fetch parents for dropdown
$parents = mysqli_query($conn, "SELECT pid, fname, lname, email FROM parent ORDER BY fname");

// Handle form submission
$flash = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $classroom = mysqli_real_escape_string($conn, $_POST['classroom']);
    $parent = mysqli_real_escape_string($conn, $_POST['parent']); // parent ID
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $sql = "UPDATE student SET
                fname='$fname',
                lname='$lname',
                classroom='$classroom',
                parent='$parent',
                email='$email'
            WHERE sid='$sid'";

    if (mysqli_query($conn, $sql)) {
        $flash = ['type' => 'ok', 'msg' => "Student $fname $lname updated successfully!"];
        // Refresh student data
        $student = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM student WHERE sid='$sid'"));
    } else {
        $flash = ['type' => 'bad', 'msg' => "Error: " . mysqli_error($conn)];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Student</title>
    <link rel="stylesheet" href="public/styles.css">
</head>
<body>
<div class="container">
    <h1>Edit Student: <?= htmlspecialchars($student['fname'] . ' ' . $student['lname']) ?></h1>

    <?php if ($flash): ?>
        <div class="flash <?= $flash['type'] ?>"><?= htmlspecialchars($flash['msg']) ?></div>
    <?php endif; ?>

    <div class="card">
        <form method="POST">
            <label>First Name</label>
            <input type="text" name="fname" value="<?= htmlspecialchars($student['fname']) ?>" required>

            <label>Last Name</label>
            <input type="text" name="lname" value="<?= htmlspecialchars($student['lname']) ?>" required>

            <label>Classroom</label>
            <input type="text" name="classroom" value="<?= htmlspecialchars($student['classroom']) ?>" required>

            <label>Parent</label>
            <select name="parent" required>
                <option value="">Select a Parent</option>
                <?php while ($p = mysqli_fetch_assoc($parents)): ?>
                    <option value="<?= (int)$p['pid'] ?>" <?= $p['pid'] == $student['parent'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['fname'] . ' ' . $p['lname'] . ' (' . $p['email'] . ')') ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" required>

            <button class="btn-primary" type="submit">Update Student</button>
        </form>
    </div>

    <br>
    <a class="btn-link" href="manage_students.php">← Back to Manage Students</a>
</div>
</body>
</html>
