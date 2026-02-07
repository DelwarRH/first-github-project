<?php 
require_once 'config/db.php'; 
$msg = "";

// নিবন্ধিত একটিভ স্কুলগুলোর তালিকা আনা
$stmt_schools = $pdo->query("SELECT u.id, u.name FROM users u JOIN schools s ON u.id = s.user_id WHERE u.role = 'school' AND u.status = 'active'");
$schools = $stmt_schools->fetchAll();

if (isset($_POST['apply_btn'])) {
    $school_id = $_POST['selected_school'];
    $name = trim($_POST['student_name']);
    $phone = trim($_POST['phone']);
    $class = $_POST['desired_class'];
    $address = trim($_POST['address']);

    // ছবি আপলোড লজিক
    $target_dir = "uploads/students/";
    if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
    $file_name = time() . "_" . $_FILES["photo"]["name"];
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
        try {
            $sql = "INSERT INTO admissions (school_id, student_name, phone, desired_class, photo, address, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')";
            $pdo->prepare($sql)->execute([$school_id, $name, $phone, $class, $target_file, $address]);
            $msg = "<div class='alert alert-success fw-bold text-center shadow-sm'>আপনার আবেদনটি সফলভাবে সংশ্লিষ্ট প্রতিষ্ঠানের পেন্ডিং তালিকায় জমা হয়েছে!</div>";
        } catch (Exception $e) { $msg = "<div class='alert alert-danger'>ভুল হয়েছে: ".$e->getMessage()."</div>"; }
    }
}
include 'includes/header.php'; 
?>

<div class="container mx-auto px-6 py-12">
    <div class="max-w-3xl mx-auto bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
        <div class="bg-blue-700 text-white p-8 text-center">
            <h2 class="text-3xl font-bold m-0"><i class="fa fa-user-graduate me-2"></i> অনলাইন ভর্তি ফরম</h2>
            <p class="opacity-80 mt-2">সঠিক তথ্য প্রদান করে আবেদন সম্পন্ন করুন</p>
        </div>
        <div class="p-8">
            <?php echo $msg; ?>
            <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">১. আপনার শিক্ষা প্রতিষ্ঠানটি নির্বাচন করুন <span class="text-danger">*</span></label>
                    <select name="selected_school" class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-blue-500 outline-none transition" required>
                        <option value="">তালিকায় থাকা নিবন্ধিত প্রতিষ্ঠান</option>
                        <?php foreach($schools as $sch) echo "<option value='{$sch['id']}'>{$sch['name']}</option>"; ?>
                    </select>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div><label class="block text-sm font-bold text-gray-700 mb-2">শিক্ষার্থীর নাম</label><input type="text" name="student_name" class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-blue-500 outline-none" required></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-2">ভর্তি হতে ইচ্ছুক শ্রেণি</label><select name="desired_class" class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 outline-none"><?php $cls=['Six','Seven','Eight','Nine','Ten']; foreach($cls as $c) echo "<option value='$c'>$c</option>"; ?></select></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-2">অভিভাবকের মোবাইল</label><input type="number" name="phone" class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-blue-500 outline-none" required></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-2">শিক্ষার্থীর ছবি</label><input type="file" name="photo" class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 outline-none" required></div>
                </div>
                <div><label class="block text-sm font-bold text-gray-700 mb-2">পূর্ণ ঠিকানা</label><textarea name="address" class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-blue-500 outline-none" rows="2" required></textarea></div>
                <button type="submit" name="apply_btn" class="w-full bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg hover:bg-blue-800 transition transform hover:scale-105 uppercase tracking-wider">আবেদন জমা দিন</button>
            </form>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>