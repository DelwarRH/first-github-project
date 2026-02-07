<?php 
session_start();
// সিকিউরিটি চেক: যদি টেম্পোরারি ইউজার আইডি না থাকে তবে লগইনে পাঠাবে
if (!isset($_SESSION['temp_user_id'])) { 
    header("Location: student_login.php"); 
    exit(); 
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>নতুন পাসওয়ার্ড সেট করুন - আস্থা</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Hind Siliguri', sans-serif; }
        .setup-card { backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.95); }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4" style="background-image: url('https://img.freepik.com/free-vector/abstract-blue-geometric-shapes-background_1035-17545.jpg'); background-size: cover;">

    <div class="max-w-md w-full setup-card p-8 rounded-3xl shadow-2xl border border-white">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-600 rounded-2xl shadow-lg mb-4 transform rotate-6">
                <i class="fas fa-key text-white text-3xl"></i>
            </div>
            <h2 class="text-3xl font-black text-slate-800">পাসওয়ার্ড সেট করুন</h2>
            <p class="text-slate-500 font-semibold mt-1">নিরাপত্তার জন্য একটি গোপন পাসওয়ার্ড দিন</p>
        </div>

        <form action="auth/save_new_pass.php" method="POST" class="space-y-5">
            
            <div class="space-y-1">
                <label class="text-sm font-bold text-slate-700 ml-1">নতুন পাসওয়ার্ড</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-4 top-4 text-slate-400"></i>
                    <input type="password" name="new_pass" placeholder="••••••••" class="w-full pl-11 pr-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-green-500 focus:bg-white outline-none transition-all font-semibold" required minlength="6">
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-sm font-bold text-slate-700 ml-1">পাসওয়ার্ডটি পুনরায় লিখুন</label>
                <div class="relative">
                    <i class="fas fa-check-double absolute left-4 top-4 text-slate-400"></i>
                    <input type="password" name="confirm_pass" placeholder="••••••••" class="w-full pl-11 pr-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-green-500 focus:bg-white outline-none transition-all font-semibold" required>
                </div>
            </div>

            <!-- সাবমিট বাটন -->
            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-black py-4 rounded-2xl shadow-xl transition-all active:scale-95 uppercase tracking-wider flex items-center justify-center gap-3 mt-6">
                <span>পাসওয়ার্ড নিশ্চিত করুন</span>
                <i class="fas fa-save"></i>
            </button>
        </form>

        <div class="mt-8 text-center border-t border-slate-100 pt-6">
            <p class="text-xs text-gray-400 font-bold uppercase">স্মার্ট সুরক্ষা - আস্থা ডিজিটাল সিস্টেম</p>
        </div>
    </div>

</body>
</html>