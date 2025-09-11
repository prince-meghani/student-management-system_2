<?php
include("inc/db.php");

$message = "";
$messageClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject = trim($_POST['subject']);
    $class = trim($_POST['class']);
    $date = $_POST['exam_date'];

   
    if ($subject == "" || $class == "" || $date == "") {
        $message = "Please fill all fields!";
        $messageClass = "error";
    } else {
        
        $stmt = $conn->prepare("INSERT INTO exam(subject, class, exam_date) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $subject, $class, $date);

        if ($stmt->execute()) {
           
            $title = "New Exam Scheduled: $subject";
            $noticeMessage = "Dear all, a new exam for class $class on '$subject' is scheduled on $date. Please be prepared.";
            $audience = "All"; 
            $noticeStmt = $conn->prepare("INSERT INTO notice(title, message, audience) VALUES (?, ?, ?)");
            $noticeStmt->bind_param("sss", $title, $noticeMessage, $audience);
            $noticeStmt->execute();
            $noticeStmt->close();

            $message = "Exam added successfully! Notice has been sent.";
            $messageClass = "success";
        } else {
            $message = "Error adding exam: " . $stmt->error;
            $messageClass = "error";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Exam</title>
    <style>
        body { font-family: Arial,sans-serif; background: #f4f6f9; margin:0; padding:0; }
        .container { max-width: 500px; margin:50px auto; background:#fff; padding:25px; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.2);}
        h2 { text-align:center; color:#333; margin-bottom:20px; }
        form { display:flex; flex-direction:column; gap:12px; }
        label { font-weight:bold; color:#555; }
        input[type="text"], input[type="date"] { padding:10px; border:1px solid #ccc; border-radius:5px; font-size:14px; }
        input:focus { border-color:#007bff; outline:none; box-shadow:0 0 5px rgba(0,123,255,0.3);}
        button { padding:10px; background:#007bff; color:#fff; border:none; border-radius:5px; font-size:16px; cursor:pointer; transition:0.3s; }
        button:hover { background:#0056b3; }
        .message { text-align:center; padding:10px; border-radius:5px; margin-bottom:20px; font-weight:bold; }
        .success { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
        .error { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }
        .btn-back { display:inline-block; margin-bottom:20px; text-decoration:none; padding:8px 12px; background:#6c757d; color:#fff; border-radius:5px;}
        .btn-back:hover { background:#5a6268; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add New Exam</h2>
        <a class="btn-back" href="index.php">&larr; Back</a>

        <?php if(!empty($message)): ?>
            <div class="message <?= $messageClass; ?>">
                <?= htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label>Subject:</label>
            <input type="text" name="subject" placeholder="Enter subject" required>

            <label>Class:</label>
            <input type="text" name="class" placeholder="Enter class" required>

            <label>Exam Date:</label>
            <input type="date" name="exam_date" required>

            <button type="submit">Add Exam</button>
        </form>
    </div>
</body>
</html>
