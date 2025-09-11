<?php
session_start();
include("inc/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != "Teacher") {
    die("Access denied. Only teachers can manage students.");
}

$flash = null;
if (isset($_GET['delete'])) {
    $sid = mysqli_real_escape_string($conn, $_GET['delete']);
    mysqli_query($conn, "DELETE FROM student WHERE sid='$sid'");
    $flash = ['type' => 'ok', 'msg' => "Student $sid deleted successfully!"];
}


$students = mysqli_query($conn, "SELECT s.*, p.fname AS parent_fname, p.lname AS parent_lname, p.email AS parent_email
                                 FROM student s
                                 LEFT JOIN parent p ON s.parent = p.pid
                                 ORDER BY s.sid DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Students</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1100px;
            margin: 50px auto;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }

        .flash {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
        }

        .flash.ok {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .flash.bad {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .card {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th,
        table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }

        table th {
            background-color: #007bff;
            color: #fff;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #e6f0ff;
        }

        .actions a {
            margin: 0 5px;
            padding: 6px 12px;
            background-color: #007bff;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            transition: 0.3s;
        }

        .actions a:hover {
            background-color: #0056b3;
        }

        .btn-primary {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            background-color: #28a745;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            transition: 0.3s;
        }

        .btn-primary:hover {
            background-color: #218838;
        }
         .btn-back a {
            background: #0088ffff;
            color: #fff;
            margin-bottom: 20px;
            text-decoration: none;
        }

        .btn-back:hover {
            background: #5a6268;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Manage Students</h1>
        <a class="btn-back" href="index.php">&larr; Back</a>
        <?php if (!empty($flash)): ?>
            <div class="flash <?= $flash['type'] ?>"><?= htmlspecialchars($flash['msg']) ?></div>
        <?php endif; ?>

        <div class="card">
            <table>
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
                                <a href="edit_student.php?sid=<?= urlencode($s['sid']) ?>">Edit</a>
                                <a href="?delete=<?= urlencode($s['sid']) ?>"
                                    onclick="return confirm('Delete this student?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <a class="btn-primary" href="add_student.php">+ Add New Student</a>
    </div>
</body>

</html>