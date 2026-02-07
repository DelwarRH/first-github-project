<?php include 'layout_header.php'; ?>
<h5 class="section-title">সিট প্ল্যান (Seat Plan Generator)</h5>

<form method="GET" class="row g-2 mb-4 bg-light p-3 border rounded no-print">
    <div class="col-md-4">
        <select name="class" class="form-select" required>
            <option value="">শ্রেণি নির্বাচন</option>
            <option value="Six">Six</option><option value="Ten">Ten</option>
        </select>
    </div>
    <div class="col-md-4">
        <input type="text" name="exam" class="form-control" placeholder="পরীক্ষার নাম (উদা: বার্ষিক পরীক্ষা)" required>
    </div>
    <div class="col-md-2"><button type="submit" class="btn btn-success w-100 fw-bold">তৈরি করুন</button></div>
</form>

<?php if(isset($_GET['class'])): ?>
<div class="text-end mb-3 no-print">
    <button onclick="window.print()" class="btn btn-dark btn-sm"><i class="fa fa-print"></i> প্রিন্ট সিট প্ল্যান</button>
</div>

<div class="row g-3">
    <?php
    $stmt = $pdo->prepare("SELECT * FROM students WHERE school_id = ? AND class = ? ORDER BY roll ASC");
    $stmt->execute([$school_id, $_GET['class']]);
    while($row = $stmt->fetch()):
    ?>
    <div class="col-md-4 col-6">
        <div class="border border-dark p-2 text-center bg-white shadow-sm" style="border-style: dashed !important; height: 160px; display: flex; flex-direction: column; justify-content: center;">
            <h6 class="m-0 fw-bold text-danger" style="font-size: 14px;"><?php echo $_SESSION['user_name']; ?></h6>
            <div class="bg-dark text-white d-inline-block px-3 my-1 small">SEAT PLAN</div>
            <p class="m-0 fw-bold text-dark fs-6"><?php echo $row['name']; ?></p>
            <p class="m-0 fw-bold">Class: <?php echo $row['class']; ?> | Roll: <span class="fs-5"><?php echo $row['roll']; ?></span></p>
            <small class="text-muted"><?php echo $_GET['exam']; ?> - <?php echo date("Y"); ?></small>
        </div>
    </div>
    <?php endwhile; ?>
</div>
<?php endif; ?>

<?php include 'layout_footer.php'; ?>