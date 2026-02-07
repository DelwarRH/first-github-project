<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) { exit; }
$my_id = $_SESSION['user_id'];
$school_id = $_SESSION['school_id'];

// ১. মেসেজ বা ফাইল পাঠানো
if (isset($_POST['send_msg'])) {
    $receiver_id = $_POST['receiver_id'];
    $message = trim($_POST['message'] ?? '');
    $msg_type = 'text';
    $file_path = null;

    // ফাইল আপলোড লজিক (ছবি বা পিডিএফ)
    if (!empty($_FILES['chat_file']['name'])) {
        $target_dir = "../uploads/chat/";
        if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }

        $file_name = time() . "_" . $_FILES["chat_file"]["name"];
        $target_file = $target_dir . $file_name;
        $ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (move_uploaded_file($_FILES["chat_file"]["tmp_name"], $target_file)) {
            $file_path = "uploads/chat/" . $file_name;
            $msg_type = (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) ? 'image' : 'file';
        }
    }

    if (!empty($message) || !empty($file_path)) {
        $stmt = $pdo->prepare("INSERT INTO chat_messages (school_id, sender_type, sender_id, receiver_id, message, file_path, msg_type) VALUES (?, 'teacher', ?, ?, ?, ?, ?)");
        $stmt->execute([$school_id, $my_id, $receiver_id, $message, $file_path, $msg_type]);
        echo "success";
    }
    exit;
}

// ২. মেসেজ লোড করা (ডিজাইন ফিক্সড)
if (isset($_GET['fetch_msg'])) {
    $chat_id = $_GET['receiver_id'];
    $stmt = $pdo->prepare("SELECT * FROM chat_messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY created_at ASC");
    $stmt->execute([$my_id, $chat_id, $chat_id, $my_id]);
    $messages = $stmt->fetchAll();

    foreach ($messages as $m) {
        $is_me = ($m['sender_id'] == $my_id);
        $align = $is_me ? 'justify-end' : 'justify-start';
        $bg = $is_me ? 'bg-blue-600 text-white rounded-tr-none' : 'bg-white text-slate-800 rounded-tl-none border border-slate-200';

        echo '<div class="flex '.$align.' mb-3">';
        echo '<div class="'.$bg.' p-3 px-4 rounded-[20px] shadow-sm max-w-[80%]">';
        
        // যদি ছবি হয়
        if ($m['msg_type'] == 'image') {
            echo '<img src="../'.$m['file_path'].'" class="rounded-lg mb-2 max-w-full cursor-pointer" onclick="window.open(this.src)">';
        } 
        // যদি পিডিএফ বা অন্য ফাইল হয়
        elseif ($m['msg_type'] == 'file') {
            echo '<a href="../'.$m['file_path'].'" target="_blank" class="flex items-center gap-2 text-inherit no-underline">
                    <i class="fa fa-file-pdf text-xl"></i> <span class="text-xs font-bold">ডাউনলোড করুন</span>
                  </a>';
        }

        if (!empty($m['message'])) {
            echo '<p class="m-0 text-[14px]">'.htmlspecialchars($m['message']).'</p>';
        }
        
        echo '</div></div>';
    }
    exit;
}