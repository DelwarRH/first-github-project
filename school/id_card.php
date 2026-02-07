<?php 
session_start();
require_once '../config/db.php'; 

if (!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit(); }

$school_id = $_SESSION['school_id'];
$students = [];

// ১. লজিক: যদি নির্দিষ্ট আইডি থাকে তবে ১ জন, আর যদি শ্রেণি থাকে তবে পুরো ক্লাস
if (isset($_GET['id'])) {
    $student_id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT s.*, u.name as school_name, sch.eiin_number, sch.school_logo 
                           FROM students s 
                           JOIN users u ON s.school_id = u.id 
                           JOIN schools sch ON u.id = sch.user_id 
                           WHERE s.id = ? AND s.school_id = ?");
    $stmt->execute([$student_id, $school_id]);
    $students = $stmt->fetchAll();
} elseif (isset($_GET['class'])) {
    $class = $_GET['class'];
    $stmt = $pdo->prepare("SELECT s.*, u.name as school_name, sch.eiin_number, sch.school_logo 
                           FROM students s 
                           JOIN users u ON s.school_id = u.id 
                           JOIN schools sch ON u.id = sch.user_id 
                           WHERE s.class = ? AND s.school_id = ? ORDER BY s.roll ASC");
    $stmt->execute([$class, $school_id]);
    $students = $stmt->fetchAll();
}

if (!$students) { die("কোনো তথ্য পাওয়া যায়নি!"); }

// লোগো পাথ ঠিক করা (প্রথম রেকর্ড থেকে লোগো নিচ্ছি যেহেতু সবার স্কুল এক)
$logo_path = !empty($students[0]['school_logo']) ? '../'.$students[0]['school_logo'] : '../uploads/logo.jpg';
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>Bulk ID Card Generation - আস্থা</title>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Hind Siliguri', sans-serif; background: #eaeff2; margin: 0; padding: 20px 0; display: flex; flex-direction: column; align-items: center; }
        
        /* প্রিন্ট বাটন এরিয়া */
        .no-print { margin-bottom: 30px; display: flex; align-items: center; gap: 20px; background: white; padding: 15px 30px; border-radius: 50px; shadow: 0 4px 10px rgba(0,0,0,0.1); position: sticky; top: 10px; z-index: 1000; border: 1px solid #ddd; }
        
        .id-card-wrapper { display: flex; gap: 40px; flex-wrap: wrap; justify-content: center; margin-bottom: 50px; page-break-after: always; }
        
        /* মেইন কার্ড ডিজাইন */
        .id-card {
            width: 330px; height: 530px; border-radius: 15px; overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1); position: relative; border: 1px solid #c0c0c0;
            background: linear-gradient(to bottom, #ffffff 0%, #f4f7f6 100%);
        }

        .front-header { background: linear-gradient(135deg, #005a00 0%, #008000 100%); height: 145px; padding: 15px; text-align: center; color: white; position: relative; z-index: 1; border-bottom: 4px solid #FFD700; }
        .header-logo { width: 55px; height: 55px; background: #fff; border-radius: 50%; padding: 2px; margin-bottom: 5px; box-shadow: 0 3px 6px rgba(0,0,0,0.2); }
        .front-header h2 { font-size: 15px; margin: 0; font-weight: 700; text-transform: uppercase; line-height: 1.3; }
        .front-header p { font-size: 10px; margin: 4px 0 0; opacity: 0.9; }

        .photo-area { text-align: center; position: relative; z-index: 10; margin-top: -45px; }
        .student-photo { width: 115px; height: 115px; border-radius: 50%; border: 5px solid #fff; object-fit: cover; box-shadow: 0 4px 10px rgba(0,0,0,0.1); background: #fff; }

        .details { padding: 15px 25px; text-align: center; }
        .details h3 { margin: 5px 0 15px; color: #006400; font-size: 21px; font-weight: 800; border-bottom: 1px solid #ddd; display: inline-block; padding-bottom: 3px; }
        
        .info-table { width: 100%; text-align: left; font-size: 13px; color: #333; line-height: 1.6; }
        .label { font-weight: 700; color: #555; width: 95px; }
        .value { font-weight: 700; color: #000; }

        .footer-front { position: absolute; bottom: 25px; width: 100%; text-align: right; padding-right: 35px; }
        .signature-box { display: inline-block; text-align: center; font-size: 11px; color: #333; }
        .sign-line { border-top: 1px solid #333; width: 110px; margin-bottom: 4px; }

        .back-side { display: flex; flex-direction: column; align-items: center; height: 100%; padding: 0; position: relative; }
        .back-top { background: #005a00; color: white; width: 100%; padding: 12px 0; text-align: center; font-weight: bold; font-size: 14px; letter-spacing: 1px; }
        .qr-wrapper { margin-top: 40px; background: #fff; padding: 12px; border: 1px solid #ddd; border-radius: 10px; }
        .qr-code { width: 135px; height: 135px; }
        .back-details { padding: 30px 20px; text-align: center; font-size: 12px; color: #444; line-height: 1.5; }
        .emergency-strip { background: #d32f2f; color: white; width: 100%; padding: 10px 0; font-size: 14px; font-weight: 800; position: absolute; bottom: 55px; text-align: center; }
        .powered-by { position: absolute; bottom: 15px; font-size: 10px; font-weight: 700; color: #005a00; }

        @media print {
            .no-print { display: none !important; }
            body { background: none; padding: 0; }
            .id-card-wrapper { gap: 20px; margin-top: 10px; margin-bottom: 20px; }
            .id-card { box-shadow: none; border: 1px solid #000; -webkit-print-color-adjust: exact; }
        }

        .btn-print { padding: 12px 35px; background: #008000; color: #fff; border: none; border-radius: 50px; cursor: pointer; font-weight: bold; font-size: 16px; box-shadow: 0 4px 12px rgba(0,128,0,0.2); transition: 0.3s; text-decoration: none; }
        .btn-print:hover { background: #006400; transform: translateY(-2px); }
    </style>
</head>
<body>

    <div class="no-print">
        <h5 class="m-0 fw-bold text-dark me-4">
            <i class="fa fa-layer-group text-success"></i> 
            <?php echo isset($_GET['class']) ? "শ্রেণি: ".$_GET['class']." (মোট ".count($students)." টি কার্ড)" : "একক আইডি কার্ড"; ?>
        </h5>
        <a href="javascript:window.print()" class="btn-print">
            <i class="fa fa-print me-2"></i> এক সাথে প্রিন্ট করুন
        </a>
        <a href="view_students.php" style="color: #64748b; font-weight: 600; text-decoration: none; margin-left: 20px;">
            <i class="fa fa-arrow-left me-1"></i> ফিরে যান
        </a>
    </div>

    <?php 
    foreach ($students as $data): 
        // কিউআর কোড ডাটা প্রতিটি শিক্ষার্থীর জন্য আলাদা
        $qr_data = "ID:" . $data['id'] . "|Roll:" . $data['roll'] . "|Name:" . $data['name'];
        $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qr_data);
    ?>
    <div class="id-card-wrapper">
        <!-- FRONT SIDE -->
        <div class="id-card">
            <div class="front-header">
                <img src="<?php echo $logo_path; ?>" class="header-logo" onerror="this.src='../uploads/logo.jpg'">
                <h2><?php echo $data['school_name']; ?></h2>
                <p>EIIN: <?php echo $data['eiin_number']; ?> | ডিজিটাল আইডি কার্ড</p>
            </div>
            <div class="photo-area">
                <img src="../<?php echo $data['photo']; ?>" class="student-photo" onerror="this.src='https://via.placeholder.com/120?text=Student'">
            </div>
            <div class="details">
                <h3><?php echo $data['name']; ?></h3>
                <table class="info-table">
                    <tr><td class="label">পিতার নাম</td><td class="value">: <?php echo $data['father']; ?></td></tr>
                    <tr><td class="label">শ্রেণি</td><td class="value">: <?php echo $data['class']; ?><?php echo !empty($data['section']) ? ' ('.$data['section'].')' : ''; ?></td></tr>
                    <tr><td class="label">রোল নম্বর</td><td class="value">: <?php echo $data['roll']; ?></td></tr>
                    <tr><td class="label">রক্তের গ্রুপ</td><td class="value">: <span style="color:#d32f2f;"><?php echo $data['blood_group'] ?: 'N/A'; ?></span></td></tr>
                    <tr><td class="label">জরুরি নম্বর</td><td class="value">: <?php echo $data['phone']; ?></td></tr>
                </table>
            </div>
            <div class="footer-front">
                <div class="signature-box">
                    <div class="sign-line"></div>
                    প্রধান শিক্ষক
                </div>
            </div>
        </div>

        <!-- BACK SIDE -->
        <div class="id-card">
            <div class="back-side">
                <div class="back-top">DIGITAL SECURITY ACCESS</div>
                <div class="qr-wrapper"><img src="<?php echo $qr_url; ?>" class="qr-code"></div>
                <div class="back-details">
                    <strong style="color: #005a00;">প্রতিষ্ঠানের ঠিকানা:</strong><br>
                    তালা সদর, সাতক্ষীরা, বাংলাদেশ<br>
                    ইমেইল: info@astha-smart.com<br>
                    ওয়েব: www.astha-sms.com
                </div>
                <div class="emergency-strip">জরুরি প্রয়োজনে: ০১৭০০-০০০০০০</div>
                <div class="powered-by"><i class="fa fa-shield-alt text-warning"></i> ASTHA SMART SCHOOL SYSTEM</div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

</body>
</html>