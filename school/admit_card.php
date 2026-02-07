<?php include 'layout_header.php'; ?>
<h5 class="section-title">পরীক্ষার প্রবেশপত্র (Admit Card) তৈরি করুন</h5>

<form method="GET" class="row g-2 mb-4 bg-light p-3 border rounded">
    <div class="col-md-4">
        <select name="class" class="form-select" required>
            <option value="">শ্রেণি নির্বাচন করুন</option>
            <option value="Six">Six</option><option value="Ten">Ten</option>
        </select>
    </div>
    <div class="col-md-4">
        <select name="exam" class="form-select" required>
            <option value="Half Yearly">Half Yearly Exam</option>
            <option value="Final">Annual Exam</option>
        </select>
    </div>
    <div class="col-md-2"><button type="submit" class="btn btn-primary w-100">Generate</button></div>
</form>

<?php if(isset($_GET['class'])): ?>
<div class="text-end mb-3"><button onclick="window.print()" class="btn btn-dark btn-sm"><i class="fa fa-print"></i> প্রিন্ট অল</button></div>
<div class="row g-3">
    <?php
    $stmt = $pdo->prepare("SELECT * FROM students WHERE school_id = ? AND class = ?");
    $stmt->execute([$school_id, $_GET['class']]);
    while($s = $stmt->fetch()):
    ?>
    <div class="col-md-6">
        <div style="border: 2px solid #000; padding: 15px; background: #fff; position: relative;">
            <div class="text-center border-bottom pb-2 mb-2">
                <h6 class="m-0 fw-bold text-danger"><?php echo $_SESSION['user_name']; ?></h6>
                <small>পরীক্ষার প্রবেশপত্র - <?php echo date("Y"); ?></small>
            </div>
            <div class="row">
                <div class="col-8">
                    <p class="m-0">নাম: <b><?php echo $s['name']; ?></b></p>
                    <p class="m-0">শ্রেণি: <?php echo $s['class']; ?> | রোল: <?php echo $s['roll']; ?></p>
                    <p class="m-0">পরীক্ষা: <?php echo $_GET['exam']; ?></p>
                </div>
                <div class="col-4 text-end">
                    <img src="../<?php echo $s['photo']; ?>" width="60" height="70" class="border">
                </div>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>
<?php endif; ?>

<?php include 'layout_footer.php'; ?>