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
      background-color: #f4f6f9;
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 1100px;
      margin: 30px auto;
      padding: 20px;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    h1 {
      color: #007bff;
      text-align: center;
      margin-bottom: 10px;
    }

    h2 {
      text-align: center;
      color: #555;
      font-size: 18px;
      margin-bottom: 25px;
    }

    .row {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
    }

    .col {
      flex: 1;
      min-width: 320px;
    }

    .card {
      background: #fafafa;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    label {
      display: block;
      margin-top: 10px;
      font-weight: bold;
    }

    select,
    input[type="date"] {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    .btn-primary {
      background: #007bff;
      color: white;
      border: none;
      padding: 10px 15px;
      border-radius: 5px;
      cursor: pointer;
      transition: 0.3s ease;
    }

    .btn-primary:hover {
      background: #0056b3;
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

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    th, td {
      padding: 10px;
      border: 1px solid #ddd;
      text-align: left;
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

    @media (max-width: 600px) {
      .row {
        flex-direction: column;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Attendance</h1>
    <h2>Create a session and view past ones</h2>

    <?php
    $flash = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
      $sid  = $_POST['schedule'] ?? '';
      $date = $_POST['date'] ?? '';

      if ($sid === '' || $date === '') {
        $flash = ['type'=>'bad','msg'=>'Please pick a schedule and a date.'];
      } else {
        $dt = date_create($date);
        if (!$dt) {
          $flash = ['type'=>'bad','msg'=>'Invalid date.'];
        } else {
          $dateYmd = $dt->format('Y-m-d');
          $stmt = $conn->prepare("INSERT INTO attendance (`sid`, `date`) VALUES (?, ?)");
          $stmt->bind_param("is", $sid, $dateYmd);
          if ($stmt->execute()) {
            $flash = ['type'=>'ok','msg'=>'Attendance session created.'];
          } else {
            $flash = ['type'=>'bad','msg'=>'Could not create session.'];
          }
          $stmt->close();
        }
      }
    }

    if ($flash) {
      echo '<div class="flash '.$flash['type'].'">'.htmlspecialchars($flash['msg']).'</div>';
    }
    ?>

    <div class="row">
      <!-- Left Column -->
      <div class="col">
        <div class="card">
          <form method="post">
            <label>Schedule</label>
            <select name="schedule" required>
              <option value="">Select a schedule…</option>
              <?php
              $res = $conn->query("SELECT id, subject, class, day, stime FROM schedule ORDER BY day, stime");
              while ($row = $res->fetch_assoc()):
                $label = $row['subject'].' — '.$row['class'].' — '.$row['day'].' — '.$row['stime'];
              ?>
                <option value="<?= (int)$row['id'] ?>"><?= htmlspecialchars($label) ?></option>
              <?php endwhile; ?>
            </select>

            <label>Date</label>
            <input type="date" name="date" required>

            <div style="margin-top:12px">
              <button class="btn-primary" name="create" value="1">Add Attendance</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Right Column -->
      <div class="col">
        <div class="card">
          <strong>All Sessions</strong>
          <table class="table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Subject</th>
                <th>Class</th>
                <th>Date</th>
                <th>Start</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql = "SELECT a.aid, a.date, s.subject, s.class, s.stime
                      FROM attendance a
                      JOIN schedule s ON a.sid = s.id
                      ORDER BY a.date DESC, s.stime DESC";
              $res = $conn->query($sql);
              while ($r = $res->fetch_assoc()):
              ?>
              <tr>
                <td><?= (int)$r['aid'] ?></td>
                <td><?= htmlspecialchars($r['subject']) ?></td>
                <td><?= htmlspecialchars($r['class']) ?></td>
                <td><?= htmlspecialchars($r['date']) ?></td>
                <td><?= htmlspecialchars($r['stime']) ?></td>
                <td class="actions">
                  <a class="btn-link" href="attendancelist.php?aid=<?= (int)$r['aid'] ?>&class=<?= urlencode($r['class']) ?>&date=<?= urlencode($r['date']) ?>&subject=<?= urlencode($r['subject']) ?>&stime=<?= urlencode($r['stime']) ?>">Open</a>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
