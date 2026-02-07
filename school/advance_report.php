<?php include 'layout_header.php'; ?>

<div class="card shadow-sm border-0 rounded-4 overflow-hidden">
    <div class="card-header bg-success text-white p-3">
        <h5 class="m-0 fw-bold"><i class="fa fa-hand-holding-usd me-2"></i> অগ্রিম (Advance) পরিশোধিত তালিকা</h5>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-center align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>রোল ও শ্রেণি</th>
                        <th class="text-start ps-3">শিক্ষার্থীর নাম</th>
                        <th>মাস</th>
                        <th>নির্ধারিত ফি</th>
                        <th>জমা দিয়েছেন</th>
                        <th>অগ্রিম জমা</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // সাবকুয়েরি ব্যবহার করে শুধুমাত্র অগ্রিম প্রদানকারীদের ফিল্টার করা
                    $sql = "SELECT p.student_id, p.month, p.class, p.roll, s.name, SUM(p.amount) as total_paid, f.amount as fixed_fee
                            FROM student_payments p
                            JOIN students s ON p.student_id = s.id
                            JOIN fee_settings f ON s.class = f.class_name AND p.school_id = f.school_id
                            WHERE p.school_id = ? AND p.payment_type = 'Monthly Fee' AND f.fee_type = 'Monthly Fee'
                            GROUP BY p.student_id, p.month
                            HAVING total_paid > fixed_fee";
                    
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$school_id]);
                    $rows = $stmt->fetchAll();

                    if(count($rows) > 0):
                        foreach($rows as $row):
                            $advance = $row['total_paid'] - $row['fixed_fee'];
                    ?>
                    <tr>
                        <td><?php echo $row['class']." - ".$row['roll']; ?></td>
                        <td class="text-start ps-3 fw-bold"><?php echo $row['name']; ?></td>
                        <td><?php echo $row['month']; ?></td>
                        <td>৳ <?php echo number_format($row['fixed_fee'], 2); ?></td>
                        <td class="text-primary fw-bold">৳ <?php echo number_format($row['total_paid'], 2); ?></td>
                        <td class="bg-success text-white fw-bold">৳ <?php echo number_format($advance, 2); ?></td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="6" class="py-4 text-muted">বর্তমানে কোনো অগ্রিম প্রদানকারী শিক্ষার্থী নেই।</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'layout_footer.php'; ?>