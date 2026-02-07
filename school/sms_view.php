<?php 
session_start();
require_once '../config/db.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'school') {
    header("Location: ../login.php"); exit();
}

$school_id = $_SESSION['user_id'];
include '../includes/header.php'; 
?>

<div class="container mt-4 mb-5">
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 fw-bold"><i class="fa fa-list me-2"></i> বিস্তারিত এসএমএস রিপোর্ট (সকল মেসেজ)</h5>
            <a href="send_sms.php" class="btn btn-sm btn-outline-warning fw-bold">মেসেজ পাঠান</a>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0 text-center align-middle">
                <thead class="table-light">
                    <tr>
                        <th>তারিখ ও সময়</th>
                        <th>প্রাপক মোবাইল</th>
                        <th class="text-start">বার্তার মূল অংশ (Message)</th>
                        <th>প্রেরক (ইউজার)</th>
                        <th>স্ট্যাটাস</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->prepare("SELECT * FROM sms_logs WHERE school_id = ? ORDER BY id DESC");
                    $stmt->execute([$school_id]);
                    $logs = $stmt->fetchAll();

                    if (count($logs) > 0) {
                        foreach($logs as $row) {
                    ?>
                    <tr>
                        <td><?php echo date("d-m-Y | h:i A", strtotime($row['date'])); ?></td>
                        <td class="fw-bold"><?php echo $row['receiver_number']; ?></td>
                        <td class="text-start ps-3 small text-muted"><?php echo $row['message']; ?></td>
                        <td><?php echo $row['sent_by']; ?></td>
                        <td><span class="badge bg-success px-3">Sent</span></td>
                    </tr>
                    <?php } } else { echo "<tr><td colspan='5' class='py-5 text-muted'>কোনো এসএমএস তথ্য পাওয়া যায়নি।</td></tr>"; } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>