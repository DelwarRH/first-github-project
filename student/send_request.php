<?php
session_start();
require_once '../config/db.php';

if (isset($_GET['t_id'])) {
    $teacher_id = $_GET['t_id'];
    $student_user_id = $_SESSION['user_id'];

    // স্টুডেন্টের প্রাইমারি আইডি খুঁজে বের করা
    $stmt_s = $pdo->prepare("SELECT id FROM students WHERE user_id = ?");
    $stmt_s->execute([$student_user_id]);
    $student_id = $stmt_s->fetchColumn();

    // অলরেডি রিকোয়েস্ট পাঠানো আছে কি না চেক
    $check = $pdo->prepare("SELECT id FROM connection_requests WHERE teacher_id = ? AND student_id = ?");
    $check->execute([$teacher_id, $student_id]);

    if ($check->rowCount() == 0) {
        $ins = $pdo->prepare("INSERT INTO connection_requests (teacher_id, student_id, status) VALUES (?, ?, 'pending')");
        $ins->execute([$teacher_id, $student_id]);
    }

    header("Location: dashboard.php");
}