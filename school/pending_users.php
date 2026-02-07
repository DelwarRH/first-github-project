<?php 
session_start();
require_once '../config/db.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'school') {
    header("Location: ../login.php"); exit();
}

$school_id = $_SESSION['user_id'];
$role_filter = $_GET['role'] ?? 'teacher';

// অনুমোদন লজিক
if (isset($_GET['approve_user'])) {
    $stmt = $pdo->prepare("UPDATE users SET status='active' WHERE id = ? AND school_id = ?");
    $stmt->execute([$_GET['approve_user'], $school_id]);
    header("Location: pending_users.php?role=$role_filter"); exit();
}

include '../includes/header.php'; 
?>

<div class="container mt-4 mb-5">
    <div class="p-3 bg-white shadow-sm rounded-4 border-start border-4 border-warning mb-4 d-flex justify-content-between align-items-center">
        <h4 class="fw-bold text-warning m-0"><i class="fa fa-user-shield me-2"></i> পেন্ডিং <?php echo strtoupper($role_filter); ?> তালিকা</h4>
        <a href="manage_students.php" class="btn btn-sm btn-danger rounded-pill px-4 fw-bold shadow-sm">ড্যাশবোর্ড</a>
    </div>

    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <table class="table table-hover text-center align-middle mb-0">
                <thead class="table-dark">
                    <tr><th>নাম</th><th>ইমেইল/ইউজারনেম</th><th>পদবী</th><th>অ্যাকশন</th></tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->prepare("SELECT * FROM users WHERE school_id = ? AND role = ? AND status='pending'");
                    $stmt->execute([$school_id, $role_filter]);
                    $users = $stmt->fetchAll();

                    if (count($users) > 0) {
                        foreach($users as $user) {
                    ?>
                    <tr>
                        <td class="fw-bold text-dark"><?php echo $user['name']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td><span class="badge bg-warning text-dark"><?php echo ucfirst($user['role']); ?></span></td>
                        <td>
                            <a href="?approve_user=<?php echo $user['id']; ?>&role=<?php echo $role_filter; ?>" class="btn btn-success btn-sm fw-bold px-4 rounded-pill shadow-sm">অনুমোদন দিন</a>
                        </td>
                    </tr>
                    <?php } } else { echo "<tr><td colspan='4' class='py-5 text-muted'>বর্তমানে কোনো পেন্ডিং ইউজার নেই।</td></tr>"; } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>