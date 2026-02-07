<?php include 'layout_header.php'; ?>
<h5 class="section-title">পরীক্ষার ফলাফল ইনপুট ও অনুসন্ধান</h5>

<div class="card shadow-sm border-0 rounded-4 p-4 mb-4 bg-light no-print">
    <form method="GET" class="row g-2 justify-content-center align-items-end">
        <div class="col-md-3">
            <label class="fw-bold mb-1">শ্রেণি</label>
            <select name="class" class="form-select shadow-sm" required>
                <option value="">বাছাই করুন</option>
                <option value="Six">Class Six</option><option value="Ten">Class Ten</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="fw-bold mb-1">পরীক্ষা</label>
            <select name="exam" class="form-select shadow-sm" required>
                <option value="Half Yearly">Half Yearly</option>
                <option value="Final">Annual Exam</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="fw-bold mb-1">বিষয়</label>
            <input type="text" name="subject" class="form-control shadow-sm" placeholder="উদা: গণিত" required>
        </div>
        <div class="col-md-2">
            <button type="submit" name="load" class="btn btn-primary w-100 fw-bold">লোড করুন</button>
        </div>
    </form>
</div>

<?php if(isset($_GET['load'])): ?>
<form action="save_marks.php" method="POST">
    <input type="hidden" name="class" value="<?php echo $_GET['class']; ?>">
    <input type="hidden" name="exam" value="<?php echo $_GET['exam']; ?>">
    <input type="hidden" name="subject" value="<?php echo $_GET['subject']; ?>">

    <div class="table-responsive">
        <table class="table table-erp">
            <thead>
                <tr>
                    <th width="100">রোল</th>
                    <th>শিক্ষার্থীর নাম</th>
                    <th width="200">প্রাপ্ত নম্বর (১০০)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->prepare("SELECT * FROM students WHERE school_id = ? AND class = ? ORDER BY roll ASC");
                $stmt->execute([$school_id, $_GET['class']]);
                while($s = $stmt->fetch()):
                ?>
                <tr>
                    <td><?php echo $s['roll']; ?></td>
                    <td class="text-start ps-4"><?php echo $s['name']; ?></td>
                    <td>
                        <input type="number" name="marks[<?php echo $s['roll']; ?>]" class="form-control text-center border-primary fw-bold" placeholder="0" max="100" required>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <div class="text-center mt-4 mb-5">
        <button type="submit" class="btn btn-success btn-lg px-5 rounded-pill shadow-lg fw-bold">ফলাফল সেভ করুন</button>
    </div>
</form>
<?php endif; ?>

<?php include 'layout_footer.php'; ?>