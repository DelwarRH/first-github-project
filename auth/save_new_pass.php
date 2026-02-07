<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['temp_user_id'])) {
    $user_id = $_SESSION['temp_user_id'];
    $new_pass = $_POST['new_pass'];
    $confirm_pass = $_POST['confirm_pass'];

    if ($new_pass !== $confirm_pass) {
        echo "<script>alert('পাসওয়ার্ড দুটি মেলেনি!'); window.location.href='../set_password.php';</script>";
        exit();
    }

    // পাসওয়ার্ড হ্যাশ করা
    $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed_password, $user_id]);

        // সেশন ক্লিন করা
        unset($_SESSION['temp_user_id']);
        
        echo "<script>alert('সাফল্য! পাসওয়ার্ড সেট হয়েছে। এখন লগইন করুন।'); window.location.href='../student_login.php';</script>";
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}