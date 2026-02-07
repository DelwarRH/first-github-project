<?php 
session_start();
require_once '../config/db.php'; 
include '../includes/header.php'; 

$days = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'];
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h3 class="fw-black text-primary"><i class="fa fa-calendar-alt"></i> আমার ক্লাস রুটিন</h3>
        <p class="text-muted">আপনার সাপ্তাহিক ক্লাসের সময়সূচী এখানে দেখুন</p>
    </div>

    <div class="row g-4">
        <?php foreach($days as $day): ?>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-success text-white py-3 text-center fw-bold">
                    <?php echo $day; ?>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <!-- ডামি ডাটা, পরে ডাটাবেজ থেকে আসবে -->
                        <li class="list-group-item d-flex justify-content-between p-3 border-0 border-bottom">
                            <div><i class="fa fa-clock text-primary me-2"></i> 10:00 AM</div>
                            <div class="fw-bold">Class 10 (History)</div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between p-3 border-0 border-bottom">
                            <div><i class="fa fa-clock text-primary me-2"></i> 11:30 AM</div>
                            <div class="fw-bold">Class 8 (History)</div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between p-3 text-center text-muted small bg-light">
                            অবসর সময়
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>