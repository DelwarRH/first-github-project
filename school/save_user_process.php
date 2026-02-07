<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $subject = $_POST['subject'];
    $school_id = $_SESSION['school_id'];
    
    // ফোল্ডার চেক এবং তৈরি
    $target_dir = "../uploads/users/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $image_path = 'uploads/users/default.png';
    if(!empty($_FILES['image']['name'])){
        $file_ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $new_name = time().'_'.uniqid().'.'.$file_ext;
        $image_path = 'uploads/users/'.$new_name;
        
        move_uploaded_file($_FILES['image']['tmp_name'], '../'.$image_path);
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, image, subject, school_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'active')");
        $stmt->execute([$name, $email, $password, $role, $image_path, $subject, $school_id]);
        
        echo "<script>alert('সফলভাবে যুক্ত হয়েছে!'); window.location.href='view_staff_teacher.php?role=$role';</script>";
    } catch (Exception $e) {
        die("ডাটাবেজ এরর: " . $e->getMessage());
    }
}