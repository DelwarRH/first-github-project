<?php
session_start();
require_once '../config/db.php';

if (isset($_GET['id']) && isset($_GET['action'])) {
    $connection_id = $_GET['id'];
    $action = $_GET['action'];
    $my_id = $_SESSION['user_id'];

    try {
        if ($action === 'accept') {
            // স্ট্যাটাস accepted করে দেওয়া
            $stmt = $pdo->prepare("UPDATE student_connections SET status = 'accepted' WHERE id = ? AND receiver_id = ?");
            $stmt->execute([$connection_id, $my_id]);
            echo "<script>alert('বন্ধুত্ব নিশ্চিত হয়েছে!'); window.location.href='dashboard.php';</script>";
        } elseif ($action === 'reject') {
            // রিকোয়েস্ট ডিলিট করে দেওয়া
            $stmt = $pdo->prepare("DELETE FROM student_connections WHERE id = ? AND receiver_id = ?");
            $stmt->execute([$connection_id, $my_id]);
            echo "<script>alert('অনুরোধ বাতিল করা হয়েছে।'); window.location.href='dashboard.php';</script>";
        }
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    header("Location: dashboard.php");
}