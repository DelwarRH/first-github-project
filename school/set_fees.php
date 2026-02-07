<?php 
include 'layout_header.php'; 
if ($_SESSION['user_role'] !== 'school') { echo "<script>alert('অ্যাক্সেস ডিনাইড!'); window.location.href='dashboard.php';</script>"; exit(); }

if (isset($_POST['save_fee'])) {
    $class = $_POST['class_name'];
    $amount = $_POST['amount'];

    // আগে থেকে ডাটা থাকলে আপডেট হবে, না থাকলে ইনসার্ট
    $stmt = $pdo->prepare("INSERT INTO fee_settings (school_id, class_name, fee_amount) 
                           VALUES (?, ?, ?) 
                           ON DUPLICATE KEY UPDATE fee_amount = VALUES(fee_amount)");
    $stmt->execute([$school_id, $class, $amount]);
    echo "<div class='alert alert-success'>সফলভাবে ফি ধার্য করা হয়েছে!</div>";
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-dark text-white p-3"><h5 class="m-0">শ্রেণি ভিত্তিক ফি ধার্য করুন</h5></div>
            <div class="card-body p-4">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="fw-bold small">শ্রেণি নির্বাচন করুন</label>
                        <select name="class_name" class="form-select" required>
                            <option value="Six">Six</option><option value="Seven">Seven</option>
                            <option value="Eight">Eight</option><option value="Nine">Nine</option>
                            <option value="Ten">Ten</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold small">মাসিক ফি'র পরিমাণ (৳)</label>
                        <input type="number" name="amount" class="form-control" placeholder="৫০০" required>
                    </div>
                    <button type="submit" name="save_fee" class="btn btn-success w-100 fw-bold">ফি সেট করুন</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'layout_footer.php'; ?>