<?php 
include 'layout_header.php'; 

$selected_class = $_GET['class'] ?? '';
$selected_month = $_GET['month'] ?? date('F');
?>

<div class="card shadow-sm border-0 rounded-4 overflow-hidden">
    <div class="card-header bg-danger text-white p-3">
        <h5 class="m-0 fw-bold"><i class="fa fa-exclamation-triangle me-2 text-warning"></i> বকেয়া ও অগ্রিম ফি প্রতিবেদন</h5>
    </div>
    <div class="card-body p-4">
        <form method="GET" class="row g-2 mb-4 bg-light p-3 border rounded no-print">
            <div class="col-md-4">
                <label class="small fw-bold">শ্রেণি বাছাই করুন</label>
                <select name="class" class="form-select" required>
                    <option value="">শ্রেণি নির্বাচন করুন</option>
                    <option value="Six" <?php echo $selected_class == 'Six' ? 'selected' : ''; ?>>Six</option>
                    <option value="Ten" <?php echo $selected_class == 'Ten' ? 'selected' : ''; ?>>Ten</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="small fw-bold">মাস</label>
                <select name="month" class="form-select">
                    <?php $m_arr=['January','February','March','April','May','June','July','August','September','October','November','December']; 
                    foreach($m_arr as $m) echo "<option value='$m'".($selected_month==$m?' selected':'').">$m</option>"; ?>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-danger w-100 fw-bold shadow">রিপোর্ট দেখুন</button>
            </div>
        </form>

        <?php if(!empty($selected_class)): ?>
        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle" style="font-size: 13px;">
                <thead class="bg-secondary text-white fw-bold">
                    <tr>
                        <th>রোল</th>
                        <th class="text-start ps-3">শিক্ষার্থীর নাম</th>
                        <th>নির্ধারিত বেতন</th>
                        <th>পরিশোধিত</th>
                        <th>বকেয়া (Due)</th>
                        <th>অগ্রিম (Advance)</th>
                        <th>অবস্থা</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // এই শ্রেণির নির্ধারিত ফি আনা
                    $stmt_fee = $pdo->prepare("SELECT amount FROM fee_settings WHERE school_id = ? AND class_name = ? AND fee_type = 'Monthly Fee'");
                    $stmt_fee->execute([$school_id, $selected_class]);
                    $defined_fee = $stmt_fee->fetchColumn() ?: 0;

                    $stmt = $pdo->prepare("SELECT s.id, s.roll, s.name, 
                        (SELECT SUM(amount) FROM student_payments WHERE student_id = s.id AND month = ? AND school_id = ? AND payment_type = 'Monthly Fee') as paid 
                        FROM students s WHERE s.school_id = ? AND s.class = ? ORDER BY s.roll ASC");
                    $stmt->execute([$selected_month, $school_id, $school_id, $selected_class]);
                    
                    while($row = $stmt->fetch()):
                        $paid = $row['paid'] ?: 0;
                        $diff = $paid - $defined_fee; // পার্থক্য বের করা
                        
                        $due = ($diff < 0) ? abs($diff) : 0; // যদি পার্থক্য নেগেটিভ হয় তবে সেটা বকেয়া
                        $advance = ($diff > 0) ? $diff : 0;   // যদি পার্থক্য পজিটিভ হয় তবে সেটা অগ্রিম
                    ?>
                    <tr>
                        <td class="fw-bold">#<?php echo $row['roll']; ?></td>
                        <td class="text-start ps-3 fw-bold"><?php echo $row['name']; ?></td>
                        <td>৳ <?php echo number_format($defined_fee, 2); ?></td>
                        <td class="text-primary fw-bold">৳ <?php echo number_format($paid, 2); ?></td>
                        <td class="text-danger fw-bold">৳ <?php echo number_format($due, 2); ?></td>
                        <td class="text-success fw-bold">৳ <?php echo number_format($advance, 2); ?></td>
                        <td>
                            <?php if($advance > 0): ?>
                                <span class="badge bg-primary">Advance</span>
                            <?php elseif($due <= 0 && $defined_fee > 0): ?>
                                <span class="badge bg-success">Paid</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Due</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'layout_footer.php'; ?>