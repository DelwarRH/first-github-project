<?php include 'layout_header.php'; ?>
<h5 class="section-title"><i class="fa fa-user-clock text-warning"></i> অনলাইন ভর্তি আবেদন (Pending)</h5>

<div class="table-responsive">
    <table class="table table-erp">
        <thead>
            <tr>
                <th>ছবি</th>
                <th>শিক্ষার্থীর নাম</th>
                <th>শ্রেণি</th>
                <th>মোবাইল</th>
                <th>তারিখ</th>
                <th>অ্যাকশন</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // admissions টেবিল থেকে পেন্ডিং আবেদন আনা (আগে আমরা admission.php তে এটি ব্যবহার করেছিলাম)
            $stmt = $pdo->prepare("SELECT * FROM admissions WHERE school_id = ? AND status = 'pending' ORDER BY id DESC");
            $stmt->execute([$school_id]);
            $applicants = $stmt->fetchAll();

            if (count($applicants) > 0) {
                foreach ($applicants as $row) {
            ?>
            <tr>
                <td><img src="../<?php echo $row['photo']; ?>" width="40" class="rounded border"></td>
                <td class="text-start ps-3">
                    <div class="fw-bold"><?php echo $row['student_name']; ?></div>
                    <small class="text-muted">পিতা: <?php echo $row['father_name'] ?? 'N/A'; ?></small>
                </td>
                <td><?php echo $row['desired_class']; ?></td>
                <td><?php echo $row['phone']; ?></td>
                <td><?php echo date('d-M-Y', strtotime($row['applied_at'] ?? date('Y-m-d'))); ?></td>
                <td>
                    <a href="approve_student.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success fw-bold rounded-pill shadow-sm">অনুমোদন</a>
                    <a href="cancel_applicant.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger rounded-pill shadow-sm" onclick="return confirm('বাতিল করতে চান?')">বাতিল</a>
                </td>
            </tr>
            <?php 
                }
            } else {
                echo "<tr><td colspan='6' class='py-4 text-muted'>কোনো পেন্ডিং আবেদন নেই।</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include 'layout_footer.php'; ?>