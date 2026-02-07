<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'school') {
    header("Location: ../login.php"); exit();
}

$school_id = $_SESSION['user_id'];

// পরিসংখ্যান সংগ্রহ
$stmt = $pdo->prepare("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN gender = 'Male' THEN 1 ELSE 0 END) as male,
    SUM(CASE WHEN gender = 'Female' THEN 1 ELSE 0 END) as female,
    SUM(CASE WHEN is_stipend = 1 THEN 1 ELSE 0 END) as stipend
    FROM students WHERE school_id = ?");
$stmt->execute([$school_id]);
$stats = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>শিক্ষার্থী ব্যবস্থাপনা - আস্থা</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8fafc; font-family: 'Hind Siliguri', sans-serif; }
        .app-card { background: white; border-radius: 20px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: 0.3s; }
        .app-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .stats-icon { width: 50px; height: 50px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 24px; }
    </style>
</head>
<body>

<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">শিক্ষার্থী ব্যবস্থাপনা</h2>
            <p class="text-muted">আপনার প্রতিষ্ঠানের সকল শিক্ষার্থীদের তথ্য এখানে দেখুন।</p>
        </div>
        <a href="add_student.php" class="btn btn-primary rounded-pill px-4 fw-bold">
            <i class="fas fa-plus-circle me-2"></i> নতুন শিক্ষার্থী
        </a>
    </div>

    <!-- Stats Boxes -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="app-card p-4 d-flex align-items-center">
                <div class="stats-icon bg-primary text-white me-3"><i class="fas fa-users"></i></div>
                <div><small class="text-muted fw-bold">মোট শিক্ষার্থী</small><h4 class="mb-0 fw-black"><?php echo $stats['total']; ?></h4></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="app-card p-4 d-flex align-items-center">
                <div class="stats-icon bg-info text-white me-3"><i class="fas fa-male"></i></div>
                <div><small class="text-muted fw-bold">ছাত্র</small><h4 class="mb-0 fw-black"><?php echo $stats['male']; ?></h4></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="app-card p-4 d-flex align-items-center">
                <div class="stats-icon bg-danger text-white me-3"><i class="fas fa-female"></i></div>
                <div><small class="text-muted fw-bold">ছাত্রী</small><h4 class="mb-0 fw-black"><?php echo $stats['female']; ?></h4></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="app-card p-4 d-flex align-items-center">
                <div class="stats-icon bg-success text-white me-3"><i class="fas fa-hand-holding-heart"></i></div>
                <div><small class="text-muted fw-bold">উপবৃত্তি পায়</small><h4 class="mb-0 fw-black"><?php echo $stats['stipend']; ?></h4></div>
            </div>
        </div>
    </div>

    <!-- Student Table -->
    <div class="app-card shadow-sm overflow-hidden">
        <div class="p-4 bg-white border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">শিক্ষার্থী তালিকা</h5>
            <div class="d-flex gap-2">
                <input type="text" class="form-control form-control-sm" placeholder="নাম বা রোল দিয়ে খুঁজুন...">
                <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-filter"></i></button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">রোল</th>
                        <th>ছবি</th>
                        <th>নাম</th>
                        <th>শ্রেণি ও শাখা</th>
                        <th>মোবাইল</th>
                        <th class="text-end pe-4">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->prepare("SELECT * FROM students WHERE school_id = ? ORDER BY class, roll ASC LIMIT 20");
                    $stmt->execute([$school_id]);
                    while($row = $stmt->fetch()):
                    ?>
                    <tr>
                        <td class="ps-4 fw-bold">#<?php echo $row['roll']; ?></td>
                        <td><img src="../<?php echo $row['photo'] ?: 'uploads/students/default.png'; ?>" class="rounded-circle border" width="40" height="40"></td>
                        <td><div class="fw-bold"><?php echo $row['name']; ?></div><small class="text-muted"><?php echo $row['father']; ?></small></td>
                        <td><span class="badge bg-blue-100 text-blue-700"><?php echo $row['class']; ?> - <?php echo $row['section']; ?></span></td>
                        <td><?php echo $row['phone']; ?></td>
                        <td class="text-end pe-4">
                            <a href="edit_student.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-light text-primary"><i class="fas fa-edit"></i></a>
                            <a href="id_card.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-light text-success"><i class="fas fa-id-card"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>