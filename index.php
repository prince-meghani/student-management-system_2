<?php
session_start();
include("inc/db.php");

if (!isset($_SESSION['email']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['role'];
$email = $_SESSION['email'];

echo "<h1>Welcome, $role</h1>";

if ($role == "Teacher") {
    echo "<h2>Teacher Menu</h2>
          <ul>
            <li><a href='attendance.php'>📌 Manage Attendance</a></li>
            <li><a href='add_exam.php'>➕ Add New Exam</a></li>
            <li><a href='list_exams.php'>📋 Manage Exams</a></li>
            <li><a href='add_notice.php'>📢 Post Notice</a></li>
          </ul>";
}

if ($role == "Student") {
    echo "<h2>Student Menu</h2>
          <ul>
            <li><a href='view_results.php'>📊 View My Exam Results</a></li>
          </ul>";
}

if ($role == "Parent") {
    echo "<h2>Parent Menu</h2>
          <ul>
            <li><a href='view_results_parent.php'>📊 View Children Exam Results</a></li>
          </ul>";
}

echo "<hr><h3>Latest Notices</h3>";

$result = mysqli_query($conn, "SELECT * FROM notice ORDER BY created_at DESC LIMIT 5");
while ($row = mysqli_fetch_assoc($result)) {
    echo "<div style='border:1px solid #ccc; padding:10px; margin-bottom:10px;'>
            <strong>{$row['title']}</strong> ({$row['audience']})<br>
            <small>{$row['created_at']}</small><br>
            <p>{$row['message']}</p>
          </div>";
}

echo "<br><a href='logout.php'>Logout</a>";
?>
