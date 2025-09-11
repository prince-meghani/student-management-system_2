<?php
session_start();
include("inc/db.php");

if (!isset($_SESSION['email']) || $_SESSION['role'] != 'Student') {
    header("Location: login.php");
    exit;
}

$email = $_SESSION['email'];

$studentQuery = mysqli_query($conn, "SELECT sid FROM student WHERE email = '$email' LIMIT 1");
$studentData = mysqli_fetch_assoc($studentQuery);

if (!$studentData) {
    die("No student record found for the logged-in user.");
}

$student_id = $studentData['sid'];

$sql = "SELECT e.subject, e.exam_date, r.marks, r.grade
        FROM exam e
        JOIN examresult r ON e.exam_id = r.exam_id
        WHERE r.student_id = '$student_id'
        ORDER BY e.exam_date DESC";

$results = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Exam Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .container {
            max-width: 700px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th {
            background: #007bff;
            color: #fff;
        }

        tr:nth-child(even) {
            background: #f2f2f2;
        }

        .no-results {
            text-align: center;
            color: #666;
            margin-top: 15px;
        }

        a.back-link {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            background: #007bff;
            color: #fff;
            padding: 8px 15px;
            border-radius: 4px;
        }

        a.back-link:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>My Exam Results</h2>

        <?php if (mysqli_num_rows($results) > 0): ?>
            <table>
                <tr>
                    <th>Subject</th>
                    <th>Date</th>
                    <th>Marks</th>
                    <th>Grade</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($results)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['subject']); ?></td>
                    <td><?php echo htmlspecialchars($row['exam_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['marks']); ?></td>
                    <td><?php echo htmlspecialchars($row['grade']); ?></td>
                </tr>
                <?php } ?>
            </table>
        <?php else: ?>
            <p class="no-results">No exam results found for you.</p>
        <?php endif; ?>

        <div style="text-align:center;">
            <a href="index.php" class="back-link">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
