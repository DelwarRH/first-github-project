<?php 
session_start();
if (!isset($_SESSION['temp_user_id'])) { header("Location: student_login.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>পাসওয়ার্ড দিন - আস্থা</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4" style="background-image: url('https://img.freepik.com/free-vector/abstract-blue-geometric-shapes-background_1035-17545.jpg'); background-size: cover;">
    <div class="max-w-md w-full bg-white p-8 rounded-3xl shadow-2xl border border-white">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-black text-slate-800">আপনার পাসওয়ার্ড দিন</h2>
            <p class="text-slate-500 font-semibold mt-1">লগইন সম্পন্ন করতে পাসওয়ার্ডটি লিখুন</p>
        </div>

        <form action="auth/verify_student_pass.php" method="POST" class="space-y-5">
            <div class="relative">
                <input type="password" name="password" placeholder="পাসওয়ার্ড লিখুন" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-blue-500 outline-none font-semibold text-slate-700" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-2xl shadow-xl transition-all uppercase">
                প্রবেশ করুন
            </button>
        </form>
    </div>
</body>
</html>