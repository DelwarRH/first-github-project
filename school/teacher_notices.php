<?php 
session_start();
require_once '../config/db.php'; 
if (!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit(); }

$school_id = $_SESSION['school_id'];
$location_id = $_SESSION['location_id'];
include '../includes/header.php'; 
?>

<div class="container py-4">
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-info text-white">
        <h4 class="fw-bold m-0"><i class="fa fa-bell me-2"></i> নোটিশ বোর্ড</h4>
    </div>

    <div class="row g-3">
        <?php
        // কলাম না থাকলে এরর এড়াতে চেক
        $stmt = $pdo->prepare("SELECT * FROM notices WHERE location_id = ? OR school_id = ? ORDER BY date DESC");
        $stmt->execute([$location_id, $school_id]);
        $notices = $stmt->fetchAll();

        if($notices):
            foreach($notices as $n):
        ?>
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-3 border-start border-4 border-info">
                <h5 class="fw-bold text-dark"><?php echo $n['title']; ?></h5>
                <p class="text-secondary mb-0"><?php echo nl2br($n['message']); ?></p>
                <small class="text-muted mt-2 d-block"><?php echo date('d M, Y', strtotime($n['date'])); ?></small>
            </div>
        <?php endforeach; else: ?>
            <div class="text-center py-5 text-muted">বর্তমানে কোনো নোটিশ নেই।</div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>