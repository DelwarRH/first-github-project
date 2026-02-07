<?php include 'layout_header.php'; ?>
<h5 class="section-title text-danger"><i class="fa fa-user-times me-2"></i> বাতিলকৃত আবেদনের তালিকা (Rejected)</h5>

<div class="table-responsive">
    <table class="table table-erp">
        <thead>
            <tr>
                <th>নাম</th>
                <th>শ্রেণি</th>
                <th>মোবাইল</th>
                <th>বাতিলের তারিখ</th>
                <th>অ্যাকশন</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->prepare("SELECT * FROM admissions WHERE school_id = ? AND status = 'cancelled' ORDER BY id DESC");
            $stmt->execute([$school_id]);
            while ($row = $stmt->fetch()) {
            ?>
            <tr>
                <td class="text-start ps-3 fw-bold"><?php echo $row['student_name']; ?></td>
                <td><?php echo $row['desired_class']; ?></td>
                <td><?php echo $row['phone']; ?></td>
                <td><?php echo date('d-M-Y'); ?></td>
                <td>
                    <a href="pending_applicants.php" class="btn btn-sm btn-outline-primary">পুনরায় দেখুন</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include 'layout_footer.php'; ?>