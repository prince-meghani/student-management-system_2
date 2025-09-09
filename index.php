<?php
session_start();
include("./inc/db.php");

if (!isset($_SESSION['email']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['role'];
$email = $_SESSION['email'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - SMS</title>
    <style>
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        
        header {
            background-color: #007bff;
            color: white;
            padding: 15px 20px;
            text-align: center;
        }

        header h1 {
            margin: 0;
            font-size: 24px;
        }

        
        .container {
            max-width: 900px;
            margin: 30px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        
        h2 {
            color: #333;
            margin-top: 20px;
        }

       
        ul {
            list-style-type: none;
            padding: 0;
        }

        ul li {
            margin: 10px 0;
        }

        ul li a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        ul li a:hover {
            color: #0056b3;
        }

     
        .notice {
            border: 1px solid #ccc;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            background-color: #fafafa;
        }

        .notice strong {
            color: #333;
            font-size: 16px;
        }

        .notice small {
            display: block;
            color: #666;
            font-size: 12px;
            margin-bottom: 8px;
        }

        
        .logout-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #a71d2a;
        }

        
        hr {
            margin: 30px 0;
            border: none;
            border-top: 1px solid #ddd;
        }

      
    </style>
</head>
<body>
    <header>
        <h1>Welcome, <?php echo htmlspecialchars($role); ?></h1>
    </header>

    <div class="container">
        <?php
        if ($role == "Teacher") {
            echo "<h2>Teacher Menu</h2>
                  <ul>
                    <li><a href='attendance.php'>Manage Attendance</a></li>
                    <li><a href='add_exam.php'>Add New Exam</a></li>
                    <li><a href='list_exams.php'>Manage Exams</a></li>
                    <li><a href='add_notice.php'>Post Notice</a></li>
                    <li><a href='add_student.php'>Add Students</a></li>
                    <li><a href='manage_students.php'>Manage Students</a></li>
                  </ul>";
        }

        if ($role == "Student") {
            echo "<h2>Student Menu</h2>
                  <ul>
                    <li><a href='view_results.php'>View My Exam Results</a></li>
                  </ul>";
        }

        if ($role == "Parent") {
            echo "<h2>Parent Menu</h2>
                  <ul>
                    <li><a href='view_results_parent.php'>View Children Exam Results</a></li>
                  </ul>";
        }

        echo "<hr><h2>Latest Notices</h2>";

        $result = mysqli_query($conn, "SELECT * FROM notice ORDER BY created_at DESC LIMIT 5");
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='notice'>
                    <strong>" . htmlspecialchars($row['title']) . "</strong> (" . htmlspecialchars($row['audience']) . ")<br>
                    <small>" . htmlspecialchars($row['created_at']) . "</small>
                    <p>" . htmlspecialchars($row['message']) . "</p>
                  </div>";
        }
        ?>

        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</body>
</html>
