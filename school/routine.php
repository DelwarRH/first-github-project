<?php 
session_start();
require_once '../config/db.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$school_id = $_SESSION['school_id'] ?? $_SESSION['user_id'];
include '../includes/header.php'; 
?>

<div class="container mt-4 mb-5">
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-dark text-white py-3">
            <h5 class="m-0 fw-bold"><i class="fa fa-calendar-alt me-2 text-warning"></i> ক্লাস রুটিন ডাউনলোড প্যানেল</h5>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="table-light text-uppercase small fw-bold">
                        <tr>
                            <th class="ps-4">ক্রমিক</th>
                            <th>রুটিনের শিরোনাম</th>
                            <th>প্রকাশের তারিখ</th>
                            <th class="text-center">ফাইল ডাউনলোড</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // এই স্কুলের রুটিনগুলো আনা (school_id ফিল্টার)
                        $stmt = $pdo->prepare("SELECT * FROM downloads WHERE category='routine' AND school_id = ? ORDER BY id DESC");
                        $stmt->execute([$school_id]);
                        $routines = $stmt->fetchAll();

                        if (count($routines) > 0) {
                            $sl = 1;
                            foreach($routines as $row) {
                        ?>
                            <tr>
                                <td class="ps-4"><?php echo $sl++; ?></td>
                                <td class="fw-bold text-dark"><?php echo $row['title']; ?></td>
                                <td class="text-muted small"><?php echo date("d M, Y", strtotime($row['date'])); ?></td>
                                <td class="text-center">
                                    <a href="<?php echo $row['file_path']; ?>" class="btn btn-sm btn-danger px-3 rounded-pill fw-bold shadow-sm" download>
                                        <i class="fa fa-file-pdf me-1"></i> ডাউনলোড
                                    </a>
                                </td>
                            </tr>
                        <?php 
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center py-5 text-muted italic'>এখনও কোনো রুটিন আপলোড করা হয়নি।</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>