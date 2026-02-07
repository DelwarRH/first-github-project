<?php 
session_start();
require_once '../config/db.php'; 
if (!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit(); }
$school_id = $_SESSION['school_id'];
include '../includes/header.php'; 
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="fw-bold text-dark border-start border-4 border-success ps-3">সম্মানিত শিক্ষক মন্ডলী</h3>
        <span class="badge bg-success px-3 py-2 rounded-pill">মোট শিক্ষক: <?php echo $pdo->query("SELECT COUNT(*) FROM users WHERE school_id=$school_id AND role='teacher'")->fetchColumn(); ?></span>
    </div>

    <div class="row g-4">
        <?php
        $stmt = $pdo->prepare("SELECT * FROM users WHERE school_id = ? AND role = 'teacher' AND status = 'active'");
        $stmt->execute([$school_id]);
        while($row = $stmt->fetch()):
        ?>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 text-center p-3">
                <img src="../<?php echo !empty($row['image']) ? $row['image'] : 'uploads/users/default.png'; ?>" class="rounded-circle mx-auto mb-3 border p-1" style="width: 100px; height: 100px; object-fit: cover;">
                <h6 class="fw-bold m-0"><?php echo $row['name']; ?></h6>
                <p class="text-primary small fw-bold mb-1"><?php echo $row['subject']; ?></p>
                <div class="mt-2">
                    <a href="mailto:<?php echo $row['email']; ?>" class="btn btn-light btn-sm rounded-circle"><i class="fa fa-envelope text-muted"></i></a>
                    <button class="btn btn-light btn-sm rounded-circle ms-1" onclick="alert('চ্যাট ফিচার শীঘ্রই আসছে')"><i class="fa fa-comment-dots text-success"></i></button>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>