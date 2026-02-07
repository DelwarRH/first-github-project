<?php
session_start();
require_once '../config/db.php';

if(isset($_GET['type']) && isset($_GET['id'])) {
    $type = $_GET['type'];
    $target_id = $_GET['id'];
    $sender_id = $_SESSION['user_id'];

    try {
        if($type == 'teacher') {
            // শিক্ষককে রিকোয়েস্ট (আগের connection_requests টেবিল ব্যবহার করে)
            $stmt_stu = $pdo->prepare("SELECT id FROM students WHERE user_id = ?");
            $stmt_stu->execute([$sender_id]);
            $student_primary_id = $stmt_stu->fetchColumn();

            $sql = "INSERT INTO connection_requests (teacher_id, student_id, status) VALUES (?, ?, 'pending')";
            $pdo->prepare($sql)->execute([$target_id, $student_primary_id]);
        } else if($type == 'student') {
            // অন্য শিক্ষার্থীকে ফ্রেন্ড রিকোয়েস্ট (student_connections টেবিল)
            $sql = "INSERT INTO student_connections (sender_id, receiver_id, status) VALUES (?, ?, 'pending')";
            $pdo->prepare($sql)->execute([$sender_id, $target_id]);
        }

        echo "<script>alert('অনুরোধ পাঠানো হয়েছে!'); window.location.href='dashboard.php';</script>";
    } catch (Exception $e) {
        echo "<script>alert('ইতিমধ্যে অনুরোধ পাঠানো আছে!'); window.location.href='dashboard.php';</script>";
    }
}