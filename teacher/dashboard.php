<?php 
session_start();
// চেক: রোল শুধুমাত্র 'teacher' হতে হবে
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
    header("Location: ../login.php");
    exit();
}
include '../includes/header.php'; 
?>

<div class="container mt-5">
    <div class="p-4 bg-white shadow-sm rounded-4 border-start border-4 border-primary mb-4">
        <h4 class="fw-bold">স্বাগতম, <?php echo $_SESSION['user_name']; ?>!</h4>
        <p class="text-muted mb-0">শিক্ষক প্যানেল | প্রতিষ্ঠান আইডি: <?php echo $_SESSION['school_id']; ?></p>
    </div>

    <div class="row g-4">
        <!-- রেজাল্ট এন্ট্রি কার্ড -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-3 h-100">
                <div class="card-body">
                    <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:60px; height:60px;">
                        <i class="fa fa-file-invoice-dollar fs-4"></i>
                    </div>
                    <h5 class="fw-bold">নম্বর পত্র (Mark Sheet)</h5>
                    <p class="small text-muted">শিক্ষার্থীদের পরীক্ষার নম্বর ইনপুট দিন</p>
                    <a href="add_result.php" class="btn btn-success rounded-pill px-4">এন্ট্রি করুন</a>
                </div>
            </div>
        </div>

        <!-- রুটিন কার্ড -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-3 h-100">
                <div class="card-body">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:60px; height:60px;">
                        <i class="fa fa-calendar-alt fs-4"></i>
                    </div>
                    <h5 class="fw-bold">ক্লাস রুটিন</h5>
                    <p class="small text-muted">আপনার ক্লাস রুটিন দেখুন বা আপডেট করুন</p>
                    <a href="routine.php" class="btn btn-primary rounded-pill px-4">দেখুন</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>