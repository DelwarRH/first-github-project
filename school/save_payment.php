<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['student_id'])) {
    $school_id  = $_SESSION['school_id'];
    $student_id = $_POST['student_id'];
    $class      = $_POST['class']; // নতুন
    $roll       = $_POST['roll'];  // নতুন
    $type       = $_POST['type'];
    $month      = $_POST['month'];
    $amount     = $_POST['amount'];
    $operator   = $_SESSION['user_name'];
    $date       = date('Y-m-d');

    try {
        // ১০টি কলামে ডাটা ইনসার্ট করা হচ্ছে
        $stmt = $pdo->prepare("INSERT INTO student_payments (school_id, student_id, class, roll, payment_type, month, year, amount, date, received_by) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$school_id, $student_id, $class, $roll, $type, $month, date('Y'), $amount, $date, $operator]);

        $last_id = $pdo->lastInsertId();
        header("Location: money_receipt.php?id=$last_id");
        exit();
    } catch (Exception $e) {
        die("ত্রুটি: " . $e->getMessage());
    }
}