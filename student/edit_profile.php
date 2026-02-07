<?php
session_start();
require_once '../config/db.php';
if (!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit(); }

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

include '../includes/header.php'; 
?>
<script src="https://cdn.tailwindcss.com"></script>

<div class="max-w-3xl mx-auto my-12 px-4">
    <div class="bg-white rounded-[30px] shadow-2xl overflow-hidden border border-slate-100">
        <div class="bg-gradient-to-r from-slate-800 to-slate-900 p-8 text-white text-center">
            <h3 class="text-3xl font-black uppercase tracking-tighter">Update Profile</h3>
            <p class="text-slate-400 text-sm font-bold mt-2">আপনার ব্যক্তিগত তথ্য এবং লুক পরিবর্তন করুন</p>
        </div>

        <form action="update_profile_process.php" method="POST" enctype="multipart/form-data" class="p-10 space-y-8">
            <!-- বায়ো এডিট -->
            <div class="space-y-2">
                <label class="block text-sm font-black text-slate-700 uppercase tracking-widest">Bio (আপনার সম্পর্কে কিছু লিখুন)</label>
                <textarea name="bio" class="w-full p-5 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none focus:border-blue-500 font-bold text-slate-600 transition-all" rows="3"><?= $user['bio'] ?></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- প্রোফাইল ছবি -->
                <div class="group p-6 border-2 border-dashed border-slate-200 rounded-3xl text-center hover:border-blue-400 transition-all bg-slate-50">
                    <div class="relative inline-block">
                        <img id="p_preview" src="../<?= $user['image'] ?: 'uploads/users/default.png' ?>" class="w-24 h-24 rounded-full mx-auto mb-4 object-cover border-4 border-white shadow-xl">
                        <div class="absolute inset-0 rounded-full bg-black/20 opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center text-white"><i class="fa fa-camera"></i></div>
                    </div>
                    <label class="block text-xs font-black text-blue-600 cursor-pointer uppercase tracking-widest">Change Profile Photo
                        <input type="file" name="profile_img" class="hidden" onchange="document.getElementById('p_preview').src = window.URL.createObjectURL(this.files[0])">
                    </label>
                </div>

                <!-- কভার ফটো -->
                <div class="group p-6 border-2 border-dashed border-slate-200 rounded-3xl text-center hover:border-green-400 transition-all bg-slate-50">
                    <div class="h-24 w-full bg-slate-200 rounded-2xl mb-4 overflow-hidden shadow-inner border-2 border-white">
                        <img id="c_preview" src="../<?= $user['cover_photo'] ?: 'uploads/covers/default.jpg' ?>" class="w-full h-full object-cover">
                    </div>
                    <label class="block text-xs font-black text-green-600 cursor-pointer uppercase tracking-widest">Change Cover Photo
                        <input type="file" name="cover_img" class="hidden" onchange="document.getElementById('c_preview').src = window.URL.createObjectURL(this.files[0])">
                    </label>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-blue-600 text-white font-black py-4 rounded-2xl shadow-xl shadow-blue-100 hover:bg-blue-700 transition transform hover:scale-[1.02] active:scale-95 uppercase tracking-widest">
                    Confirm Updates
                </button>
            </div>
            
            <div class="text-center">
                <a href="dashboard.php" class="text-sm font-black text-slate-400 hover:text-slate-900 transition flex items-center justify-center gap-2">
                    <i class="fa fa-arrow-left"></i> Cancel and Go Back
                </a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>