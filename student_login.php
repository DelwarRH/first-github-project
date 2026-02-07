<?php 
require_once 'config/db.php'; 

// স্কুলগুলোর তালিকা আনা - এখানে u.id (User ID) নিচ্ছি যা students টেবিলের school_id এর সাথে মিলবে
$stmt = $pdo->query("SELECT id, name FROM users WHERE role = 'school' AND status = 'active'");
$schools = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>শিক্ষার্থী লগইন - আস্থা</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Hind Siliguri', sans-serif; }
        .login-card { backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.95); }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4" style="background-image: url('https://img.freepik.com/free-vector/abstract-blue-geometric-shapes-background_1035-17545.jpg'); background-size: cover;">

    <div class="max-w-md w-full login-card p-8 rounded-3xl shadow-2xl border border-white">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded-2xl shadow-lg mb-4 transform -rotate-6">
                <i class="fas fa-user-graduate text-white text-3xl"></i>
            </div>
            <h2 class="text-3xl font-black text-slate-800">শিক্ষার্থী লগইন</h2>
            <p class="text-slate-500 font-semibold mt-1">আস্থা ডিজিটাল এডুকেশন সিস্টেম</p>
        </div>

        <form action="auth/student_login_process.php" method="POST" class="space-y-5">
            
            <!-- স্কুল সিলেকশন -->
            <div class="space-y-1">
                <label class="text-sm font-bold text-slate-700 ml-1">আপনার শিক্ষা প্রতিষ্ঠান</label>
                <div class="relative">
                    <i class="fas fa-university absolute left-4 top-4 text-slate-400"></i>
                    <select name="school_id" class="w-full pl-11 pr-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-blue-500 focus:bg-white outline-none transition-all appearance-none font-semibold text-slate-700" required>
                        <option value="">প্রতিষ্ঠান নির্বাচন করুন</option>
                        <?php foreach($schools as $sch): ?>
                            <option value="<?= $sch['id'] ?>"><?= $sch['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <!-- শ্রেণি -->
                <div class="space-y-1">
                    <label class="text-sm font-bold text-slate-700 ml-1">শ্রেণি</label>
                    <select name="class" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-blue-500 outline-none font-semibold text-slate-700" required>
                        <option value="Six">Six</option>
                        <option value="Seven">Seven</option>
                        <option value="Eight">Eight</option>
                        <option value="Nine">Nine</option>
                        <option value="Ten">Ten</option>
                    </select>
                </div>
                <!-- রোল -->
                <div class="space-y-1">
                    <label class="text-sm font-bold text-slate-700 ml-1">রোল নম্বর</label>
                    <input type="number" name="roll" placeholder="01" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-blue-500 outline-none font-semibold text-slate-700" required>
                </div>
            </div>

            <!-- মোবাইল নম্বর -->
            <div class="space-y-1">
                <label class="text-sm font-bold text-slate-700 ml-1">অভিভাবকের মোবাইল নম্বর</label>
                <div class="relative">
                    <i class="fas fa-phone-alt absolute left-4 top-4 text-slate-400"></i>
                    <input type="number" name="phone" placeholder="017XXXXXXXX" class="w-full pl-11 pr-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-blue-500 outline-none font-semibold text-slate-700" required>
                </div>
            </div>

            <!-- সাবমিট বাটন -->
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-2xl shadow-xl transition-all active:scale-95 uppercase tracking-wider flex items-center justify-center gap-3">
                <span>লগইন করুন</span>
                <i class="fas fa-arrow-right"></i>
            </button>
        </form>

        <!-- Footer Footer -->
        <div class="mt-8 text-center border-t border-slate-100 pt-6">
            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Digital Data Management - ASTHA</p>
            <a href="index.php" class="inline-block mt-4 text-sm text-blue-600 font-bold hover:underline">
                <i class="fas fa-home mr-1"></i> হোমপেজে ফিরে যান
            </a>
        </div>
    </div>

</body>
</html>