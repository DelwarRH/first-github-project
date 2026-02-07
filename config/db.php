<?php
// config/db.php
$host = 'localhost';
$db   = 'astha_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // আপনার আগের কোডের $conn ভেরিয়েবল বজায় রাখার জন্য (mysqli compatibility)
    $conn = new mysqli($host, $user, $pass, $db);
    $conn->set_charset("utf8mb4");
} catch (PDOException $e) {
    die("কানেকশন ব্যর্থ: " . $e->getMessage());
}
?>