<?php include 'layout_header.php'; ?>
<div class="row justify-content-center">
    <div class="col-md-9">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-primary text-white p-3 text-center">
                <h4 class="fw-bold m-0"><i class="fa fa-user-graduate me-2"></i> নতুন শিক্ষার্থী ভর্তি ফরম</h4>
            </div>
            <div class="card-body p-4 bg-light">
                <form action="save_student_process.php" method="POST" enctype="multipart/form-data" class="row g-3">
                    <div class="col-md-6">
                        <label class="fw-bold small">শিক্ষার্থীর নাম</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="fw-bold small">শ্রেণি</label>
                        <select name="class" class="form-select" required>
                            <option value="Six">Six</option><option value="Seven">Seven</option>
                            <option value="Eight">Eight</option><option value="Nine">Nine</option><option value="Ten">Ten</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="fw-bold small">শাখা (Section)</label>
                        <select name="section" class="form-select" required>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="None">None</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="fw-bold small">রোল নম্বর</label>
                        <input type="number" name="roll" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="fw-bold small">পিতার নাম</label>
                        <input type="text" name="father" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="fw-bold small">ধর্ম</label>
                        <select name="religion" class="form-select">
                            <option value="Islam">Islam</option><option value="Hindu">Hindu</option><option value="Christian">Christian</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="fw-bold small">লিঙ্গ</label>
                        <select name="gender" class="form-select">
                            <option value="Male">Male</option><option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold small">অভিভাবকের মোবাইল</label>
                        <input type="number" name="phone" class="form-control" placeholder="017XXXXXXXX" required>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold small">শিক্ষার্থীর ছবি</label>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                    </div>
                    <div class="col-md-12 mt-4">
                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-pill shadow-lg">ভর্তি সম্পন্ন করুন</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'layout_footer.php'; ?>