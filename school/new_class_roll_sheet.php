<?php 
session_start();
require_once '../config/db.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'school') {
    header("Location: ../login.php"); exit();
}

$school_id = $_SESSION['user_id'];
include '../includes/header.php'; 
?>

<div class="container-fluid px-5 mt-4 no-print">
    <div class="p-3 bg-white shadow-sm rounded-4 border-start border-4 border-success mb-4 d-flex justify-content-between align-items-center">
        <h4 class="fw-bold text-success m-0"><i class="fa fa-calendar-check me-2"></i> হাজিরা রেজিস্টার তৈরি (Attendance Sheet)</h4>
        <a href="manage_students.php" class="btn btn-sm btn-danger px-3">ড্যাশবোর্ড</a>
    </div>
</div>

<div class="main-wrapper mx-auto" style="max-width: 1400px;">
    <!-- ফিল্টার -->
    <div class="card shadow-sm border-0 rounded-4 p-3 mb-4 no-print bg-light">
        <form method="GET" class="row g-2 justify-content-center align-items-end">
            <div class="col-md-3"><label class="small fw-bold">শ্রেণি</label><select name="class" class="form-select shadow-sm" required><?php $cls=['Six','Seven','Eight','Nine','Ten']; foreach($cls as $c) echo "<option value='$c'>$c</option>"; ?></select></div>
            <div class="col-md-3"><label class="small fw-bold">মাস</label><select name="month" class="form-select shadow-sm"><?php $months=['January','February','March','April','May','June','July','August','September','October','November','December']; foreach($months as $m) echo "<option value='$m'>$m</option>"; ?></select></div>
            <div class="col-md-2"><button type="submit" class="btn btn-success w-100 fw-bold shadow-sm">শিট তৈরি করুন</button></div>
        </form>
    </div>

    <?php 
    if (isset($_GET['class'])) {
        $stmt = $pdo->prepare("SELECT * FROM students WHERE class = ? AND school_id = ? ORDER BY roll ASC");
        $stmt->execute([$_GET['class'], $school_id]);
        $students = $stmt->fetchAll();

        if (count($students) > 0) {
    ?>
    <div class="bg-white p-4 shadow-sm rounded-4 border" style="overflow-x: auto;">
        <div class="text-center mb-4">
            <h3 class="fw-bold m-0 text-success text-uppercase"><?php echo $_SESSION['user_name']; ?></h3>
            <p class="fw-bold mb-0">Class: <?php echo $_GET['class']; ?> | Month: <?php echo $_GET['month']; ?> | Year: <?php echo date("Y"); ?></p>
            <h5 class="bg-dark text-white d-inline-block px-4 py-1 rounded mt-2 shadow-sm" style="font-size: 13px;">STUDENT ATTENDANCE REGISTER</h5>
        </div>

        <table class="table table-bordered text-center align-middle" style="font-size: 11px;">
            <thead class="table-light border-dark">
                <tr>
                    <th width="40">Roll</th>
                    <th width="180" class="text-start ps-2">Student Name</th>
                    <?php for($i=1; $i<=31; $i++) echo "<th width='25'>$i</th>"; ?>
                    <th width="40">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($students as $row) { ?>
                <tr style="height: 35px;">
                    <td class="fw-bold"><?php echo $row['roll']; ?></td>
                    <td class="text-start ps-2"><?php echo $row['name']; ?></td>
                    <?php for($i=1; $i<=31; $i++) echo "<td></td>"; ?>
                    <td></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="d-flex justify-content-between mt-5 pt-4 no-print">
            <button onclick="window.print()" class="btn btn-dark fw-bold px-5 rounded-pill shadow-lg"><i class="fa fa-print"></i> রেজিস্টার প্রিন্ট করুন</button>
        </div>
    </div>
    <?php } } ?>
</div>

<style>
@media print {
    @page { size: A4 landscape; margin: 10mm; }
    .no-print { display: none !important; }
    .main-wrapper { max-width: 100% !important; padding: 0 !important; }
    .table-bordered th, .table-bordered td { border: 1px solid #000 !important; }
}
</style>