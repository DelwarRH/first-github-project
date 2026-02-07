<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once '../config/db.php';

// রোল চেক
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['school', 'teacher', 'operator'])) {
    header("Location: ../login.php"); exit();
}

$school_id = $_SESSION['school_id'];
$stmt_set = $pdo->prepare("SELECT bg_image, school_logo FROM schools WHERE user_id = ?");
$stmt_set->execute([$school_id]);
$settings = $stmt_set->fetch();
$bg_url = !empty($settings['bg_image']) ? '../'.$settings['bg_image'] : '../uploads/bg.jpg';
$logo_url = !empty($settings['school_logo']) ? '../'.$settings['school_logo'] : '../uploads/logo.jpg';
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #BFBFBF; font-family: 'Hind Siliguri', sans-serif; font-size: 12px; background-image: url('<?php echo $bg_url; ?>'); background-size: cover; background-repeat: no-repeat; background-attachment: fixed; background-position: center; margin: 0; }
        .dashboard-content { background-color: rgba(255, 255, 255, 0.94); border-radius: 8px; padding: 20px; margin-top: 15px; margin-bottom: 30px; box-shadow: 0 0 20px rgba(0,0,0,0.4); }
        .sms-header { background-color: #E1E1E1; padding: 12px; display: flex; justify-content: center; align-items: center; border-bottom: 2px solid #ccc; position: relative; }
        .school-title { color: red; font-size: 26px; font-weight: 800; text-transform: uppercase; text-shadow: 1px 1px 0px white; }
        .sms-navbar { background-color: #008000; padding: 0; position: sticky; top: 0; z-index: 1000; }
        .sms-navbar .nav-link { color: white !important; font-weight: bold; padding: 11px 18px !important; border-right: 1px solid rgba(255,255,255,0.2); font-size: 13px; }
        .sms-navbar .nav-link:hover { background-color: #005000; color: #FFD700 !important; }
        .dropdown:hover > .dropdown-menu { display: block; margin-top: 0; }
        .dropdown-menu { background-color: #008000; border: none; border-radius: 0; padding: 0; }
        .dropdown-item { color: white; font-size: 12px; font-weight: bold; padding: 10px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .dropdown-item:hover { background-color: #005000; color: #FFD700; }
        .table-erp th { background-color: #8B4513; color: white; text-align: center; border: 1px solid #fff; padding: 6px; }
        .table-erp td { text-align: center; border: 1px solid #999; padding: 6px; font-weight: bold; background: #fff; }
    </style>
</head>
<body>
<div class="sms-header">
    <img src="<?php echo $logo_url; ?>" height="60" class="rounded-circle me-3 border shadow-sm"> 
    <span class="school-title"><?php echo strtoupper($_SESSION['user_name']); ?></span>
    <div style="position: absolute; right: 20px; top: 15px; font-size: 11px; background: #fff; padding: 5px 12px; border: 1px solid #999; border-radius: 4px;">
        <b>ইউজার:</b> <?php echo strtoupper($_SESSION['user_role']); ?><br><b>তারিখ:</b> <?php echo date("d-M-Y"); ?>
    </div>
</div>
<!-- এখানে ড্যাশবোর্ড মেনুবার থাকবে (যা আপনি আগের মেসেজে দিয়েছিলেন) -->
<nav class="navbar navbar-expand-lg sms-navbar">
    <div class="container-fluid">
        <div class="collapse navbar-collapse justify-content-center">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#">View</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="view_staff_teacher.php?role=teacher">Teacher</a></li>
                        <li><a class="dropdown-item" href="view_staff_teacher.php?role=staff">Staff</a></li>
                        <li><a class="dropdown-item" href="view_students.php">Student</a></li>
                        <li><a class="dropdown-item" href="fees_collection.php">Collection</a></li>
                    </ul>
                </li>
                <!-- বাকি মেনুগুলো একইভাবে যুক্ত করুন... -->
                 <li class="nav-item"><a class="nav-link bg-danger text-white px-3" href="../auth/logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="container-fluid"><div class="container dashboard-content">