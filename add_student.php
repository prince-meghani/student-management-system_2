<?php
session_start();
include("inc/db.php");

// Only allow teachers
if (!isset($_SESSION['role']) || $_SESSION['role'] != "Teacher") {
    die("Access denied. Only teachers can add students.");
}

// Handle form submission
$flash = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $classroom = mysqli_real_escape_string($conn, $_POST['classroom']);
    $parent = mysqli_real_escape_string($conn, $_POST['parent']); // parent ID
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Auto-generate student ID
    $last = mysqli_query($conn, "SELECT sid FROM student ORDER BY sid DESC LIMIT 1");
    $last_row = mysqli_fetch_assoc($last);
    $last_id = $last_row ? intval(substr($last_row['sid'], 3)) : 0;
    $sid = "STU" . str_pad($last_id + 1, 5, "0", STR_PAD_LEFT);

    $sql = "INSERT INTO student (sid, fname, lname, classroom, parent, email) 
            VALUES ('$sid', '$fname', '$lname', '$classroom', '$parent', '$email')";

    if (mysqli_query($conn, $sql)) {
        $flash = ['type' => 'ok', 'msg' => "Student $fname $lname added successfully!"];
    } else {
        $flash = ['type' => 'bad', 'msg' => "Error: " . mysqli_error($conn)];
    }
}

// Fetch parents for dropdown
$parents = mysqli_query($conn, "SELECT pid, fname, lname, email FROM parent ORDER BY fname");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Student</title>
    <link rel="stylesheet" href="public/styles.css">
</head>
<body>
<div class="container">
    <h1>Add New Student</h1>

    <?php if ($flash): ?>
        <div class="flash <?= $flash['type'] ?>"><?= htmlspecialchars($flash['msg']) ?></div>
    <?php endif; ?>

    <div class="card">
        <form method="POST">
            <label>First Name</label>
            <input type="text" name="fname" required>

            <label>Last Name</label>
            <input type="text" name="lname" required>

            <label>Classroom</label>
            <input type="text" name="classroom" required placeholder="e.g., 4-A">

            <label>Parent</label>
            <select name="parent" required>
                <option value="">Select a Parent</option>
                <?php while ($p = mysqli_fetch_assoc($parents)): ?>
                    <option value="<?= (int)$p['pid'] ?>">
                        <?= htmlspecialchars($p['fname'] . ' ' . $p['lname'] . ' (' . $p['email'] . ')') ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Email</label>
            <input type="email" name="email" required placeholder="student@example.com">

            <button class="btn-primary" type="submit">Add Student</button>
        </form>
    </div>
</div>
</body>
</html>
