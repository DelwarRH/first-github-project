<?php
session_start();
require_once '../config/db.php';

// ১. সিকিউরিটি চেক
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') { 
    header("Location: ../login.php"); exit(); 
}

$user_id = $_SESSION['user_id'];
$school_id = $_SESSION['school_id'];

// ২. ডাটাবেজ থেকে তথ্য আনা (একক কুয়েরিতে লোগো সহ)
$stmt = $pdo->prepare("SELECT u.*, scl.school_logo, scl.bg_image, sch.name as school_name 
                       FROM users u 
                       JOIN schools scl ON u.school_id = scl.user_id 
                       JOIN users sch ON scl.user_id = sch.id 
                       WHERE u.id = ?");
$stmt->execute([$user_id]);
$teacher = $stmt->fetch();

// আনপড় মেসেজ সংখ্যা
$unread_total = $pdo->query("SELECT COUNT(*) FROM chat_messages WHERE receiver_id = $user_id AND is_read = 0")->fetchColumn();

include '../includes/header.php';
?>

<!-- Tailwind CSS, Google Fonts & Icons -->
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    body { background-color: #f0f2f5; font-family: 'Hind Siliguri', sans-serif; margin:0; padding:0; overflow-x: hidden; }
    header, nav, .school-title-bar, .top-nav { display: none !important; } /* গ্লোবাল নেভিগেশন হাইড */

    /* টপ বার */
    .astha-top-bar { background: #fff; border-bottom: 1px solid #e5e7eb; position: sticky; top: 0; z-index: 1000; padding: 10px 0; }
    .school-name-top { color: #cc0000; font-weight: 900; text-transform: uppercase; font-size: 20px; }

    /* প্রোফাইল ও কভার */
    .cover-box { height: 250px; background: linear-gradient(135deg, #064e3b 0%, #059669 100%); position: relative; border-radius: 0 0 20px 20px; overflow:hidden; }
    .profile-info-wrap { margin-top: -85px; padding: 0 50px 20px; display: flex; align-items: flex-end; position: relative; z-index: 10; }
    .profile-pic-box img { width: 170px; height: 170px; border: 6px solid white; border-radius: 50%; object-fit: cover; box-shadow: 0 4px 15px rgba(0,0,0,0.15); background: white; }

    /* রঙিন কার্ড ডিজাইন */
    .astha-card { border-radius: 18px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 25px; padding: 22px; border: 1px solid #eef2f6; background: white; transition: 0.3s; }
    .card-intro { background-color: #f8fafc; border-left: 5px solid #3b82f6; }
    .card-shortcuts { background: #1a202c; color: white; border: none; }
    .card-messenger { background: #f0fdf4; border-top: 5px solid #10b981; height: 80vh; display: flex; flex-direction: column; padding: 0; overflow: hidden; }

    /* মেসেঞ্জার পপ-আপ */
    #messengerPopup { 
        position: fixed; bottom: 0; right: 50px; width: 340px; background: white; 
        border-radius: 15px 15px 0 0; box-shadow: 0 10px 40px rgba(0,0,0,0.2); z-index: 5000; 
        display: none; border: 1px solid #ddd; overflow: hidden;
    }
    .msg-header { background: linear-gradient(to right, #1e3a8a, #3b82f6); color: white; padding: 12px; display: flex; justify-content: space-between; align-items: center; }
    .chat-body { height: 320px; overflow-y: auto; background-color: #e5ddd5; background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png'); background-size: cover; padding: 15px; display: flex; flex-direction: column; }

    /* ইমোজি পিকার */
    .emoji-picker { position: absolute; bottom: 75px; right: 20px; background: white; border: 1px solid #ddd; border-radius: 15px; padding: 10px; display: none; grid-template-columns: repeat(4, 1fr); gap: 10px; z-index: 6000; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }

    .custom-scroll::-webkit-scrollbar { width: 4px; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 10px; }
    .cursor-pointer { cursor: pointer; }

    /* মেনুবার একদম সোজা করার ফাইনাল ফিক্স */
    .teacher-custom-nav {
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        background: white;
        border-top: 1px solid #eee;
        height: 60px !important; /* পুরো বারের একটি নির্দিষ্ট উচ্চতা */
        padding: 0 !important;
    }

    .teacher-custom-nav .nav-link {
        display: flex !important;
        align-items: center !important; /* খাড়াভাবে মাঝখানে রাখবে */
        justify-content: center !important;
        height: 60px !important; /* লিঙ্কের উচ্চতা বারের সমান হবে */
        padding: 0 25px !important;
        margin: 0 !important;
        font-weight: 800 !important;
        font-size: 13px !important;
        color: #4b5563 !important;
        text-transform: uppercase;
        border-bottom: 4px solid transparent !important;
        transition: all 0.3s;
        line-height: 1 !important;
    }

    /* আইকন ফিক্স */
    .teacher-custom-nav .nav-link i {
        margin-right: 8px !important;
        font-size: 18px !important; /* আইকন একটু বড় ও স্পষ্ট হবে */
        line-height: 0 !important; /* আইকন যাতে টেক্সটকে ঠেলে নিচে না নামায় */
        display: inline-flex !important;
        align-items: center !important;
    }

    .teacher-custom-nav .active {
        color: #1877f2 !important;
        border-bottom-color: #1877f2 !important;
    }

    .teacher-custom-nav .nav-link:hover {
        background-color: #f8fafc;
        color: #1877f2 !important;
    }
</style>

<!-- ১. ব্র্যান্ডিং হেডার বার -->
<div class="astha-top-bar shadow-sm">
    <div class="max-w-[1440px] mx-auto px-10 flex justify-between items-center">
        <div class="flex items-center gap-4 text-decoration-none">
            <img src="../<?= $teacher['school_logo'] ?: 'uploads/logo.jpg' ?>" class="w-11 h-11 rounded-full border shadow-sm object-cover">
            <h4 class="school-name-top tracking-tighter"><?= $teacher['school_name'] ?></h4>
        </div>
        <div class="flex items-center gap-5">
            <div class="relative cursor-pointer text-slate-600 hover:text-blue-600 transition" onclick="toggleMessengerSidebar()">
                <i class="fab fa-facebook-messenger text-2xl"></i>
                <?php if($unread_total > 0): ?><span class="absolute -top-2 -right-2 bg-red-600 text-white text-[10px] w-5 h-5 flex items-center justify-center rounded-full border-2 border-white"><?= $unread_total ?></span><?php endif; ?>
            </div>
            <a href="../auth/logout.php" class="bg-red-500 text-white px-5 py-2 rounded-xl font-black text-xs shadow-lg hover:bg-red-700 transition">LOGOUT</a>
        </div>
    </div>
</div>

<!-- ২. প্রোফাইল হেডার -->
<div class="bg-white shadow-sm border-b">
    <div class="max-w-[1440px] mx-auto">
        <div class="cover-box shadow-inner">
             <h2 class="absolute inset-0 flex items-center justify-center text-white/10 font-black text-6xl uppercase pointer-events-none"><?= $teacher['school_name'] ?></h2>
        </div>
        
        <div class="profile-info-wrap">
            <div class="profile-pic-box relative">
                <?php $p_img = !empty($teacher['image']) ? '../'.$teacher['image'] : 'https://via.placeholder.com/150'; ?>
                <img src="<?= $p_img ?>" class="profile-image shadow-2xl border-4 border-white">
                <label class="absolute bottom-2 right-2 bg-slate-100 p-2 rounded-full border border-white cursor-pointer shadow-sm hover:bg-slate-200 transition"><i class="fa fa-camera"></i></label>
            </div>
            <div class="info-details ml-8 mb-4 flex-grow">
                <h1 class="text-3xl font-black text-slate-800 m-0"><?= $teacher['name'] ?></h1>
                <p class="text-slate-500 font-bold text-lg">বিষয়: <span class="text-blue-600"><?= $teacher['subject'] ?></span> | পদবী: শিক্ষক</p>
            </div>
            <div class="pb-5 flex gap-2">
                <a href="teacher_profile.php" class="bg-blue-600 text-white px-8 py-3 rounded-2xl font-black shadow-xl flex items-center gap-2 transition hover:bg-blue-700"><i class="fa fa-user-edit"></i> Profile Edit</a>
            </div>
        </div>

        <div class="flex justify-center border-t py-1 overflow-x-auto whitespace-nowrap bg-white sticky top-[62px] z-[900]">
            <a href="teacher_dashboard.php" class="px-6 py-3 text-blue-600 font-black border-b-4 border-blue-600 uppercase text-xs no-underline">Timeline</a>
            <a href="lesson_history.php" class="px-6 py-3 text-slate-500 font-black hover:bg-slate-50 uppercase text-xs no-underline">Lessons</a>
            <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#guardianSMSModal" style="color: #cc0000;"><i class="fa fa-envelope-open-text me-2"></i>Guardian Notice</a>
            <a href="student_society.php" class="px-6 py-3 text-slate-500 font-black hover:bg-slate-50 uppercase text-xs no-underline">Society</a>
        </div>
    </div>
</div>

<div class="max-w-[1440px] mx-auto px-4 lg:px-10 mt-8 pb-20">
    <div class="grid grid-cols-12 gap-8">
        
        <!-- ৩. বাম কলাম (Intro & Shortcuts) -->
        <div class="col-span-12 lg:col-span-3">
            <div class="astha-card card-intro p-6">
                <h5 class="font-black text-slate-800 mb-5 border-b pb-3 uppercase text-[11px] tracking-widest"><i class="fa fa-info-circle me-2"></i> Work Info</h5>
                <div class="space-y-4 text-sm font-bold text-slate-600">
                    <div class="flex items-center gap-3"><i class="fa fa-university text-blue-500 w-5"></i> <?= $teacher['school_name'] ?></div>
                    <div class="flex items-center gap-3"><i class="fa fa-book-open text-green-500 w-5"></i> Dept: <?= $teacher['subject'] ?></div>
                    <div class="flex items-center gap-3"><i class="fa fa-map-marker-alt text-red-500 w-5"></i> Tala, Satkhira</div>
                </div>
            </div>

            <div class="astha-card card-shortcuts p-6">
                <h6 class="font-black text-white/50 mb-5 border-b border-white/10 pb-3 uppercase text-[10px] tracking-widest">Shortcuts</h6>
                <div class="space-y-2">
                    <a href="add_result.php" class="flex items-center gap-4 p-3 hover:bg-white/10 rounded-xl transition text-white no-underline">
                        <i class="fa fa-poll text-blue-400 text-xl"></i> ফলাফল প্রস্তুত
                    </a>
                    <a href="class_routine.php" class="flex items-center gap-4 p-3 hover:bg-white/10 rounded-xl transition text-white no-underline">
                        <i class="fa fa-calendar-alt text-green-400 text-xl"></i> ক্লাস রুটিন
                    </a>
                    <a href="teacher_gallery.php" class="flex items-center gap-4 p-3 hover:bg-white/10 rounded-xl transition text-white no-underline">
                        <i class="fa fa-images text-red-400 text-xl"></i> ফটো গ্যালারি
                    </a>
                </div>
            </div>
        </div>

        <!-- ৪. মাঝখানের কলাম (News Feed) -->
        <div class="col-span-12 lg:col-span-6">
            <div class="astha-card p-6 mb-6 border-t-4 border-blue-600">
                <div class="flex gap-4 mb-5">
                    <img src="<?= $p_img ?>" class="w-12 h-12 rounded-full border shadow-sm">
                    <div class="bg-slate-100 hover:bg-slate-200 transition-all flex-1 py-3 px-6 rounded-full text-slate-500 font-bold cursor-pointer border" data-bs-toggle="modal" data-bs-target="#lessonModal">
                        ছাত্রদের জন্য আজ কি লেসন পাবলিশ করবেন?
                    </div>
                </div>
                <div class="flex justify-around border-t pt-4 text-slate-500 font-bold text-xs uppercase">
                    <span class="cursor-pointer hover:bg-slate-50 px-5 py-2 rounded-xl transition" data-bs-toggle="modal" data-bs-target="#lessonModal"><i class="fa fa-file-image text-green-500 text-lg me-2"></i> ছবি/ফাইল</span>
                    <span class="cursor-pointer hover:bg-slate-50 px-5 py-2 rounded-xl transition" data-bs-toggle="modal" data-bs-target="#feelingModal"><i class="fa fa-smile text-yellow-500 text-lg me-2"></i> অনুভূতি</span>
                </div>
            </div>

            <!-- নিউজ ফিড লুপ -->
            <div class="space-y-8">
                <?php
                $stmt_l = $pdo->prepare("SELECT * FROM lessons WHERE teacher_id = ? ORDER BY date DESC LIMIT 10");
                $stmt_l->execute([$user_id]);
                while($row = $stmt_l->fetch()): ?>
                    <div class="astha-card p-0 overflow-hidden shadow-md border border-slate-200">
                        <div class="flex items-center p-4 bg-slate-50">
                            <img src="<?= $p_img ?>" class="w-11 h-11 rounded-full border me-3">
                            <div class="leading-tight">
                                <h6 class="font-black text-slate-800 m-0"><?= $teacher['name'] ?></h6>
                                <small class="text-slate-400 font-bold"><?= date('d M \a\t h:i A', strtotime($row['date'])) ?> · <i class="fa fa-globe-asia"></i></small>
                            </div>
                        </div>
                        <div class="px-6 pb-6 pt-2">
                            <h5 class="text-blue-900 font-black mb-3 uppercase text-sm border-l-4 border-blue-600 ps-3"><?= $row['title'] ?></h5>
                            <p class="text-slate-700 leading-relaxed text-[16px]"><?= nl2br($row['content']) ?></p>
                        </div>
                        <div class="flex justify-around border-t py-3 bg-slate-50/50 text-slate-500 font-black text-[10px] uppercase tracking-widest">
                            <span class="cursor-pointer hover:text-blue-600"><i class="fa fa-thumbs-up me-2 text-sm"></i> Like</span>
                            <span class="cursor-pointer hover:text-green-600"><i class="fa fa-comment me-2 text-sm"></i> Comment</span>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- ৫. ডান কলাম (Messenger & Requests) -->
        <!-- ৫. ডান কলাম: প্রফেশনাল মেসেঞ্জার প্যানেল (সংশোধিত ও এরর মুক্ত) -->
        <div class="col-span-12 lg:col-span-3">
            <div class="astha-card card-messenger shadow-xl border-t-4 border-emerald-500 sticky top-[130px] flex flex-col h-[80vh]">
                
                <div class="p-4 border-b bg-slate-50">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-xl font-black text-slate-800 tracking-tighter">MESSENGER</h4>
                        <!-- রিকোয়েস্ট কাউন্ট লজিক এখানে ফিক্স করা হয়েছে -->
                        <?php 
                        $stmt_count = $pdo->prepare("SELECT COUNT(*) FROM connection_requests WHERE teacher_id = ? AND status = 'pending'");
                        $stmt_count->execute([$user_id]);
                        $pending_count = $stmt_count->fetchColumn();
                        ?>
                    </div>

                    <!-- ট্যাব বাটন (ওভারল্যাপ সমস্যা সমাধান করা হয়েছে) -->
                    <ul class="nav nav-pills flex bg-slate-200 p-1 rounded-xl mb-4 gap-1" role="tablist">
                        <li class="nav-item flex-1">
                            <button class="nav-link active w-full py-1.5 font-black text-[10px] uppercase rounded-lg transition-all" data-bs-toggle="pill" data-bs-target="#community-tab">Community</button>
                        </li>
                        <li class="nav-item flex-1">
                            <button class="nav-link w-full py-1.5 font-black text-[10px] uppercase rounded-lg transition-all relative" data-bs-toggle="pill" data-bs-target="#requests-tab">
                                Requests
                                <?php if($pending_count > 0): ?>
                                <span class="absolute -top-1 -right-1 bg-red-600 text-white w-4 h-4 rounded-full flex items-center justify-center text-[8px] border border-white"><?= $pending_count ?></span>
                                <?php endif; ?>
                            </button>
                        </li>
                    </ul>

                    <!-- মেসেঞ্জার সার্চ -->
                    <div class="relative">
                        <i class="fa fa-search absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                        <input type="text" placeholder="Search Messenger" class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-2xl text-xs outline-none focus:ring-2 ring-emerald-100 font-bold transition-all">
                    </div>
                </div>

                <div class="tab-content flex-grow overflow-hidden custom-scroll overflow-y-auto bg-white">
                    <!-- ১. কমিউনিটি ট্যাব (একসেপ্ট করা শিক্ষার্থী) -->
                    <div class="tab-pane fade show active p-2" id="community-tab">
                        <?php
                        $stmt_f = $pdo->prepare("SELECT s.name, s.photo, u.id as uid FROM students s JOIN connection_requests cr ON s.id = cr.student_id JOIN users u ON s.user_id = u.id WHERE cr.teacher_id = ? AND cr.status = 'accepted'");
                        $stmt_f->execute([$user_id]);
                        $friends = $stmt_f->fetchAll();

                        if($friends): 
                            foreach($friends as $f):
                                $f_img = !empty($f['photo']) ? '../'.$f['photo'] : 'https://via.placeholder.com/150';
                                // আনপড় মেসেজ চেক
                                $unread = $pdo->query("SELECT COUNT(*) FROM chat_messages WHERE sender_id={$f['uid']} AND receiver_id=$user_id AND is_read=0")->fetchColumn();
                        ?>
                        <div class="flex items-center gap-3 p-3 cursor-pointer hover:bg-slate-50 rounded-2xl transition group relative mb-1" onclick="openChat('<?= $f['name'] ?>', '<?= $f_img ?>', '<?= $f['uid'] ?>')">
                            <div class="relative flex-shrink-0">
                                <img src="<?= $f_img ?>" class="w-12 h-12 rounded-full object-cover border-2 border-slate-100 shadow-sm group-hover:scale-105 transition">
                                <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full shadow-sm"></div>
                            </div>
                            <div class="flex-grow overflow-hidden leading-tight">
                                <h6 class="text-[13px] font-black text-slate-800 m-0 truncate"><?= $f['name'] ?></h6>
                                <p class="text-[10px] <?= $unread ? 'text-blue-600 font-black' : 'text-slate-400' ?> m-0">
                                    <?= $unread ? 'নতুন মেসেজ এসেছে' : 'Active Now' ?>
                                </p>
                            </div>
                            <?php if($unread > 0): ?><div class="w-2.5 h-2.5 bg-blue-600 rounded-full shadow-sm"></div><?php endif; ?>
                        </div>
                        <?php endforeach; else: ?>
                            <div class="text-center py-10 opacity-30">
                                <i class="fa fa-user-friends fa-3x mb-2"></i>
                                <p class="text-[10px] font-bold uppercase">No Active Friends</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- ২. অনুরোধ ট্যাব (পেন্ডিং রিকোয়েস্ট) -->
                    <div class="tab-pane fade p-3" id="requests-tab">
                        <?php
                        $stmt_req = $pdo->prepare("SELECT s.name, s.photo, cr.id as req_id FROM students s JOIN connection_requests cr ON s.id = cr.student_id WHERE cr.teacher_id = ? AND cr.status = 'pending'");
                        $stmt_req->execute([$user_id]);
                        $reqs = $stmt_req->fetchAll();
                        if($reqs): foreach($reqs as $r): 
                        ?>
                        <div class="p-3 bg-slate-50 border rounded-2xl mb-3">
                            <div class="flex items-center gap-3 mb-3">
                                <img src="../<?= $r['photo'] ?: 'uploads/logo.jpg' ?>" class="w-10 h-10 rounded-full border shadow-sm">
                                <span class="font-black text-xs text-slate-800"><?= $r['name'] ?></span>
                            </div>
                            <div class="flex gap-2">
                                <a href="action_request.php?action=accepted&id=<?= $r['req_id'] ?>" class="bg-blue-600 text-white text-[9px] font-black flex-1 py-2 rounded-lg text-center shadow-md no-underline">ACCEPT</a>
                                <a href="action_request.php?action=rejected&id=<?= $r['req_id'] ?>" class="bg-white border text-slate-500 text-[9px] font-black flex-1 py-2 rounded-lg text-center no-underline shadow-sm">REJECT</a>
                            </div>
                        </div>
                        <?php endforeach; else: ?>
                            <p class="text-center text-slate-300 mt-10 text-xs font-bold uppercase tracking-widest">No pending requests</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="p-3 border-t bg-slate-50 text-center">
                    <a href="#" class="text-blue-600 text-[10px] font-black uppercase tracking-widest hover:underline">See all in Messenger</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ৪. ফ্লোটিং চ্যাট পপ-আপ (হুবহু ফেসবুক লুক) -->
<div id="messengerPopup" class="shadow-2xl border border-slate-200">
    <div class="msg-header shadow-lg">
        <div class="flex items-center gap-2">
            <img id="chatUserImg" src="" class="w-9 h-9 rounded-full border-2 border-white shadow-sm object-cover">
            <div class="leading-none"><span id="chatUserName" class="text-[13px] font-black text-white"></span><br><small class="text-[9px] opacity-80 font-bold uppercase tracking-widest">Active connection</small></div>
        </div>
        <div class="flex gap-5 text-white text-md">
            <i class="fa fa-phone cursor-pointer hover:text-green-300 transition" onclick="alert('Calling...')"></i>
            <i class="fa fa-video cursor-pointer hover:text-green-300 transition" onclick="alert('Calling...')"></i>
            <i class="fa fa-times cursor-pointer hover:text-red-300 transition" onclick="closeChat()"></i>
        </div>
    </div>
    <div class="chat-body custom-scroll flex flex-col gap-3" id="chatMessages"></div>
    <div class="p-3 bg-white border-top">
        <div class="flex gap-4 text-blue-600 text-lg mb-3 ps-2">
            <label class="cursor-pointer hover:scale-125 transition"><i class="fa fa-image"></i><input type="file" id="imgInp" hidden accept="image/*" onchange="sendFile()"></label>
            <label class="cursor-pointer hover:scale-125 transition"><i class="fa fa-file-pdf"></i><input type="file" id="pdfInp" hidden accept=".pdf" onchange="sendFile()"></label>
            <i class="fa fa-smile cursor-pointer hover:scale-125 transition" onclick="toggleEmoji()"></i>
        </div>
        <form id="chatForm" class="flex items-center gap-2" onsubmit="event.preventDefault(); sendMessage();">
            <div class="flex-1 bg-slate-100 rounded-full px-5 py-2.5 shadow-inner border flex items-center">
                <input type="text" id="msgInput" class="bg-transparent border-0 outline-none text-sm w-full font-bold" placeholder="Aa" autocomplete="off">
            </div>
            <button type="submit" class="text-blue-600 transform hover:scale-125 transition"><i class="fa fa-paper-plane text-2xl"></i></button>
            <i class="fa fa-thumbs-up text-blue-600 text-2xl cursor-pointer hover:scale-125 transition" onclick="sendEmoji('👍')"></i>
        </form>
        <div id="emojiBox" class="emoji-picker shadow-2xl">
            <span onclick="sendEmoji('😊')">😊</span><span onclick="sendEmoji('😂')">😂</span><span onclick="sendEmoji('❤️')">❤️</span><span onclick="sendEmoji('🔥')">🔥</span>
        </div>
    </div>
</div>

<!-- মোডাল (পাবলিশ লেসন) -->
<div class="modal fade" id="lessonModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <form action="save_lesson.php" method="POST" enctype="multipart/form-data" class="modal-content border-0 rounded-3xl shadow-2xl overflow-hidden">
            <div class="p-4 border-b bg-slate-50 flex justify-between items-center px-8 border-slate-200"><h5 class="font-black text-slate-800 m-0 uppercase text-xs tracking-widest">Publish Lesson</h5><button type="button" class="text-slate-400 hover:text-red-500 fs-4" data-bs-dismiss="modal"><i class="fa fa-times-circle"></i></button></div>
            <div class="p-8">
                <div class="grid grid-cols-2 gap-5 mb-5"><select name="class" class="p-3 bg-slate-50 border border-slate-100 rounded-xl font-bold outline-none shadow-sm"><option value="Six">Six</option><option value="Ten">Ten</option></select><input type="text" value="<?= $teacher['subject'] ?>" class="p-3 bg-slate-50 border border-slate-100 rounded-xl font-bold text-slate-400" readonly></div>
                <input type="text" name="title" placeholder="পাঠের শিরোনাম..." class="w-full mb-5 p-3 bg-slate-50 border border-slate-100 rounded-xl font-bold outline-none shadow-inner" required>
                <textarea name="content" class="w-full h-40 border-0 outline-none text-lg p-2" placeholder="বিস্তারিত পড়া এখানে লিখুন..." required></textarea>
            </div>
            <div class="p-4 bg-slate-50 border-t"><button type="submit" class="w-full bg-blue-600 text-white font-black py-4 rounded-2xl shadow-xl hover:bg-blue-700 transition">POST TO NEWS FEED</button></div>
        </form>
    </div>
</div>
<!-- Guardian Notice (SMS) Modal -->
<div class="modal fade" id="guardianSMSModal" tabindex="-1" aria-labelledby="guardianSMSModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="send_guardian_sms.php" method="POST" class="modal-content border-0 rounded-3xl shadow-2xl overflow-hidden">
            <div class="p-5 border-b bg-red-600 text-white text-center">
                <h5 class="font-black m-0 uppercase tracking-widest" id="guardianSMSModalLabel"><i class="fa fa-sms me-2"></i> Guardian Notice (SMS)</h5>
            </div>
            <div class="p-6 bg-slate-50">
                <div class="mb-4">
                    <label class="block text-xs font-black text-slate-500 uppercase mb-2">শ্রেণি নির্বাচন করুন</label>
                    <select name="class" class="w-full p-3 bg-white border border-slate-200 rounded-xl font-bold outline-none shadow-sm" required>
                        <option value="Six">Class Six</option>
                        <option value="Seven">Class Seven</option>
                        <option value="Eight">Class Eight</option>
                        <option value="Nine">Class Nine</option>
                        <option value="Ten">Class Ten</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-black text-slate-500 uppercase mb-2">পরামর্শ বা বার্তা (সর্বোচ্চ ১৬০ অক্ষর)</label>
                    <textarea name="message" class="w-full h-32 p-4 bg-white border border-slate-200 rounded-2xl outline-none font-bold text-slate-700 shadow-inner" placeholder="আপনার বার্তা এখানে লিখুন..." required maxlength="160"></textarea>
                </div>
                <div class="text-center p-3 bg-yellow-100 rounded-xl text-yellow-800 text-[10px] font-bold">
                    <i class="fa fa-info-circle me-1"></i> এই বার্তাটি সরাসরি অভিভাবকের রেজিস্টার্ড মোবাইল নম্বরে যাবে।
                </div>
            </div>
            <div class="p-5 bg-white border-t flex gap-3">
                <button type="button" class="flex-1 bg-slate-100 text-slate-500 font-black py-3 rounded-2xl" data-bs-dismiss="modal">বাতিল</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white font-black py-3 rounded-2xl shadow-lg hover:bg-blue-700 transition">এসএমএস পাঠান</button>
            </div>
        </form>
    </div>
</div>

<script>
    let chatInterval = null; let currentChatId = null;
    function openChat(name, img, id) {
        currentChatId = id; document.getElementById('messengerPopup').style.display = 'block';
        document.getElementById('chatUserName').innerText = name; document.getElementById('chatUserImg').src = img;
        loadMessages(); if(chatInterval) clearInterval(chatInterval);
        chatInterval = setInterval(loadMessages, 3000);
    }
    function closeChat() { document.getElementById('messengerPopup').style.display = 'none'; if(chatInterval) clearInterval(chatInterval); }
    function toggleEmoji() { const eb = document.getElementById('emojiBox'); eb.style.display = (eb.style.display === 'grid') ? 'none' : 'grid'; }
    function sendEmoji(e) { if(!currentChatId) return; processChat(e, null); document.getElementById('emojiBox').style.display='none'; }
    function sendMessage() { const i = document.getElementById('msgInput'); if(i.value.trim()){ processChat(i.value.trim(), null); i.value=''; } }
    function sendFile() { const file = event.target.files[0]; if(file) processChat('', file); }
    function processChat(m, f) {
        let fd = new FormData(); fd.append('send_msg', '1'); fd.append('receiver_id', currentChatId); fd.append('message', m);
        if(f) fd.append('chat_file', f);
        fetch('chat_handler.php', { method: 'POST', body: fd }).then(() => loadMessages());
    }
    function loadMessages() {
        if(!currentChatId) return;
        fetch(`chat_handler.php?fetch_msg=1&receiver_id=${currentChatId}`)
            .then(res => res.text()).then(data => {
                const box = document.getElementById('chatMessages'); box.innerHTML = data; box.scrollTop = box.scrollHeight;
            });
    }
</script>

<?php include '../includes/footer.php'; ?>