<?php include 'layout_header.php'; ?>
<h5 class="section-title">পরীক্ষার ফলাফল ব্যবস্থাপনা</h5>

<form method="GET" class="row g-2 mb-4 bg-light p-3 border rounded no-print">
    <div class="col-md-3">
        <select name="class" class="form-select" required>
            <option value="">শ্রেণি নির্বাচন করুন</option>
            <option value="Six">Six</option><option value="Ten">Ten</option>
        </select>
    </div>
    <div class="col-md-3">
        <select name="exam" class="form-select" required>
            <option value="Half Yearly">Half Yearly</option>
            <option value="Final">Annual Exam</option>
        </select>
    </div>
    <div class="col-md-2"><button type="submit" class="btn btn-primary w-100">ডাটা লোড করুন</button></div>
</form>

<?php if(isset($_GET['class'])): ?>
<div class="table-responsive">
    <table class="table table-erp">
        <thead>
            <tr>
                <th>রোল</th>
                <th>শিক্ষার্থীর নাম</th>
                <th>বিষয়</th>
                <th>প্রাপ্ত নম্বর</th>
                <th>গ্রেড</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->prepare("SELECT r.*, s.name FROM results r JOIN students s ON r.student_roll = s.roll WHERE r.school_id = ? AND r.class_name = ? AND r.exam_term = ? AND s.school_id = ?");
            $stmt->execute([$school_id, $_GET['class'], $_GET['exam'], $school_id]);
            while($row = $stmt->fetch()):
            ?>
            <tr>
                <td><?php echo $row['student_roll']; ?></td>
                <td class="text-start ps-3"><?php echo $row['name']; ?></td>
                <td><?php echo $row['subject']; ?></td>
                <td><span class="badge bg-info text-dark"><?php echo $row['marks']; ?></span></td>
                <td class="fw-bold text-success"><?php echo $row['marks'] >= 33 ? 'Passed' : 'Failed'; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php include 'layout_footer.php'; ?>