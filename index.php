<?php 
require_once 'config/db.php'; 
// ইনডেক্স পেজে কমন হেডার দরকার নেই কারণ ডিজাইন আলাদা
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>আস্থা - স্মার্ট এডুকেশন</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white">

<!-- Simple Branding Header -->
<div class="container py-3 flex justify-between items-center border-b">
    <div class="flex items-center space-x-2">
        <img src="https://upload.wikimedia.org/wikipedia/commons/8/84/Government_Seal_of_Bangladesh.svg" width="40">
        <span class="text-2xl font-bold text-green-700">আস্থা</span>
    </div>
    <div class="space-x-4">
        <a href="login.php" class="px-4 py-2 border rounded-full text-green-700">লগইন</a>
        <a href="register.php" class="px-4 py-2 bg-green-700 text-white rounded-full">রেজিস্ট্রেশন</a>
        <a href="student_login.php" class="px-5 py-2 bg-yellow-500 text-blue-900 font-bold rounded-full shadow-md hover:bg-yellow-400 transition">শিক্ষার্থী লগইন</a>
    </div>
</div>

<section class="container mx-auto px-6 py-16 flex flex-col md:flex-row items-center">
    <div class="md:w-1/2">
        <h1 class="text-5xl font-black text-slate-800 leading-tight">শিক্ষার মান উন্নয়ন ও <br><span class="text-green-600">ডিজিটাল মনিটরিং</span></h1>
        <p class="mt-6 text-slate-600 text-lg">উপজেলা ও জেলা প্রশাসনের সরাসরি তত্ত্বাবধানে পরিচালিত ডিজিটাল শিক্ষা ব্যবস্থাপনা প্ল্যাটফর্ম।</p>
        
        <div class="mt-8 p-2 border rounded-xl flex shadow-lg max-w-lg">
            <select class="flex-1 border-none outline-none px-4">
                <option>ভর্তি হতে ইচ্ছুক প্রতিষ্ঠানটি বেছে নিন...</option>
            </select>
            <button class="bg-yellow-500 px-6 py-3 rounded-lg font-bold">আবেদন করুন</button>
        </div>
    </div>
    <div class="md:w-1/2">
        <img src="https://img.freepik.com/free-vector/data-management-concept-illustration_114360-1011.jpg" class="w-full">
    </div>
</section>

<!-- Stats Image (As per your Image 1) -->
<div class="container mx-auto grid grid-cols-1 md:grid-cols-3 gap-4 py-10">
    <div class="p-6 border rounded-xl shadow-sm text-center">
        <h3 class="text-3xl font-bold">২০০+</h3>
        <p>শিক্ষা প্রতিষ্ঠান</p>
    </div>
    <div class="p-6 border rounded-xl shadow-sm text-center">
        <h3 class="text-3xl font-bold">১০,০০০+</h3>
        <p>মোট শিক্ষার্থী</p>
    </div>
    <div class="p-6 border rounded-xl shadow-sm text-center">
        <h3 class="text-3xl font-bold">তালা</h3>
        <p>সাতক্ষীরা</p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>