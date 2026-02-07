<?php
session_start();
require_once '../config/db.php';

if(isset($_GET['id']) && isset($_GET['action'])) {
    $id = $_GET['id'];
    $action = $_GET['action']; // accepted or rejected
    $teacher_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("UPDATE connection_requests SET status = ? WHERE id = ? AND teacher_id = ?");
    $stmt->execute([$action, $id, $teacher_id]);

    header("Location: teacher_dashboard.php");
}