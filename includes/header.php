<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>আস্থা - ডিজিটাল শিক্ষা ব্যবস্থাপনা</title>
    <!-- Fonts & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary-color: #008000; --secondary-color: #004d00; --accent-color: #ffcc00; }
        body { font-family: 'Hind Siliguri', sans-serif; background-color: #f0f2f5; display: flex; flex-direction: column; min-height: 100vh; }
        .main-wrapper { flex: 1; }
        
        /* Global Nav (Only for non-teacher pages) */
        .global-nav { background: var(--primary-color) !important; padding: 0; }
        .global-nav .nav-link { color: white !important; font-weight: 600; font-size: 14px; padding: 12px 15px !important; border-right: 1px solid rgba(255,255,255,0.1); }
        .global-nav .nav-link:hover { background: var(--secondary-color); color: var(--accent-color) !important; }
    </style>
</head>
<body>

<?php 
$current_page = basename($_SERVER['PHP_SELF']); 
// যদি শিক্ষক ড্যাশবোর্ড না হয়, তবেই মূল মেনু দেখাবে
if(isset($_SESSION['user_id']) && $current_page !== 'teacher_dashboard.php'): 
?>
    <div class="bg-white border-bottom py-2 shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <img src="../uploads/logo.jpg" width="45">
            <h1 class="h4 fw-bold text-danger m-0 uppercase"><?php echo $_SESSION['user_name']; ?></h1>
            <div class="small fw-bold border p-1 bg-light rounded">User: <?php echo strtoupper($_SESSION['user_role']); ?></div>
        </div>
    </div>
    <nav class="navbar navbar-expand-lg global-nav sticky-top">
        <div class="container justify-content-center">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">ড্যাশবোর্ড</a></li>
                <li class="nav-item"><a class="nav-link" href="view_students.php">শিক্ষার্থী</a></li>
                <li class="nav-item"><a class="nav-link" href="attendance.php">হাজিরা</a></li>
                <li class="nav-item"><a class="nav-link" href="fees_collection.php">ফি আদায়</a></li>
                <li class="nav-item"><a class="nav-link bg-danger text-white" href="../auth/logout.php">লগআউট</a></li>
            </ul>
        </div>
    </nav>
<?php endif; ?>

<div class="main-wrapper">