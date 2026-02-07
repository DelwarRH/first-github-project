<?php include 'includes/header.php'; $user_id = $_GET['user_id']; ?>

<div class="min-h-screen bg-gray-100 flex items-center justify-center py-10">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-2xl overflow-hidden border-t-8 border-[#D12053]">
        <div class="p-8 text-center">
            <img src="https://www.logo.wine/a/logo/BKash/BKash-bKash-Logo.wine.svg" class="w-40 mx-auto mb-4">
            <h2 class="text-2xl font-black text-gray-800">নিরাপদ পেমেন্ট গেটওয়ে</h2>
            <p class="text-gray-500 mb-6 font-bold">আস্থা ডিজিটাল সিস্টেম অ্যাক্টিভেশন</p>
            
            <div class="bg-pink-50 rounded-2xl p-6 mb-8 border-2 border-dashed border-pink-200">
                <span class="text-sm font-bold text-pink-600 block mb-1">প্রদেয় মোট টাকা</span>
                <span class="text-5xl font-black text-gray-800">৳ ৫,০০০</span>
            </div>

            <ul class="text-left space-y-3 mb-8 text-sm font-bold text-gray-600">
                <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2"></i> আজীবনের জন্য প্রশাসনিক এক্সেস</li>
                <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2"></i> ১০,০০০ পর্যন্ত ফ্রি এসএমএস</li>
                <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2"></i> ২৪/৭ আইসিটি সাপোর্ট</li>
            </ul>

            <form action="auth/payment_process.php" method="POST">
                <input type="hidden" name="user_id" value="<?= $user_id ?>">
                <input type="hidden" name="amount" value="5000">
                <button type="submit" class="w-full bg-[#D12053] hover:bg-[#b01a46] text-white py-4 rounded-2xl font-black text-lg shadow-xl transition transform active:scale-95">
                    বিকাশ দিয়ে পেমেন্ট করুন
                </button>
            </form>
            
            <p class="mt-6 text-xs text-gray-400 font-bold uppercase tracking-widest">Powered by Tech Space BD</p>
        </div>
    </div>
</div>