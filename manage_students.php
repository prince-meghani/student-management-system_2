<?php
session_start();
include("inc/db.php");


if (!isset($_SESSION['role']) || $_SESSION['role'] != "Teacher") {
    die("Access denied. Only teachers can manage students.");
}


if (isset($_GET['delete'])) {
    $sid = mysqli_real_escape_string($conn, $_GET['delete']);
    mysqli_query($conn, "DELETE FROM student WHERE sid='$sid'");
    $flash = ['type'=>'ok','msg'=>"Student $sid deleted successfully!"];
}

$students = mysqli_query($conn, "SELECT s.*, p.fname AS parent_fname, p.lname AS parent_lname, p.email AS parent_email
                                 FROM student s
                                 LEFT JOIN parent p ON s.parent = p.pid
                                 ORDER BY s.classroom, s.fname");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Students</title>
    <link rel="stylesheet" href="public/styles.css">
</head>
<body>
<div class="container">
    <h1>Manage Students</h1>

    <?php if (!empty($flash)): ?>
        <div class="flash <?= $flash['type'] ?>"><?= htmlspecialchars($flash['msg']) ?></div>
    <?php endif; ?>

    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Classroom</th>
                    <th>Parent</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($s = mysqli_fetch_assoc($students)): ?>
                <tr>
                    <td><?= htmlspecialchars($s['sid']) ?></td>
                    <td><?= htmlspecialchars($s['fname'] . ' ' . $s['lname']) ?></td>
                    <td><?= htmlspecialchars($s['classroom']) ?></td>
                    <td>
                        <?= htmlspecialchars($s['parent_fname'] . ' ' . $s['parent_lname']) ?>
                        <br>
                        <small><?= htmlspecialchars($s['parent_email']) ?></small>
                    </td>
                    <td><?= htmlspecialchars($s['email']) ?></td>
                    <td class="actions">
                        <a class="btn-link" href="edit_student.php?sid=<?= urlencode($s['sid']) ?>">Edit</a>
                        <a class="btn-link" href="?delete=<?= urlencode($s['sid']) ?>" onclick="return confirm('Delete this student?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <br>
    <a class="btn-primary"  href="add_student.php">Add New Student</a>
</div>
</body>
</html>
