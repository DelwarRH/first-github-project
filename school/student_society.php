<?php 
session_start();
require_once '../config/db.php'; 
include '../includes/header.php'; 
$school_id = $_SESSION['school_id'];
?>

<div class="container py-4">
    <div class="bg-white p-4 rounded-4 shadow-sm mb-4 border-bottom border-4 border-success">
        <h4 class="fw-bold text-success m-0"><i class="fa fa-users me-2"></i> শিক্ষার্থী সমাজ (Student Community)</h4>
        <p class="text-muted small m-0">আপনার প্রতিষ্ঠানের সকল নিবন্ধিত শিক্ষার্থী</p>
    </div>

    <div class="row g-3">
        <?php
        $stmt = $pdo->prepare("SELECT * FROM students WHERE school_id = ? ORDER BY class ASC");
        $stmt->execute([$school_id]);
        while($s = $stmt->fetch()):
            $s_img = $s['photo'] ? '../'.$s['photo'] : 'https://i.pravatar.cc/150?u='.$s['id'];
        ?>
        <div class="col-6 col-md-3 col-lg-2">
            <div class="card h-100 border-0 shadow-sm rounded-4 text-center p-3 hover-shadow">
                <img src="<?php echo $s_img; ?>" class="rounded-circle mx-auto mb-2 border p-1" width="70" height="70" style="object-fit:cover;">
                <h6 class="fw-bold m-0 small"><?php echo $s['name']; ?></h6>
                <div class="text-muted" style="font-size: 10px;">ID: <?php echo $s['roll']; ?> | Class: <?php echo $s['class']; ?></div>
                <button onclick="alert('ইনবক্সে মেসেজ পাঠান')" class="btn btn-primary btn-xs mt-2 rounded-pill py-0 px-3" style="font-size: 10px;">Message</button>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>