<?php
session_start();
require_once '../config/db.php';

if (isset($_GET['id']) && isset($_GET['u_id'])) {
    $student_id = $_GET['id'];
    $user_id = $_GET['u_id'];
    $school_id = $_SESSION['school_id'];

    try {
        $pdo->beginTransaction();

        // ১. প্রথমে ইউজার টেবিল থেকে ডিলিট করা (এটি করলে অটোমেটিক স্টুডেন্ট টেবিল থেকেও ডিলিট হতে পারে যদি ক্যাসকেড থাকে)
        $stmt1 = $pdo->prepare("DELETE FROM users WHERE id = ? AND school_id = ?");
        $stmt1->execute([$user_id, $school_id]);

        // ২. ব্যাকআপ হিসেবে স্টুডেন্ট টেবিল থেকেও ডিলিট করা
        $stmt2 = $pdo->prepare("DELETE FROM students WHERE id = ? AND school_id = ?");
        $stmt2->execute([$student_id, $school_id]);

        $pdo->commit();
        echo "<script>alert('শিক্ষার্থীর সকল তথ্য সফলভাবে মুছে ফেলা হয়েছে।'); window.location.href='view_students.php';</script>";
    } catch (Exception $e) {
        $pdo->rollBack();
        die("ভুল হয়েছে: " . $e->getMessage());
    }
} else {
    header("Location: view_students.php");
}