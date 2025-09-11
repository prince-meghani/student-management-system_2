<?php
include("inc/db.php");

$exam_id = isset($_GET['exam_id']) ? (int) $_GET['exam_id'] : 0;


$examResult = mysqli_query($conn, "SELECT * FROM exam WHERE exam_id='$exam_id'");
$exam = mysqli_fetch_assoc($examResult);
if (!$exam) {
    header("Location: manage_exams.php");
    exit;
}

$class = $exam['class'];
$subject = $exam['subject'];
$exam_date = $exam['exam_date'];

$message = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['sid']) && is_array($_POST['sid'])) {
        $stmt = $conn->prepare("INSERT INTO examresult (exam_id, student_id, marks, grade) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE marks=?, grade=?");

        foreach ($_POST['sid'] as $i => $sid) {
            $marks = (int)($_POST['marks'][$i] ?? 0);
          
            if ($marks >= 90) $grade = 'A';
            elseif ($marks >= 75) $grade = 'B';
            elseif ($marks >= 50) $grade = 'C';
            else $grade = 'D';

            $stmt->bind_param("isissi", $exam_id, $sid, $marks, $grade, $marks, $grade);
            $stmt->execute();
        }

        $stmt->close();
        $message = "Marks saved successfully!";
    } else {
        $message = "No students found for this class.";
    }
}


$students = mysqli_query($conn, "SELECT * FROM student WHERE classroom='$class' ORDER BY sid ASC");


$existingMarks = [];
$resMarks = mysqli_query($conn, "SELECT * FROM examresult WHERE exam_id='$exam_id'");
while ($row = mysqli_fetch_assoc($resMarks)) {
    $existingMarks[$row['student_id']] = $row;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Enter Marks - <?= htmlspecialchars($subject) ?></title>
    <style>
        body { font-family: Arial,sans-serif; background:#f4f6f9; margin:0; padding:0;}
        .container { max-width:900px; margin:30px auto; background:#fff; padding:25px; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.2);}
        h2 { text-align:center; margin-bottom:20px;}
        table { width:100%; border-collapse:collapse; margin-bottom:20px;}
        th,td { border:1px solid #ddd; padding:10px; text-align:center;}
        th { background:#007bff; color:#fff;}
        input[type=number] { width:60px; padding:5px; text-align:center;}
        .btn-primary { padding:10px 15px; background:#007bff; color:#fff; border:none; border-radius:5px; cursor:pointer;}
        .btn-primary:hover { background:#0056b3;}
        .btn-back { margin-bottom:15px; display:inline-block; color:#007bff; text-decoration:none; font-weight:bold;}
        .message { text-align:center; margin-bottom:15px; color:green; font-weight:bold;}
    </style>
</head>
<body>
<div class="container">
    <a class="btn-back" href="list_exams.php">&larr; Back</a>
    <h2>Enter Marks for <?= htmlspecialchars($subject) ?> (<?= htmlspecialchars($class) ?>)</h2>

    <?php if($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post">
        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Marks</th>
                </tr>
            </thead>
            <tbody>
                <?php while($student = mysqli_fetch_assoc($students)): 
                    $sid = $student['sid'];
                    $marks = $existingMarks[$sid]['marks'] ?? '';
                ?>
                <tr>
                    <td><?= htmlspecialchars($sid) ?></td>
                    <td><?= htmlspecialchars($student['fname'].' '.$student['lname']) ?></td>
                    <td>
                        <input type="hidden" name="sid[]" value="<?= htmlspecialchars($sid) ?>">
                        <input type="number" name="marks[]" value="<?= htmlspecialchars($marks) ?>" min="0" max="100" required>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <button type="submit" class="btn-primary">Save Marks</button>
    </form>
</div>
</body>
</html>
