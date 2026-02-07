<?php 
session_start();
require_once '../config/db.php'; 

// কোন স্কুলের জন্য আবেদন করা হচ্ছে?
$school_id = $_GET['school_id'] ?? $_POST['school_id'] ?? null;
if (!$school_id) { die("ভুল ইউআরএল! প্রতিষ্ঠানের আইডি পাওয়া যায়নি।"); }

$msg = "";

if (isset($_POST['apply_btn'])) {
    $name = $_POST['student_name'];
    $father = $_POST['father_name'];
    $mother = $_POST['mother_name'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $class = $_POST['desired_class'];
    $address = $_POST['address'];

    // ছবি আপলোড লজিক
    $target_dir = "../uploads/students/";
    if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
    
    $file_name = time() . "_" . $_FILES["photo"]["name"];
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
        try {
            $sql = "INSERT INTO admissions (school_id, student_name, father_name, mother_name, dob, gender, phone, desired_class, address, photo, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$school_id, $name, $father, $mother, $dob, $gender, $phone, $class, $address, $target_file]);
            
            $msg = "<div class='alert alert-success shadow-sm fw-bold'>অভিনন্দন! আপনার আবেদনটি সফলভাবে জমা হয়েছে।</div>";
        } catch (Exception $e) {
            $msg = "<div class='alert alert-danger'>ত্রুটি: " . $e->getMessage() . "</div>";
        }
    }
}

include '../includes/header.php'; 
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-success text-white text-center py-4">
                    <h3 class="fw-bold m-0"><i class="fa fa-user-graduate me-2"></i> অনলাইন ভর্তি আবেদন ফরম</h3>
                    <p class="m-0 small opacity-75">সঠিক তথ্য প্রদান করে আপনার ভর্তি নিশ্চিত করুন</p>
                </div>
                
                <div class="card-body p-5 bg-light">
                    <?php echo $msg; ?>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="school_id" value="<?php echo $school_id; ?>">
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">শিক্ষার্থীর নাম (বাংলায়)</label>
                                <input type="text" name="student_name" class="form-control shadow-sm" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">ভর্তি হতে ইচ্ছুক শ্রেণি</label>
                                <select name="desired_class" class="form-select shadow-sm" required>
                                    <option value="Six">Six</option><option value="Ten">Ten</option>
                                </select>
                            </div>
                            <!-- বাকি সব ইনপুট আপনার আগের কোড অনুযায়ী এখানে থাকবে... -->
                            <div class="col-md-12">
                                <label class="form-label fw-bold">পাসপোর্ট সাইজ ছবি</label>
                                <input type="file" name="photo" class="form-control shadow-sm" accept="image/*" required>
                            </div>
                        </div>

                        <div class="text-center mt-5">
                            <button type="submit" name="apply_btn" class="btn btn-success btn-lg px-5 rounded-pill shadow fw-bold">আবেদন জমা দিন</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>