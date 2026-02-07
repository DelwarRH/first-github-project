<?php
session_start();
require_once '../config/db.php';

if(isset($_POST['send_msg'])) {
    $teacher_id = $_SESSION['user_id'];
    $school_id  = $_SESSION['school_id'];
    $student_id = $_POST['receiver_id'];
    $message    = trim($_POST['message']);

    if(!empty($message)) {
        $stmt = $pdo->prepare("INSERT INTO chat_messages (school_id, sender_type, sender_id, receiver_id, message) VALUES (?, 'teacher', ?, ?, ?)");
        $stmt->execute([$school_id, $teacher_id, $student_id, $message]);
        echo "sent";
    }
}

// মেসেজ লোড করার অংশ (এটি পরবর্তী ধাপে আরও বিস্তারিত হবে)
if(isset($_GET['fetch_msg'])) {
    $teacher_id = $_SESSION['user_id'];
    $student_id = $_GET['student_id'];

    $stmt = $pdo->prepare("SELECT * FROM chat_messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY created_at ASC");
    $stmt->execute([$teacher_id, $student_id, $student_id, $teacher_id]);
    $messages = $stmt->fetchAll();

    foreach($messages as $m) {
        $align = ($m['sender_type'] == 'teacher') ? 'text-end' : 'text-start';
        $bg = ($m['sender_type'] == 'teacher') ? 'bg-primary text-white' : 'bg-white border';
        echo "<div class='$align mb-2'><span class='$bg p-2 rounded-3 shadow-sm d-inline-block' style='max-width: 80%;'>{$m['message']}</span></div>";
    }
}
?>