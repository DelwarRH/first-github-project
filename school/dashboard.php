<?php 
session_start();
require_once '../config/db.php'; 

// ১. সিকিউরিটি চেক
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['school', 'teacher', 'operator'])) {
    header("Location: ../login.php");
    exit();
}

$school_id = $_SESSION['school_id'];
$user_name = $_SESSION['user_name'];

// ২. সেটিংস ও ব্যাকগ্রাউন্ড
$stmt_set = $pdo->prepare("SELECT bg_image, school_logo FROM schools WHERE user_id = ?");
$stmt_set->execute([$school_id]);
$settings = $stmt_set->fetch();
$bg_url = !empty($settings['bg_image']) ? '../'.$settings['bg_image'] : '../uploads/bg.jpg';
$logo_url = !empty($settings['school_logo']) ? '../'.$settings['school_logo'] : '../uploads/logo.jpg';

// ৩. ডাটা ক্যালকুলেশন ফাংশন
function getStat($pdo, $sql, $params = []) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchColumn();
}

$today = date("Y-m-d");

// শিক্ষার্থী পরিসংখ্যান
$total_students = getStat($pdo, "SELECT COUNT(*) FROM students WHERE school_id = ?", [$school_id]);
$male_students = getStat($pdo, "SELECT COUNT(*) FROM students WHERE school_id = ? AND gender = 'Male'", [$school_id]);
$female_students = getStat($pdo, "SELECT COUNT(*) FROM students WHERE school_id = ? AND gender = 'Female'", [$school_id]);

// উপস্থিত শিক্ষার্থী
$present_total = getStat($pdo, "SELECT COUNT(*) FROM student_attendance WHERE school_id = ? AND date = ? AND status = 'Present'", [$school_id, $today]);
$present_male = getStat($pdo, "SELECT COUNT(*) FROM student_attendance sa JOIN students s ON sa.student_id = s.id WHERE sa.school_id = ? AND sa.date = ? AND sa.status = 'Present' AND s.gender = 'Male'", [$school_id, $today]);
$present_female = $present_total - $present_male;

$absent_total = $total_students - $present_total;
$pres_percent = ($total_students > 0) ? round(($present_total / $total_students) * 100, 2) : 0;
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Overview - আস্থা</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { 
            background-color: #BFBFBF; font-family: 'Hind Siliguri', sans-serif; font-size: 12px;
            background-image: url('<?php echo $bg_url; ?>'); background-size: cover; background-repeat: no-repeat;
            background-attachment: fixed; background-position: center; margin: 0; padding: 0;
        }
        .dashboard-content { background-color: rgba(255, 255, 255, 0.94); border-radius: 8px; padding: 20px; margin-top: 15px; margin-bottom: 30px; box-shadow: 0 0 20px rgba(0,0,0,0.4); }
        .sms-header { background-color: #E1E1E1; padding: 12px 20px; display: flex; justify-content: center; align-items: center; border-bottom: 2px solid #ccc; position: relative; }
        .logo-img { height: 65px; width: auto; margin-right: 15px; border-radius: 50%; background: white; padding: 2px; border: 1px solid #ccc; }
        .school-title { color: red; font-size: 26px; font-weight: 800; text-transform: uppercase; text-shadow: 1px 1px 0px white; }
        .user-info { position: absolute; right: 20px; top: 15px; font-size: 11px; background: #fff; padding: 5px 12px; border: 1px solid #999; border-radius: 4px; line-height: 1.4; }
        
        .sms-navbar { background-color: #008000; padding: 0; position: sticky; top: 0; z-index: 1000; box-shadow: 0 4px 10px rgba(0,0,0,0.2); }
        .sms-navbar .nav-link { color: white !important; font-weight: bold; padding: 11px 18px !important; border-right: 1px solid rgba(255,255,255,0.2); font-size: 13px; }
        .sms-navbar .nav-link:hover { background-color: #005000; color: #FFD700 !important; }
        .navbar-nav .dropdown:hover > .dropdown-menu { display: block; margin-top: 0; }
        .dropdown-menu { background-color: #008000; border: none; border-radius: 0; padding: 0; min-width: 180px; }
        .dropdown-item { color: white; font-size: 12px; font-weight: bold; padding: 10px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .dropdown-item:hover { background-color: #005000; color: #FFD700; }
        .logout-btn { background: linear-gradient(to bottom, #d9534f, #c9302c); color: white !important; padding: 4px 15px !important; border-radius: 4px; font-weight: bold; }

        /* টেবিল ডিজাইন (As per image) */
        .section-title { text-align: center; font-weight: bold; margin: 15px 0; color: #000; text-transform: uppercase; border-bottom: 2px solid #008000; display: inline-block; padding-bottom: 2px; }
        .table-erp { width: 100%; background: #fff; border: 1px solid #999; }
        .table-erp th { background-color: #8B4513; color: white; text-align: center; border: 1px solid #fff; padding: 5px; }
        .table-erp td { text-align: center; border: 1px solid #999; padding: 4px; font-weight: bold; }
        .bg-blue-row { background-color: #0d6efd !important; color: white; }
        .bg-grey-row { background-color: #6c757d !important; color: white; }
        .bg-cyan-row { background-color: #17a2b8 !important; color: white; }
        .sub-header th { background-color: #4682B4 !important; }
        .bg-pink-cell { background-color: #ffdae0 !important; }
    </style>
</head>
<body>

<div class="sms-header">
    <div class="header-content d-flex align-items-center">
        <img src="<?php echo $logo_url; ?>" class="logo-img" alt="Logo"> 
        <span class="school-title"><?php echo strtoupper($user_name); ?></span>
    </div>
    <div class="user-info">
        <b>ইউজার:</b> ADMIN<br><b>তারিখ:</b> <?php echo date("d-M-Y"); ?>
    </div>
</div>

<!-- আপনার মূল মেনুবারটি অপরিবর্তিত রাখা হলো -->
<nav class="navbar navbar-expand-lg sms-navbar">
    <div class="container-fluid">
        <div class="collapse navbar-collapse justify-content-center">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#">View</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="view_teachers.php">Teacher</a></li>
                        <li><a class="dropdown-item" href="view_staff_teacher.php">Staff</a></li>
                        <li><a class="dropdown-item" href="view_students.php">Student</a></li>
                        <li><a class="dropdown-item" href="fees_collection.php">Collection</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#">Student Report</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="collection_report.php">Collection Report</a></li>
                        <li><a class="dropdown-item" href="due_report.php">Due Report</a></li>
                        <li><a class="dropdown-item" href="all_students.php">All Students</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#">Examination</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="exam_result.php">Exam Result</a></li>
                        <li><a class="dropdown-item" href="tabulation_sheet.php">Tabulation Sheet</a></li>
                        <li><a class="dropdown-item" href="merit_list.php">Merit List</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#">Admit & Other</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="admit_card.php">Admit Card</a></li>
                        <li><a class="dropdown-item" href="seat_plan.php">Seat Plan</a></li>
                        <li><a class="dropdown-item" href="testimonial.php">Testimonial</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#">Attendance</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="student_attendance_entry.php">Student Attendance</a></li>
                        <li><a class="dropdown-item" href="staff_attendance_entry.php">Teacher & Staff Attendance</a></li>
                        <li><a class="dropdown-item" href="daily_attendance_report.php">Student Daily Attendance Report</a></li>
                        <li><a class="dropdown-item" href="staff_attendance_report.php">Teacher & Staff Attendance Report</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#">SMS</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="send_sms.php">Send SMS</a></li>
                        <li><a class="dropdown-item" href="sms_log.php">SMS View</a></li>
                    </ul>
                </li>
                <!-- ৭. Pending ড্রপডাউন মেনু -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="pendingMenu" data-bs-toggle="dropdown">Pending</a>
                    <ul class="dropdown-menu shadow-lg">
                        <li><a class="dropdown-item" href="pending_users.php?role=teacher">Pending Teacher</a></li>
                        <li><a class="dropdown-item" href="pending_users.php?role=student">Pending Student</a></li>
                        <li><a class="dropdown-item" href="pending_users.php?role=staff">Pending Staff</a></li>
                        <li><hr class="dropdown-divider" style="border-color: rgba(255,255,255,0.2);"></li>
                        <li><a class="dropdown-item" href="pending_applicants.php">Applicant (Online)</a></li>
                        <li><a class="dropdown-item" href="admit_log.php">Admit List (Log)</a></li>
                        <li><a class="dropdown-item" href="cancelled_list.php">Cancel List</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#">Settings</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="settings.php">Profile</a></li>
                        <li><a class="dropdown-item" href="bg_setting.php">BG Image</a></li>
                    </ul>
                </li>
            </ul>
            <a class="nav-link logout-btn shadow-sm" href="../auth/logout.php">LOGOUT</a>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="container dashboard-content">
        <h5 class="text-center fw-bold border-bottom pb-2">DASHBOARD OVERVIEW</h5>
        
        <!-- অংশ ১: Attendance Summary (হুবহু ছবির মতো) -->
        <div class="row g-2 justify-content-center mb-4">
            <div class="col-md-4">
                <div class="text-start"><span class="section-title" style="font-size: 11px;">Student's Attendance</span></div>
                <table class="table-erp">
                    <thead><tr><th>Student</th><th>IN</th><th>Out</th><th>Absent</th><th>Present</th><th>Not Out</th></tr></thead>
                    <tbody>
                        <tr class="bg-blue-row"><td>Total</td><td>0</td><td>0</td><td><?php echo $absent_total; ?></td><td><?php echo $pres_percent; ?>%</td><td>0</td></tr>
                        <tr class="bg-grey-row"><td>Male</td><td>0</td><td>0</td><td><?php echo ($male_students - $present_male); ?></td><td>0%</td><td>0</td></tr>
                        <tr class="bg-cyan-row"><td>Female</td><td>0</td><td>0</td><td><?php echo ($female_students - $present_female); ?></td><td>0%</td><td>0</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4"><div class="text-start"><span class="section-title" style="font-size: 11px;">Teacher's Attendance</span></div><table class="table-erp"><thead><tr><th>Teacher</th><th>IN</th><th>Out</th><th>Absent</th><th>Present</th><th>Not Out</th></tr></thead><tbody><tr><td>-</td><td>0</td><td>0</td><td>0</td><td>0.00%</td><td>0</td></tr></tbody></table></div>
            <div class="col-md-4"><div class="text-start"><span class="section-title" style="font-size: 11px;">Staff's Attendance</span></div><table class="table-erp"><thead><tr><th>Staff</th><th>IN</th><th>Out</th><th>Absent</th><th>Present</th><th>Not Out</th></tr></thead><tbody><tr><td>-</td><td>0</td><td>0</td><td>0</td><td>0.00%</td><td>0</td></tr></tbody></table></div>
        </div>

        <!-- অংশ ২: Class Wise Summary (হুবহু ছবির কলাম অনুযায়ী) -->
        <div class="mb-4">
            <div class="text-center"><span class="section-title">Class & Section wise Student Attendance</span></div>
            <div class="table-responsive">
                <table class="table-erp">
                    <thead class="sub-header text-white">
                        <tr>
                            <th rowspan="2">Class</th><th rowspan="2">Section</th><th rowspan="2">Student</th>
                            <th colspan="5">Male</th><th colspan="5">Female</th>
                        </tr>
                        <tr>
                            <th>Islam</th><th>Hindu</th><th>Christian</th><th>Present</th><th>%</th>
                            <th>Islam</th><th>Hindu</th><th>Christian</th><th>Present</th><th>%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $classes = ['Six', 'Seven', 'Eight', 'Nine', 'Ten'];
                        foreach ($classes as $c) {
                            $c_total = getStat($pdo, "SELECT COUNT(*) FROM students WHERE school_id=? AND class=?", [$school_id, $c]);
                            $m_islam = getStat($pdo, "SELECT COUNT(*) FROM students WHERE school_id=? AND class=? AND gender='Male' AND religion='Islam'", [$school_id, $c]);
                            $f_islam = getStat($pdo, "SELECT COUNT(*) FROM students WHERE school_id=? AND class=? AND gender='Female' AND religion='Islam'", [$school_id, $c]);
                            echo "<tr>
                                <td>$c</td><td>A</td><td class='bg-pink-cell'>$c_total</td>
                                <td>$m_islam</td><td>0</td><td>0</td><td>0</td><td>0%</td>
                                <td>$f_islam</td><td>0</td><td>0</td><td>0</td><td>0%</td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- অংশ ৩: Religion Summary (হুবহু ছবির মতো) -->
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="text-center"><span class="section-title">Religion Wise Student</span></div>
                <table class="table-erp">
                    <thead><tr><th>Religion</th><th>Total</th></tr></thead>
                    <tbody>
                        <?php 
                        $islam = getStat($pdo, "SELECT COUNT(*) FROM students WHERE school_id=? AND religion='Islam'", [$school_id]);
                        $hindu = getStat($pdo, "SELECT COUNT(*) FROM students WHERE school_id=? AND religion='Hindu'", [$school_id]);
                        ?>
                        <tr><td>ISLAM</td><td><?php echo $islam; ?></td></tr>
                        <tr><td>HINDU</td><td><?php echo $hindu; ?></td></tr>
                        <tr class="bg-grey-row"><td>Grand Total</td><td><?php echo ($islam + $hindu); ?></td></tr>
                    </tbody>
                </table>
            </div>

            <!-- গ্রাফ রিপোর্ট ফিল্টার বাটনসহ -->
            <div class="col-md-10 mt-5">
                <div class="section-title bg-warning py-1 d-block w-100">Attendance Graph Report</div>
                <div class="row g-0 border border-secondary bg-white">
                    <div class="col-md-3 p-3 bg-light border-end">
                        <h6 class="fw-bold mb-3 border-bottom pb-2">Select Type</h6>
                        <div class="form-check mb-2"><input class="form-check-input" type="radio" name="gf" onclick="updateChart('day')"><label class="form-check-label">Daily Report</label></div>
                        <div class="form-check mb-2"><input class="form-check-input" type="radio" name="gf" onclick="updateChart('week')"><label class="form-check-label">Weekly Report</label></div>
                        <div class="form-check mb-2"><input class="form-check-input" type="radio" name="gf" onclick="updateChart('month')" checked><label class="form-check-label">Monthly Report</label></div>
                    </div>
                    <div class="col-md-9 p-3">
                        <div style="height: 280px;"><canvas id="atChart"></canvas></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('atChart').getContext('2d');
    let myChart;

    function initChart(labels, data, title) {
        if (myChart) myChart.destroy();
        myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{ label: title, data: data, backgroundColor: 'rgba(0, 128, 0, 0.7)' }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }

    // প্রাথমিক লোড (Monthly)
    initChart(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], [60, 75, 80, 70, 85, 90, 88, 92, 85, 80, 75, <?php echo $pres_percent; ?>], 'Monthly Attendance %');

    function updateChart(type) {
        if(type === 'day') {
            initChart(['Sat', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu'], [80, 85, 70, 90, 88, 92], 'Daily Attendance %');
        } else if(type === 'week') {
            initChart(['Week 1', 'Week 2', 'Week 3', 'Week 4'], [75, 82, 88, 91], 'Weekly Attendance %');
        } else {
            initChart(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], [60, 75, 80, 70, 85, 90, 88, 92, 85, 80, 75, <?php echo $pres_percent; ?>], 'Monthly Attendance %');
        }
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>