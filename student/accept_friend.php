<?php
session_start();
require_once '../config/db.php';

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("UPDATE student_connections SET status = 'accepted' WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    header("Location: dashboard.php");
}