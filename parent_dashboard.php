<?php
session_start();
require __DIR__.'/inc/db.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'Parent') {
  header('Location: index.php');
  exit;
}

$parentEmail = $_SESSION['user'];

// Find parent's PID
$stmt = $conn->prepare("SELECT pid, fname, lname FROM parent WHERE email=? LIMIT 1");
$stmt->bind_param("s", $parentEmail);
$stmt->execute();
$parent = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$parent) {
  die("Parent record not found!");
}

$pid = $parent['pid'];

// Fetch attendance for all children
$query = "
SELECT st.fname AS student_fname, st.lname AS student_lname, a.date, s.subject, s.class, ar.status
FROM student st
JOIN attendancereport ar ON st.sid = ar.sid
JOIN attendance a ON ar.aid = a.aid
JOIN schedule s ON a.sid = s.id
WHERE st.parent = ?
ORDER BY a.date DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $pid);
$stmt->execute();
$result = $stmt->get_result();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Parent Dashboard</title>
  <link rel="stylesheet" href="public/styles.css">
</head>
<body>
  <div class="container">
    <h1>Welcome, <?= htmlspecialchars($parent['fname'].' '.$parent['lname']) ?></h1>
    <p><a class="btn-link" href="logout.php">Logout</a></p>

    <div class="card">
      <strong>Your Children's Attendance</strong>
      <table class="table">
        <thead>
          <tr>
            <th>Student</th>
            <th>Date</th>
            <th>Subject</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['student_fname'].' '.$row['student_lname']) ?></td>
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
