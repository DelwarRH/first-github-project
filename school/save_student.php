<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SESSION['user_role'] == 'school') {
    $school_id = $_SESSION['user_id'];
    $name = $_POST['student_name'];
    $father = $_POST['father_name'];
    $class = $_POST['class'];
    $roll = $_POST['roll_no'];
    $gender = $_POST['gender'];
    $contact = $_POST['contact_no'];

    try {
        $stmt = $pdo->prepare("INSERT INTO students (school_id, student_name, class, roll_no, gender, father_name, contact_no) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$school_id, $name, $class, $roll, $gender, $father, $contact]);

        echo "<script>alert('শিক্ষার্থীর তথ্য সফলভাবে সংরক্ষিত হয়েছে!'); window.location.href='dashboard.php';</script>";
    } catch (Exception $e) {
        die("ভুল হয়েছে: " . $e->getMessage());
    }
}