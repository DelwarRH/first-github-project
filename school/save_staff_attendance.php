<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['status'])) {
    $school_id = $_SESSION['school_id'];
    $role = $_POST['role'];
    $date = $_POST['date'];

    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO staff_attendance (school_id, user_id, role, date, status) VALUES (?, ?, ?, ?, ?)");

        foreach ($_POST['status'] as $user_id => $status) {
            $stmt->execute([$school_id, $user_id, $role, $date, $status]);
        }

        $pdo->commit();
        echo "<script>alert('শিক্ষক/স্টাফ হাজিরা সফলভাবে সংরক্ষিত হয়েছে!'); window.location.href='staff_attendance_entry.php';</script>";
    } catch (Exception $e) {
        $pdo->rollBack();
        die("ভুল হয়েছে: " . $e->getMessage());
    }
}