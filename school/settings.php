<?php 
include 'layout_header.php'; 
// বর্তমান তথ্য আনা
$stmt = $pdo->prepare("SELECT * FROM schools WHERE user_id = ?");
$stmt->execute([$school_id]);
$sch_info = $stmt->fetch();
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-dark text-white p-3"><h5 class="m-0 fw-bold">প্রতিষ্ঠান প্রোফাইল সেটিংস</h5></div>
            <div class="card-body p-4">
                <form action="update_school_process.php" method="POST" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-md-12 text-center mb-3">
                            <img src="<?php echo $logo_url; ?>" width="100" class="rounded-circle border p-1 mb-2">
                            <input type="file" name="logo" class="form-control form-control-sm mx-auto" style="max-width: 250px;">
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold small">প্রতিষ্ঠানের নাম (English)</label>
                            <input type="text" name="school_name" class="form-control" value="<?php echo $_SESSION['user_name']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold small">EIIN নম্বর</label>
                            <input type="text" name="eiin" class="form-control" value="<?php echo $sch_info['eiin_number']; ?>" required>
                        </div>
                        <div class="col-md-12 mt-4 text-center">
                            <button type="submit" class="btn btn-success px-5 fw-bold rounded-pill">সেভ পরিবর্তন</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'layout_footer.php'; ?>