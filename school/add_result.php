<?php 
session_start();
require_once '../config/db.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
    header("Location: ../login.php"); exit();
}

$school_id = $_SESSION['school_id'];
$teacher_subject = $_SESSION['user_subject'] ?? 'History'; // উদাহরণস্বরূপ

include '../includes/header.php'; 
?>

<div class="container py-5">
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-gradient-to-r from-blue-700 to-blue-500 text-white p-4">
            <h4 class="fw-bold m-0"><i class="fa fa-poll-h me-2"></i> শিক্ষার্থীদের পরীক্ষার নম্বর এন্ট্রি</h4>
            <p class="small m-0 opacity-75">বিষয়: <?php echo $teacher_subject; ?></p>
        </div>
        <div class="card-body p-4">
            <!-- ফিল্টার অংশ -->
            <form method="GET" class="row g-3 mb-4 bg-light p-3 rounded-3 border">
                <div class="col-md-4">
                    <label class="small fw-bold">শ্রেণি নির্বাচন করুন</label>
                    <select name="class" class="form-select shadow-sm" required>
                        <option value="">বাছাই করুন</option>
                        <option value="Six">Class Six</option>
                        <option value="Ten">Class Ten</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold">পরীক্ষার ধরণ</label>
                    <select name="exam" class="form-select shadow-sm" required>
                        <option value="Half Yearly">অর্ধ-বার্ষিক</option>
                        <option value="Final">বার্ষিক পরীক্ষা</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100 fw-bold shadow"><i class="fa fa-search"></i> ছাত্র তালিকা দেখুন</button>
                </div>
            </form>

            <?php if(isset($_GET['class'])): ?>
            <form action="save_marks.php" method="POST">
                <input type="hidden" name="class" value="<?php echo $_GET['class']; ?>">
                <input type="hidden" name="exam" value="<?php echo $_GET['exam']; ?>">
                <input type="hidden" name="subject" value="<?php echo $teacher_subject; ?>">

                <div class="table-responsive">
                    <table class="table table-hover align-middle border">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th class="text-center">রোল</th>
                                <th>শিক্ষার্থীর নাম</th>
                                <th width="150" class="text-center">প্রাপ্ত নম্বর</th>
                                <th>মন্তব্য</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->prepare("SELECT * FROM students WHERE class = ? AND school_id = ? ORDER BY roll ASC");
                            $stmt->execute([$_GET['class'], $school_id]);
                            while($row = $stmt->fetch()):
                            ?>
                            <tr>
                                <td class="text-center fw-bold text-primary">#<?php echo $row['roll']; ?></td>
                                <td class="fw-bold"><?php echo $row['name']; ?></td>
                                <td>
                                    <input type="number" name="marks[<?php echo $row['roll']; ?>]" class="form-control text-center border-primary shadow-sm" placeholder="00" max="100" required>
                                </td>
                                <td><input type="text" name="note[<?php echo $row['roll']; ?>]" class="form-control form-control-sm border-0 bg-light" placeholder="ঐচ্ছিক"></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-success btn-lg px-5 rounded-pill shadow-lg fw-bold">ফলাফল সংরক্ষণ করুন</button>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>