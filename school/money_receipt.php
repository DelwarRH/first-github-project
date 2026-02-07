<?php 
session_start();
require_once '../config/db.php'; 

if (!isset($_GET['id'])) { die("অ্যাক্সেস ডিনাইড!"); }

// ডাটাবেজ থেকে পেমেন্ট, স্টুডেন্ট এবং স্কুলের লোগো একসাথে আনা
$stmt = $pdo->prepare("SELECT p.*, s.name as student_name, s.father, sch.eiin_number, sch.school_logo 
                       FROM student_payments p 
                       JOIN students s ON p.student_id = s.id 
                       JOIN schools sch ON p.school_id = sch.user_id 
                       WHERE p.id = ?");
$stmt->execute([$_GET['id']]);
$data = $stmt->fetch();

if (!$data) { die("ডাটা পাওয়া যায়নি!"); }

$logo_url = !empty($data['school_logo']) ? '../'.$data['school_logo'] : '../uploads/logo.jpg';
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>অফিসিয়াল মানি রিসিট - #<?php echo $data['id']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Hind Siliguri', sans-serif; background: #f0f0f0; padding: 30px; margin: 0; }
        
        /* মেইন রিসিট বক্স */
        .receipt-container {
            width: 650px; background: #fff; margin: 0 auto; 
            border: 15px double #006400; /* সুন্দর সবুজ ডাবল বর্ডার */
            padding: 30px; position: relative; box-shadow: 0 0 25px rgba(0,0,0,0.2);
            overflow: hidden;
            background-color: #fffaf0; /* হালকা ফ্লোরাল ব্যাকগ্রাউন্ড কালার */
        }

        /* জলছাপ (Watermark) */
        .watermark {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            width: 300px; opacity: 0.06; z-index: 0; pointer-events: none;
        }

        .content-wrap { position: relative; z-index: 1; }

        /* হেডার ডিজাইন */
        .header { display: flex; align-items: center; border-bottom: 3px solid #cc0000; padding-bottom: 15px; margin-bottom: 25px; }
        .logo-box img { width: 80px; height: 80px; border-radius: 50%; border: 2px solid #006400; background: #fff; padding: 3px; }
        .school-info { flex-grow: 1; text-align: center; }
        .school-name { color: #cc0000; font-size: 28px; font-weight: 800; text-transform: uppercase; margin: 0; text-shadow: 1px 1px 2px #ccc; }
        .eiin-text { color: #006400; font-weight: bold; font-size: 14px; }

        /* ইনফরমেশন টেবিল */
        .info-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .info-table td { padding: 10px 5px; border-bottom: 1px dashed #bbb; font-size: 15px; color: #333; }
        .label { font-weight: bold; color: #000; width: 130px; }
        .value { color: #004d00; font-weight: 600; }

        /* পেমেন্ট হাইলাইট */
        .payment-summary {
            margin-top: 30px; background: linear-gradient(135deg, #006400 0%, #004d00 100%);
            color: #fff; padding: 15px; border-radius: 10px; text-align: center;
            box-shadow: 0 5px 15px rgba(0,100,0,0.3);
        }
        .total-text { font-size: 20px; font-weight: 700; border: 2px dashed #ffcc00; padding: 5px 20px; display: inline-block; border-radius: 5px; }

        /* সিগনেচার */
        .footer-sign { margin-top: 60px; display: flex; justify-content: space-between; padding: 0 20px; }
        .sign-box { text-align: center; width: 150px; font-size: 13px; font-weight: bold; color: #333; }
        .line { border-top: 2px solid #333; margin-bottom: 5px; }

        .system-tag { text-align: center; font-size: 10px; color: #999; margin-top: 30px; text-transform: uppercase; }

        @media print {
            .no-print { display: none; }
            body { background: #fff; padding: 0; }
            .receipt-container { box-shadow: none; margin: 0 auto; border: 10px double #006400; -webkit-print-color-adjust: exact; }
        }

        .btn-print { padding: 12px 35px; background: #008000; color: #fff; border: none; cursor: pointer; font-weight: bold; border-radius: 50px; font-size: 16px; box-shadow: 0 4px 10px rgba(0,0,0,0.2); transition: 0.3s; }
        .btn-print:hover { background: #004d00; transform: scale(1.05); }
    </style>
</head>
<body>

    <div class="no-print" style="text-align:center; margin-bottom:25px; display: flex; justify-content: center; gap: 15px;">
        <button onclick="window.print()" class="btn-print"><i class="fa fa-print"></i> মানি রশিদ প্রিন্ট করুন</button>
        <a href="fees_collection.php" style="padding: 12px 25px; background: #666; color: #fff; text-decoration: none; border-radius: 50px; font-weight: bold; font-size: 16px;">ফিরে যান</a>
    </div>
    
    <div class="receipt-container">
        <!-- জলছাপ -->
        <img src="<?php echo $logo_url; ?>" class="watermark">

        <div class="content-wrap">
            <div class="header">
                <div class="logo-box">
                    <img src="<?php echo $logo_url; ?>" onerror="this.src='../uploads/logo.jpg'">
                </div>
                <div class="school-info">
                    <h1 class="school-name"><?php echo $_SESSION['user_name']; ?></h1>
                    <div class="eiin-text">EIIN: <?php echo $data['eiin_number']; ?> | অফিসিয়াল মানি রিসিট</div>
                </div>
            </div>
            
            <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                <span style="background: #006400; color: #fff; padding: 2px 15px; border-radius: 5px; font-weight: bold;">রসিদ নং: #<?php echo $data['id']; ?></span>
                <span class="fw-bold" style="color: #cc0000;">তারিখ: <?php echo date('d-M-Y', strtotime($data['date'])); ?></span>
            </div>

            <table class="info-table">
                <tr>
                    <td class="label">শিক্ষার্থীর নাম</td>
                    <td class="value">: <?php echo htmlspecialchars($data['student_name']); ?></td>
                </tr>
                <tr>
                    <td class="label">পিতার নাম</td>
                    <td class="value">: <?php echo htmlspecialchars($data['father']); ?></td>
                </tr>
                <tr>
                    <td class="label">শ্রেণি ও রোল</td>
                    <td class="value">: 
                        <?php 
                            // এখানে এরর প্রতিরোধের ব্যবস্থা করা হয়েছে
                            $class_name = $data['class'] ?? 'N/A';
                            $roll_no = $data['roll'] ?? 'N/A';
                            echo "$class_name (রোল: $roll_no)"; 
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="label">ফি এর ধরণ</td>
                    <td class="value">: <span style="color: #cc0000;"><?php echo htmlspecialchars($data['payment_type']); ?></span></td>
                </tr>
                <tr>
                    <td class="label">মাস ও বছর</td>
                    <td class="value">: <?php echo htmlspecialchars($data['month'] ?? ''); ?> - <?php echo htmlspecialchars($data['year'] ?? ''); ?></td>
                </tr>
            </table>

            <div class="payment-summary">
                <div style="font-size: 14px; margin-bottom: 5px; opacity: 0.9;">আদায়ের পরিমাণ (কথায়: .............................................................)</div>
                <div class="total-text">মোট আদায়কৃত টাকা: ৳ <?php echo number_format($data['amount'], 2); ?></div>
            </div>

            <div class="footer-sign">
                <div class="sign-box"><div class="line"></div>শিক্ষার্থীর স্বাক্ষর</div>
                <div class="sign-box"><div class="line"></div>ক্যাশিয়ারের স্বাক্ষর</div>
            </div>

            <div class="system-tag">ডিজিটাল ডাটা ম্যানেজমেন্ট সিস্টেম - আস্থা | টেক স্পেস বিডি</div>
        </div>
    </div>
</body>
</html>