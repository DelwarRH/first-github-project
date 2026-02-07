<?php
require_once '../config/db.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['school_name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $eiin = trim($_POST['eiin']);
    $location_id = $_POST['upazila']; 

    try {
        $pdo->beginTransaction();

        // ১. ইউজারকে Pending হিসেবে সেভ করা
        $stmt1 = $pdo->prepare("INSERT INTO users (name, email, password, role, location_id, status) VALUES (?, ?, ?, 'school', ?, 'pending')");
        $stmt1->execute([$name, $email, $password, $location_id]);
        $user_id = $pdo->lastInsertId();

        // ২. স্কুল ডিটেইলস সেভ
        $stmt2 = $pdo->prepare("INSERT INTO schools (user_id, eiin_number, subscription_status) VALUES (?, ?, 'unpaid')");
        $stmt2->execute([$user_id, $eiin]);

        $pdo->commit();

        // ৩. সরাসরি পেমেন্ট গেটওয়ে পেজে পাঠানো
        header("Location: ../payment.php?user_id=" . $user_id . "&type=registration");
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        die("রেজিস্ট্রেশন ব্যর্থ: " . $e->getMessage());
    }
}