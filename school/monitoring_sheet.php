<?php include 'layout_header.php'; ?>
<h5 class="section-title">পরীক্ষা হল মনিটরিং শিট</h5>

<form method="GET" class="row g-2 mb-4 bg-light p-3 border rounded no-print">
    <div class="col-md-4"><select name="class" class="form-select" required><option value="Six">Six</option><option value="Ten">Ten</option></select></div>
    <div class="col-md-3"><input type="text" name="subject" class="form-control" placeholder="বিষয়ের নাম" required></div>
    <div class="col-md-2"><button type="submit" class="btn btn-primary w-100">জেনারেট</button></div>
</form>

<?php if(isset($_GET['class'])): ?>
<div class="card p-4 border-dark bg-white">
    <div class="text-center mb-4">
        <h4 class="fw-bold m-0"><?php echo $_SESSION['user_name']; ?></h4>
        <h6 class="border-bottom d-inline-block px-4 pb-1">EXAM ATTENDANCE & MONITORING SHEET</h6>
        <div class="d-flex justify-content-between mt-3 px-4 fw-bold">
            <span>শ্রেণি: <?php echo $_GET['class']; ?></span>
            <span>বিষয়: <?php echo $_GET['subject']; ?></span>
            <span>তারিখ: .................</span>
        </div>
    </div>
    <table class="table table-bordered border-dark text-center align-middle">
        <thead>
            <tr class="bg-light">
                <th width="80">রোল</th>
                <th>শিক্ষার্থীর নাম</th>
                <th width="150">অতিরিক্ত উত্তরপত্র</th>
                <th width="200">শিক্ষার্থীর স্বাক্ষর</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->prepare("SELECT roll, name FROM students WHERE school_id = ? AND class = ? ORDER BY roll ASC");
            $stmt->execute([$school_id, $_GET['class']]);
            while($row = $stmt->fetch()):
            ?>
            <tr style="height: 45px;">
                <td class="fw-bold"><?php echo $row['roll']; ?></td>
                <td class="text-start ps-3"><?php echo $row['name']; ?></td>
                <td></td>
                <td></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <div class="mt-5 d-flex justify-content-around fw-bold no-print">
        <button onclick="window.print()" class="btn btn-dark"><i class="fa fa-print"></i> প্রিন্ট শিট</button>
    </div>
</div>
<?php endif; ?>

<?php include 'layout_footer.php'; ?>