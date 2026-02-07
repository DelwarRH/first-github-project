<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SESSION['user_role'] == 'teacher') {
    $teacher_id = $_SESSION['user_id'];
    $school_id  = $_SESSION['school_id'];
    $class      = $_POST['class'];
    $subject    = $_POST['subject'];
    $title      = $_POST['title'];
    $content    = $_POST['content'];

    try {
        $stmt = $pdo->prepare("INSERT INTO lessons (school_id, teacher_id, class, subject, title, content, date) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$school_id, $teacher_id, $class, $subject, $title, $content]);
        
        echo "<script>alert('লেসনটি সফলভাবে টাইমলাইনে পাবলিশ করা হয়েছে!'); window.location.href='teacher_dashboard.php';</script>";
    } catch (Exception $e) {
        die("ত্রুটি: " . $e->getMessage());
    }
}