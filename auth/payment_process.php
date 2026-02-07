<?php
// auth/payment_process.php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $amount = $_POST['amount'];
    $transaction_id = "BKASH-" . strtoupper(uniqid()); // ডামি ট্রানজেকশন আইডি
    $expiry_date = date('Y-m-d', strtotime('+1 year')); // ১ বছরের মেয়াদ

    try {
        $pdo->beginTransaction();

        // ১. পেমেন্ট রেকর্ড সেভ করা
        $stmt_pay = $pdo->prepare("INSERT INTO payments (user_id, amount, transaction_id, payment_type, status) VALUES (?, ?, ?, 'registration', 'completed')");
        $stmt_pay->execute([$user_id, $amount, $transaction_id]);

        // ২. ইউজার স্ট্যাটাস একটিভ করা
        $stmt_user = $pdo->prepare("UPDATE users SET status = 'active' WHERE id = ?");
        $stmt_user->execute([$user_id]);

        // ৩. স্কুলের সাবস্ক্রিপশন স্ট্যাটাস এবং মেয়াদ আপডেট করা
        $stmt_school = $pdo->prepare("UPDATE schools SET subscription_status = 'paid', expiry_date = ? WHERE user_id = ?");
        $stmt_school->execute([$expiry_date, $user_id]);

        $pdo->commit();

        // সেশন মেসেজ সেট করা
        $_SESSION['success_msg'] = "পেমেন্ট সফল হয়েছে! এখন লগইন করুন।";
        header("Location: ../login.php");
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        die("পেমেন্ট প্রসেস করতে ত্রুটি হয়েছে: " . $e->getMessage());
    }
} else {
    header("Location: ../index.php");
}