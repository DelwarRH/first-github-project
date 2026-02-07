<?php
session_start();
require_once '../config/db.php';
if (!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit(); }

$school_id = $_SESSION['school_id'];

// ড্যাশবোর্ডের জন্য কিছু কুইক ডাটা (Principal/Admin এর জন্য)
$total_stu = $pdo->prepare("SELECT COUNT(*) FROM students WHERE school_id = ?");
$total_stu->execute([$school_id]);
$stu_count = $total_stu->fetchColumn();

include '../includes/header.php'; 
?>

<style>
    :root { --glass-bg: rgba(255, 255, 255, 0.95); --card-shadow: 0 10px 30px rgba(0,0,0,0.1); }
    body { background-color: #f0f2f5; font-family: 'Hind Siliguri', sans-serif; }
    
    /* স্বাগতম ব্যানার */
    .welcome-banner {
        background: linear-gradient(135deg, #003366 0%, #004080 100%);
        color: white; border-radius: 20px; padding: 30px; margin-bottom: 40px;
        box-shadow: 0 15px 35px rgba(0, 51, 102, 0.2);
    }

    /* আধুনিক কার্ড ডিজাইন */
    .mgmt-card {
        background: var(--glass-bg); border: none; border-radius: 20px;
        transition: all 0.3s ease; height: 100%; box-shadow: var(--card-shadow);
        border-bottom: 5px solid transparent;
    }
    .mgmt-card:hover { transform: translateY(-10px); border-bottom: 5px solid #ffcc00; }
    
    .icon-box {
        width: 70px; height: 70px; border-radius: 18px; display: flex;
        align-items: center; justify-content: center; margin: 0 auto 20px;
        font-size: 30px;
    }
    
    .btn-action {
        font-size: 12px; font-weight: 700; border-radius: 10px;
        padding: 8px 15px; margin: 3px; display: inline-block;
        text-decoration: none; transition: 0.2s;
    }
    .btn-view { background: #eef2ff; color: #4338ca; }
    .btn-view:hover { background: #4338ca; color: white; }
</style>

<div class="container py-4">
    <!-- স্বাগতম সেকশন -->
    <div class="welcome-banner d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h2 class="fw-black mb-1"><i class="fas fa-user-shield me-2 text-warning"></i> অ্যাডমিন কন্ট্রোল প্যানেল</h2>
            <p class="mb-0 opacity-75">স্মার্ট শিক্ষা ব্যবস্থাপনা ও ডিজিটাল মনিটরিং সিস্টেম - আস্থা</p>
        </div>
        <div class="text-end">
            <h3 class="fw-bold m-0"><?php echo $stu_count; ?></h3>
            <small class="text-uppercase fw-bold opacity-75">মোট শিক্ষার্থী</small>
        </div>
    </div>

    <!-- ৮টি ক্যাটাগরি গ্রিড (মেনু অনুযায়ী) -->
    <div class="row g-4">
        
        <!-- ১. View মডিউল -->
        <div class="col-md-4 col-lg-3">
            <div class="mgmt-card p-4 text-center">
                <div class="icon-box bg-primary bg-opacity-10 text-primary"><i class="fas fa-eye"></i></div>
                <h5 class="fw-bold mb-3">View প্যানেল</h5>
                <div class="d-flex flex-wrap justify-content-center">
                    <a href="add_user.php" class="btn btn-success btn-sm mt-2 w-100 fw-bold">+ Add Teacher/Staff</a>
                    <a href="add_student.php" class="btn btn-primary btn-sm mt-2 w-100 fw-bold">+ Add New Student</a>
                    <a href="manage_fees.php" class="btn btn-warning btn-sm mt-2 w-100 fw-bold"><i class="fa fa-coins me-1"></i> ফি কনফিগারেশন</a>
                    <a href="fees_collection.php" class="btn-action btn-view">Collection</a>
                </div>
            </div>
        </div>

        <!-- ২. Student Report -->
        <div class="col-md-4 col-lg-3">
            <div class="mgmt-card p-4 text-center">
                <div class="icon-box bg-success bg-opacity-10 text-success"><i class="fas fa-file-invoice"></i></div>
                <h5 class="fw-bold mb-3">রিপোর্ট সেকশন</h5>
                <div class="d-flex flex-wrap justify-content-center">
                    <a href="collection_report.php" class="btn-action btn-view">আদায় রিপোর্ট</a>
                    <a href="due_report.php" class="btn-action btn-view">বকেয়া তালিকা</a>
                    <a href="all_student_list.php" class="btn-action btn-view">সকল ছাত্র</a>
                </div>
            </div>
        </div>

        <!-- ৩. Examination -->
        <div class="col-md-4 col-lg-3">
            <div class="mgmt-card p-4 text-center">
                <div class="icon-box bg-dark bg-opacity-10 text-dark"><i class="fas fa-graduation-cap"></i></div>
                <h5 class="fw-bold mb-3">পরীক্ষা ও ফলাফল</h5>
                <div class="d-flex flex-wrap justify-content-center">
                    <a href="exam_result_sms.php" class="btn-action btn-view">ফলাফল এন্ট্রি</a>
                    <a href="tabulation_sheet.php" class="btn-action btn-view">ট্যাবুলেশন শিট</a>
                    <a href="merit_list.php" class="btn-action btn-view">মেধা তালিকা</a>
                </div>
            </div>
        </div>

        <!-- ৪. Admit & Other -->
        <div class="col-md-4 col-lg-3">
            <div class="mgmt-card p-4 text-center">
                <div class="icon-box bg-danger bg-opacity-10 text-danger"><i class="fas fa-id-badge"></i></div>
                <h5 class="fw-bold mb-3">প্রবেশপত্র ও অন্যান্য</h5>
                <div class="d-flex flex-wrap justify-content-center">
                    <a href="admit_card.php" class="btn-action btn-view">Admit Card</a>
                    <a href="seat_plan.php" class="btn-action btn-view">Seat Plan</a>
                    <a href="testimonial.php" class="btn-action btn-view">Testimonial</a>
                </div>
            </div>
        </div>

        <!-- ৫. Attendance -->
        <div class="col-md-4 col-lg-3">
            <div class="mgmt-card p-4 text-center">
                <div class="icon-box bg-info bg-opacity-10 text-info"><i class="fas fa-calendar-check"></i></div>
                <h5 class="fw-bold mb-3">হাজিরা ব্যবস্থাপনা</h5>
                <div class="d-flex flex-wrap justify-content-center">
                    <a href="student_attendance_entry.php" class="btn-action btn-view">Student</a>
                    <a href="staff_attendance_entry.php" class="btn-action btn-view">Teacher/Staff</a>
                    <a href="daily_attendance_report.php" class="btn-action btn-view">ডেইলি রিপোর্ট</a>
                </div>
            </div>
        </div>

        <!-- ৬. SMS Panel -->
        <div class="col-md-4 col-lg-3">
            <div class="mgmt-card p-4 text-center">
                <div class="icon-box bg-warning bg-opacity-10 text-warning"><i class="fas fa-paper-plane"></i></div>
                <h5 class="fw-bold mb-3">ডিজিটাল এসএমএস</h5>
                <div class="d-flex flex-wrap justify-content-center">
                    <a href="send_sms.php" class="btn-action btn-view">Send SMS</a>
                    <a href="sms_view.php" class="btn-action btn-view">SMS Log</a>
                </div>
            </div>
        </div>

        <!-- ৭. Pending & Admissions কার্ড -->
        <div class="col-md-4 col-lg-3">
            <div class="mgmt-card p-4 text-center">
                <div class="icon-box bg-secondary bg-opacity-10 text-secondary">
                <i class="fas fa-user-clock"></i>
                </div>
                <h5 class="fw-bold mb-3">অনুমোদন পেন্ডিং</h5>
                <div class="d-flex flex-wrap justify-content-center">
             <!-- অনলাইন থেকে আসা আবেদন দেখার জন্য -->
                <a href="pending_applicants.php" class="btn-action btn-view">ভর্তি আবেদন</a>
                <!-- নতুন শিক্ষক বা স্টাফ একাউন্ট এপ্রুভ করার জন্য -->
                <a href="pending_users.php" class="btn-action btn-view">পেন্ডিং ইউজার</a>
                </div>
            </div>
        </div>

        <!-- ৮. Student Management (System View) -->
        <div class="col-md-4 col-lg-3">
            <div class="mgmt-card p-4 text-center border-start border-4 border-success">
                <div class="icon-box bg-success bg-opacity-10 text-success"><i class="fas fa-chart-pie"></i></div>
                <h5 class="fw-bold mb-2 text-success">সার্বিক পরিসংখ্যান</h5>
                <p class="small text-muted mb-3">গ্রাফ ও হাজিরা রিপোর্ট</p>
                <a href="dashboard.php" class="btn btn-success w-100 rounded-pill fw-bold">প্রবেশ করুন</a>
            </div>
        </div>

    </div> <!-- Row End -->
</div>

<?php include '../includes/footer.php'; ?>