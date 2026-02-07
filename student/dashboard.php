<?php
session_start();
require_once '../config/db.php';

// ১. সিকিউরিটি চেক
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: ../login.php"); exit();
}

$user_id = $_SESSION['user_id'];

// ২. ডাটাবেজ থেকে শিক্ষার্থীর তথ্য, স্কুলের নাম এবং লোগো আনা
$stmt = $pdo->prepare("
    SELECT u.*, s.name as student_name, s.class, s.roll, sch.name as school_name, scl.school_logo, scl.bg_image 
    FROM users u 
    JOIN students s ON u.id = s.user_id 
    JOIN users sch ON s.school_id = sch.id 
    JOIN schools scl ON sch.id = scl.user_id
    WHERE u.id = ?
");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$school_id = $user['school_id'];

// বন্ধুর সংখ্যা গণনা
$stmt_f_count = $pdo->prepare("SELECT COUNT(*) FROM student_connections WHERE (sender_id = ? OR receiver_id = ?) AND status = 'accepted'");
$stmt_f_count->execute([$user_id, $user_id]);
$friend_count = $stmt_f_count->fetchColumn() ?: 0;

include '../includes/header.php'; 
?>

<!-- Tailwind CSS & Fonts -->
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* গ্লোবাল নেভিগেশন হাইড */
    .school-title-bar, .top-nav, .navbar, header, nav { display: none !important; }
    
    body { background-color: #f0f2f5; font-family: 'Hind Siliguri', sans-serif; margin: 0; padding: 0; }

    /* টপ ব্র্যান্ডিং বার */
    .astha-top-bar { background: #fff; border-bottom: 1px solid #e5e7eb; padding: 10px 0; position: sticky; top: 0; z-index: 1000; }
    .school-name-top { color: #cc0000; font-weight: 900; text-transform: uppercase; font-size: 20px; }

    /* প্রোফাইল হেডার */
    .profile-header-wrap { background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,0.1); border-radius: 0 0 20px 20px; }
    .cover-box { 
        height: 280px; background-size: cover; background-position: center; 
        background-image: url('../<?= !empty($user['bg_image']) ? $user['bg_image'] : 'uploads/covers/default.jpg' ?>');
        background-color: #064e3b; position: relative; border-radius: 0 0 15px 15px; 
    }
    .cover-overlay { position: absolute; inset: 0; background: linear-gradient(to bottom, transparent, rgba(0,0,0,0.4)); }
    .cover-school-watermark { position: absolute; font-size: 4.5rem; font-weight: 900; color: rgba(255,255,255,0.08); top: 40%; left: 50%; transform: translate(-50%, -50%); white-space: nowrap; pointer-events: none; text-transform: uppercase; }

    .profile-info-container { display: flex; align-items: flex-end; padding: 0 60px 25px; margin-top: -100px; position: relative; z-index: 20; }
    .profile-image-big { width: 170px; height: 170px; border: 5px solid white; border-radius: 50%; object-fit: cover; background: #fff; box-shadow: 0 8px 20px rgba(0,0,0,0.15); }
    
    .name-bio-section { margin-left: 25px; flex: 1; padding-bottom: 15px; }
    .name-bio-section h1 { font-size: 34px; font-weight: 900; color: #1c1e21; letter-spacing: -1px; }
    .friends-count { color: #65676b; font-weight: 700; font-size: 16px; margin-top: 5px; }
    .bio-text { font-size: 14px; color: #1877f2; font-weight: 700; margin-top: 5px; font-style: italic; }

    /* ট্যাব মেনু */
    .astha-tabs { display: flex; border-top: 1px solid #f0f2f5; margin: 0 50px; padding: 0; overflow-x: auto; white-space: nowrap; }
    .astha-tabs li { padding: 18px 25px; font-weight: 700; color: #65676b; border-bottom: 3px solid transparent; cursor: pointer; transition: 0.3s; list-style: none; }
    .astha-tabs li:hover { background: #f2f2f2; border-radius: 8px; }
    .astha-tabs li.active { color: #1877f2; border-bottom-color: #1877f2; border-radius: 0; }

    /* বডি ও কার্ডস */
    .astha-card { border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.04); margin-bottom: 20px; padding: 20px; background: white; border: 1px solid #eef2f6; }
    .card-intro { background-color: #f8fafc; }
    .card-teachers { background-color: #f0f7ff; }
    .card-friends { background-color: #f0fdf4; }

    #messengerBox { position: fixed; bottom: 0; right: 50px; width: 300px; background: white; border-radius: 10px 10px 0 0; box-shadow: 0 0 20px rgba(0,0,0,0.2); z-index: 9999; display: none; }
    .custom-scroll::-webkit-scrollbar { width: 4px; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 10px; }
    .cursor-pointer { cursor: pointer; }

    /* মেসেঞ্জার পপ-আপ প্রফেশনাল ডিজাইন */
    #messengerBox { 
        position: fixed; bottom: 0; right: 50px; width: 340px; background: #fff; 
        border-radius: 15px 15px 0 0; box-shadow: 0 12px 28px rgba(0,0,0,0.2); 
        z-index: 10000; display: none; border: 1px solid #ddd; overflow: hidden;
    }
    .messenger-header { background: linear-gradient(to right, #1e3a8a, #3b82f6); color: white; padding: 12px; display: flex; justify-content: space-between; align-items: center; }
    
    .messenger-header { 
        background: linear-gradient(to right, #1e3a8a, #3b82f6); 
        color: white; padding: 12px; display: flex; justify-content: space-between; align-items: center; 
    }

    /* ছবির মতো চ্যাট ব্যাকগ্রাউন্ড প্যাটার্ন */
    .chat-body { 
        height: 380px; overflow-y: auto; padding: 15px; 
        background-color: #e5ddd5;
        background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png');
        background-size: contain; display: flex; flex-direction: column;
    }
    .chat-footer-icons { display: flex; gap: 15px; padding: 10px 15px; border-top: 1px solid #f0f2f5; color: #1877f2; font-size: 18px; }

    /* মেসেজ বাবল ডিজাইন */
    .msg-bubble-me { 
        background: #0084ff; color: white; border-radius: 18px 18px 0 18px; 
        padding: 8px 14px; max-width: 80%; align-self: flex-end; 
        box-shadow: 0 1px 2px rgba(0,0,0,0.1); font-size: 14px;
    }
    .msg-bubble-them { 
        background: #fff; color: #000; border-radius: 18px 18px 18px 0; 
        padding: 8px 14px; max-width: 80%; align-self: flex-start; 
        box-shadow: 0 1px 2px rgba(0,0,0,0.1); font-size: 14px;
    }

    .custom-scroll::-webkit-scrollbar { width: 5px; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 10px; }
</style>

<!-- ১. ব্র্যান্ডিং বার -->
<div class="astha-top-bar shadow-sm">
    <div class="max-w-[1500px] mx-auto px-10 flex justify-between items-center">
        <div class="flex items-center gap-4">
            <img src="../<?= $user['school_logo'] ?: 'uploads/logo.jpg' ?>" class="w-10 h-10 rounded-full border">
            <h4 class="school-name-top tracking-tighter"><?= $user['school_name'] ?></h4>
        </div>
        <div class="bg-slate-50 px-5 py-1.5 rounded-2xl border flex items-center gap-4">
            <span class="text-[11px] font-black text-slate-500 uppercase tracking-widest">User: STUDENT</span>
            <div class="w-px h-4 bg-slate-300"></div>
            <span class="text-xs font-black text-blue-600 uppercase"><?= date('d M, Y') ?></span>
        </div>
    </div>
</div>

<!-- ২. প্রোফাইল হেডার -->
<div class="profile-header-wrap">
    <div class="max-w-[1500px] mx-auto">
        <div class="cover-box shadow-inner">
            <div class="cover-overlay"></div>
            <div class="cover-school-watermark"><?= $user['school_name'] ?></div>
        </div>
        
        <div class="profile-info-container flex-wrap justify-between items-center">
            <div class="flex items-end">
                <div class="relative">
                    <img src="../<?= $user['image'] ?: 'uploads/users/default.png' ?>" class="profile-image-big">
                    <label class="absolute bottom-3 right-3 bg-slate-100 p-2.5 rounded-full border-2 border-white cursor-pointer hover:bg-slate-200 shadow-lg transition">
                        <i class="fas fa-camera text-slate-700"></i><input type="file" hidden>
                    </label>
                </div>
                <div class="name-bio-section">
                    <h1><?= $user['student_name'] ?></h1>
                    <div class="friends-count"><?= $friend_count ?> Friends • 0 Following</div>
                    <div class="bio-text"><?= $user['bio'] ?: 'Add your bio...' ?></div>
                </div>
            </div>
            <div class="flex gap-3 mb-6">
                <a href="edit_profile.php" class="bg-blue-600 text-white px-8 py-3 rounded-xl font-black shadow-lg shadow-blue-200 transition hover:bg-blue-700 hover:scale-105">
                    <i class="fa fa-pen-to-square mr-2"></i> Edit Profile
                </a>
                <a href="../auth/logout.php" class="bg-red-600 text-white px-6 py-3 rounded-xl font-black shadow-lg shadow-red-200 transition hover:bg-red-700 hover:scale-105">
                    <i class="fa fa-power-off"></i>
                </a>
            </div>
        </div>

        <ul class="astha-tabs">
            <li>About</li>
            <li>Photos</li>
            <li>Reels</li>
            <li class="active">Lesson</li>
            <li>Friends</li>
            <li>Community</li>
            <li>More <i class="fa fa-caret-down ml-1"></i></li>
        </ul>
    </div>
</div>

<!-- ৩. বডি সেকশন -->
<div class="max-w-[1500px] mx-auto px-10 mt-8">
    
    <!-- আধুনিক সার্চ ও নোটিশ বার -->
    <div class="flex flex-col lg:flex-row gap-5 mb-8">
        <div class="lg:w-3/4 bg-white p-2 rounded-2xl shadow-sm border flex items-center gap-2">
            <div class="flex-1 flex items-center px-5 border-r border-slate-100">
                <i class="fa fa-university text-slate-300 mr-3"></i>
                <input type="text" placeholder="প্রতিষ্ঠানের নাম..." class="w-full py-2 outline-none font-bold text-sm bg-transparent">
            </div>
            <div class="w-56 flex items-center px-5 border-r border-slate-100 hidden md:flex">
                <i class="fa fa-map-pin text-slate-300 mr-3"></i>
                <input type="text" placeholder="উপজেলা নির্বাচন করুন" class="w-full py-2 outline-none font-bold text-sm bg-transparent">
            </div>
            <button class="bg-slate-800 text-white px-10 py-3 rounded-2xl font-black text-sm hover:bg-black transition shadow-lg">Search</button>
        </div>

        <div class="lg:w-1/4 bg-red-600 rounded-[25px] p-2 px-6 flex items-center shadow-xl shadow-red-100">
            <div class="bg-white text-red-600 text-[10px] font-black px-2 py-0.5 rounded-md uppercase mr-4">Notice</div>
            <marquee class="text-white text-xs font-black" scrollamount="6">স্বাগতম <?= $user['student_name'] ?>! তোমার প্রতিষ্ঠানের সব আপডেট এখানে পাবে।</marquee>
        </div>
    </div>

    <div class="row g-5 pb-20">
        <!-- ১. বাম কলাম (Intro & Teachers) -->
        <div class="col-md-3">
            <div class="astha-card card-intro mb-6">
                <h5 class="font-black text-slate-800 mb-5 flex items-center gap-3 border-b pb-3"><i class="fa fa-info-circle text-blue-500"></i> Intro</h5>
                <div class="space-y-5 text-sm font-bold text-slate-600">
                    <div class="flex items-center gap-3"><i class="fa fa-school text-slate-400 w-5"></i> <?= $user['school_name'] ?></div>
                    <div class="flex items-center gap-3"><i class="fa fa-graduation-cap text-slate-400 w-5"></i> Class <?= $user['class'] ?> | Roll <?= $user['roll'] ?></div>
                    <div class="flex items-center gap-3"><i class="fa fa-location-dot text-slate-400 w-5"></i> Tala, Satkhira</div>
                </div>
            </div>

            <div class="astha-card card-teachers">
                <h6 class="font-black text-blue-800 mb-5 border-b border-blue-200 pb-2 uppercase text-[11px] tracking-widest">Connect Teachers</h6>
                <div class="custom-scroll space-y-4 overflow-y-auto max-h-80 pr-2">
                    <?php
                    $stmt_s_id = $pdo->prepare("SELECT id FROM students WHERE user_id = ?");
                    $stmt_s_id->execute([$user_id]);
                    $student_primary_id = $stmt_s_id->fetchColumn();

                    $stmt_t = $pdo->prepare("SELECT * FROM users WHERE school_id = ? AND role = 'teacher' LIMIT 15");
                    $stmt_t->execute([$school_id]);
                    $teachers_list = $stmt_t->fetchAll();

                    if($teachers_list):
                        foreach($teachers_list as $t):
                            $c_stmt = $pdo->prepare("SELECT status FROM connection_requests WHERE teacher_id = ? AND student_id = ?");
                            $c_stmt->execute([$t['id'], $student_primary_id]);
                            $c_status = $c_stmt->fetchColumn();
                    ?>
                    <div class="flex items-center justify-between p-1 transition group">
                        <div class="flex items-center gap-3">
                            <img src="../<?= $t['image'] ?: 'uploads/users/default.png' ?>" class="w-9 h-9 rounded-full border-2 border-white shadow-sm group-hover:scale-105 transition">
                            <div class="leading-none">
                                <p class="text-[11px] font-black text-slate-800"><?= $t['name'] ?></p>
                                <p class="text-[9px] text-blue-500 font-black mt-1 uppercase"><?= $t['subject'] ?></p>
                            </div>
                        </div>
                        <?php if(!$c_status): ?>
                            <a href="request_process.php?type=teacher&id=<?= $t['id'] ?>" class="bg-blue-600 text-white text-[9px] font-black px-3 py-1.5 rounded-lg shadow-sm hover:bg-blue-700 transition">CONNECT</a>
                        <?php elseif($c_status == 'pending'): ?>
                            <span class="text-slate-400 text-[9px] font-bold">PENDING</span>
                        <?php else: ?>
                            <i class="fab fa-facebook-messenger text-blue-500 cursor-pointer" onclick="openChat('<?= $t['name'] ?>', '../<?= $t['image'] ?>', '<?= $t['id'] ?>')"></i>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>

        <!-- ২. মাঝের কলাম (Feed & Requests) -->
        <div class="col-md-6">
            
            <!-- Friend Requests -->
            <?php
            $stmt_pending = $pdo->prepare("
                SELECT sc.id as connection_id, s.name, u.image 
                FROM student_connections sc 
                JOIN users u ON sc.sender_id = u.id 
                JOIN students s ON u.id = s.user_id 
                WHERE sc.receiver_id = ? AND sc.status = 'pending'
            ");
            $stmt_pending->execute([$user_id]);
            $p_reqs = $stmt_pending->fetchAll();

            if ($p_reqs):
                foreach ($p_reqs as $req):
            ?>
            <div class="astha-card bg-white border-l-4 border-blue-600 mb-6 p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <img src="../<?= $req['image'] ?: 'uploads/users/default.png' ?>" class="w-10 h-10 rounded-full border-2 border-white shadow-sm">
                        <p class="font-black text-sm m-0">Friend Request from <span class="text-blue-600"><?= $req['name'] ?></span></p>
                    </div>
                    <div class="flex gap-2">
                        <a href="manage_connection.php?action=accept&id=<?= $req['connection_id'] ?>" class="bg-blue-600 text-white px-4 py-1.5 rounded-xl text-[10px] font-black shadow-lg">ACCEPT</a>
                        <a href="manage_connection.php?action=reject&id=<?= $req['connection_id'] ?>" class="bg-white text-slate-500 border px-4 py-1.5 rounded-xl text-[10px] font-black">REJECT</a>
                    </div>
                </div>
            </div>
            <?php endforeach; endif; ?>

            <!-- পোস্ট বক্স -->
            <div class="astha-card">
                <div class="flex gap-4 mb-5">
                    <img src="../<?= $user['image'] ?: 'uploads/users/default.png' ?>" class="w-12 h-12 rounded-full border border-slate-200">
                    <div class="bg-slate-100 hover:bg-slate-200 transition flex-1 py-3 px-6 rounded-full text-slate-500 font-bold cursor-pointer border border-slate-100" data-bs-toggle="modal" data-bs-target="#postModal">
                        বন্ধুদের সাথে আজ কি শেয়ার করতে চাও?
                    </div>
                </div>
                <div class="flex justify-around border-t pt-4 text-slate-500 font-bold text-sm">
                    <button class="flex-1 hover:bg-slate-50 py-2.5 rounded-2xl transition flex items-center justify-center gap-3" data-bs-toggle="modal" data-bs-target="#postModal"><i class="fa fa-image text-green-500 text-lg"></i> ছবি</button>
                    <button class="flex-1 hover:bg-slate-50 py-2.5 rounded-2xl transition flex items-center justify-center gap-3" data-bs-toggle="modal" data-bs-target="#postModal"><i class="fa fa-video text-red-500 text-lg"></i> ভিডিও</button>
                    <button class="flex-1 hover:bg-slate-50 py-2.5 rounded-2xl transition flex items-center justify-center gap-3" data-bs-toggle="modal" data-bs-target="#postModal"><i class="fa fa-smile text-yellow-500 text-lg"></i> অনুভূতি</button>
                </div>
            </div>

            <!-- ২. টাইমলাইন পোস্টসমূহ লোড করা (ডায়নামিক লুপ) -->
            <div id="timelineContainer">
                <?php
                $stmt_posts = $pdo->prepare("
                    SELECT p.*, u.name as author_name, u.image as author_img 
                    FROM posts p 
                    JOIN users u ON p.user_id = u.id 
                    WHERE p.school_id = ? 
                    ORDER BY p.created_at DESC LIMIT 30
                ");
                $stmt_posts->execute([$user['school_id']]);
                $posts = $stmt_posts->fetchAll();

                if ($posts):
                    foreach ($posts as $post):
                        $author_img = !empty($post['author_img']) ? '../'.$post['author_img'] : '../uploads/users/default.png';
                ?>
                    <div class="astha-card bg-white p-5 mb-6 border border-slate-100">
                    <!-- পোস্ট হেডার (নাম ও ছবি) -->
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex gap-3">
                            <img src="<?= $author_img ?>" class="w-11 h-11 rounded-full border-2 border-slate-100 shadow-sm object-cover">
                            <div class="leading-tight">
                                <h6 class="font-black text-slate-800 m-0"><?= htmlspecialchars($post['author_name']) ?></h6>
                                <small class="text-slate-400 font-bold"><?= date('d M \a\t h:i A', strtotime($post['created_at'])) ?></small>
                            </div>
                        </div>
                    </div>

                    <!-- পোস্ট টাইটেল -->
                    <?php if(!empty($post['title'])): ?>
                        <h5 class="text-blue-900 font-black mb-2 text-lg">বিষয়: <?= htmlspecialchars($post['title']) ?></h5>
                    <?php endif; ?>

                    <!-- পোস্টের মূল লেখা (Content) -->
                    <p class="text-slate-700 leading-relaxed text-[16px] mb-4">
                        <?= nl2br(htmlspecialchars($post['content'])) ?>
                    </p>

                    <!-- ******************************************* -->
                    <!-- এই অংশটিই আপনার প্রয়োজন (মিডিয়া ডিসপ্লে) -->
                    <!-- ******************************************* -->
                    <?php if(!empty($post['media_path'])): ?>
                        <div class="mt-4 rounded-2xl overflow-hidden border border-slate-100 bg-slate-50">
                            <?php if($post['media_type'] == 'image'): ?>
                                <!-- ছবি দেখানোর জন্য -->
                                <img src="../<?= $post['media_path'] ?>" class="w-full h-auto max-h-[500px] object-contain mx-auto">
                            <?php elseif($post['media_type'] == 'video'): ?>
                                <!-- ভিডিও দেখানোর জন্য -->
                                <video controls class="w-full max-h-[500px]">
                                    <source src="../<?= $post['media_path'] ?>" type="video/mp4">
                                </video>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <!-- ******************************************* -->

                    <!-- লাইক ও কমেন্ট বাটন (নিচে থাকবে) -->
                    <!-- লাইক ও কমেন্ট বাটন -->
                    <div class="flex justify-around border-t pt-2 mt-4 text-slate-500 font-bold text-sm">
                        <button onclick="handleLike(<?= $post['id'] ?>, this)" class="flex-1 hover:bg-slate-50 py-2 rounded-xl transition flex items-center justify-center gap-2">
                            <i class="fa-<?= ($pdo->query("SELECT id FROM post_likes WHERE post_id={$post['id']} AND user_id=$user_id")->rowCount() > 0) ? 'solid text-blue-600' : 'regular' ?> fa-thumbs-up"></i> 
                            <span>লাইক</span>
                        </button>
                        <button onclick="toggleCommentBox(<?= $post['id'] ?>)" class="flex-1 hover:bg-slate-50 py-2 rounded-xl transition flex items-center justify-center gap-2">
                            <i class="fa-regular fa-comment"></i> মতামত
                        </button>
                    </div>

                    <!-- কমেন্ট বক্স (প্রাথমিকভাবে হাইড থাকবে) -->
                    <div id="commentBox-<?= $post['id'] ?>" class="hidden mt-3 border-t pt-3">
                        <div class="flex gap-2 mb-3">
                            <input type="text" id="commentInput-<?= $post['id'] ?>" class="flex-1 bg-slate-100 rounded-full px-4 py-2 text-sm outline-none" placeholder="একটি মতামত লিখুন...">
                                <button onclick="submitComment(<?= $post['id'] ?>)" class="text-blue-600 px-3 font-bold">পাঠান</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; else: ?>
                <p class="text-center text-slate-400 py-20">কোনো পোস্ট পাওয়া যায়নি।</p>
            <?php endif; ?>
        </div>
        </div>

        <!-- ৩. ডান কলাম (Accepted Friends) -->
        <div class="col-md-3">
            <div class="astha-card card-friends">
                <div class="flex justify-between items-center mb-5 border-b border-green-200 pb-3">
                    <h6 class="font-black text-emerald-800 m-0 text-sm uppercase tracking-tighter">Active Friends</h6>
                    <!-- এটি এখন আপনার বড় হাব মোডাল ওপেন করবে -->
                    <button class="bg-emerald-600 text-white px-3 py-1.5 rounded-xl text-[9px] font-black uppercase shadow-sm hover:bg-emerald-700 transition" data-bs-toggle="modal" data-bs-target="#communityHubModal">
                        <i class="fa fa-user-plus me-1"></i> Hub
                    </button>
                </div>
                <div class="custom-scroll space-y-4 overflow-y-auto max-h-[600px] pr-2">
                    <?php
                    // শুধুমাত্র Accepted বন্ধুদের তালিকা
                    $stmt_friends = $pdo->prepare("
                        SELECT s.*, u.id as uid, u.image as u_img 
                        FROM student_connections c 
                        JOIN users u ON (c.sender_id = u.id OR c.receiver_id = u.id)
                        JOIN students s ON u.id = s.user_id
                        WHERE (c.sender_id = ? OR c.receiver_id = ?) 
                        AND c.status = 'accepted' AND u.id != ?
                    ");
                    $stmt_friends->execute([$user_id, $user_id, $user_id]);
                    $friends_list = $stmt_friends->fetchAll();

                    if($friends_list):
                        foreach($friends_list as $f):
                            $f_img = !empty($f['u_img']) ? '../'.$f['u_img'] : '../uploads/users/default.png';
                    ?>
                    <div class="flex items-center justify-between p-1 cursor-pointer group transition" onclick="openChat('<?= $f['name'] ?>', '<?= $f_img ?>', '<?= $f['uid'] ?>')">
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <img src="<?= $f_img ?>" class="w-10 h-10 rounded-full border-2 border-white shadow-sm group-hover:ring-2 ring-emerald-400 transition">
                                <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border border-white rounded-full"></span>
                            </div>
                        <p class="text-[13px] font-black text-slate-800 group-hover:text-emerald-700 transition leading-tight"><?= $f['name'] ?></p>
                    </div>
                    <i class="fab fa-facebook-messenger text-slate-200 group-hover:text-blue-500 text-sm transition"></i>
                </div>
                <?php endforeach; else: ?>
                    <p class="text-center text-[10px] text-slate-400 py-4 italic">কোনো বন্ধু যুক্ত নেই। হাব থেকে বন্ধু যোগ করুন।</p>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- পোস্ট মোডাল -->
<div class="modal fade" id="postModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="save_post.php" method="POST" enctype="multipart/form-data" class="modal-content border-0 rounded-3xl shadow-2xl">
            <div class="p-4 border-b text-center"><h5 class="font-black text-slate-800 m-0">নতুন পোস্ট তৈরি করুন</h5></div>
            <div class="p-6">
                <input type="text" name="title" placeholder="বিষয় বা শিরোনাম দিন..." class="w-full mb-4 p-3 bg-slate-50 border-0 rounded-2xl outline-none font-black text-blue-900 shadow-inner">
                <textarea name="content" class="w-full h-40 border-0 outline-none text-lg p-2" placeholder="<?= explode(' ', $user['student_name'])[0] ?>, বন্ধুদের সাথে আজ কি শেয়ার করতে চাও?" required></textarea>
                <div class="mt-4 p-4 border rounded-2xl flex justify-between items-center bg-slate-50">
                    <span class="font-black text-slate-500 text-sm">Add to post</span>
                    <input type="file" name="media" class="text-xs">
                </div>
            </div>
            <div class="p-4"><button type="submit" class="w-full bg-blue-600 text-white font-black py-4 rounded-2xl shadow-xl">Publish Now</button></div>
        </form>
    </div>
</div>

<!-- সাজেশন মোডাল -->
<div class="modal fade" id="suggestionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 rounded-3xl shadow-2xl">
            <div class="p-4 border-b text-center"><h5 class="font-black text-slate-800 m-0">নতুন বন্ধু খুঁজুন</h5></div>
            <div class="p-4 overflow-y-auto max-h-[400px] custom-scroll">
                <?php
                $stmt_sug = $pdo->prepare("SELECT s.*, u.id as uid, u.image FROM students s JOIN users u ON s.user_id = u.id WHERE s.school_id = ? AND u.id != ? AND u.id NOT IN (SELECT receiver_id FROM student_connections WHERE sender_id = ?) AND u.id NOT IN (SELECT sender_id FROM student_connections WHERE receiver_id = ?)");
                $stmt_sug->execute([$school_id, $user_id, $user_id, $user_id]);
                $sug_list = $stmt_sug->fetchAll();
                if($sug_list):
                    foreach($sug_list as $sug):
                ?>
                <div class="flex items-center justify-between mb-4 p-2 hover:bg-slate-50 rounded-2xl transition">
                    <div class="flex items-center gap-3">
                        <img src="../<?= $sug['image'] ?: 'uploads/users/default.png' ?>" class="w-11 h-11 rounded-full border shadow-sm">
                        <div class="leading-none"><p class="font-black text-sm"><?= $sug['name'] ?></p><small class="text-slate-400 font-bold">Class <?= $sug['class'] ?></small></div>
                    </div>
                    <a href="request_friend.php?id=<?= $sug['uid'] ?>" class="bg-blue-600 text-white px-4 py-1.5 rounded-xl text-xs font-black">Add Friend</a>
                </div>
                <?php endforeach; else: ?>
                    <p class="text-center text-slate-400">নতুন কোনো শিক্ষার্থী পাওয়া যায়নি।</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ফ্লোটিং মেসেঞ্জার বক্স -->
<!-- মেসেঞ্জার পপ-আপ -->
<div id="messengerBox">
    <div class="messenger-header shadow-md">
        <div class="flex items-center gap-2">
            <img id="chatUserImg" src="" class="w-9 h-9 rounded-full border-2 border-white/50 object-cover">
            <div class="leading-none">
                <span id="chatUserName" class="text-xs font-black block"></span>
                <small class="text-[9px] font-bold opacity-80 uppercase">Active Now</small>
            </div>
        </div>
        <div class="flex gap-4 text-white text-sm">
            <i class="fa fa-phone cursor-pointer hover:text-green-300 transition" onclick="startCall('audio')"></i>
            <i class="fa fa-video cursor-pointer hover:text-green-300 transition" onclick="startCall('video')"></i>
            <i class="fa fa-minus cursor-pointer" onclick="closeChat()"></i>
            <i class="fa fa-times cursor-pointer" onclick="closeChat()"></i>
        </div>
    </div>
    
    <div class="chat-body custom-scroll" id="chatMessages"></div>

    <div class="bg-white border-top">
        <!-- আইকন বার (ছবির মতো) -->
        <div class="chat-footer-icons">
            <i class="fa fa-plus-circle cursor-pointer hover:scale-110 transition"></i>
            <label class="cursor-pointer hover:scale-110 transition"><i class="fa fa-image"></i><input type="file" hidden onchange="alert('ছবি আপলোড শীঘ্রই আসছে')"></label>
            <i class="fa fa-sticky-note cursor-pointer hover:scale-110 transition"></i>
        </div>
        <!-- ইনপুট বার -->
        <div class="p-2 pb-3 flex items-center gap-2 px-3">
            <div class="flex-1 bg-slate-100 rounded-full px-4 py-2 flex items-center shadow-inner border">
                <input type="text" id="msgInput" class="bg-transparent border-0 outline-none text-xs w-full font-bold" placeholder="Aa" autocomplete="off">
                <i class="fa fa-smile text-blue-600 cursor-pointer hover:scale-110 transition"></i>
            </div>
            <button class="text-blue-600 transform hover:scale-125 transition" onclick="sendMessage()"><i class="fa fa-paper-plane text-xl"></i></button>
            <i class="fa fa-thumbs-up text-blue-600 text-2xl cursor-pointer ml-1 hover:scale-110 transition" onclick="sendEmoji('👍')"></i>
        </div>
    </div>
</div>

<script src="https://meet.jit.si/external_api.js"></script>
<script>
    let chatInterval = null;
    let currentChatId = null;

    function openChat(name, img, id) {
        currentChatId = id;
        document.getElementById('messengerBox').style.display = 'block';
        document.getElementById('chatUserName').innerText = name;
        document.getElementById('chatUserImg').src = img;
        loadMessages();
        if(chatInterval) clearInterval(chatInterval);
        chatInterval = setInterval(loadMessages, 3000);
    }

    function closeChat() {
        document.getElementById('messengerBox').style.display = 'none';
        if(chatInterval) clearInterval(chatInterval);
    }

    function loadMessages() {
        if(!currentChatId) return;
        fetch(`chat_handler.php?fetch_msg=1&receiver_id=${currentChatId}`)
            .then(res => res.text()).then(data => {
                const box = document.getElementById('chatMessages');
                box.innerHTML = data;
                box.scrollTop = box.scrollHeight;
            });
    }

    document.getElementById('chatForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const msgInput = document.getElementById('msgInput');
        const msg = msgInput.value;
        if(!msg) return;
        let fd = new FormData();
        fd.append('send_msg', '1');
        fd.append('receiver_id', currentChatId);
        fd.append('message', msg);
        fetch('chat_handler.php', { method: 'POST', body: fd }).then(() => {
            msgInput.value = '';
            loadMessages();
        });
    });

    function startCall(type) { alert("ভিডিও কল সার্ভিসটি খুব শীঘ্রই চালু হচ্ছে..."); }

    function handleLike(postId, btn) {
    let fd = new FormData();
    fd.append('action', 'like');
    fd.append('post_id', postId);

    fetch('post_actions.php', { method: 'POST', body: fd })
    .then(res => res.text())
    .then(data => {
        let icon = btn.querySelector('i');
        if(data === 'liked') {
            icon.classList.replace('fa-regular', 'fa-solid');
            icon.classList.add('text-blue-600');
        } else {
            icon.classList.replace('fa-solid', 'fa-regular');
            icon.classList.remove('text-blue-600');
        }
    });
}

function toggleCommentBox(postId) {
    let box = document.getElementById('commentBox-' + postId);
    box.classList.toggle('hidden');
}

function submitComment(postId) {
    let input = document.getElementById('commentInput-' + postId);
    let text = input.value;
    if(!text) return;

    let fd = new FormData();
    fd.append('action', 'comment');
    fd.append('post_id', postId);
    fd.append('comment_text', text);

    fetch('post_actions.php', { method: 'POST', body: fd })
    .then(() => {
        alert('মতামত সফলভাবে যুক্ত হয়েছে!');
        input.value = '';
        location.reload(); // আপাতত রিফ্রেশ দিয়ে দেখানো হচ্ছে
    });
}

// ১. মেসেজ পাঠানো (Send বাটন)
function sendMessage() {
    const input = document.getElementById('msgInput');
    const msg = input.value.trim();
    if(!msg || !currentChatId) return;
    processMessage(msg);
    input.value = '';
}

// ২. ইমোজি/লাইক পাঠানো (Thumbs up বাটন)
function sendEmoji(emoji) {
    if(!currentChatId) return;
    processMessage(emoji);
}

// ৩. কোর মেসেজ প্রসেসিং (Ajax)
function processMessage(msg) {
    let fd = new FormData();
    fd.append('send_msg', '1');
    fd.append('receiver_id', currentChatId);
    fd.append('message', msg);
    
    // বর্তমান ফোল্ডারের chat_handler.php কে কল করবে
    fetch('chat_handler.php', { method: 'POST', body: fd }).then(() => {
        loadMessages(); // মেসেজ পাঠানোর পর রিলোড
    });
}
</script>

<!-- Community Hub Modal: রিকোয়েস্ট ও সাজেশন এক সাথে -->
<div class="modal fade" id="communityHubModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-[30px] shadow-2xl overflow-hidden">
            <div class="bg-slate-900 p-4 px-6 flex justify-between items-center">
                <h5 class="font-black text-white m-0 tracking-widest uppercase text-sm">Community Hub</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body p-0 bg-slate-50 custom-scroll" style="max-height: 80vh; overflow-y: auto;">
                
                <!-- অংশ ১: ফ্রেন্ড রিকোয়েস্ট (যদি থাকে) -->
                <?php
                $stmt_pending = $pdo->prepare("
                    SELECT sc.id as connection_id, s.name, u.image, sch.name as sch_name
                    FROM student_connections sc 
                    JOIN users u ON sc.sender_id = u.id 
                    JOIN students s ON u.id = s.user_id 
                    JOIN users sch ON s.school_id = sch.id
                    WHERE sc.receiver_id = ? AND sc.status = 'pending'
                ");
                $stmt_pending->execute([$user_id]);
                $pending_reqs = $stmt_pending->fetchAll();
                ?>
                <div class="p-5 border-b bg-white">
                    <h6 class="font-black text-blue-600 mb-4 uppercase text-xs tracking-tighter"><i class="fa fa-user-check me-2"></i> Friend Requests (<?= count($pending_reqs) ?>)</h6>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php if($pending_reqs): foreach($pending_reqs as $pr): ?>
                        <div class="p-3 bg-slate-50 border rounded-2xl flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <img src="../<?= $pr['image'] ?: 'uploads/users/default.png' ?>" class="w-12 h-12 rounded-full border-2 border-white shadow-sm">
                                <div>
                                    <p class="font-black text-sm text-slate-800 m-0"><?= $pr['name'] ?></p>
                                    <small class="text-[9px] font-bold text-slate-400 uppercase"><?= $pr['sch_name'] ?></small>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <a href="manage_connection.php?action=accept&id=<?= $pr['connection_id'] ?>" class="bg-blue-600 text-white px-4 py-1.5 rounded-xl text-[10px] font-black shadow-lg">ACCEPT</a>
                                <a href="manage_connection.php?action=reject&id=<?= $pr['connection_id'] ?>" class="bg-white text-slate-400 border px-4 py-1.5 rounded-xl text-[10px] font-black">REJECT</a>
                            </div>
                        </div>
                        <?php endforeach; else: ?>
                            <p class="text-slate-400 text-xs italic">কোনো নতুন অনুরোধ নেই।</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- অংশ ২: ফ্রেন্ড সাজেশন (সর্বোচ্চ ৫০ জন) -->
                <div class="p-5">
                    <div class="flex justify-between items-center mb-4">
                        <h6 class="font-black text-slate-400 uppercase text-xs tracking-tighter">People You May Know (Suggestions)</h6>
                        <span class="text-[10px] bg-slate-200 px-2 py-0.5 rounded-full font-bold">Showing 50 Random</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4">
                        <?php
                        // সাজেশন লজিক: একই স্কুলের বা অন্যান্য স্কুলের শিক্ষার্থী যারা এখনো বন্ধু নয়
                        $stmt_sug = $pdo->prepare("
                            SELECT s.*, u.id as uid, u.image, sch.name as sch_name 
                            FROM students s 
                            JOIN users u ON s.user_id = u.id 
                            JOIN users sch ON s.school_id = sch.id
                            WHERE u.id != ? 
                            AND u.id NOT IN (SELECT receiver_id FROM student_connections WHERE sender_id = ?)
                            AND u.id NOT IN (SELECT sender_id FROM student_connections WHERE receiver_id = ?)
                            ORDER BY RAND() LIMIT 50
                        ");
                        $stmt_sug->execute([$user_id, $user_id, $user_id]);
                        $sug_list = $stmt_sug->fetchAll();

                        if($sug_list): foreach($sug_list as $sug):
                        ?>
                        <div class="p-3 bg-white border border-slate-100 rounded-2xl flex items-center justify-between hover:shadow-md transition group">
                            <div class="flex items-center gap-3">
                                <img src="../<?= $sug['image'] ?: 'uploads/users/default.png' ?>" class="w-11 h-11 rounded-full border group-hover:scale-105 transition">
                                <div>
                                    <p class="font-black text-sm text-slate-800 m-0"><?= $sug['name'] ?></p>
                                    <small class="text-blue-500 font-bold text-[9px] uppercase"><?= $sug['sch_name'] ?></small>
                                </div>
                            </div>
                            <a href="request_friend.php?id=<?= $sug['uid'] ?>" class="bg-slate-100 text-blue-600 px-4 py-1.5 rounded-xl text-[10px] font-black hover:bg-blue-600 hover:text-white transition shadow-sm">ADD FRIEND</a>
                        </div>
                        <?php endforeach; else: ?>
                            <p class="text-center text-slate-400 py-10">কোনো সাজেশন পাওয়া যায়নি।</p>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
            <div class="bg-white p-4 border-t text-center">
                <button class="bg-slate-100 text-slate-500 px-8 py-2 rounded-2xl font-black text-xs hover:bg-slate-200 transition" data-bs-dismiss="modal">Close Hub</button>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>