<?php
include("inc/db.php");
$exams = mysqli_query($conn, "SELECT * FROM exam ORDER BY exam_date DESC");
?>

<h2>Manage Exams</h2>
<table border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>Subject</th>
        <th>Class</th>
        <th>Date</th>
        <th>Action</th>
    </tr>
    <?php while ($exam = mysqli_fetch_assoc($exams)) { ?>
    <tr>
        <td><?php echo $exam['exam_id']; ?></td>
        <td><?php echo $exam['subject']; ?></td>
        <td><?php echo $exam['class']; ?></td>
        <td><?php echo $exam['exam_date']; ?></td>
        <td><a href="add_marks.php?exam_id=<?php echo $exam['exam_id']; ?>">Enter Marks</a></td>
    </tr>
    <?php } ?>
</table>

<br>
<a href="add_exam.php">➕ Add New Exam</a>
