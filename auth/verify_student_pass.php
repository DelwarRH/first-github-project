<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['temp_user_id'])) {
    $user_id = $_SESSION['temp_user_id'];
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // সেশন সেট করা
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = 'student';
            $_SESSION['school_id'] = $user['school_id'];

            unset($_SESSION['temp_user_id']);
            header("Location: ../student/dashboard.php");
        } else {
            echo "<script>alert('ভুল পাসওয়ার্ড!'); window.location.href='../student_pass_input.php';</script>";
        }
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}