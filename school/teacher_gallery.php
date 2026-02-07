<?php 
session_start();
require_once '../config/db.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
    header("Location: ../login.php"); exit();
}

$school_id = $_SESSION['school_id'];
$user_id = $_SESSION['user_id'];

include '../includes/header.php'; 

// ছবি ডিলিট করার লজিক (ঐচ্ছিক কিন্তু জরুরি)
if (isset($_GET['delete_id'])) {
    $del_id = $_GET['delete_id'];
    $stmt_del = $pdo->prepare("DELETE FROM gallery WHERE id = ? AND teacher_id = ?");
    $stmt_del->execute([$del_id, $user_id]);
    echo "<script>window.location.href='teacher_gallery.php';</script>";
}
?>

<div class="container py-5">
    <div class="card shadow-sm border-0 rounded-4 p-4 mb-5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white" style="background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h3 class="fw-bold m-0"><i class="fa fa-images me-2 text-warning"></i> ফটো গ্যালারি ম্যানেজমেন্ট</h3>
                <p class="small opacity-75 m-0">আপনার আপলোড করা সকল স্মৃতিময় মুহূর্তগুলো এখানে সংরক্ষিত আছে</p>
            </div>
            <div class="col-md-4 text-md-end">
                <button class="btn btn-light rounded-pill fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i class="fa fa-cloud-upload-alt me-1 text-primary"></i> ছবি আপলোড করুন
                </button>
            </div>
        </div>
    </div>

    <!-- গ্যালারি গ্রিড (ডাটাবেজ থেকে ছবি লোড করা হচ্ছে) -->
    <div class="row g-4">
        <?php 
        // ডাটাবেজ থেকে এই স্কুলের এবং এই শিক্ষকের আপলোড করা ছবি আনা হচ্ছে
        $stmt = $pdo->prepare("SELECT * FROM gallery WHERE school_id = ? ORDER BY date DESC");
        $stmt->execute([$school_id]);
        $images = $stmt->fetchAll();

        if (count($images) > 0):
            foreach($images as $row):
                // ডাটাবেজে স্টোর করা পাথ অনুযায়ী ইমেজ সোর্স সেট করা
                $img_src = "../" . $row['image_path']; 
        ?>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden gallery-card h-100">
                <div style="height: 250px; overflow: hidden; background: #f8f9fa;">
                    <img src="<?php echo $img_src; ?>" class="card-img-top w-100 h-100" style="object-fit: cover; transition: 0.5s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                </div>
                <div class="card-body">
                    <h6 class="fw-bold mb-1 text-dark"><?php echo $row['title']; ?></h6>
                    <small class="text-muted"><i class="fa fa-calendar-day me-1"></i> <?php echo date('d M, Y', strtotime($row['date'])); ?></small>
                </div>
                <div class="card-footer bg-white border-0 d-flex justify-content-between pb-3">
                    <button class="btn btn-sm btn-light text-primary border rounded-pill px-3"><i class="fa fa-thumbs-up me-1"></i> লাইক</button>
                    <a href="?delete_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-light text-danger border rounded-pill px-3" onclick="return confirm('আপনি কি নিশ্চিতভাবে এই ছবিটি মুছে ফেলতে চান?')">
                        <i class="fa fa-trash"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php 
            endforeach; 
        else: 
        ?>
        <!-- ছবি না থাকলে এই মেসেজ দেখাবে -->
        <div class="col-12 text-center py-5">
            <div class="opacity-25 mb-3"><i class="fa fa-images fa-5x"></i></div>
            <h5 class="text-muted">গ্যালারীতে বর্তমানে কোনো ছবি নেই।</h5>
            <p class="small text-secondary">উপরের বাটনে ক্লিক করে প্রথম ছবি আপলোড করুন।</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- আপলোড মোডাল (আগের মতোই থাকবে) -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">নতুন ছবি যোগ করুন</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="save_gallery.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="small fw-bold text-secondary">অ্যালবাম টাইটেল</label>
                        <input type="text" name="title" class="form-control shadow-sm" placeholder="যেমন: বার্ষিক ক্রীড়া প্রতিযোগিতা" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold text-secondary">ছবি নির্বাচন করুন</label>
                        <input type="file" name="image" class="form-control shadow-sm" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 pt-0">
                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2 rounded-3 shadow">আপলোড নিশ্চিত করুন</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .gallery-card { transition: 0.3s ease-in-out; }
    .gallery-card:hover { box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important; }
</style>

<?php include '../includes/footer.php'; ?>