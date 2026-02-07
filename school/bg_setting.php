<?php 
include 'layout_header.php'; 
$msg = "";

if (isset($_POST['update_bg'])) {
    $target_dir = "../uploads/bg/";
    if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
    
    $file_ext = pathinfo($_FILES["bg_image"]["name"], PATHINFO_EXTENSION);
    $file_name = "bg_" . $school_id . "." . $file_ext;
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES["bg_image"]["tmp_name"], $target_file)) {
        $db_path = "uploads/bg/" . $file_name;
        $stmt = $pdo->prepare("UPDATE schools SET bg_image = ? WHERE user_id = ?");
        $stmt->execute([$db_path, $school_id]);
        $msg = "<div class='alert alert-success fw-bold'>ব্যাকগ্রাউন্ড সফলভাবে পরিবর্তন হয়েছে!</div>";
        echo "<script>setTimeout(()=> { window.location.href='dashboard.php'; }, 2000);</script>";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow border-0 rounded-4 p-4">
            <h5 class="fw-bold mb-4 text-center border-bottom pb-2">ড্যাশবোর্ড ব্যাকগ্রাউন্ড পরিবর্তন</h5>
            <?php echo $msg; ?>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-4 text-center">
                    <p class="small text-muted">বর্তমান ব্যাকগ্রাউন্ডের প্রিভিউ:</p>
                    <img src="<?php echo $bg_url; ?>" class="img-fluid rounded border shadow-sm" style="max-height: 150px;">
                </div>
                <div class="mb-3">
                    <label class="fw-bold small">নতুন ছবি নির্বাচন করুন (JPG/PNG)</label>
                    <input type="file" name="bg_image" class="form-control" accept="image/*" required>
                </div>
                <button type="submit" name="update_bg" class="btn btn-primary w-100 fw-bold py-2 shadow-lg">আপডেট করুন</button>
            </form>
        </div>
    </div>
</div>

<?php include 'layout_footer.php'; ?>