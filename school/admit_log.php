<?php include 'layout_header.php'; ?>
<h5 class="section-title text-success"><i class="fa fa-clipboard-check me-2"></i> ভর্তিকৃত শিক্ষার্থীর লগ (Admit Log)</h5>

<div class="table-responsive">
    <table class="table table-erp">
        <thead>
            <tr>
                <th>ছবি</th>
                <th>নাম</th>
                <th>শ্রেণি</th>
                <th>মোবাইল</th>
                <th>ভর্তির তারিখ</th>
                <th>অবস্থা</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->prepare("SELECT * FROM admissions WHERE school_id = ? AND status = 'admitted' ORDER BY id DESC");
            $stmt->execute([$school_id]);
            $logs = $stmt->fetchAll();

            if (count($logs) > 0) {
                foreach ($logs as $row) {
            ?>
            <tr>
                <td><img src="../<?php echo $row['photo']; ?>" width="40" class="rounded border"></td>
                <td class="text-start ps-3">
                    <div class="fw-bold"><?php echo $row['student_name']; ?></div>
                    <small class="text-muted">পিতা: <?php echo $row['father_name']; ?></small>
                </td>
                <td><?php echo $row['desired_class']; ?></td>
                <td><?php echo $row['phone']; ?></td>
                <td><?php echo date('d-M-Y', strtotime($row['applied_at'])); ?></td>
                <td><span class="badge bg-success">Admitted</span></td>
            </tr>
            <?php 
                }
            } else {
                echo "<tr><td colspan='6' class='py-4 text-muted'>এখনও কোনো অনলাইন ভর্তি সম্পন্ন হয়নি।</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include 'layout_footer.php'; ?>