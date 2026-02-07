<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) { exit; }
$user_id = $_SESSION['user_id'];

// ১. লাইক হ্যান্ডেলিং
if (isset($_POST['action']) && $_POST['action'] == 'like') {
    $post_id = $_POST['post_id'];
    
    // চেক করা অলরেডি লাইক আছে কি না
    $check = $pdo->prepare("SELECT id FROM post_likes WHERE post_id = ? AND user_id = ?");
    $check->execute([$post_id, $user_id]);
    
    if ($check->rowCount() > 0) {
        $pdo->prepare("DELETE FROM post_likes WHERE post_id = ? AND user_id = ?")->execute([$post_id, $user_id]);
        echo "unliked";
    } else {
        $pdo->prepare("INSERT INTO post_likes (post_id, user_id) VALUES (?, ?)")->execute([$post_id, $user_id]);
        echo "liked";
    }
}

// ২. কমেন্ট হ্যান্ডেলিং
if (isset($_POST['action']) && $_POST['action'] == 'comment') {
    $post_id = $_POST['post_id'];
    $comment = trim($_POST['comment_text']);
    
    if (!empty($comment)) {
        $stmt = $pdo->prepare("INSERT INTO post_comments (post_id, user_id, comment_text) VALUES (?, ?, ?)");
        $stmt->execute([$post_id, $user_id, $comment]);
        echo "success";
    }
}