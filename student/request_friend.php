<?php
session_start();
require_once '../config/db.php';

if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
    $sender_id = $_SESSION['user_id'];
    $receiver_id = $_GET['id'];

    try {
        // ১. আগে থেকেই রিকোয়েস্ট পাঠানো আছে কি না চেক
        $check = $pdo->prepare("SELECT * FROM student_connections WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)");
        $check->execute([$sender_id, $receiver_id, $receiver_id, $sender_id]);

        if ($check->rowCount() == 0) {
            // ২. রিকোয়েস্ট ইনসার্ট করা
            $stmt = $pdo->prepare("INSERT INTO student_connections (sender_id, receiver_id, status) VALUES (?, ?, 'pending')");
            $stmt->execute([$sender_id, $receiver_id]);
        }
        
        // ৩. সফলভাবে কাজ শেষ হলে ড্যাশবোর্ডে ফিরে যাওয়া (এটি না দিলে পেজ সাদা হয়ে থাকবে)
        header("Location: dashboard.php?msg=request_sent");
        exit();

    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    header("Location: dashboard.php");
    exit();
}