<?php 
session_start();
require_once '../config/db.php'; 

// গ্রেড বের করার ফাংশন
function getGrade($marks) {
    if ($marks >= 80) return "A+";
    elseif ($marks >= 70) return "A";
    elseif ($marks >= 60) return "A-";
    elseif ($marks >= 50) return "B";
    elseif ($marks >= 40) return "C";
    elseif ($marks >= 33) return "D";
    else return "F";
}

// পয়েন্ট বের করার ফাংশন
function getPoint($marks) {
    if ($marks >= 80) return 5.00;
    elseif ($marks >= 70) return 4.00;
    elseif ($marks >= 60) return 3.50;
    elseif ($marks >= 50) return 3.00;
    elseif ($marks >= 40) return 2.00;
    elseif ($marks >= 33) return 1.00;
    else return 0.00;
}

// কোন স্কুলের রেজাল্ট দেখা হচ্ছে?
$school_id = $_GET['school_id'] ?? $_POST['school_id'] ?? null;
if (!$school_id) { die("প্রতিষ্ঠানের আইডি পাওয়া যায়নি।"); }

// প্রতিষ্ঠানের তথ্য আনা (মার্কশিট হেডার ও ওয়াটারমার্কের জন্য)
$stmt_info = $pdo->prepare("SELECT u.name, s.eiin_number, s.school_logo FROM users u JOIN schools s ON u.id = s.user_id WHERE u.id = ?");
$stmt_info->execute([$school_id]);
$school = $stmt_info->fetch();

include '../includes/header.php'; 
?>

<style>
    .marksheet-container { background: #fff; border: 5px double #154360; padding: 40px; position: relative; margin-top: 30px; box-shadow: 0 0 20px rgba(0,0,0,0.1); overflow: hidden; z-index: 1; }
    .watermark { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 350px; opacity: 0.08; z-index: 0; pointer-events: none; }
    .sheet-header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 15px; margin-bottom: 25px; position: relative; }
    .sheet-logo { width: 80px; position: absolute; left: 0; top: 5px; }
    .school-headline { font-size: 28px; font-weight: bold; color: #154360; margin: 0; }
    .result-table th, .result-table td { border: 1px solid #000; padding: 8px; text-align: center; font-size: 14px; }
    @media print { .no-print { display: none !important; } .marksheet-container { border: 3px solid #000; margin: 0 auto; width: 100%; } }
</style>

<div class="container mb-5">
    <div class="row justify-content-center no-print mt-5">
        <div class="col-md-8">
            <div class="card shadow border-success">
                <div class="card-header bg-success text-white text-center fw-bold">
                    <i class="fa fa-search"></i> ফলাফল অনুসন্ধান করুন (<?php echo $school['name']; ?>)
                </div>
                <div class="card-body bg-light p-4">
                    <form action="" method="GET">
                        <input type="hidden" name="school_id" value="<?php echo $school_id; ?>">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <select name="class_name" class="form-select border-success" required>
                                    <option value="">শ্রেণি বাছাই</option>
                                    <option value="Six">Six</option><option value="Ten">Ten</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="exam_term" class="form-select border-success" required>
                                    <option value="Half Yearly">অর্ধবার্ষিক</option>
                                    <option value="Final">বার্ষিক</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="number" name="roll" class="form-control border-success" placeholder="রোল নম্বর" required>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <button type="submit" name="search" class="btn btn-success fw-bold px-5 rounded-pill shadow">ফলাফল দেখুন</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php
    if (isset($_GET['search'])) {
        $class = $_GET['class_name'];
        $exam = $_GET['exam_term'];
        $roll = $_GET['roll'];

        // ছাত্রের তথ্য আনা (school_id ফিল্টার সহ)
        $stmt_stu = $pdo->prepare("SELECT * FROM students WHERE class = ? AND roll = ? AND school_id = ?");
        $stmt_stu->execute([$class, $roll, $school_id]);
        $student = $stmt_stu->fetch();

        // রেজাল্ট ডাটা আনা
        $stmt_res = $pdo->prepare("SELECT * FROM results WHERE class_name = ? AND student_roll = ? AND exam_term = ? AND school_id = ?");
        $stmt_res->execute([$class, $roll, $exam, $school_id]);
        $results = $stmt_res->fetchAll();

        if (count($results) > 0 && $student) {
    ?>
        <div class="marksheet-container mt-5">
            <img src="../<?php echo $school['school_logo']; ?>" class="watermark">
            <div class="sheet-header">
                <img src="../<?php echo $school['school_logo']; ?>" class="sheet-logo">
                <h1 class="school-headline"><?php echo $school['name']; ?></h1>
                <p class="m-0 fw-bold">EIIN: <?php echo $school['eiin_number']; ?> | ডিজিটাল ডাটা ম্যানেজমেন্ট সিস্টেম - আস্থা</p>
                <div class="badge bg-dark px-4 py-2 mt-2">ACADEMIC TRANSCRIPT</div>
            </div>

            <div class="row mb-4">
                <div class="col-9">
                    <table class="table table-borderless table-sm">
                        <tr><td width="120">শিক্ষার্থীর নাম</td><td>: <b><?php echo $student['name']; ?></b></td></tr>
                        <tr><td>শ্রেণি ও রোল</td><td>: <?php echo $class; ?> (রোল: <?php echo $roll; ?>)</td></tr>
                        <tr><td>পরীক্ষার নাম</td><td>: <?php echo $exam; ?> - <?php echo date("Y"); ?></td></tr>
                    </table>
                </div>
                <div class="col-3 text-end">
                    <img src="../<?php echo !empty($student['photo']) ? $student['photo'] : 'https://via.placeholder.com/120'; ?>" style="width: 100px; border: 1px solid #000; padding: 2px;">
                </div>
            </div>

            <table class="result-table w-100">
                <thead>
                    <tr class="bg-light"><th>Sl.</th><th>বিষয়ের নাম</th><th>প্রাপ্ত নম্বর</th><th>গ্রেড</th><th>পয়েন্ট</th></tr>
                </thead>
                <tbody>
                    <?php
                    $sl = 1; $total_marks = 0; $total_gp = 0; $is_fail = false;
                    foreach($results as $row) {
                        $grade = getGrade($row['marks']);
                        $point = getPoint($row['marks']);
                        if($grade == 'F') $is_fail = true;
                        echo "<tr><td>".$sl++."</td><td class='text-start ps-3'>".$row['subject']."</td><td>".$row['marks']."</td><td>".$grade."</td><td>".number_format($point, 2)."</td></tr>";
                        $total_marks += $row['marks'];
                        $total_gp += $point;
                    }
                    $gpa = $is_fail ? 0.00 : ($total_gp / count($results));
                    ?>
                    <tr class="fw-bold bg-light">
                        <td colspan="2" class="text-end pe-3">সর্বমোট ও জিপিএ</td>
                        <td><?php echo $total_marks; ?></td>
                        <td><?php echo $is_fail ? 'F' : 'GPA'; ?></td>
                        <td><?php echo number_format($gpa, 2); ?></td>
                    </tr>
                </tbody>
            </table>

            <div class="mt-4 d-flex justify-content-between text-center fw-bold" style="margin-top: 80px;">
                <div style="width: 150px; border-top: 1px solid #000;">শ্রেণি শিক্ষক</div>
                <div style="width: 150px; border-top: 1px solid #000;">পরীক্ষা নিয়ন্ত্রক</div>
                <div style="width: 150px; border-top: 1px solid #000;">প্রধান শিক্ষক</div>
            </div>
        </div>

        <div class="text-center mt-4 mb-5 no-print">
            <button onclick="window.print()" class="btn btn-dark fw-bold shadow px-5"><i class="fa fa-print"></i> মার্কশিট প্রিন্ট করুন</button>
        </div>
    <?php 
        } else {
            echo '<div class="alert alert-danger text-center mt-5">দুঃখিত! এই রোল ও শ্রেণির কোনো ফলাফল পাওয়া যায়নি।</div>';
        }
    }
    ?>
</div>

<?php include '../includes/footer.php'; ?>