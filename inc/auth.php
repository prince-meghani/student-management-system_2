<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'Teacher') {
  header('Location: ./index.php');
  exit;
}
