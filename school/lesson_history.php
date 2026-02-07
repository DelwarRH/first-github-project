<?php 
session_start();
require_once '../config/db.php'; 
include '../includes/header.php'; 
$teacher_id = $_SESSION['user_id'];
?>

<div class="container py-4">
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-primary text-white">
        <h4 class="fw-bold m-0"><i class="fa fa-history me-2"></i> লেসন আর্কাইভ (তারিখ ভিত্তিক)</h4>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?php
            $stmt = $pdo->prepare("SELECT * FROM lessons WHERE teacher_id = ? ORDER BY date DESC");
            $stmt->execute([$teacher_id]);
            $history = $stmt->fetchAll();

            if($history):
                foreach($history as $row):
            ?>
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-3 border-start border-4 border-primary">
                <div class="d-flex justify-content-between">
                    <h5 class="fw-bold text-dark"><?php echo $row['title']; ?></h5>
                    <span class="badge bg-light text-dark border"><?php echo date('d M, Y', strtotime($row['date'])); ?></span>
                </div>
                <p class="text-muted small">শ্রেণি: <?php echo $row['class']; ?> | বিষয়: <?php echo $row['subject']; ?></p>
                <div class="text-secondary"><?php echo nl2br($row['content']); ?></div>
            </div>
            <?php endforeach; else: ?>
                <div class="text-center py-5 text-muted">কোনো লেসন রেকর্ড পাওয়া যায়নি।</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>