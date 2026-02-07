<?php include 'layout_header.php'; ?>
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-success text-white p-3 text-center">
                <h4 class="fw-bold m-0"><i class="fa fa-user-plus me-2"></i> নতুন শিক্ষক / স্টাফ যুক্ত করুন</h4>
            </div>
            <div class="card-body p-4 bg-light">
                <form action="save_user_process.php" method="POST" enctype="multipart/form-data" class="row g-3">
                    <div class="col-md-12">
                        <label class="fw-bold small">পূর্ণ নাম (বাংলা/ইংরেজি)</label>
                        <input type="text" name="name" class="form-control shadow-sm" placeholder="যেমন: মো: কামরুল হাসান" required>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold small">ইমেইল (লগইনের জন্য)</label>
                        <input type="email" name="email" class="form-control shadow-sm" placeholder="example@mail.com" required>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold small">পাসওয়ার্ড</label>
                        <input type="password" name="password" class="form-control shadow-sm" placeholder="******" required>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold small">পদবী / রোল</label>
                        <select name="role" class="form-select shadow-sm" required>
                            <option value="teacher">শিক্ষক (Teacher)</option>
                            <option value="operator">কম্পিউটার অপারেটর</option>
                            <option value="staff">অন্যান্য স্টাফ</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold small">প্রধান বিষয় (শিক্ষকের জন্য)</label>
                        <input type="text" name="subject" class="form-control shadow-sm" placeholder="যেমন: গণিত">
                    </div>
                    <div class="col-md-12">
                        <label class="fw-bold small">প্রোফাইল ছবি</label>
                        <input type="file" name="image" class="form-control shadow-sm" accept="image/*">
                    </div>
                    <div class="col-md-12 mt-4">
                        <button type="submit" class="btn btn-success w-100 py-2 fw-bold rounded-pill shadow">তথ্য সংরক্ষণ করুন</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'layout_footer.php'; ?>