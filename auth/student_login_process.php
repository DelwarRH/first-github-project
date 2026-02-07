<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ডাটা গ্রহণ এবং বাড়তি স্পেস রিমুভ (trim)
    $school_id = $_POST['school_id'];
    $class = trim($_POST['class']);
    $roll = trim($_POST['roll']);
    $phone = trim($_POST['phone']);

    try {
        // ১. শিক্ষার্থী টেবিলে হুবহু তথ্যটি আছে কি না চেক করা
        $stmt_stu = $pdo->prepare("SELECT * FROM students WHERE school_id = ? AND class = ? AND roll = ? AND phone = ?");
        $stmt_stu->execute([$school_id, $class, $roll, $phone]);
        $student = $stmt_stu->fetch();

        if ($student) {
            $user_id = $student['user_id'];
            
            // ২. user_id লিঙ্ক করা আছে কি না দেখা
            if (empty($user_id)) {
                die("<script>alert('Error: আপনার তথ্যের সাথে ইউজার একাউন্ট লিঙ্ক করা নেই।'); window.location.href='../student_login.php';</script>");
            }

            // ৩. ইউজার টেবিল থেকে পাসওয়ার্ড স্ট্যাটাস চেক
            $stmt_user = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt_user->execute([$user_id]);
            $user = $stmt_user->fetch();

            if ($user) {
                $_SESSION['temp_user_id'] = $user['id'];
                $_SESSION['school_id'] = $school_id;
                $_SESSION['user_role'] = 'student';

                // ৪. পাসওয়ার্ড সেট করা না থাকলে (NULL বা খালি)
                if (empty($user['password'])) {
                    header("Location: ../set_password.php");
                } else {
                    // পাসওয়ার্ড সেট করা থাকলে পাসওয়ার্ড ইনপুট পেজে যাবে
                    header("Location: ../student_pass_input.php");
                }
                exit();
            } else {
                echo "<script>alert('Error: ইউজার একাউন্ট পাওয়া যায়নি।'); window.location.href='../student_login.php';</script>";
            }
        } else {
            // যদি তথ্য না মিলে তবে ডিবাগ করার জন্য নিচের অ্যালার্ট
            echo "<script>alert('ভুল তথ্য! আপনার প্রদানকৃত স্কুল, শ্রেণি, রোল বা ফোন নম্বর ডাটাবেজের সাথে মিলছে না।'); window.location.href='../student_login.php';</script>";
        }
    } catch (Exception $e) {
        die("System Error: " . $e->getMessage());
    }
}