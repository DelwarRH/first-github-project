<?php 
session_start();
require_once '../config/db.php'; 

// স্কুল আইডি সেশন থেকে অথবা ইউআরএল থেকে নেওয়া
$school_id = $_SESSION['school_id'] ?? $_GET['school_id'] ?? null;
if (!$school_id) { die("প্রতিষ্ঠানের আইডি পাওয়া যায়নি।"); }

include '../includes/header.php'; 
?>

<div class="container mt-5 mb-5">
    <div class="text-center mb-5">
        <h2 class="text-success fw-bold">ফটো গ্যালারি</h2>
        <p class="text-muted"><?php echo $_SESSION['user_name'] ?? 'শিক্ষা প্রতিষ্ঠান'; ?></p>
        <hr class="w-25 mx-auto border-success">
    </div>

    <div class="row">
        <?php
        // শুধুমাত্র এই স্কুলের ছবি আনা হচ্ছে
        $stmt = $pdo->prepare("SELECT * FROM gallery WHERE school_id = ? AND show_on_album = 1 ORDER BY id DESC");
        $stmt->execute([$school_id]);
        $images = $stmt->fetchAll();
        
        if (count($images) > 0) {
            foreach($images as $row) {
        ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100 border-0 rounded-4 overflow-hidden">
                    <div style="height: 250px; overflow: hidden;">
                        <img src="../<?php echo $row['image_path']; ?>" class="card-img-top w-100 h-100" style="object-fit: cover; transition: transform 0.4s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                    </div>
                    <div class="card-body text-center bg-white">
                        <p class="card-text fw-bold text-dark mb-1"><?php echo $row['title']; ?></p>
                        <small class="text-muted"><i class="fa fa-calendar-alt"></i> <?php echo date("d M Y", strtotime($row['date'])); ?></small>
                    </div>
                </div>
            </div>
        <?php 
            }
        } else {
            echo '<div class="alert alert-warning text-center w-100 fw-bold">গ্যালারিতে বর্তমানে কোনো ছবি নেই।</div>';
        }
        ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>