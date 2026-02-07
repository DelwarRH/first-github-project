<?php 
include 'layout_header.php'; 
if ($_SESSION['user_role'] !== 'school') { echo "<script>alert('অ্যাক্সেস ডিনাইড!'); window.location.href='dashboard.php';</script>"; exit(); }

$msg = "";
// ফি সেভ বা আপডেট করার লজিক
if (isset($_POST['save_fee'])) {
    $class = $_POST['class_name'];
    $fee_type = $_POST['fee_type'];
    $amount = $_POST['amount'];

    try {
        $stmt = $pdo->prepare("INSERT INTO fee_settings (school_id, class_name, fee_type, amount) 
                               VALUES (?, ?, ?, ?) 
                               ON DUPLICATE KEY UPDATE amount = VALUES(amount)");
        $stmt->execute([$school_id, $class, $fee_type, $amount]);
        $msg = "<div class='alert alert-success fw-bold shadow-sm'>সফলভাবে $fee_type সেট করা হয়েছে!</div>";
    } catch (Exception $e) { $msg = "<div class='alert alert-danger'>ভুল হয়েছে: ".$e->getMessage()."</div>"; }
}
?>

<div class="row g-4">
    <!-- ফি সেট করার ফরম -->
    <div class="col-md-5">
        <div class="card shadow border-0 rounded-4">
            <div class="card-header bg-dark text-white p-3"><h5 class="m-0 fw-bold"><i class="fa fa-cogs me-2"></i> ফি কনফিগারেশন</h5></div>
            <div class="card-body p-4">
                <?php echo $msg; ?>
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="fw-bold small">শ্রেণি নির্বাচন করুন</label>
                        <select name="class_name" class="form-select shadow-sm" required>
                            <option value="Six">Six</option><option value="Seven">Seven</option>
                            <option value="Eight">Eight</option><option value="Nine">Nine</option>
                            <option value="Ten">Ten</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold small">ফি এর ধরণ (Type)</label>
                        <select name="fee_type" class="form-select shadow-sm" required>
                            <option value="Monthly Fee">মাসিক বেতন</option>
                            <option value="Admission Fee">ভর্তি ফি</option>
                            <option value="Session Fee">সেশন ফি</option>
                            <option value="Exam Fee">পরীক্ষা ফি</option>
                            <option value="Coaching Fee">কোচিং ফি</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="fw-bold small">টাকার পরিমাণ (৳)</label>
                        <input type="number" name="amount" class="form-control shadow-sm" placeholder="যেমন: ৫০০" required>
                    </div>
                    <button type="submit" name="save_fee" class="btn btn-success w-100 fw-bold rounded-pill">ফি সেভ করুন</button>
                </form>
            </div>
        </div>
    </div>

    <!-- বর্তমান ফি তালিকা -->
    <div class="col-md-7">
        <div class="card shadow border-0 rounded-4">
            <div class="card-header bg-primary text-white p-3"><h5 class="m-0 fw-bold">নির্ধারিত ফি তালিকা</h5></div>
            <div class="card-body p-0">
                <table class="table table-hover text-center align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>শ্রেণি</th><th>ফি এর ধরণ</th><th>পরিমাণ</th></tr>
                    </thead>
                    <tbody>
                        <?php 
                        $stmt = $pdo->prepare("SELECT * FROM fee_settings WHERE school_id = ? ORDER BY class_name ASC");
                        $stmt->execute([$school_id]);
                        while($row = $stmt->fetch()):
                        ?>
                        <tr>
                            <td class="fw-bold"><?php echo $row['class_name']; ?></td>
                            <td><?php echo $row['fee_type']; ?></td>
                            <td class="text-danger fw-bold">৳ <?php echo number_format($row['amount'], 2); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'layout_footer.php'; ?>