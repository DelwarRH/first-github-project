<?php
session_start();
require_once '../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'school') { header("Location: ../login.php"); exit(); }

$school_id = $_SESSION['user_id'];
// স্কুলের লোগো ও তথ্য আনা
$stmt = $pdo->prepare("SELECT * FROM schools WHERE user_id = ?");
$stmt->execute([$school_id]);
$school_data = $stmt->fetch();

include '../includes/header.php';
?>

<div class="min-h-screen bg-gray-100">
    <!-- School Header Branding -->
    <div class="bg-blue-900 text-white py-6 shadow-lg">
        <div class="container mx-auto px-4 flex flex-col items-center">
            <img src="../<?php echo $school_data['school_logo']; ?>" class="w-24 h-24 rounded-full border-4 border-white mb-3 bg-white">
            <h1 class="text-3xl font-bold"><?php echo $_SESSION['user_name']; ?></h1>
            <p class="opacity-80">স্থাপিত: ১৯৯৮ ইং | EIIN: <?php echo $school_data['eiin_number']; ?> | তালা, সাতক্ষীরা</p>
        </div>
    </div>

    <div class="container mx-auto px-4 py-10">
        <h2 class="text-center text-2xl font-black text-blue-900 mb-8 uppercase tracking-widest border-b-2 border-blue-200 inline-block mx-auto flex justify-center">
            <i class="fas fa-th-large mr-2"></i> এডমিন ড্যাশবোর্ড
        </h2>

        <!-- Dashboard Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <!-- Card: বাণী ব্যবস্থাপনা -->
            <div class="bg-white rounded-xl shadow-md border-t-4 border-pink-500 p-6 text-center hover:shadow-2xl transition">
                <i class="fas fa-user-tie text-4xl text-pink-500 mb-4"></i>
                <h3 class="font-bold text-lg mb-4">বাণী ব্যবস্থাপনা</h3>
                <a href="#" class="inline-block w-full py-2 bg-pink-500 text-white rounded-lg font-bold hover:bg-pink-600">আপডেট করুন</a>
            </div>

            <!-- Card: শিক্ষার্থী তালিকা -->
            <div class="bg-white rounded-xl shadow-md border-t-4 border-green-600 p-6 text-center hover:shadow-2xl transition">
                <i class="fas fa-user-graduate text-4xl text-green-600 mb-4"></i>
                <h3 class="font-bold text-lg mb-4">শিক্ষার্থী তালিকা</h3>
                <a href="manage_students.php" class="inline-block w-full py-2 bg-green-600 text-white rounded-lg font-bold hover:bg-green-700">ম্যানেজ করুন</a>
            </div>

            <!-- Card: ফলাফল (রেজাল্ট) -->
            <div class="bg-white rounded-xl shadow-md border-t-4 border-slate-800 p-6 text-center hover:shadow-2xl transition">
                <i class="fas fa-file-invoice text-4xl text-slate-800 mb-4"></i>
                <h3 class="font-bold text-lg mb-4">ফলাফল (রেজাল্ট)</h3>
                <a href="add_result.php" class="inline-block w-full py-2 bg-slate-800 text-white rounded-lg font-bold hover:bg-slate-900">নম্বর দিন</a>
            </div>

            <!-- Card: ডিজিটাল হাজিরা (System View) -->
            <div class="bg-white rounded-xl shadow-md border-t-4 border-blue-600 p-6 text-center hover:shadow-2xl transition">
                <i class="fas fa-chart-pie text-4xl text-blue-600 mb-4"></i>
                <h3 class="font-bold text-lg mb-4">Student Management</h3>
                <p class="text-xs text-gray-500 mb-3">হাজিরা ও বিস্তারিত রিপোর্টের জন্য</p>
                <a href="dashboard.php" class="inline-block w-full py-2 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700">প্রবেশ করুন</a>
            </div>

            <!-- Card: ফটো গ্যালারি -->
            <div class="bg-white rounded-xl shadow-md border-t-4 border-yellow-500 p-6 text-center hover:shadow-2xl transition">
                <i class="fas fa-images text-4xl text-yellow-500 mb-4"></i>
                <h3 class="font-bold text-lg mb-4">ফটো গ্যালারি</h3>
                <a href="#" class="inline-block w-full py-2 bg-yellow-500 text-white rounded-lg font-bold hover:bg-yellow-600">ছবি আপলোড</a>
            </div>

            <!-- Card: সেটিংস -->
            <div class="bg-white rounded-xl shadow-md border-t-4 border-purple-600 p-6 text-center hover:shadow-2xl transition">
                <i class="fas fa-cogs text-4xl text-purple-600 mb-4"></i>
                <h3 class="font-bold text-lg mb-4">প্রতিষ্ঠান সেটিংস</h3>
                <a href="settings.php" class="inline-block w-full py-2 bg-purple-600 text-white rounded-lg font-bold hover:bg-purple-700">পরিবর্তন করুন</a>
            </div>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>