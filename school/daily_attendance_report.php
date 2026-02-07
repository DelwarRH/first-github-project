<?php include 'layout_header.php'; ?>
<h5 class="section-title">দৈনিক হাজিরা রিপোর্ট</h5>

<form method="GET" class="row g-2 mb-4 bg-light p-3 border rounded no-print">
    <div class="col-md-4">
        <select name="class" class="form-select" required>
            <option value="">শ্রেণি নির্বাচন করুন</option>
            <?php $cls=['Six','Seven','Eight','Nine','Ten']; foreach($cls as $c) echo "<option value='$c'>$c</option>"; ?>
        </select>
    </div>
    <div class="col-md-3"><input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required></div>
    <div class="col-md-2"><button type="submit" class="btn btn-dark w-100">রিপোর্ট দেখুন</button></div>
</form>

<?php if(isset($_GET['class'])): 
    $stmt = $pdo->prepare("SELECT sa.status, s.name, s.roll FROM student_attendance sa 
                           JOIN students s ON sa.student_id = s.id 
                           WHERE sa.school_id = ? AND sa.class = ? AND sa.date = ? ORDER BY s.roll ASC");
    $stmt->execute([$school_id, $_GET['class'], $_GET['date']]);
    $rows = $stmt->fetchAll();
    
    if($rows):
?>
    <div class="card p-3 border-0 shadow-sm bg-white">
        <div class="text-center mb-3">
            <h6 class="fw-bold">শ্রেণি: <?php echo $_GET['class']; ?> | তারিখ: <?php echo date('d-M-Y', strtotime($_GET['date'])); ?></h6>
        </div>
        <table class="table table-bordered text-center align-middle">
            <thead class="bg-light">
                <tr><th>রোল</th><th>নাম</th><th>অবস্থা</th></tr>
            </thead>
            <tbody>
                <?php foreach($rows as $r): ?>
                <tr>
                    <td><?php echo $r['roll']; ?></td>
                    <td class="text-start ps-3"><?php echo $r['name']; ?></td>
                    <td>
                        <?php if($r['status'] == 'Present'): ?>
                            <span class="text-success fw-bold"><i class="fa fa-check-circle"></i> Present</span>
                        <?php else: ?>
                            <span class="text-danger fw-bold"><i class="fa fa-times-circle"></i> Absent</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="text-center no-print"><button onclick="window.print()" class="btn btn-outline-dark btn-sm"><i class="fa fa-print"></i> প্রিন্ট রিপোর্ট</button></div>
    </div>
<?php else: echo "<div class='alert alert-info text-center'>এই তারিখে বা এই শ্রেণির কোনো হাজিরা তথ্য পাওয়া যায়নি।</div>"; endif; endif; ?>

<?php include 'layout_footer.php'; ?>