<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND status = 'active'");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role']; // school (Principal), teacher, operator
        $_SESSION['location_id'] = $user['location_id'];
        $_SESSION['school_id'] = ($user['role'] == 'school') ? $user['id'] : $user['school_id'];

        // রোল অনুযায়ী রিডাইরেক্ট
        if ($user['role'] == 'school') {
            header("Location: ../school/admin_dashboard.php");
        } elseif ($user['role'] == 'teacher') {
            header("Location: ../school/teacher_dashboard.php");
        } elseif ($user['role'] == 'operator') {
            header("Location: ../school/operator_dashboard.php");
        } elseif (in_array($user['role'], ['uno', 'dc'])) {
            header("Location: ../admin/dashboard.php");
        }
        exit();
    } else {
        echo "<script>alert('ভুল ইমেইল বা পাসওয়ার্ড!'); window.location.href='../login.php';</script>";
    }
}