<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $school_id = $_SESSION['school_id'];
    $title = !empty($_POST['title']) ? trim($_POST['title']) : null;
    $content = !empty($_POST['content']) ? trim($_POST['content']) : null;
    
    $media_path = null;
    $media_type = 'text';

    // ছবি বা ভিডিও আপলোড লজিক
    if (!empty($_FILES['media']['name'])) {
        $target_dir = "../uploads/posts/";
        // ফোল্ডার না থাকলে তৈরি করবে
        if (!is_dir($target_dir)) { 
            mkdir($target_dir, 0777, true); 
        }

        $file_name = time() . "_" . basename($_FILES["media"]["name"]);
        $target_file = $target_dir . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (move_uploaded_file($_FILES["media"]["tmp_name"], $target_file)) {
            // ডাটাবেজে সেভ হবে শুধু uploads/posts/... অংশটি
            $media_path = "uploads/posts/" . $file_name;
            
            $image_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $video_exts = ['mp4', 'webm', 'mov'];
            
            if (in_array($file_type, $image_exts)) { $media_type = 'image'; }
            elseif (in_array($file_type, $video_exts)) { $media_type = 'video'; }
        }
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO posts (user_id, school_id, title, content, media_path, media_type) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $school_id, $title, $content, $media_path, $media_type]);
        
        echo "<script>alert('পোস্ট সফল হয়েছে!'); window.location.href='dashboard.php';</script>";
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}