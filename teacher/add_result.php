<?php 
session_start();
require_once '../config/db.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
    header("Location: ../login.php");
    exit();
}

$school_id = $_SESSION['school_id']; // শিক্ষকের সাথে যুক্ত স্কুলের আইডি
$msg = "";

// রেজাল্ট সেভ করার লজিক (PDO মেথডে)
if (isset($_POST['submit_result'])) {
    $roll = $_POST['roll'];
    $class = $_POST['class'];
    $exam = $_POST['exam'];
    $subject = $_POST['subject'];
    $marks = $_POST['marks'];
    $added_by = $_SESSION['user_name'];

    try {
        // ১. চেক করা: এই রোল ও ক্লাসের ছাত্রটি এই স্কুলেই আছে কিনা
        $stmt = $pdo->prepare("SELECT name FROM students WHERE roll = ? AND class = ? AND school_id = ?");
        $stmt->execute([$roll, $class, $school_id]);
        $student = $stmt->fetch();

        if ($student) {
            // ২. রেজাল্ট ইনসার্ট (Multi-school logic: school_id সহ)
            $sql = "INSERT INTO results (school_id, student_roll, class_name, exam_term, subject, marks, year, added_by) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $pdo->prepare($sql)->execute([$school_id, $roll, $class, $exam, $subject, $marks, date("Y"), $added_by]);
            
            $msg = "<div class='alert alert-success'>সফল! <b>{$student['name']}</b> এর নম্বর সংরক্ষিত হয়েছে।</div>";
        } else {
            $msg = "<div class='alert alert-danger'>দুঃখিত! এই রোল ও শ্রেণির কোনো শিক্ষার্থী আপনার প্রতিষ্ঠানে নেই।</div>";
        }
    } catch (Exception $e) {
        $msg = "<div class='alert alert-danger'>ভুল হয়েছে: " . $e->getMessage() . "</div>";
    }
}
include '../includes/header.php'; 
?>

<div class="container mt-5">
    <div class="row">
        <!-- ফরম অংশ -->
        <div class="col-md-5">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-dark text-white fw-bold py-3"><i class="fa fa-pen-square me-2"></i> নম্বর এন্ট্রি</div>
                <div class="card-body p-4">
                    <?php echo $msg; ?>
                    <form method="POST">
                        <label class="small fw-bold">শ্রেণি</label>
                        <select name="class" class="form-select mb-3 shadow-sm" required>
                            <option value="Six">Six</option><option value="Ten">Ten</option>
                        </select>
                        
                        <label class="small fw-bold">পরীক্ষা</label>
                        <select name="exam" class="form-select mb-3 shadow-sm" required>
                            <option value="Final">বার্ষিক পরীক্ষা</option>
                        </select>

                        <div class="row">
                            <div class="col-6"><label class="small fw-bold">রোল</label><input type="number" name="roll" class="form-control mb-3 shadow-sm" required></div>
                            <div class="col-6"><label class="small fw-bold">নম্বর</label><input type="number" name="marks" class="form-control mb-3 shadow-sm" required></div>
                        </div>

                        <label class="small fw-bold">বিষয়</label>
                        <input type="text" name="subject" class="form-control mb-4 shadow-sm" placeholder="যেমন: গণিত" required>

                        <button type="submit" name="submit_result" class="btn btn-primary w-100 fw-bold py-2 shadow">সেভ করুন</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- তালিকা অংশ -->
        <div class="col-md-7">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-success text-white fw-bold py-3">সাম্প্রতিক এন্ট্রি (আপনার স্কুল)</div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr><th>রোল</th><th>বিষয়</th><th>নম্বর</th><th>অ্যাকশন</th></tr>
                        </thead>
                        <tbody>
                            <?php 
                            $stmt = $pdo->prepare("SELECT * FROM results WHERE school_id = ? ORDER BY id DESC LIMIT 10");
                            $stmt->execute([$school_id]);
                            while($row = $stmt->fetch()) {
                                echo "<tr><td>{$row['student_roll']}</td><td>{$row['subject']}</td><td><span class='badge bg-info'>{$row['marks']}</span></td><td><i class='fa fa-trash text-danger cursor-pointer'></i></td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>