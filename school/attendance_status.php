<?php 
session_start();
require_once '../config/db.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'school') {
    header("Location: ../login.php"); exit();
}

$school_id = $_SESSION['user_id'];
include '../includes/header.php'; 
?>

<div class="page-header d-flex justify-content-between align-items-center no-print">
    <span><i class="fa fa-chart-bar me-2"></i> মাসিক হাজিরা স্ট্যাটাস রিপোর্ট</span>
    <a href="manage_students.php" class="btn btn-sm btn-danger rounded-pill px-4 fw-bold">ড্যাশবোর্ড</a>
</div>

<div class="container mt-4 mb-5">
    <div class="card shadow-sm border-0 rounded-4 p-3 mb-4 bg-white no-print">
        <form method="GET" class="row g-2 justify-content-center align-items-end">
            <div class="col-md-3">
                <label class="small fw-bold text-muted">শ্রেণি</label>
                <select name="class" class="form-select shadow-sm" required>
                    <option value="">শ্রেণি বাছাই</option>
                    <?php $cls=['Six','Seven','Eight','Nine','Ten']; foreach($cls as $c) echo "<option value='$c' ".(isset($_GET['class']) && $_GET['class']==$c?'selected':'').">$c</option>"; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="small fw-bold text-muted">মাস ও বছর</label>
                <input type="month" name="month" class="form-control shadow-sm" value="<?php echo $_GET['month'] ?? date('Y-m'); ?>" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-info text-white w-100 fw-bold shadow-sm">চেক স্ট্যাটাস</button>
            </div>
        </form>
    </div>

    <?php 
    if (isset($_GET['class']) && isset($_GET['month'])) {
        $class = $_GET['class'];
        $month_val = $_GET['month']; 
        
        // সাবকুয়েরিতে school_id যুক্ত করা হয়েছে
        $sql = "SELECT s.roll, s.name, s.id,
                (SELECT COUNT(*) FROM student_attendance WHERE student_id=s.id AND class=? AND date LIKE ? AND status='Present' AND school_id=?) as present_days,
                (SELECT COUNT(*) FROM student_attendance WHERE student_id=s.id AND class=? AND date LIKE ? AND school_id=?) as total_working_days
                FROM students s WHERE s.class=? AND s.school_id=? ORDER BY s.roll ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$class, $month_val.'%', $school_id, $class, $month_val.'%', $school_id, $class, $school_id]);
        $rows = $stmt->fetchAll();

        if (count($rows) > 0) {
    ?>
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-dark text-white text-center py-2 fw-bold">
            মাসিক হাজিরা সারসংক্ষেপ: <?php echo date("F, Y", strtotime($month_val)); ?> (শ্রেণি: <?php echo $class; ?>)
        </div>
        <div class="card-body p-0 bg-white">
            <table class="table table-bordered table-hover text-center align-middle mb-0" style="font-size: 14px;">
                <thead class="table-light">
                    <tr><th>রোল</th><th>শিক্ষার্থীর নাম</th><th>মোট দিন</th><th>উপস্থিত</th><th>অনুপস্থিত</th><th>শতকরা (%)</th></tr>
                </thead>
                <tbody>
                    <?php foreach($rows as $row) { 
                        $total = $row['total_working_days'];
                        $present = $row['present_days'];
                        $absent = $total - $present;
                        $percent = ($total > 0) ? round(($present/$total)*100, 2) : 0;
                    ?>
                    <tr>
                        <td class="fw-bold"><?php echo $row['roll']; ?></td>
                        <td class="text-start ps-4 fw-bold text-dark"><?php echo $row['name']; ?></td>
                        <td><?php echo $total; ?></td>
                        <td class="text-success"><?php echo $present; ?></td>
                        <td class="text-danger"><?php echo $absent; ?></td>
                        <td><span class="badge <?php echo ($percent < 70) ? 'bg-danger':'bg-success'; ?> px-3"><?php echo $percent; ?>%</span></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer text-center no-print bg-light">
            <button onclick="window.print()" class="btn btn-dark fw-bold rounded-pill px-5 shadow"><i class="fa fa-print"></i> রিপোর্ট প্রিন্ট করুন</button>
        </div>
    </div>
    <?php } else { echo "<div class='alert alert-warning text-center fw-bold shadow-sm'>দুঃখিত! এই শ্রেণির কোনো তথ্য পাওয়া যায়নি।</div>"; } } ?>
</div>
<?php include '../includes/footer.php'; ?>