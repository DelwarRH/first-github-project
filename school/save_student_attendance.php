<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['attendance'])) {
    $school_id = $_SESSION['school_id'];
    $class = $_POST['class'];
    $date = $_POST['date'];

    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO student_attendance (school_id, student_id, class, date, status) VALUES (?, ?, ?, ?, ?)");

        foreach ($_POST['attendance'] as $student_id => $status) {
            $stmt->execute([$school_id, $student_id, $class, $date, $status]);
        }

        $pdo->commit();
        echo "<script>alert('হাজিরা সফলভাবে সংরক্ষিত হয়েছে!'); window.location.href='student_attendance_entry.php';</script>";
    } catch (Exception $e) {
        $pdo->rollBack();
        die("ত্রুটি: " . $e->getMessage());
    }
}