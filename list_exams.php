<?php
include("inc/db.php");

$exams = mysqli_query($conn, "SELECT * FROM exam  ORDER BY exam_id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Exams</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; margin:0; padding:0; }
        .container { max-width: 900px; margin:50px auto; background:#fff; padding:25px; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.2);}
        h2 { text-align:center; color:#333; margin-bottom:20px;}
        table { width:100%; border-collapse:collapse; background:#fff;}
        table thead { background-color:#007bff; color:white;}
        table th, table td { padding:12px; text-align:center; border:1px solid #ddd; font-size:14px;}
        .action-link { text-decoration:none; color:#007bff; font-weight:bold; transition:color 0.3s;}
        .action-link:hover { color:#0056b3; text-decoration:underline;}
        .add-btn, .back-btn { display:inline-block; margin-top:20px; padding:10px 15px; background-color:#28a745; color:white; text-decoration:none; border-radius:5px; transition:0.3s;}
        .add-btn:hover, .back-btn:hover { background-color:#218838; }
        .empty-message { text-align:center; color:#777; font-size:16px; padding:20px 0;}
    </style>
</head>
<body>
    <div class="container">
        <h2>Manage Exams</h2>
         <a class="btn-back" href="index.php">&larr; Back</a>
        <?php if (mysqli_num_rows($exams) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Subject</th>
                        <th>Class</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($exam = mysqli_fetch_assoc($exams)): ?>
                    <tr>
                        <td><?= htmlspecialchars($exam['exam_id']); ?></td>
                        <td><?= htmlspecialchars($exam['subject']); ?></td>
                        <td><?= htmlspecialchars($exam['class']); ?></td>
                        <td><?= htmlspecialchars($exam['exam_date']); ?></td>
                        <td>
                            <a class="action-link" href="add_marks.php?exam_id=<?= urlencode($exam['exam_id']); ?>&class=<?= urlencode($exam['class']); ?>">
                                Enter Marks
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="empty-message">No exams found. Please add a new exam.</p>
        <?php endif; ?>

        <div style="text-align:center;">
            <a href="add_exam.php" class="add-btn">+ Add New Exam</a>
        </div>
    </div>
</body>
</html>
