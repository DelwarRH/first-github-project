<?php include 'layout_header.php'; ?>
<h5 class="section-title">ছাত্র-ছাত্রীদের দৈনিক হাজিরা এন্ট্রি</h5>

<form method="GET" class="row g-2 mb-4 bg-light p-3 border rounded no-print">
    <div class="col-md-4">
        <select name="class" class="form-select" required>
            <option value="">শ্রেণি নির্বাচন করুন</option>
            <option value="Six">Six</option><option value="Seven">Seven</option><option value="Eight">Eight</option><option value="Nine">Nine</option><option value="Ten">Ten</option>
        </select>
    </div>
    <div class="col-md-3">
        <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
    </div>
    <div class="col-md-2"><button type="submit" class="btn btn-primary w-100 fw-bold">শিক্ষার্থী আনুন</button></div>
</form>

<?php if(isset($_GET['class'])): 
    $class = $_GET['class'];
    $date = $_GET['date'];
    
    // চেক করা অলরেডি হাজিরা নেওয়া হয়েছে কিনা
    $check = $pdo->prepare("SELECT id FROM student_attendance WHERE school_id=? AND class=? AND date=?");
    $check->execute([$school_id, $class, $date]);
    if($check->rowCount() > 0) {
        echo "<div class='alert alert-warning text-center fw-bold'>দুঃখিত! এই শ্রেণির আজকের হাজিরা ইতিপূর্বে নেওয়া হয়েছে।</div>";
    } else {
?>
<form action="save_student_attendance.php" method="POST">
    <input type="hidden" name="class" value="<?php echo $class; ?>">
    <input type="hidden" name="date" value="<?php echo $date; ?>">

    <div class="table-responsive">
        <table class="table table-erp">
            <thead>
                <tr>
                    <th width="100">রোল</th>
                    <th class="text-start ps-4">শিক্ষার্থীর নাম</th>
                    <th>অবস্থা (P=Present, A=Absent)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->prepare("SELECT * FROM students WHERE school_id = ? AND class = ? ORDER BY roll ASC");
                $stmt->execute([$school_id, $class]);
                while($s = $stmt->fetch()):
                ?>
                <tr>
                    <td class="fw-bold">#<?php echo $s['roll']; ?></td>
                    <td class="text-start ps-4 fw-bold text-dark"><?php echo $s['name']; ?></td>
                    <td>
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="attendance[<?php echo $s['id']; ?>]" id="p<?php echo $s['id']; ?>" value="Present" checked>
                            <label class="btn btn-outline-success btn-sm px-4 fw-bold" for="p<?php echo $s['id']; ?>">P</label>

                            <input type="radio" class="btn-check" name="attendance[<?php echo $s['id']; ?>]" id="a<?php echo $s['id']; ?>" value="Absent">
                            <label class="btn btn-outline-danger btn-sm px-4 fw-bold" for="a<?php echo $s['id']; ?>">A</label>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <div class="text-center mt-4 mb-5">
        <button type="submit" class="btn btn-success btn-lg px-5 rounded-pill shadow-lg fw-bold">হাজিরা সাবমিট করুন</button>
    </div>
</form>
<?php } endif; ?>

<?php include 'layout_footer.php'; ?>