<?php
session_start();
require_once '../config/db.php';
if ($_SESSION['user_role'] !== 'operator') { header("Location: ../login.php"); exit(); }

$school_id = $_SESSION['school_id'];
// বিগত ৭ দিনের কালেকশন রিপোর্ট
$stmt = $pdo->prepare("SELECT SUM(amount) FROM student_payments WHERE school_id = ? AND date >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
$stmt->execute([$school_id]);
$last_7_days = $stmt->fetchColumn() ?: 0;

include '../includes/header.php';
?>

<div class="container py-4">
    <div class="row g-4">
        <!-- ফিন্যান্সিয়াল রিপোর্ট বক্স -->
        <div class="col-md-12">
            <div class="card bg-dark text-white p-4 rounded-4 shadow">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="fw-bold mb-0">অপারেটর কন্ট্রোল প্যানেল</h4>
                        <p class="small opacity-75">বিগত ৭ দিনের মোট আদায়কৃত ফি</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <h2 class="fw-bold text-warning">৳ <?php echo number_format($last_7_days, 2); ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- কুইক মেনু কার্ডস -->
        <?php 
        $menus = [
            ['title' => 'ছাত্রী ভর্তি', 'icon' => 'fa-user-plus', 'link' => 'add_student.php'],
            ['title' => 'মাসিক ফি গ্রহণ', 'icon' => 'fa-money-bill-wave', 'link' => 'fees_collection.php'],
            ['title' => 'এডমিট কার্ড', 'icon' => 'fa-id-card', 'link' => 'admit_card.php'],
            ['title' => 'গ্যালারী আপডেট', 'icon' => 'fa-images', 'link' => 'gallery.php'],
            ['title' => 'বকেয়া তালিকা', 'icon' => 'fa-file-invoice-dollar', 'link' => 'due_report.php'],
        ];
        foreach($menus as $m): ?>
        <div class="col-md-3">
            <a href="<?php echo $m['link']; ?>" class="text-decoration-none">
                <div class="card shadow-sm border-0 rounded-4 text-center p-4 hover-shadow">
                    <i class="fas <?php echo $m['icon']; ?> fa-2x text-primary mb-2"></i>
                    <h6 class="fw-bold text-dark mb-0"><?php echo $m['title']; ?></h6>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>