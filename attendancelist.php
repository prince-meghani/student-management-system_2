<?php
require __DIR__ . '/inc/auth.php';
require __DIR__ . '/inc/db.php';

$aid = isset($_GET['aid']) ? (int) $_GET['aid'] : 0;
if ($aid <= 0) {
    header('Location: attendance.php');
    exit;
}


$stmt = $conn->prepare("SELECT subject, classroom, date, lecture_duration FROM attendance WHERE aid=?");
$stmt->bind_param("i", $aid);
$stmt->execute();
$res = $stmt->get_result();
$session = $res->fetch_assoc();
$stmt->close();

if (!$session) {
    header('Location: attendance.php');
    exit;
}

$subject = $session['subject'];
$class = $session['classroom'];
$date = $session['date'];
$lecture_duration = $session['lecture_duration'];

$flash = null;


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitatt'])) {
    if (!empty($_POST['sid']) && is_array($_POST['sid'])) {
        $stmt = $conn->prepare("INSERT INTO attendancereport (aid, sid, status) VALUES (?, ?, ?)");
        foreach ($_POST['sid'] as $idx => $studentId) {
            $status = ($_POST['att'][$idx] ?? 'Present') === 'Absent' ? 'Absent' : 'Present';
            $stmt->bind_param("iss", $aid, $studentId, $status);
            $stmt->execute();
        }
        $stmt->close();
        $flash = ['type' => 'ok', 'msg' => 'Attendance saved successfully.'];
    } else {
        $flash = ['type' => 'bad', 'msg' => 'No students selected for attendance.'];
    }
}


$already = $conn->prepare("SELECT COUNT(*) AS c FROM attendancereport WHERE aid=?");
$already->bind_param("i", $aid);
$already->execute();
$marked = (int) $already->get_result()->fetch_assoc()['c'] > 0;
$already->close();
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Attendance #<?= $aid ?></title>
<style>
body { font-family: Arial,sans-serif; background: #f4f6f9; margin:0; padding:0; }
.container { max-width: 1100px; margin:30px auto; padding:20px; background:#fff; border-radius:10px; box-shadow:0 4px 10px rgba(0,0,0,0.1);}
h1 { color:#007bff; text-align:center; margin-bottom:5px;}
h2 { text-align:center; color:#555; font-size:18px; margin-bottom:10px;}
.btn-link { color:#007bff; text-decoration:none; font-weight:bold;}
.btn-link:hover { text-decoration:underline;}
.card { background:#fafafa; padding:20px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1); margin-top:15px;}
table { width:100%; border-collapse:collapse; margin-top:15px;}
th,td { padding:10px; border:1px solid #ddd; text-align:left;}
th { background:#f1f1f1;}
input[type="radio"] { margin-right:5px;}
.flash { padding:10px; margin-bottom:15px; border-radius:5px; text-align:center; font-weight:bold;}
.flash.ok { background:#d4edda; color:#155724;}
.flash.bad { background:#f8d7da; color:#721c24;}
.btn-primary { background:#007bff; color:#fff; border:none; padding:10px 15px; border-radius:5px; cursor:pointer; margin-top:10px;}
.btn-primary:hover { background:#0056b3;}
.table-container { max-height:400px; overflow-y:auto; }
</style>
</head>
<body>
<div class="container">
    <a class="btn-link" href="attendance.php">&larr; Back</a>
    <h1>Attendance #<?= $aid ?></h1>
    <h2><?= htmlspecialchars($subject) ?> — <?= htmlspecialchars($class) ?></h2>
    <p><small>Date: <?= htmlspecialchars($date) ?> • Time: <?= htmlspecialchars($lecture_duration) ?></small></p>

    <?php if ($flash): ?>
        <div class="flash <?= $flash['type'] ?>"><?= htmlspecialchars($flash['msg']) ?></div>
    <?php endif; ?>

    <?php if (!$marked): ?>
      
        <form method="post" class="card">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Class</th>
                            <th>Attendance</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $stmt = $conn->prepare("SELECT sid, fname, lname, classroom FROM student WHERE classroom=? ORDER BY sid ASC");
                    $stmt->bind_param("s", $class);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    $i = 0;
                    while ($row = $res->fetch_assoc()):
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($row['sid']) ?></td>
                            <td><?= htmlspecialchars($row['fname'].' '.$row['lname']) ?></td>
                            <td><?= htmlspecialchars($row['classroom']) ?></td>
                            <td>
                                <input type="hidden" name="sid[<?= $i ?>]" value="<?= htmlspecialchars($row['sid']) ?>">
                                <label><input type="radio" name="att[<?= $i ?>]" value="Present" checked> Present</label>
                                <label style="margin-left:16px"><input type="radio" name="att[<?= $i ?>]" value="Absent"> Absent</label>
                            </td>
                        </tr>
                    <?php $i++; endwhile; $stmt->close(); ?>
                    </tbody>
                </table>
            </div>
            <button class="btn-primary" name="submitatt" value="1">Save Attendance</button>
        </form>
    <?php else: ?>
        
        <div class="card table-container">
            <strong>Recorded Attendance</strong>
            <table>
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Class</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $stmt = $conn->prepare("
                    SELECT s.sid, s.fname, s.lname, s.classroom, ar.status
                    FROM attendancereport ar
                    JOIN student s ON ar.sid = s.sid
                    WHERE ar.aid=?
                    ORDER BY s.sid ASC
                ");
                $stmt->bind_param("i", $aid);
                $stmt->execute();
                $res = $stmt->get_result();
                while ($r = $res->fetch_assoc()):
                ?>
                    <tr>
                        <td><?= htmlspecialchars($r['sid']) ?></td>
                        <td><?= htmlspecialchars($r['fname'].' '.$r['lname']) ?></td>
                        <td><?= htmlspecialchars($r['classroom']) ?></td>
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
