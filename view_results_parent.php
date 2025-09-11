<?php
include("inc/db.php");
session_start();


if (!isset($_SESSION['email']) || $_SESSION['role'] != 'Parent') {
    header("Location: login.php");
    exit;
}

$parent_email = $_SESSION['email'];


$parent_query = mysqli_query($conn, "SELECT pid FROM parent WHERE email='$parent_email' LIMIT 1");
$parent = mysqli_fetch_assoc($parent_query);

if (!$parent) {
    die("No parent record found.");
}

$parent_id = $parent['pid'];

$children = mysqli_query($conn, "SELECT * FROM student WHERE parent = $parent_id");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Children Exam Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        h3 {
            color: #007bff;
            margin-top: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background: #007bff;
            color: #fff;
        }

        tr:nth-child(even) {
            background: #f4f4f4;
        }

        .no-results {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin: 15px 0;
        }

        .child-section {
            margin-bottom: 40px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
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
        <h2>Children Exam Results</h2>

        <?php if (mysqli_num_rows($children) > 0): ?>
            <?php while ($child = mysqli_fetch_assoc($children)): ?>
                <div class="child-section">
                    <h3><?php echo htmlspecialchars($child['fname'] . " " . $child['lname']); ?> (Class: <?php echo htmlspecialchars($child['classroom']); ?>)</h3>
                    
                    <?php
                    $sid = $child['sid'];
                    $results = mysqli_query($conn, "
                        SELECT e.subject, e.exam_date, r.marks, r.grade 
                        FROM exam e 
                        JOIN examresult r ON e.exam_id = r.exam_id 
                        WHERE r.student_id = '$sid' 
                        ORDER BY e.exam_date DESC
                    ");
                    ?>

                    <?php if (mysqli_num_rows($results) > 0): ?>
                        <table>
                            <tr>
                                <th>Subject</th>
                                <th>Date</th>
                                <th>Marks</th>
                                <th>Grade</th>
                            </tr>
                            <?php while ($row = mysqli_fetch_assoc($results)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['subject']); ?></td>
                                    <td><?php echo htmlspecialchars($row['exam_date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['marks']); ?></td>
                                    <td><?php echo htmlspecialchars($row['grade']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </table>
                    <?php else: ?>
                        <p class="no-results">No exam results found for this child.</p>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="no-results">No children found for this parent account.</p>
        <?php endif; ?>

        <div style="text-align:center;">
            <a href="index.php" class="back-link">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
