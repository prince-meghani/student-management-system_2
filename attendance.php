<?php
include __DIR__ . "/inc/auth.php";
include __DIR__ . "/inc/db.php";
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Attendance</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f6f9;
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 1100px;
      margin: 30px auto;
      padding: 20px;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    h1 {
      color: #007bff;
      text-align: center;
    }

    h2 {
      text-align: center;
      color: #555;
      font-size: 18px;
      margin-bottom: 10px;
    }

    .row {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
    }

    .col {
      flex: 1;
      min-width: 320px;
    }

    .card {
      background: #fafafa;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    label {
      display: block;
      margin-top: 10px;
      font-weight: bold;
    }

    input,
    select {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    .btn-primary {
      background: #007bff;
      color: #fff;
      border: none;
      padding: 10px 15px;
      border-radius: 5px;
      cursor: pointer;
      margin: 15px 0px;
      text-decoration: none;
      display: inline-block;
    }

    .btn-primary:hover {
      background: #0056b3;
    }

    .btn-back {
      background: #6c757d;
    }

    .flash {
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 5px;
      text-align: center;
      font-weight: bold;
    }

    .flash.ok {
      background: #d4edda;
      color: #155724;
    }

    .flash.bad {
      background: #f8d7da;
      color: #721c24;
    }

    .table-container {
      max-height: 400px;
      overflow-y: auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
      table-layout: fixed;
    }

    th,
    td {
      padding: 10px;
      border: 1px solid #ddd;
      text-align: left;
      word-wrap: break-word;
    }

    th {
      background: #f1f1f1;
    }

    .btn-link {
      color: #007bff;
      text-decoration: none;
      font-weight: bold;
    }

    .btn-link:hover {
      text-decoration: underline;
    }

    .search-bar {
      display: flex;
      gap: 10px;
      margin-bottom: 15px;
    }

    .search-bar input,
    .search-bar select {
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
  </style>
</head>

<body>
  <div class="container">
    <h1>Attendance</h1>
    <h2>Add and View Sessions</h2>

    <div style="text-align:left; margin-bottom: 15px;">
      <a href="index.php" class="btn-primary btn-back">&larr; Back</a>
    </div>

    <?php
    $flash = null;

 
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
      $subject = trim($_POST['subject']);
      $class = trim($_POST['class']);
      $date = $_POST['date'];
      $lecture_time = trim($_POST['lecture_time']);

      if ($subject == '' || $class == '' || $date == '' || $lecture_time == '') {
        $flash = ['type' => 'bad', 'msg' => 'Please fill all fields.'];
      } else {
        $stmt = $conn->prepare("INSERT INTO attendance (subject,classroom,date,lecture_duration) VALUES (?,?,?,?)");
        $stmt->bind_param("ssss", $subject, $class, $date, $lecture_time);
        if ($stmt->execute()) {
          $stmt->close();
          header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
          exit();
        } else {
          $flash = ['type' => 'bad', 'msg' => 'Failed to add session'];
          $stmt->close();
        }
      }
    }

    if (isset($_GET['success'])) {
      $flash = ['type' => 'ok', 'msg' => 'Attendance session added successfully'];
    }

    if ($flash) {
      echo '<div class="flash ' . $flash['type'] . '">' . htmlspecialchars($flash['msg']) . '</div>';
    }

   
    $searchClass = isset($_POST['search_class']) ? trim($_POST['search_class']) : '';
    $searchDate = isset($_POST['search_date']) ? trim($_POST['search_date']) : '';
    $searchSubject = isset($_POST['search_subject']) ? trim($_POST['search_subject']) : '';

    $conditions = [];
    if ($searchClass != '') {
      $conditions[] = "classroom LIKE '%" . $conn->real_escape_string($searchClass) . "%'";
    }
    if ($searchDate != '') {
      $conditions[] = "date = '" . $conn->real_escape_string($searchDate) . "'";
    }
    if ($searchSubject != '') {
      $conditions[] = "subject LIKE '%" . $conn->real_escape_string($searchSubject) . "%'";
    }

    $where = '';
    if (count($conditions) > 0) {
      $where = "WHERE " . implode(" AND ", $conditions);
    }

    $sql = "SELECT * FROM attendance $where ORDER BY aid DESC";
    $res = $conn->query($sql);
    ?>

    <div class="row">

      <div class="col">
        <div class="card">
          <form method="post">
            <label>Subject</label>
            <input type="text" name="subject" required>

            <label>Class</label>
            <input type="text" name="class" placeholder="e.g. B.Tech, MCA" required>

            <label>Date</label>
            <input type="date" name="date" required>

            <label>Lecture Time / Duration</label>
            <input type="text" name="lecture_time" placeholder="e.g. 09:00-10:00" required>

            <button class="btn-primary" name="create">Add Session</button>
          </form>
        </div>
      </div>

   
      <div class="col">
        <div class="card">
          <strong>All Sessions</strong>

        
          <form method="post" class="search-bar">
            <input type="text" name="search_class" placeholder="Search by Class" value="<?= htmlspecialchars($searchClass) ?>">
            <input type="text" name="search_subject" placeholder="Search by Subject" value="<?= htmlspecialchars($searchSubject) ?>">
            <input type="date" name="search_date" value="<?= htmlspecialchars($searchDate) ?>">
            <button type="submit" class="btn-primary">Search</button>
          </form>

          <div class="table-container">
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Subject</th>
                  <th>Class</th>
                  <th>Date</th>
                  <th>Lecture Time</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($res->num_rows > 0): ?>
                  <?php while ($r = $res->fetch_assoc()): ?>
                    <tr>
                      <td><?= (int) $r['aid'] ?></td>
                      <td><?= htmlspecialchars($r['subject']) ?></td>
                      <td><?= htmlspecialchars($r['classroom']) ?></td>
                      <td><?= htmlspecialchars($r['date']) ?></td>
                      <td><?= htmlspecialchars($r['lecture_duration']) ?></td>
                      <td>
                  
                        <a class="btn-link" href="attendancelist.php?aid=<?= (int) $r['aid'] ?>">Open</a>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="6" style="text-align:center; color:#777;">No sessions found.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>
