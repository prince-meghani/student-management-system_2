<?php
include("inc/db.php");

$message = "";
$messageClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject = $_POST['subject'];
    $class = $_POST['class'];
    $date = $_POST['exam_date'];

    $sql = "INSERT INTO exam(subject, class, exam_date) VALUES('$subject', '$class', '$date')";
    if (mysqli_query($conn, $sql)) {
        $message = "Exam Added Successfully!";
        $messageClass = "success";
    } else {
        $message = "Error: " . mysqli_error($conn);
        $messageClass = "error";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Exam</title>
    <style>
        /* General Page Styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 500px;
            margin: 50px auto;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        /* Form Styles */
        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            margin-bottom: 6px;
            color: #555;
        }

        input[type="text"],
        input[type="date"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
            transition: 0.3s;
        }

        input[type="text"]:focus,
        input[type="date"]:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 5px rgba(0,123,255,0.3);
        }

        /* Submit Button */
        button {
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Message Box */
        .message {
            text-align: center;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add New Exam</h2>

        <!-- Success / Error Message -->
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $messageClass; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Exam Form -->
        <form method="POST">
            <label>Subject:</label>
            <input type="text" name="subject" placeholder="Enter subject" required>

            <label>Class:</label>
            <input type="text" name="class" value="4-A" required>

            <label>Exam Date:</label>
            <input type="date" name="exam_date" required>

            <button type="submit">Add Exam</button>
        </form>
    </div>
</body>
</html>
