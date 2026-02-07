<?php 
include 'layout_header.php'; 

// কাস্টম তারিখ ফিল্টার
$from_date = $_GET['from_date'] ?? date('Y-m-d', strtotime('-7 days'));
$to_date = $_GET['to_date'] ?? date('Y-m-d');

// ১. বিগত ৭ দিনের মোট আদায় (Quick Summary)
$stmt_7days = $pdo->prepare("SELECT SUM(amount) FROM student_payments WHERE school_id = ? AND date >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
$stmt_7days->execute([$school_id]);
$total_7days = $stmt_7days->fetchColumn() ?: 0;
?>

<div class="row g-3 no-print">
    <div class="col-md-4">
        <div class="p-3 bg-white border-start border-4 border-success shadow-sm rounded-3">
            <small class="text-muted fw-bold d-block uppercase">বিগত ৭ দিনের আদায়</small>
            <h3 class="fw-black m-0 text-success">৳ <?php echo number_format($total_7days, 2); ?></h3>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4 mt-4 overflow-hidden">
    <div class="card-header bg-dark text-white p-3 d-flex justify-content-between align-items-center">
        <h5 class="m-0 fw-bold"><i class="fa fa-chart-line me-2"></i> আদায়কৃত ফি প্রতিবেদন</h5>
        <button onclick="window.print()" class="btn btn-light btn-sm fw-bold no-print"><i class="fa fa-print"></i> প্রিন্ট</button>
    </div>
    <div class="card-body p-4">
        <!-- ফিল্টার ফরম -->
        <form method="GET" class="row g-2 mb-4 bg-light p-3 border rounded no-print">
            <div class="col-md-4">
                <label class="small fw-bold">হতে</label>
                <input type="date" name="from_date" class="form-control" value="<?php echo $from_date; ?>">
            </div>
            <div class="col-md-4">
                <label class="small fw-bold">পর্যন্ত</label>
                <input type="date" name="to_date" class="form-control" value="<?php echo $to_date; ?>">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100 fw-bold">রিপোর্ট দেখুন</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover text-center align-middle" style="font-size: 13px;">
                <thead class="bg-light fw-bold">
                    <tr>
                        <th>তারিখ</th>
                        <th>রোল ও শ্রেণি</th>
                        <th class="text-start ps-3">শিক্ষার্থীর নাম</th>
                        <th>ফি এর ধরণ</th>
                        <th>মাস</th>
                        <th>পরিমাণ</th>
                        <th>আদায়ককারী</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->prepare("SELECT p.*, s.name FROM student_payments p JOIN students s ON p.student_id = s.id 
                                           WHERE p.school_id = ? AND p.date BETWEEN ? AND ? ORDER BY p.date DESC");
                    $stmt->execute([$school_id, $from_date, $to_date]);
                    $grand_total = 0;
                    while($row = $stmt->fetch()):
                        $grand_total += $row['amount'];
                    ?>
                    <tr>
                        <td><?php echo date('d-m-Y', strtotime($row['date'])); ?></td>
                        <td><?php echo $row['class']; ?> - <?php echo $row['roll']; ?></td>
                        <td class="text-start ps-3 fw-bold text-dark"><?php echo $row['name']; ?></td>
                        <td><span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2"><?php echo $row['payment_type']; ?></span></td>
                        <td><?php echo $row['month']; ?></td>
                        <td class="fw-bold text-primary">৳ <?php echo number_format($row['amount'], 2); ?></td>
                        <td><small class="text-muted"><?php echo $row['received_by']; ?></small></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot class="bg-dark text-white fw-bold">
                    <tr>
                        <td colspan="5" class="text-end pe-4">সর্বমোট আদায় :</td>
                        <td class="fs-6">৳ <?php echo number_format($grand_total, 2); ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php include 'layout_footer.php'; ?>