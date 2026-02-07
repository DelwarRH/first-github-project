<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['marks'])) {
    $school_id = $_SESSION['school_id'];
    $class = $_POST['class'];
    $exam = $_POST['exam'];
    $subject = $_POST['subject'];
    $added_by = $_SESSION['user_name'];

    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO results (school_id, student_roll, class_name, exam_term, subject, marks, year, added_by) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?) 
                               ON DUPLICATE KEY UPDATE marks = VALUES(marks)");

        foreach ($_POST['marks'] as $roll => $mark) {
            $stmt->execute([$school_id, $roll, $class, $exam, $subject, $mark, date("Y"), $added_by]);
        }

        $pdo->commit();
        echo "<script>alert('নম্বর সফলভাবে সংরক্ষিত হয়েছে!'); window.location.href='add_result.php';</script>";
    } catch (Exception $e) {
        $pdo->rollBack();
        die("ভুল হয়েছে: " . $e->getMessage());
    }
}