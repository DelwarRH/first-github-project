<?php 
include 'layout_header.php'; 
$role_filter = $_GET['role'] ?? 'teacher';
?>

<div class="card shadow-sm border-0 rounded-4 overflow-hidden bg-white">
    <div class="card-header bg-success text-white p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <h4 class="fw-bold m-0"><i class="fa fa-users me-2"></i> <?php echo ($role_filter == 'teacher') ? 'সম্মানিত শিক্ষক তালিকা' : 'কর্মচারী তালিকা'; ?></h4>
        <div class="btn-group shadow-sm no-print">
            <a href="?role=teacher" class="btn btn-light btn-sm fw-bold <?php echo ($role_filter == 'teacher') ? 'active' : ''; ?>">Teacher</a>
            <a href="?role=staff" class="btn btn-light btn-sm fw-bold <?php echo ($role_filter == 'staff') ? 'active' : ''; ?>">Staff</a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-erp mb-0">
            <thead>
                <tr>
                    <th width="80">ছবি</th>
                    <th>নাম</th>
                    <th>পদবী/বিষয়</th>
                    <th>ইমেইল</th>
                    <th>অবস্থা</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->prepare("SELECT * FROM users WHERE school_id = ? AND role = ? AND status = 'active' ORDER BY name ASC");
                $stmt->execute([$school_id, $role_filter]);
                while($row = $stmt->fetch()):
                    $img = !empty($row['image']) ? '../'.$row['image'] : 'https://via.placeholder.com/50';
                ?>
                <tr>
                    <td><img src="<?php echo $img; ?>" width="40" height="40" class="rounded-circle border shadow-sm" style="object-fit: cover;"></td>
                    <td class="text-start ps-3 fw-bold text-dark"><?php echo $row['name']; ?></td>
                    <td><span class="badge bg-info text-dark"><?php echo $row['subject'] ?: ucfirst($row['role']); ?></span></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><span class="text-success fw-bold small">Active</span></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'layout_footer.php'; ?>