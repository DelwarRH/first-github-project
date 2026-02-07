<?php 
include 'includes/header.php'; 
include 'config/db.php'; 
?>

<div class="min-h-screen flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full bg-white p-8 rounded-xl shadow-lg border border-gray-100">
        <h2 class="text-3xl font-bold text-center text-blue-800 mb-8">প্রতিষ্ঠান রেজিস্ট্রেশন</h2>
        
        <form action="auth/register_process.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">প্রতিষ্ঠানের নাম</label>
                <input type="text" name="school_name" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">ইআইআইএন (EIIN) নম্বর</label>
                <input type="text" name="eiin" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">বিভাগ</label>
                    <select name="division" class="w-full px-4 py-2 border rounded-lg">
                        <option value="1">Khulna</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">জেলা</label>
                    <select name="district" class="w-full px-4 py-2 border rounded-lg">
                        <option value="2">Satkhira</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">উপজেলা নির্বাচন করুন</label>
                <select name="upazila" class="w-full px-4 py-2 border rounded-lg">
                    <option value="3">Tala</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">ইমেইল ঠিকানা</label>
                <input type="email" name="email" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">পাসওয়ার্ড</label>
                <input type="password" name="password" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 transition shadow-md">
                রেজিস্ট্রেশন করুন ও পেমেন্টে যান
            </button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>