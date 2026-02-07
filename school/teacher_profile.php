<?php 
session_start();
require_once '../config/db.php'; 

// এটি নিশ্চিত করুন যে সেশন আইডি আছে কি না
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') { 
    header("Location: ../login.php"); 
    exit(); 
}

$user_id = $_SESSION['user_id'];
// ডাটাবেজ থেকে তথ্য আনা...
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

include '../includes/header.php'; 
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-dark text-white p-4 text-center">
                    <h4 class="fw-bold m-0">প্রোফাইল আপডেট করুন</h4>
                </div>
                <div class="card-body p-5">
                    <form action="update_profile_process.php" method="POST" enctype="multipart/form-data">
                        <div class="text-center mb-4">
                            <img src="../<?php echo !empty($user['image']) ? $user['image'] : 'uploads/users/default.png'; ?>" class="rounded-circle border p-1 shadow-sm" width="120" height="120" id="preview">
                            <div class="mt-2">
                                <label class="btn btn-sm btn-outline-primary rounded-pill">
                                    ছবি পরিবর্তন <input type="file" name="profile_img" hidden onchange="document.getElementById('preview').src = window.URL.createObjectURL(this.files[0])">
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold">শিক্ষকের নাম</label>
                            <input type="text" name="name" class="form-control rounded-3" value="<?php echo $user['name']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold">বিষয় (Subject)</label>
                            <input type="text" name="subject" class="form-control rounded-3" value="<?php echo $user['subject']; ?>" required>
                        </div>
                        <div class="mb-4">
                            <label class="small fw-bold">ইমেইল ঠিকানা</label>
                            <input type="email" name="email" class="form-control rounded-3" value="<?php echo $user['email']; ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2 rounded-3 shadow">তথ্য আপডেট করুন</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>