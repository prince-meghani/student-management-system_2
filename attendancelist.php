<?php require __DIR__.'/inc/auth.php'; require __DIR__.'/inc/db.php';

$aid    = isset($_GET['aid']) ? (int)$_GET['aid'] : 0;
$class  = $_GET['class']  ?? '';
$subject= $_GET['subject']?? '';
$date   = $_GET['date']   ?? '';
$stime  = $_GET['stime']  ?? '';
if ($aid <= 0) { header('Location: attendance.php'); exit; }

$flash = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitatt'])) {
  if (isset($_POST['sid'], $_POST['att']) && is_array($_POST['sid']) && is_array($_POST['att'])) {
    $stmt = $conn->prepare("INSERT INTO attendancereport (aid, sid, status) VALUES (?, ?, ?)");
    foreach ($_POST['sid'] as $idx => $studentId) {
      $status = ($_POST['att'][$idx] ?? 'Present') === 'Absent' ? 'Absent' : 'Present';
      $stmt->bind_param("iss", $aid, $studentId, $status);
      $stmt->execute();
    }
    $stmt->close();
    $flash = ['type'=>'ok','msg'=>'Attendance saved.'];
  } else {
    $flash = ['type'=>'bad','msg'=>'Nothing to save.'];
  }
}

$already = $conn->prepare("SELECT COUNT(*) AS c FROM attendancereport WHERE aid=?");
$already->bind_param("i", $aid);
$already->execute();
$marked = (int)$already->get_result()->fetch_assoc()['c'] > 0;
$already->close();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Attendance #<?= $aid ?></title>
  <link rel="stylesheet" href="public/styles.css">
</head>
<body>
  <div class="container">
    <a class="btn-link" href="attendance.php">← Back</a>
    <h1>Attendance #<?= $aid ?></h1>
    <h2><?= htmlspecialchars($subject) ?> — <?= htmlspecialchars($class) ?></h2>
    <p><small>Date: <?= htmlspecialchars($date) ?> • Start: <?= htmlspecialchars($stime) ?></small></p>

    <?php if ($flash): ?>
      <div class="flash <?= $flash['type'] ?>"><?= htmlspecialchars($flash['msg']) ?></div>
    <?php endif; ?>

    <?php if (!$marked): ?>
      <form method="post" class="card">
        <table class="table">
          <thead>
            <tr><th>Student ID</th><th>Name</th><th>Attendance</th></tr>
          </thead>
          <tbody>
            <?php
            $stmt = $conn->prepare("SELECT sid, fname, lname FROM student WHERE classroom = ?");
            $stmt->bind_param("s", $class);
            $stmt->execute();
            $res = $stmt->get_result();
            $i = 0;
            while ($row = $res->fetch_assoc()):
            ?>
            <tr>
              <td><?= htmlspecialchars($row['sid']) ?></td>
              <td><?= htmlspecialchars($row['fname'].' '.$row['lname']) ?></td>
              <td>
                <input type="hidden" name="sid[<?= $i ?>]" value="<?= htmlspecialchars($row['sid']) ?>">
                <label><input type="radio" name="att[<?= $i ?>]" value="Present" checked> Present</label>
                <label style="margin-left:16px"><input type="radio" name="att[<?= $i ?>]" value="Absent"> Absent</label>
              </td>
            </tr>
            <?php $i++; endwhile; $stmt->close(); ?>
          </tbody>
        </table>
        <div style="margin-top:12px">
          <button class="btn-primary" name="submitatt" value="1">Save Attendance</button>
        </div>
      </form>
    <?php else: ?>
      <div class="card">
        <strong>Recorded Attendance</strong>
        <table class="table">
          <thead><tr><th>Student ID</th><th>Name</th><th>Status</th></tr></thead>
          <tbody>
            <?php
            $stmt = $conn->prepare(
              "SELECT s.sid, s.fname, s.lname, ar.status
               FROM attendancereport ar
               JOIN student s ON ar.sid = s.sid
               WHERE ar.aid = ?"
            );
            $stmt->bind_param("i", $aid);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($r = $res->fetch_assoc()):
            ?>
              <tr>
                <td><?= htmlspecialchars($r['sid']) ?></td>
                <td><?= htmlspecialchars($r['fname'].' '.$r['lname']) ?></td>
                <td><?= htmlspecialchars($r['status']) ?></td>
              </tr>
            <?php endwhile; $stmt->close(); ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
