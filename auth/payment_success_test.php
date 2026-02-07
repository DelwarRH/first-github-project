<?php
// auth/payment_success_test.php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $expiry_date = date('Y-m-d', strtotime('+1 year')); // ১ বছরের মেয়াদ

    try {
        $pdo->beginTransaction();

        // ১. ইউজার স্ট্যাটাস একটিভ করা
        $stmt1 = $pdo->prepare("UPDATE users SET status = 'active' WHERE id = ?");
        $stmt1->execute([$user_id]);

        // ২. স্কুলের সাবস্ক্রিপশন পেইড করা এবং মেয়াদ বাড়ানো
        $stmt2 = $pdo->prepare("UPDATE schools SET subscription_status = 'paid', expiry_date = ? WHERE user_id = ?");
        $stmt2->execute([$expiry_date, $user_id]);

        $pdo->commit();

        echo "<script>
                alert('পেমেন্ট সফল হয়েছে! আপনার অ্যাকাউন্টটি এখন সক্রিয়। দয়া করে লগইন করুন।');
                window.location.href='../login.php';
              </script>";
    } catch (Exception $e) {
        $pdo->rollBack();
        die("পেমেন্ট আপডেট করতে সমস্যা হয়েছে: " . $e->getMessage());
    }
}