<?php include 'layout_header.php'; ?>
<h5 class="section-title">শিক্ষক ও স্টাফ মাসিক হাজিরা রিপোর্ট</h5>

<form method="GET" class="row g-2 mb-4 bg-light p-3 border rounded no-print">
    <div class="col-md-4">
        <select name="month" class="form-select" required>
            <?php $m=['January','February','March','April','May','June','July','August','September','October','November','December'];
            foreach($m as $month) echo "<option value='$month'".(date('F')==$month?' selected':'').">$month</option>"; ?>
        </select>
    </div>
    <div class="col-md-3">
        <select name="role" class="form-select" required>
            <option value="teacher">Teacher</option><option value="staff">Staff</option>
        </select>
    </div>
    <div class="col-md-2"><button type="submit" class="btn btn-dark w-100">রিপোর্ট দেখুন</button></div>
</form>

<?php if(isset($_GET['month'])): ?>
<div class="table-responsive">
    <table class="table table-bordered text-center align-middle bg-white">
        <thead class="bg-primary text-white">
            <tr>
                <th>নাম</th>
                <th>মোট দিন</th>
                <th>উপস্থিত</th>
                <th>অনুপস্থিত</th>
                <th>ছুটি (Leave)</th>
                <th>শতাংশ (%)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->prepare("SELECT id, name FROM users WHERE school_id = ? AND role = ? AND status='active'");
            $stmt->execute([$school_id, $_GET['role']]);
            while($user = $stmt->fetch()):
                // ক্যালকুলেশন
                $c_stmt = $pdo->prepare("SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status='Present' THEN 1 ELSE 0 END) as p,
                    SUM(CASE WHEN status='Absent' THEN 1 ELSE 0 END) as a,
                    SUM(CASE WHEN status='Leave' THEN 1 ELSE 0 END) as l
                    FROM staff_attendance WHERE user_id=? AND date LIKE ?");
                $c_stmt->execute([$user['id'], date('Y').'-'.date('m', strtotime($_GET['month'])).'%']);
                $stats = $c_stmt->fetch();
                $percent = ($stats['total'] > 0) ? round(($stats['p'] / $stats['total']) * 100, 2) : 0;
            ?>
            <tr>
                <td class="text-start ps-3 fw-bold"><?php echo $user['name']; ?></td>
                <td><?php echo $stats['total']; ?></td>
                <td class="text-success"><?php echo $stats['p']; ?></td>
                <td class="text-danger"><?php echo $stats['a']; ?></td>
                <td class="text-warning"><?php echo $stats['l']; ?></td>
                <td class="fw-bold"><?php echo $percent; ?>%</td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php include 'layout_footer.php'; ?>