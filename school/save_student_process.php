<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $roll = trim($_POST['roll']);
    $class = trim($_POST['class']);
    $section = $_POST['section'] ?? 'A';
    $father = trim($_POST['father']);
    $religion = $_POST['religion'] ?? 'Islam';
    $gender = $_POST['gender'];
    $phone = trim($_POST['phone']);
    $school_id = $_SESSION['school_id'];

    // শিক্ষার্থীর জন্য ইউনিক ইমেইল আইডি
    $student_email = $roll . "_" . $class . "_" . $school_id . "@astha.com";

    try {
        $pdo->beginTransaction();

        // ১. users টেবিলে এন্ট্রি (password কলামে NULL পাঠানো হচ্ছে)
        $stmt_user = $pdo->prepare("INSERT INTO users (name, email, password, role, school_id, status) VALUES (?, ?, NULL, 'student', ?, 'active')");
        $stmt_user->execute([$name, $student_email, $school_id]);
        $new_user_id = $pdo->lastInsertId();

        // ২. ছবি আপলোড
        $target_dir = "../uploads/students/";
        if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }

        $photo_path = 'uploads/students/default.png';
        if(!empty($_FILES['photo']['name'])){
            $file_ext = pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION);
            $photo_path = 'uploads/students/'.time().'_'.uniqid().'.'.$file_ext;
            move_uploaded_file($_FILES['photo']['tmp_name'], '../'.$photo_path);
        }

        // ৩. students টেবিলে এন্ট্রি (user_id সহ)
        $sql = "INSERT INTO students (user_id, school_id, name, roll, class, section, father, phone, photo, religion, gender, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')";
        $stmt_stu = $pdo->prepare($sql);
        $stmt_stu->execute([$new_user_id, $school_id, $name, $roll, $class, $section, $father, $phone, $photo_path, $religion, $gender]);
        
        $pdo->commit();
        echo "<script>alert('শিক্ষার্থী সফলভাবে ভর্তি হয়েছে!'); window.location.href='view_students.php';</script>";

    } catch (Exception $e) {
        $pdo->rollBack();
        die("সিস্টেম এরর: " . $e->getMessage());
    }
}