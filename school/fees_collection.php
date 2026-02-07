<?php include 'layout_header.php'; ?>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-white p-4 border-bottom"><h5 class="m-0 fw-bold text-primary"><i class="fa fa-money-bill-wave me-2"></i> শিক্ষার্থী ফি আদায় ও অটো-ক্যালকুলেশন</h5></div>
    <div class="card-body p-4">
        <!-- সার্চ ফরম -->
        <form method="GET" class="row g-2 mb-4 bg-light p-3 border rounded no-print">
            <div class="col-md-3"><input type="number" name="roll" class="form-control" placeholder="রোল নম্বর" value="<?php echo $_GET['roll'] ?? ''; ?>" required></div>
            <div class="col-md-3">
                <select name="class" id="searchClass" class="form-select" required>
                    <option value="">শ্রেণি নির্বাচন করুন</option>
                    <option value="Six" <?php echo (isset($_GET['class']) && $_GET['class'] == 'Six') ? 'selected' : ''; ?>>Six</option>
                    <option value="Ten" <?php echo (isset($_GET['class']) && $_GET['class'] == 'Ten') ? 'selected' : ''; ?>>Ten</option>
                </select>
            </div>
            <div class="col-md-2"><button type="submit" name="search" class="btn btn-dark w-100 fw-bold">সার্চ করুন</button></div>
        </form>

        <?php 
        if (isset($_GET['search'])) {
            $stmt_stu = $pdo->prepare("SELECT * FROM students WHERE class=? AND roll=? AND school_id=?");
            $stmt_stu->execute([$_GET['class'], $_GET['roll'], $school_id]);
            $s = $stmt_stu->fetch();

            if ($s):
                // এই শ্রেণির সকল নির্ধারিত ফি ডাটাবেজ থেকে নিয়ে আসা
                $stmt_fees = $pdo->prepare("SELECT fee_type, amount FROM fee_settings WHERE school_id=? AND class_name=?");
                $stmt_fees->execute([$school_id, $_GET['class']]);
                $all_fees = $stmt_fees->fetchAll(PDO::FETCH_KEY_PAIR); // এটি ['Monthly Fee' => 500] এই ফরম্যাটে আনবে
        ?>
            <div class="row g-4 mt-2">
                <div class="col-md-4">
                    <div class="p-3 border rounded-4 bg-white text-center shadow-sm h-100">
                        <img src="../<?php echo $s['photo'] ?: 'uploads/logo.jpg'; ?>" class="rounded-circle border mb-3 shadow-sm" width="100" height="100">
                        <h5 class="fw-bold m-0"><?php echo $s['name']; ?></h5>
                        <p class="text-muted small">ID: <?php echo $s['id']; ?> | Roll: <?php echo $s['roll']; ?></p>
                        <hr>
                        <div class="text-start">
                            <?php foreach($all_fees as $type => $amt): ?>
                                <p class="small m-0 fw-bold text-secondary"><?php echo $type; ?>: <span class="float-end text-dark">৳ <?php echo $amt; ?></span></p>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="p-4 border rounded-4 bg-light shadow-sm">
                        <form action="save_payment.php" method="POST">
                            <input type="hidden" name="student_id" value="<?php echo $s['id']; ?>">
                            <input type="hidden" name="class" value="<?php echo $s['class']; ?>"> <!-- এটি নিশ্চিত করুন -->
                            <input type="hidden" name="roll" value="<?php echo $s['roll']; ?>">   <!-- এটি নিশ্চিত করুন -->
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="fw-bold small">ফি এর ক্যাটাগরি</label>
                                    <select name="type" id="feeType" class="form-select shadow-sm" required onchange="updatePayable()">
                                        <option value="">বাছাই করুন</option>
                                        <?php foreach($all_fees as $type => $amt): ?>
                                            <option value="<?php echo $type; ?>" data-amt="<?php echo $amt; ?>"><?php echo $type; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="fw-bold small">মাস (যদি প্রযোজ্য হয়)</label>
                                    <select name="month" class="form-select shadow-sm"><?php $m_arr=['January','February','March','April','May','June','July','August','September','October','November','December']; foreach($m_arr as $m) echo "<option value='$m'".(date('F')==$m?' selected':'').">$m</option>"; ?></select>
                                </div>
                                <div class="col-md-6">
                                    <label class="fw-bold small text-danger">নির্ধারিত টাকা (Payable)</label>
                                    <input type="text" id="payable_amt" class="form-control bg-white fw-bold text-danger" readonly value="0.00">
                                </div>
                                <div class="col-md-6">
                                    <label class="fw-bold small text-success">জমা দিচ্ছেন (Collection)</label>
                                    <input type="number" name="amount" id="collect_amt" class="form-control border-success fw-bold" placeholder="0.00" required oninput="calcDue()">
                                </div>
                                <div class="col-md-12">
                                    <div class="bg-dark text-white p-3 rounded-3 text-center shadow-sm">
                                        <h5 class="m-0">বকেয়া থাকবে (Due): ৳ <span id="due_show" class="text-warning">0.00</span></h5>
                                    </div>
                                </div>
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow">পেমেন্ট সেভ ও মানি রিসিট</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script>
                // ফি টাইপ সিলেক্ট করলে টাকা লোড হওয়া
                function updatePayable() {
                    const select = document.getElementById('feeType');
                    const amount = select.options[select.selectedIndex].getAttribute('data-amt') || 0;
                    document.getElementById('payable_amt').value = amount;
                    document.getElementById('collect_amt').value = amount; // ডিফল্টভাবে ফুল টাকা বসিয়ে দিবে
                    calcDue();
                }

                // বকেয়া হিসাব করা
                function calcDue() {
                    const payable = parseFloat(document.getElementById('payable_amt').value) || 0;
                    const collect = parseFloat(document.getElementById('collect_amt').value) || 0;
                    const due = payable - collect;
                    document.getElementById('due_show').innerText = (due > 0) ? due.toFixed(2) : "0.00";
                }
            </script>
        <?php else: echo "<div class='alert alert-danger text-center'>শিক্ষার্থী পাওয়া যায়নি।</div>"; endif; } ?>
    </div>
</div>

<?php include 'layout_footer.php'; ?>