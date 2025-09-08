<?php require __DIR__.'/inc/auth.php'; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Dashboard</title>
  <link rel="stylesheet" href="public/styles.css">
</head>
<body>
  <div class="container">
    <h1>Welcome, Teacher</h1>
    <p>Use the links below to manage attendance.</p>
    <div class="card">
      <p><a class="btn-link" href="attendance.php">Manage Attendance</a></p>
      <p><a class="btn-link" href="logout.php">Logout</a></p>
    </div>
  </div>
</body>
</html>
