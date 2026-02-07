<?php 
session_start();
require_once '../config/db.php'; 

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM notices WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
} else {
    header("Location: notice_board.php");
    exit();
}

include '../includes/header.php'; 
?>

<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <?php if ($row) { ?>
                    <div class="card-header bg-dark text-white py-3 px-4">
                        <h4 class="m-0 fw-bold"><i class="fa fa-file-alt text-warning me-2"></i> <?php echo $row['title']; ?></h4>
                    </div>
                    <div class="card-body p-5 bg-white">
                        <div class="text-muted border-bottom pb-2 mb-4 d-flex justify-content-between small">
                            <span><i class="fa fa-calendar-check"></i> প্রকাশের তারিখ: <?php echo date("d M, Y", strtotime($row['date'])); ?></span>
                            <span class="no-print" onclick="window.print()" style="cursor:pointer"><i class="fa fa-print"></i> প্রিন্ট করুন</span>
                        </div>
                        <div class="notice-text" style="font-size: 17px; line-height: 1.8; color: #333; text-align: justify;">
                            <?php echo nl2br($row['description']); ?>
                        </div>
                        <div class="mt-5 text-center no-print border-top pt-4">
                            <a href="notice_board.php?school_id=<?php echo $row['school_id']; ?>" class="btn btn-secondary rounded-pill px-4">তালিকায় ফিরুন</a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>