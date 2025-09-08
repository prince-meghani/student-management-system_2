<?php
include("inc/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject = $_POST['subject'];
    $class = $_POST['class'];
    $date = $_POST['exam_date'];

    $sql = "INSERT INTO exam(subject, class, exam_date) VALUES('$subject', '$class', '$date')";
    if (mysqli_query($conn, $sql)) {
        echo "<p style='color:green;'>✅ Exam Added Successfully!</p>";
    } else {
        echo "<p style='color:red;'>❌ Error: " . mysqli_error($conn) . "</p>";
    }
}
?>

<h2>Add New Exam</h2>
<form method="POST">
    <label>Subject:</label>
    <input type="text" name="subject" required><br><br>

    <label>Class:</label>
    <input type="text" name="class" value="4-A" required><br><br>

    <label>Exam Date:</label>
    <input type="date" name="exam_date" required><br><br>

    <button type="submit">Add Exam</button>
</form>
