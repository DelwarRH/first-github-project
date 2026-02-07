<?php
session_start();
require_once '../config/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $school_id = $_SESSION['school_id'];

    try {
        $pdo->beginTransaction();

        // ১. আবেদনকারীর তথ্য আনা
        $stmt = $pdo->prepare("SELECT * FROM admissions WHERE id = ? AND school_id = ?");
        $stmt->execute([$id, $school_id]);
        $data = $stmt->fetch();

        if ($data) {
            // ২. স্টুডেন্ট টেবিলে ডাটা ইনসার্ট করা (ভর্তি নিশ্চিত করা)
            // নোট: রোল নম্বর ডিফল্ট হিসেবে ৯৯৯ দেওয়া হয়েছে, পরে অপারেটর এডিট করে নিবে
            $ins = $pdo->prepare("INSERT INTO students (school_id, name, roll, class, father, phone, photo, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'active')");
            $ins->execute([$school_id, $data['student_name'], 999, $data['desired_class'], $data['father_name'], $data['phone'], $data['photo']]);

            // ৩. আবেদনপত্রের স্ট্যাটাস আপডেট করা
            $upd = $pdo->prepare("UPDATE admissions SET status = 'admitted' WHERE id = ?");
            $upd->execute([$id]);

            $pdo->commit();
            echo "<script>alert('শিক্ষার্থী সফলভাবে ভর্তি করা হয়েছে!'); window.location.href='pending_applicants.php';</script>";
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        die("ভুল হয়েছে: " . $e->getMessage());
    }
}