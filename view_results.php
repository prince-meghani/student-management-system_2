<?php
include("inc/db.php");

// Example: Replace with logged-in student ID
$student_id = "STU10001";

$sql = "SELECT e.subject, e.exam_date, r.marks, r.grade
        FROM exam e
        JOIN examresult r ON e.exam_id = r.exam_id
        WHERE r.student_id = '$student_id'
        ORDER BY e.exam_date DESC";

$results = mysqli_query($conn, $sql);
?>

<h2>My Exam Results</h2>
<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>Subject</th>
        <th>Date</th>
        <th>Marks</th>
        <th>Grade</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($results)) { ?>
    <tr>
        <td><?php echo $row['subject']; ?></td>
        <td><?php echo $row['exam_date']; ?></td>
        <td><?php echo $row['marks']; ?></td>
        <td><?php echo $row['grade']; ?></td>
    </tr>
    <?php } ?>
</table>
