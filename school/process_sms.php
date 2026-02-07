<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_sms'])) {
    $school_id = $_SESSION['school_id'];
    $sent_by = $_SESSION['user_name']; // হেডমাস্টার বা অপারেটরের নাম
    $message = trim($_POST['message']);
    $class = $_POST['class'];
    $custom_number = trim($_POST['custom_number']);

    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO sms_logs (school_id, receiver_number, message, sent_by) VALUES (?, ?, ?, ?)");

        // যদি ক্লাস ভিত্তিক পাঠানো হয় (Bulk)
        if (!empty($class)) {
            $stu_stmt = $pdo->prepare("SELECT phone FROM students WHERE school_id = ? AND class = ?");
            $stu_stmt->execute([$school_id, $class]);
            while ($s = $stu_stmt->fetch()) {
                if (!empty($s['phone'])) {
                    $stmt->execute([$school_id, $s['phone'], $message, $sent_by]);
                }
            }
        } 
        // যদি নির্দিষ্ট নম্বরে পাঠানো হয় (Single)
        elseif (!empty($custom_number)) {
            $stmt->execute([$school_id, $custom_number, $message, $sent_by]);
        }

        $pdo->commit();
        echo "<script>alert('মেসেজ সফলভাবে পাঠানো হয়েছে!'); window.location.href='send_sms.php';</script>";
    } catch (Exception $e) {
        $pdo->rollBack();
        die("ভুল হয়েছে: " . $e->getMessage());
    }
}