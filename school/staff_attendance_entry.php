<?php include 'layout_header.php'; ?>
<h5 class="section-title">শিক্ষক ও স্টাফ ম্যানুয়াল হাজিরা এন্ট্রি</h5>

<form method="GET" class="row g-2 mb-4 bg-light p-3 border rounded no-print">
    <div class="col-md-4">
        <select name="role" class="form-select" required>
            <option value="">পদবী নির্বাচন করুন</option>
            <option value="teacher">শিক্ষক (Teacher)</option>
            <option value="staff">কর্মচারী (Staff)</option>
        </select>
    </div>
    <div class="col-md-3">
        <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
    </div>
    <div class="col-md-2"><button type="submit" class="btn btn-success w-100 fw-bold shadow">তালিকা আনুন</button></div>
</form>

<?php if(isset($_GET['role'])): 
    $role = $_GET['role'];
    $date = $_GET['date'];
    
    // হাজিরা চেক
    $check = $pdo->prepare("SELECT id FROM staff_attendance WHERE school_id=? AND role=? AND date=?");
    $check->execute([$school_id, $role, $date]);
    if($check->rowCount() > 0) {
        echo "<div class='alert alert-warning text-center fw-bold'>দুঃখিত! আজকের হাজিরা ইতিপূর্বে নেওয়া হয়েছে।</div>";
    } else {
?>
<form action="save_staff_attendance.php" method="POST">
    <input type="hidden" name="role" value="<?php echo $role; ?>">
    <input type="hidden" name="date" value="<?php echo $date; ?>">

    <div class="table-responsive">
        <table class="table table-erp">
            <thead>
                <tr>
                    <th>ছবি</th>
                    <th class="text-start ps-4">নাম</th>
                    <th>হাজিরা (P / A / L)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->prepare("SELECT * FROM users WHERE school_id = ? AND role = ? AND status='active' ORDER BY name ASC");
                $stmt->execute([$school_id, $role]);
                while($row = $stmt->fetch()):
                    $img = !empty($row['image']) ? '../'.$row['image'] : 'https://via.placeholder.com/50';
                ?>
                <tr>
                    <td><img src="<?php echo $img; ?>" width="40" class="rounded-circle border"></td>
                    <td class="text-start ps-4 fw-bold text-dark"><?php echo $row['name']; ?></td>
                    <td>
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="status[<?php echo $row['id']; ?>]" id="p<?php echo $row['id']; ?>" value="Present" checked>
                            <label class="btn btn-outline-success btn-sm px-3 fw-bold" for="p<?php echo $row['id']; ?>">P</label>

                            <input type="radio" class="btn-check" name="status[<?php echo $row['id']; ?>]" id="a<?php echo $row['id']; ?>" value="Absent">
                            <label class="btn btn-outline-danger btn-sm px-3 fw-bold" for="a<?php echo $row['id']; ?>">A</label>

                            <input type="radio" class="btn-check" name="status[<?php echo $row['id']; ?>]" id="l<?php echo $row['id']; ?>" value="Leave">
                            <label class="btn btn-outline-warning btn-sm px-3 fw-bold" for="l<?php echo $row['id']; ?>">L</label>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <div class="text-center mt-4">
        <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow-lg fw-bold">হাজিরা নিশ্চিত করুন</button>
    </div>
</form>
<?php } endif; ?>

<?php include 'layout_footer.php'; ?>