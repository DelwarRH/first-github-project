<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $bio = trim($_POST['bio']);

    try {
        $pdo->beginTransaction();

        // ১. বায়ো আপডেট
        $pdo->prepare("UPDATE users SET bio = ? WHERE id = ?")->execute([$bio, $user_id]);

        // ২. প্রোফাইল ইমেজ আপলোড
        if (!empty($_FILES['profile_img']['name'])) {
            $target_p = "../uploads/users/";
            if (!file_exists($target_p)) { mkdir($target_p, 0777, true); }
            $p_name = time() . "_p_" . $_FILES['profile_img']['name'];
            move_uploaded_file($_FILES['profile_img']['tmp_name'], $target_p . $p_name);
            $pdo->prepare("UPDATE users SET image = ? WHERE id = ?")->execute(["uploads/users/" . $p_name, $user_id]);
        }

        // ৩. কভার ফটো আপলোড (Fix)
        if (!empty($_FILES['cover_img']['name'])) {
            $target_c = "../uploads/covers/";
            if (!file_exists($target_c)) { mkdir($target_c, 0777, true); }
            $c_name = time() . "_c_" . $_FILES['cover_img']['name'];
            if(move_uploaded_file($_FILES['cover_img']['tmp_name'], $target_c . $c_name)) {
                $pdo->prepare("UPDATE users SET cover_photo = ? WHERE id = ?")->execute(["uploads/covers/" . $c_name, $user_id]);
            }
        }

        $pdo->commit();
        echo "<script>alert('প্রোফাইল ও কভার ফটো সফলভাবে আপডেট হয়েছে!'); window.location.href='dashboard.php';</script>";
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error: " . $e->getMessage());
    }
}