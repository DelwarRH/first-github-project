<?php
require_once 'config/db.php';

$email = "uno@tala.com";
$new_password = "123456";
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

try {
    // ১. আগের ভুল ডাটা মুছে ফেলা
    $pdo->prepare("DELETE FROM users WHERE email = ?")->execute([$email]);

    // ২. নতুন করে ইউএনও তৈরি করা
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, location_id, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        'Upazila Nirbahi Officer',
        $email,
        $hashed_password,
        'uno',
        3, // তালা উপজেলার আইডি
        'active'
    ]);

    echo "<h2 style='color:green;'>সাফল্য! ইউএনও অ্যাকাউন্টটি রিসেট করা হয়েছে।</h2>";
    echo "<p>এখন লগইন করুন:<br>ইমেইল: <b>uno@tala.com</b><br>পাসওয়ার্ড: <b>123456</b></p>";
    echo "<a href='login.php'>লগইন পেজে যান</a>";

} catch (Exception $e) {
    echo "<h2 style='color:red;'>ত্রুটি: " . $e->getMessage() . "</h2>";
}
?>