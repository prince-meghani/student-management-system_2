<?php
session_start();
include("inc/db.php");


if (!isset($_SESSION['role']) || $_SESSION['role'] != "Teacher") {
    die("<div class='error-message'>Access denied. Only teachers can manage notices.</div>");
}

$message = "";
$messageClass = "";


if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    mysqli_query($conn, "DELETE FROM notice WHERE id=$id");
    $message = "Notice deleted successfully!";
    $messageClass = "success";
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $noticeMessage = mysqli_real_escape_string($conn, $_POST['message']);
    $audience = $_POST['audience'];

    $sql = "INSERT INTO notice (title, message, audience) VALUES ('$title', '$noticeMessage', '$audience')";
    if (mysqli_query($conn, $sql)) {
        $message = "Notice added successfully!";
        $messageClass = "success";
    } else {
        $message = "Error: " . mysqli_error($conn);
        $messageClass = "error";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Notices</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 900px;
            margin: 30px auto;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        h2,
        h3 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .flash-message {
            text-align: center;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: bold;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }


        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
            color: #555;
        }

        input[type="text"],
        textarea,
        select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            width: 100%;
            transition: 0.3s;
        }

        input[type="text"]:focus,
        textarea:focus,
        select:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }


        button {
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        hr {
            margin: 30px 0;
            border: none;
            border-top: 1px solid #ddd;
        }


        .notice-card {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            background-color: #fafafa;
            transition: 0.3s ease;
        }

        .notice-card:hover {
            background-color: #f1f1f1;
        }

        .notice-card strong {
            font-size: 18px;
            color: #333;
        }

        .notice-card small {
            display: block;
            color: #666;
            font-size: 12px;
            margin-top: 4px;
        }


        .delete-link {
            color: #dc3545;
            font-size: 14px;
            margin-left: 10px;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .delete-link:hover {
            color: #a71d2a;
            text-decoration: underline;
        }

        p {
            margin-top: 8px;
            color: #444;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Manage Notices</h2>
        <a class="btn-back" href="index.php">&larr; Back</a>
        <?php if (!empty($message)): ?>
            <div class="flash-message <?php echo $messageClass; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>


        <form method="POST">
            <div>
                <label>Title:</label>
                <input type="text" name="title" placeholder="Enter notice title" required>
            </div>

            <div>
                <label>Message:</label>
                <textarea name="message" placeholder="Write your notice message here..." required></textarea>
            </div>

            <div>
                <label>Audience:</label>
                <select name="audience" required>
                    <option value="">Select audience</option>
                    <option value="All">All</option>
                    <option value="Students">Students</option>
                    <option value="Parents">Parents</option>
                </select>
            </div>

            <button type="submit">Post Notice</button>
        </form>

        <hr>

        <h3>Existing Notices</h3>
        <?php
        $res = mysqli_query($conn, "SELECT * FROM notice ORDER BY created_at DESC");
        if (mysqli_num_rows($res) > 0):
            while ($row = mysqli_fetch_assoc($res)):
                ?>
                <div class="notice-card">
                    <strong><?php echo htmlspecialchars($row['title']); ?></strong>
                    (<?php echo htmlspecialchars($row['audience']); ?>)
                    <a class="delete-link" href="?delete=<?php echo (int) $row['id']; ?>">[Delete]</a>
                    <small><?php echo htmlspecialchars($row['created_at']); ?></small>
                    <p><?php echo nl2br(htmlspecialchars($row['message'])); ?></p>
                </div>
                <?php
            endwhile;
        else:
            echo "<p style='text-align:center; color:#777;'>No notices available.</p>";
        endif;
        ?>
    </div>
</body>

</html>