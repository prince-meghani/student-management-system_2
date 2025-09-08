<?php require __DIR__.'/inc/auth.php'; require __DIR__.'/inc/db.php'; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Attendance</title>
  <link rel="stylesheet" href="public/styles.css">
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
                <th></th>
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
