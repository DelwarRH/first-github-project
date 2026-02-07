<?php 
session_start();
require_once '../config/db.php'; 

// ১. সিকিউরিটি চেক
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$school_id = $_SESSION['school_id'] ?? $_SESSION['user_id']; 
include '../includes/header.php'; 
?>

<div class="container mt-4 mb-5">
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden" style="background: rgba(255,255,255,0.9);">
        <div class="card-header bg-primary text-white py-3 fw-bold">
            <i class="fa fa-users me-2"></i> শিক্ষার্থী তথ্য অনুসন্ধান
        </div>
        
        <div class="card-body p-4">
            <!-- সার্চ অপশন -->
            <div class="row justify-content-center mb-4">
                <div class="col-md-6 bg-light p-3 rounded-4 shadow-sm border">
                    <form action="" method="GET" class="d-flex gap-2">
                        <select name="class_filter" class="form-select border-primary shadow-sm" required>
                            <option value="">শ্রেণি নির্বাচন করুন</option>
                            <?php 
                            $classes = ['Six', 'Seven', 'Eight', 'Nine', 'Ten'];
                            foreach($classes as $c){
                                $selected = (isset($_GET['class_filter']) && $_GET['class_filter'] == $c) ? 'selected' : '';
                                echo "<option value='$c' $selected>$c</option>";
                            }
                            ?>
                        </select>
                        <button type="submit" class="btn btn-primary fw-bold px-4 shadow">খুঁজুন</button>
                    </form>
                </div>
            </div>

            <!-- তালিকা প্রদর্শন -->
            <?php
            if (isset($_GET['class_filter'])) {
                $class = $_GET['class_filter'];
                
                // শুধুমাত্র এই স্কুলের শিক্ষার্থীদের ডাটা আনা (school_id ফিল্টার)
                $stmt = $pdo->prepare("SELECT * FROM students WHERE class = ? AND school_id = ? ORDER BY roll ASC");
                $stmt->execute([$class, $school_id]);
                $students = $stmt->fetchAll();

                if (count($students) > 0) {
            ?>
                <h4 class="text-center text-success fw-bold mb-4 border-bottom pb-2">শ্রেণি: <?php echo $class; ?> এর তালিকা</h4>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered text-center align-middle bg-white shadow-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>রোল</th>
                                <th>ছবি</th>
                                <th>শিক্ষার্থীর নাম</th>
                                <th>পিতার নাম</th>
                                <th>শাখা</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($students as $row) { 
                                $photo = !empty($row['photo']) ? $row['photo'] : 'https://via.placeholder.com/50';
                            ?>
                            <tr>
                                <td class="fw-bold text-primary"><?php echo $row['roll']; ?></td>
                                <td><img src="<?php echo $photo; ?>" width="50" height="50" class="rounded-circle border shadow-sm"></td>
                                <td class="text-start fw-bold"><?php echo $row['name']; ?></td>
                                <td class="text-start text-muted"><?php echo $row['father']; ?></td>
                                <td><span class="badge bg-info text-dark">ক (A)</span></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php 
                } else {
                    echo '<div class="alert alert-warning text-center fw-bold">দুঃখিত! এই শ্রেণিতে কোনো শিক্ষার্থী পাওয়া যায়নি।</div>';
                }
            } else {
                echo '<div class="text-center text-muted py-5"><i class="fa fa-search fa-3x mb-3 opacity-25"></i><br>দয়া করে একটি শ্রেণি নির্বাচন করে অনুসন্ধান করুন।</div>';
            }
            ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>