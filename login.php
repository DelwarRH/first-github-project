<?php require_once 'config/db.php'; include 'includes/header.php'; ?>

<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="bg-white p-5 rounded-4 shadow-lg border border-light">
                <div class="text-center mb-4">
                    <h2 class="fw-bold text-dark">লগইন করুন</h2>
                    <p class="text-muted small">ডিজিটাল ডাটা ম্যানেজমেন্ট সিস্টেম - আস্থা</p>
                </div>
                
                <form action="auth/login_process.php" method="POST">
                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted">ইমেইল ঠিকানা</label>
                        <input name="email" type="email" required class="form-control form-control-lg bg-light border-0" placeholder="khstala@gmail.com">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted">পাসওয়ার্ড</label>
                        <input name="password" type="password" required class="form-control form-control-lg bg-light border-0" placeholder="******">
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="rem">
                            <label class="form-check-label small" for="rem">তথ্য মনে রাখুন</label>
                        </div>
                        <a href="#" class="small text-decoration-none">পাসওয়ার্ড ভুলে গেছেন?</a>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow">লগইন করুন</button>
                </form>
                
                <div class="text-center mt-4">
                    <p class="small text-muted">নতুন প্রতিষ্ঠান? <a href="register.php" class="fw-bold">এখান থেকে রেজিস্ট্রেশন করুন</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>