<?php 
session_start();
require_once '../config/db.php'; 

// ১. সিকিউরিটি ও রোল চেক
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'school') {
    header("Location: ../login.php");
    exit();
}

$school_id = $_SESSION['user_id'];

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // শুধুমাত্র নিজের স্কুলের আবেদন দেখা যাবে (Multi-tenant check)
    $stmt = $pdo->prepare("SELECT * FROM admissions WHERE id = ? AND school_id = ?");
    $stmt->execute([$id, $school_id]);
    $row = $stmt->fetch();
    
    if (!$row) { die("আবেদনটি খুঁজে পাওয়া যায়নি বা আপনার এক্সেস নেই।"); }
} else {
    header("Location: dashboard.php");
}
?>

<!-- আপনার আগের এইচটিএমএল এবং ডিজাইন এখানে ঠিক থাকবে -->
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>আবেদন পত্র - <?php echo $row['student_name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f4f7; font-family: 'Hind Siliguri', sans-serif; }
        .print-area { background: white; padding: 40px; border-radius: 10px; max-width: 800px; margin: 30px auto; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 1px solid #ddd; }
        @media print { .no-print { display: none; } .print-area { box-shadow: none; margin: 0; } }
    </style>
</head>
<body>
<div class="container">
    <div class="text-center mt-4 no-print">
        <button onclick="window.print()" class="btn btn-primary px-4 fw-bold"><i class="fa fa-print"></i> প্রিন্ট করুন</button>
    </div>

    <div class="print-area">
        <div class="text-center mb-4 border-bottom pb-3">
            <h2 class="fw-bold" style="color: #0D47A1;"><?php echo $_SESSION['user_name']; ?></h2>
            <p class="m-0">ডিজিটাল ডাটা ম্যানেজমেন্ট সিস্টেম - আস্থা</p>
            <h4 class="mt-3 bg-dark text-white d-inline-block px-4 py-1 rounded">ভর্তি আবেদন পত্র</h4>
        </div>
        <!-- বাকি তথ্য আপনার আগের কোড অনুযায়ী $row থেকে আসবে -->
        <table class="table table-bordered">
            <tr><td>নাম:</td><td><b><?php echo $row['student_name']; ?></b></td></tr>
            <tr><td>পিতা:</td><td><?php echo $row['father_name']; ?></td></tr>
            <tr><td>শ্রেণি:</td><td><?php echo $row['desired_class']; ?></td></tr>
            <tr><td>মোবাইল:</td><td><?php echo $row['phone']; ?></td></tr>
        </table>
    </div>
</div>
</body>
</html>