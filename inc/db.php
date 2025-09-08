<?php

$host = "localhost";       
$user = "root";            
$pass = "";                
$db   = "student-management-system";      


date_default_timezone_set('Asia/Kolkata'); 

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("❌ Database Connection Failed: " . mysqli_connect_error());
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


function clean_input($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}
?>
