<?php 
session_start();
require_once '../config/db.php'; 

// স্কুল আইডি সেশন থেকে নেওয়া (লগইন করা থাকলে) অথবা ইউআরএল থেকে (ছাত্রদের জন্য)
$school_id = $_SESSION['school_id'] ?? $_GET['school_id'] ?? null;

if (!$school_id) { die("প্রতিষ্ঠানের আইডি পাওয়া যায়নি।"); }

include '../includes/header.php'; 
?>

<div class="container mt-4 mb-5">
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-primary text-white py-3 fw-bold">
            <i class="fa fa-bell me-2"></i> নোটিশ বোর্ড (প্রতিষ্ঠানের সকল বিজ্ঞপ্তি)
        </div>
        
        <div class="card-body p-4 bg-white">
            <div class="table-responsive">
                <table class="table table-hover align-middle border">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">তারিখ</th>
                            <th>নোটিশের বিষয়</th>
                            <th class="text-center">বিস্তারিত</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // এই স্কুলের নোটিশ আনা হচ্ছে
                        $stmt = $pdo->prepare("SELECT * FROM notices WHERE school_id = ? ORDER BY id DESC");
                        $stmt->execute([$school_id]);
                        $notices = $stmt->fetchAll();

                        if (count($notices) > 0) {
                            foreach($notices as $row) {
                        ?>
                        <tr>
                            <td class="text-center text-muted small fw-bold">
                                <i class="fa fa-calendar-alt text-danger"></i> <?php echo date("d M, Y", strtotime($row['date'])); ?>
                            </td>
                            <td class="fw-bold text-dark"><?php echo $row['title']; ?></td>
                            <td class="text-center">
                                <a href="notice_details.php?id=<?php echo $row['id']; ?>&school_id=<?php echo $school_id; ?>" class="btn btn-sm btn-danger px-3 rounded-pill fw-bold">
                                    <i class="fa fa-eye"></i> দেখুন
                                </a>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo '<tr><td colspan="3" class="text-center text-muted py-5">বর্তমানে কোনো নোটিশ নেই।</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>