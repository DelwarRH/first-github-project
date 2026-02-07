<?php
session_start();
require_once '../config/db.php';

if(isset($_GET['type']) && isset($_GET['id'])) {
    $type = $_GET['type'];
    $target_id = $_GET['id'];
    $sender_user_id = $_SESSION['user_id'];

    if($type == 'teacher') {
        // স্টুডেন্টের প্রাইমারি আইডি বের করা
        $stmt = $pdo->prepare("SELECT id FROM students WHERE user_id = ?");
        $stmt->execute([$sender_user_id]);
        $student_id = $stmt->fetchColumn();

        $sql = "INSERT INTO connection_requests (teacher_id, student_id, status) VALUES (?, ?, 'pending')";
        $pdo->prepare($sql)->execute([$target_id, $student_id]);
    } else if($type == 'student') {
        $sql = "INSERT INTO student_connections (sender_id, receiver_id, status) VALUES (?, ?, 'pending')";
        $pdo->prepare($sql)->execute([$sender_user_id, $target_id]);
    }

    echo "<script>alert('অনুরোধ পাঠানো হয়েছে!'); window.location.href='dashboard.php';</script>";
}