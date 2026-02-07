<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) { exit; }

$my_id = $_SESSION['user_id'];
$school_id = $_SESSION['school_id'];

// ১. মেসেজ পাঠানো (Student to Teacher/Student)
if (isset($_POST['send_msg'])) {
    $receiver_id = $_POST['receiver_id'];
    $message = trim($_POST['message']);

    if (!empty($message)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO chat_messages (school_id, sender_type, sender_id, receiver_id, message) VALUES (?, 'student', ?, ?, ?)");
            $stmt->execute([$school_id, $my_id, $receiver_id, $message]);
            echo "success";
        } catch (Exception $e) {
            echo "error";
        }
    }
    exit;
}

// ২. মেসেজ লোড করা
if (isset($_GET['fetch_msg'])) {
    $receiver_id = $_GET['receiver_id'];

    $stmt = $pdo->prepare("SELECT * FROM chat_messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY created_at ASC");
    $stmt->execute([$my_id, $receiver_id, $receiver_id, $my_id]);
    $messages = $stmt->fetchAll();

    if ($messages) {
        foreach ($messages as $m) {
            $is_me = ($m['sender_id'] == $my_id);
            if ($is_me) {
                echo '<div class="flex justify-end mb-3">
                        <div class="bg-blue-600 text-white p-3 px-4 rounded-[22px] rounded-tr-none shadow-sm max-w-[80%] text-[15px] leading-snug">
                            '.htmlspecialchars($m['message']).'
                        </div>
                      </div>';
            } else {
                echo '<div class="flex justify-start mb-3">
                        <div class="bg-white text-slate-800 p-3 px-4 rounded-[22px] rounded-tl-none shadow-md max-w-[80%] text-[15px] leading-snug border border-slate-100">
                            '.htmlspecialchars($m['message']).'
                        </div>
                      </div>';
            }
        }
    } else {
        echo '<div class="text-center text-slate-400 text-xs mt-10">পড়াশোনা নিয়ে আলোচনা শুরু করুন...</div>';
    }
    exit;
}
?>