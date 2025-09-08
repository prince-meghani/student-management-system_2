<?php
session_start();
require __DIR__.'/inc/db.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'Student') {
  header('Location: index.php');
  exit;
}

$email = $_SESSION['user'];

// Find student's ID
$stmt = $conn->prepare("SELECT sid, fname, lname, classroom FROM student WHERE email=? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$student) {
  die("Student record not found!");
}

$sid = $student['sid'];
$classroom = $student['classroom'];

// Fetch attendance for this student
$query = "
SELECT a.date, s.subject, s.class, s.stime, ar.status
FROM attendancereport ar
JOIN attendance a ON ar.aid = a.aid
JOIN schedule s ON a.sid = s.id
WHERE ar.sid = ?
ORDER BY a.date DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $sid);
$stmt->execute();
$result = $stmt->get_result();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Student Dashboard</title>
  <link rel="stylesheet" href="public/styles.css">
</head>
<body>
  <div class="container">
    <h1>Welcome, <?= htmlspecialchars($student['fname'].' '.$student['lname']) ?></h1>
    <h2>Class: <?= htmlspecialchars($classroom) ?></h2>
    <p><a class="btn-link" href="logout.php">Logout</a></p>

    <div class="card">
      <strong>Your Attendance</strong>
      <table class="table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Subject</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['date']) ?></td>
              <td><?= htmlspecialchars($row['subject']) ?></td>
              <td><?= htmlspecialchars($row['status']) ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
