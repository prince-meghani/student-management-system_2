<?php
include("inc/db.php");

$exam_id = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0;

if ($exam_id == 0) {
    die("<p style='color:red;'>❌ No exam selected.</p>");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_POST['marks'] as $sid => $mark) {
        $grade = ($mark >= 80) ? 'A' : (($mark >= 60) ? 'B' : 'C');
        $sql = "INSERT INTO examresult (exam_id, student_id, marks, grade)
                VALUES ($exam_id, '$sid', $mark, '$grade')
                ON DUPLICATE KEY UPDATE marks=$mark, grade='$grade'";
        mysqli_query($conn, $sql);
    }
    echo "<p style='color:green;'>✅ Marks Saved Successfully!</p>";
}

$students = mysqli_query($conn, "SELECT * FROM student WHERE classroom='4-A'");
?>

<h2>Enter Marks for Exam #<?php echo $exam_id; ?></h2>
<form method="POST">
<?php while ($s = mysqli_fetch_assoc($students)) { ?>
    <p>
        <?php echo $s['fname'] . " " . $s['lname']; ?>:
        <input type="number" name="marks[<?php echo $s['sid']; ?>]" min="0" max="100" required>
    </p>
<?php } ?>
    <button type="submit">Save Marks</button>
</form>
