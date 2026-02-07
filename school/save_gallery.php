<?php
session_start();
require_once '../config/db.php';

// ১. চেক করা ইউজার লগইন আছে কি না
if (!isset($_SESSION['user_id'])) {
    die("অ্যাক্সেস ডিনাইড!");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $teacher_id = $_SESSION['user_id'];
    $school_id = $_SESSION['school_id'];
    $title = trim($_POST['title']);

    // ২. ফোল্ডার তৈরির লজিক
    $target_dir = "../uploads/gallery/";
    if (!file_exists($target_dir)) { 
        mkdir($target_dir, 0777, true); 
    }

    // ৩. ইউনিক ফাইলের নাম তৈরি
    $file_ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
    $file_name = time() . "_" . uniqid() . "." . $file_ext;
    $target_file = $target_dir . $file_name;

    // ৪. ফাইল আপলোড প্রসেস
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $db_path = "uploads/gallery/" . $file_name; // ডাটাবেজের জন্য পাথ
        
        try {
            $stmt = $pdo->prepare("INSERT INTO gallery (school_id, teacher_id, title, image_path) VALUES (?, ?, ?, ?)");
            $stmt->execute([$school_id, $teacher_id, $title, $db_path]);
            
            echo "<script>alert('ছবি সফলভাবে আপলোড হয়েছে!'); window.location.href='teacher_gallery.php';</script>";
        } catch (Exception $e) {
            die("ডাটাবেজ এরর: " . $e->getMessage());
        }
    } else {
        echo "<script>alert('দুঃখিত, ছবি আপলোড হতে সমস্যা হয়েছে।'); window.location.href='teacher_gallery.php';</script>";
    }
} else {
    header("Location: teacher_gallery.php");
}
?>