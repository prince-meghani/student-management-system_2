<?php
session_start();
include("inc/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != "Teacher") {
    die("Access denied. Only teachers can manage notices.");
}

// Delete notice
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM notice WHERE id=$id");
    echo "<p style='color:green;'>Notice deleted!</p>";
}

// Add new notice
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $audience = $_POST['audience'];

    $sql = "INSERT INTO notice (title, message, audience) VALUES ('$title', '$message', '$audience')";
    mysqli_query($conn, $sql);
    echo "<p style='color:green;'>Notice added successfully!</p>";
}
?>

<h2>Manage Notices</h2>
<form method="POST">
    <label>Title:</label><br>
    <input type="text" name="title" required><br><br>

    <label>Message:</label><br>
    <textarea name="message" required></textarea><br><br>

    <label>Audience:</label><br>
    <select name="audience" required>
        <option value="All">All</option>
        <option value="Students">Students</option>
        <option value="Parents">Parents</option>
    </select><br><br>

    <button type="submit">Post Notice</button>
</form>

<hr>
<h3>Existing Notices</h3>
<?php
$res = mysqli_query($conn, "SELECT * FROM notice ORDER BY created_at DESC");
while ($row = mysqli_fetch_assoc($res)) {
    echo "<div style='border:1px solid #ccc; padding:10px; margin-bottom:10px;'>
            <strong>{$row['title']}</strong> ({$row['audience']}) 
            <a href='?delete={$row['id']}' style='color:red;'>[Delete]</a><br>
            <small>{$row['created_at']}</small><br>
            <p>{$row['message']}</p>
          </div>";
}
?>
