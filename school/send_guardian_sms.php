<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $school_id = $_SESSION['school_id'];
    $teacher_name = $_SESSION['user_name'];
    $class = $_POST['class'];
    $message = trim($_POST['message']);

    try {
        $pdo->beginTransaction();

        // ১. ওই নির্দিষ্ট শ্রেণির সকল শিক্ষার্থীর মোবাইল নম্বর সংগ্রহ করা
        $stmt_students = $pdo->prepare("SELECT phone FROM students WHERE school_id = ? AND class = ?");
        $stmt_students->execute([$school_id, $class]);
        $students = $stmt_students->fetchAll();

        if (count($students) > 0) {
            // ২. প্রতিটি নম্বরের জন্য এসএমএস লগে ডাটা ইনসার্ট করা
            $stmt_log = $pdo->prepare("INSERT INTO sms_logs (school_id, receiver_number, message, sent_by) VALUES (?, ?, ?, ?)");
            
            foreach ($students as $stu) {
                if (!empty($stu['phone'])) {
                    $stmt_log->execute([$school_id, $stu['phone'], $message, $teacher_name]);
                }
            }

            $pdo->commit();
            echo "<script>alert('সফল! শ্রেণি: $class-এর সকল অভিভাবকের কাছে এসএমএস পাঠানো হয়েছে।'); window.location.href='teacher_dashboard.php';</script>";
        } else {
            echo "<script>alert('দুঃখিত! এই শ্রেণিতে কোনো শিক্ষার্থী পাওয়া যায়নি।'); window.location.href='teacher_dashboard.php';</script>";
        }

    } catch (Exception $e) {
        $pdo->rollBack();
        die("ভুল হয়েছে: " . $e->getMessage());
    }
} else {
    header("Location: teacher_dashboard.php");
}