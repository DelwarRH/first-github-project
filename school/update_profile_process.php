<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $name = $_POST['name'];
    $subject = $_POST['subject'];
    $email = $_POST['email'];

    try {
        if (!empty($_FILES['profile_img']['name'])) {
            $target_dir = "../uploads/users/";
            $file_name = time() . "_" . $_FILES['profile_img']['name'];
            move_uploaded_file($_FILES['profile_img']['tmp_name'], $target_dir . $file_name);
            $image_path = "uploads/users/" . $file_name;
            
            $stmt = $pdo->prepare("UPDATE users SET name=?, subject=?, email=?, image=? WHERE id=?");
            $stmt->execute([$name, $subject, $email, $image_path, $user_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET name=?, subject=?, email=? WHERE id=?");
            $stmt->execute([$name, $subject, $email, $user_id]);
        }
        
        $_SESSION['user_name'] = $name; // সেশন আপডেট
        echo "<script>alert('প্রোফাইল সফলভাবে আপডেট হয়েছে!'); window.location.href='teacher_dashboard.php';</script>";
    } catch (Exception $e) { die($e->getMessage()); }
}
```