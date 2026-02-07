<?php 
session_start();
require_once '../config/db.php'; 

// সিকিউরিটি চেক
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'school') {
    header("Location: ../login.php"); exit();
}

$school_id = $_SESSION['user_id']; // বর্তমান লগইন করা স্কুলের আইডি
include '../includes/header.php'; 

// ইউজার ডিলিট লজিক
if (isset($_GET['delete_id'])) {
    $del_id = $_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND school_id = ?");
    $stmt->execute([$del_id, $school_id]);
    echo "<script>alert('মুছে ফেলা হয়েছে!'); window.location.href='manage_users.php';</script>";
}
?>

<div class="container mt-5 mb-5" style="min-height: 70vh;">
    <!-- শিরোনাম এবং বাটন -->
    <div class="p-4 bg-white shadow-sm rounded-4 border-start border-4 border-success mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold text-success m-0">শিক্ষক ও স্টাফ তালিকা</h3>
            <p class="text-muted mb-0"><?php echo $_SESSION['user_name']; ?> প্যানেল</p>
        </div>
        <a href="add_user.php" class="btn btn-success fw-bold rounded-pill px-4 shadow-sm">
            <i class="fa fa-plus-circle"></i> নতুন ইউজার যুক্ত করুন
        </a>
    </div>
    
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <table class="table table-hover text-center mb-0 align-middle">
                <thead class="bg-dark text-white">
                    <tr>
                        <th class="py-3">ছবি</th>
                        <th>নাম</th>
                        <th>ইউজারনেম / ইমেইল</th>
                        <th>পদবী (Role)</th>
                        <th>বিষয়</th>
                        <th>অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // ডাটাবেজ থেকে তথ্য আনা (PDO মেথড ব্যবহার করা হয়েছে এরর এড়াতে)
                    $stmt = $pdo->prepare("SELECT * FROM users WHERE school_id = ? AND role != 'school' ORDER BY id ASC");
                    $stmt->execute([$school_id]);
                    $users = $stmt->fetchAll();

                    if (count($users) > 0) {
                        foreach($users as $row) {
                            $role_badge = ($row['role'] == 'teacher') ? 'bg-success' : 'bg-info';
                            $img_src = !empty($row['image']) ? '../'.$row['image'] : 'https://via.placeholder.com/50';
                    ?>
                        <tr>
                            <td><img src="<?php echo $img_src; ?>" class="rounded-circle border" width="45" height="45"></td>
                            <td class="text-start fw-bold text-dark"><?php echo $row['name']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><span class="badge <?php echo $role_badge; ?>"><?php echo ucfirst($row['role']); ?></span></td>
                            <td><?php echo ($row['subject'] ?? '-'); ?></td>
                            <td>
                                <a href="manage_users.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-outline-danger btn-sm rounded-pill" onclick="return confirm('নিশ্চিত মুছে ফেলবেন?')">
                                    <i class="fa fa-trash"></i> ডিলিট
                                </a>
                            </td>
                        </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='6' class='py-5 text-muted'>কোনো শিক্ষক বা স্টাফ পাওয়া যায়নি।</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>