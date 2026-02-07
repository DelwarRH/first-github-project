<?php include 'layout_header.php'; include '../includes/functions.php'; ?>
<h5 class="section-title">ট্যাবুলেশন শিট (Tabulation Sheet)</h5>

<form method="GET" class="row g-2 mb-4 no-print bg-light p-3 border rounded">
    <div class="col-md-4"><select name="class" class="form-select" required><option value="Six">Six</option><option value="Ten">Ten</option></select></div>
    <div class="col-md-4"><select name="exam" class="form-select" required><option value="Half Yearly">Half Yearly</option><option value="Final">Annual Exam</option></select></div>
    <div class="col-md-2"><button type="submit" class="btn btn-dark w-100">জেনারেট শিট</button></div>
</form>

<?php if(isset($_GET['class'])): ?>
<div class="table-responsive">
    <table class="table table-bordered text-center align-middle bg-white shadow-sm" style="font-size: 13px;">
        <thead class="bg-dark text-white">
            <tr>
                <th>রোল</th>
                <th>শিক্ষার্থীর নাম</th>
                <th>মোট নম্বর</th>
                <th>গড় জিপিএ</th>
                <th>ফলাফল</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->prepare("SELECT student_roll, SUM(marks) as total, COUNT(subject) as subs 
                                   FROM results WHERE school_id = ? AND class_name = ? AND exam_term = ? 
                                   GROUP BY student_roll ORDER BY student_roll ASC");
            $stmt->execute([$school_id, $_GET['class'], $_GET['exam']]);
            while($row = $stmt->fetch()):
                // GPA লজিক (সরলীকরণ)
                $stmt_sub = $pdo->prepare("SELECT marks FROM results WHERE student_roll = ? AND school_id = ?");
                $stmt_sub->execute([$row['student_roll'], $school_id]);
                $all_m = $stmt_sub->fetchAll();
                $total_gp = 0; $fail = false;
                foreach($all_m as $m) {
                    $gp = getPoint($m['marks']);
                    $total_gp += $gp;
                    if($gp == 0) $fail = true;
                }
                $gpa = $fail ? 0.00 : round($total_gp / count($all_m), 2);
            ?>
            <tr>
                <td class="fw-bold">#<?php echo $row['student_roll']; ?></td>
                <td class="text-start ps-3">
                    <?php 
                    $s_name = $pdo->prepare("SELECT name FROM students WHERE roll=? AND school_id=?");
                    $s_name->execute([$row['student_roll'], $school_id]);
                    echo $s_name->fetchColumn();
                    ?>
                </td>
                <td class="fw-bold text-primary"><?php echo $row['total']; ?></td>
                <td class="fw-bold text-success"><?php echo number_format($gpa, 2); ?></td>
                <td><span class="badge <?php echo $fail ? 'bg-danger' : 'bg-success'; ?>"><?php echo $fail ? 'Failed' : 'Passed'; ?></span></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<div class="text-center mt-3 no-print"><button onclick="window.print()" class="btn btn-outline-dark btn-sm"><i class="fa fa-print"></i> প্রিন্ট শিট</button></div>
<?php endif; ?>

<?php include 'layout_footer.php'; ?>