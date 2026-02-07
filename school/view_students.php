<?php 
include 'layout_header.php'; 
?>

<div class="card shadow-sm border-0 rounded-4 overflow-hidden">
    <div class="card-header bg-white p-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3">
        <h5 class="section-title m-0"><i class="fa fa-user-graduate me-2"></i> শিক্ষার্থী ডাটাবেজ</h5>
        
        <div class="d-flex align-items-center gap-2 no-print">
            <form method="GET" class="d-flex gap-2">
                <select name="class" class="form-select form-select-sm shadow-sm" style="min-width: 120px;">
                    <option value="">সকল শ্রেণি</option>
                    <option value="Six" <?php echo (isset($_GET['class']) && $_GET['class'] == 'Six') ? 'selected' : ''; ?>>Six</option>
                    <option value="Seven" <?php echo (isset($_GET['class']) && $_GET['class'] == 'Seven') ? 'selected' : ''; ?>>Seven</option>
                    <option value="Eight" <?php echo (isset($_GET['class']) && $_GET['class'] == 'Eight') ? 'selected' : ''; ?>>Eight</option>
                    <option value="Nine" <?php echo (isset($_GET['class']) && $_GET['class'] == 'Nine') ? 'selected' : ''; ?>>Nine</option>
                    <option value="Ten" <?php echo (isset($_GET['class']) && $_GET['class'] == 'Ten') ? 'selected' : ''; ?>>Ten</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm px-3 shadow-sm">ফিল্টার</button>
            </form>

            <!-- বাল্ক প্রিন্ট বাটন (শুধুমাত্র শ্রেণি সিলেক্ট থাকলে দেখাবে) -->
            <?php if(isset($_GET['class']) && !empty($_GET['class'])): ?>
                <a href="id_card.php?class=<?php echo $_GET['class']; ?>" target="_blank" class="btn btn-success btn-sm fw-bold rounded-pill px-4 shadow-sm">
                    <i class="fa fa-print me-1"></i> Print All ID Cards (Class: <?php echo $_GET['class']; ?>)
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 text-center" style="font-size: 13px;">
            <thead class="bg-light">
                <tr class="small text-uppercase fw-bold text-muted border-bottom">
                    <th class="ps-4">রোল</th>
                    <th>ছবি</th>
                    <th class="text-start">নাম ও পিতা</th>
                    <th>শ্রেণি</th>
                    <th>মোবাইল</th>
                    <th>অবস্থা</th>
                    <th class="text-end pe-4">অ্যাকশন</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // কুয়েরিতে user_id কলামটি নিশ্চিত করা হয়েছে
                $sql = "SELECT id, user_id, name, roll, class, phone, photo, father FROM students WHERE school_id = ?";
                $params = [$school_id];
                if(isset($_GET['class']) && !empty($_GET['class'])) { 
                    $sql .= " AND class = ?"; 
                    $params[] = $_GET['class'];
                }
                $sql .= " ORDER BY roll ASC";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
    
                while($s = $stmt->fetch()):
                    $photo_path = !empty($s['photo']) ? '../'.$s['photo'] : '../uploads/students/default.png';
                    $uid = $s['user_id'] ?? 0; // যদি user_id না থাকে তবে ০ ধরবে
                ?>
                <tr>
                    <td class="ps-4 fw-bold">#<?php echo $s['roll']; ?></td>
                    <td><img src="<?php echo $photo_path; ?>" class="rounded-circle border" width="40" height="40" style="object-fit:cover;"></td>
                    <td class="text-start">
                        <div class="fw-bold text-dark"><?php echo $s['name']; ?></div>
                        <small class="text-muted">পিতা: <?php echo $s['father']; ?></small>
                    </td>
                    <td><span class="badge bg-blue-100 text-blue-700"><?php echo $s['class']; ?></span></td>
                    <td class="fw-bold text-secondary"><?php echo $s['phone']; ?></td>
                    <td><span class="text-success small fw-bold">সক্রিয়</span></td>
                    <td class="text-end pe-4">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="id_card.php?id=<?php echo $s['id']; ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3 fw-bold shadow-sm" target="_blank"><i class="fa fa-id-card"></i></a>
                
                            <a href="delete_student.php?id=<?php echo $s['id']; ?>&u_id=<?php echo $uid; ?>" 
                                class="btn btn-sm btn-danger rounded-pill px-2 shadow-sm" 
                                onclick="return confirm('আপনি কি নিশ্চিতভাবে এই শিক্ষার্থীকে ডিলিট করতে চান?')">
                                <i class="fa fa-trash-alt"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .bg-blue-100 { background-color: #ebf5ff; }
    .text-blue-700 { color: #1e429f; }
    .table-hover tbody tr:hover { background-color: #f9fafb; }
    .btn-success { background-color: #008000; border-color: #008000; }
    .btn-success:hover { background-color: #006400; border-color: #006400; }
</style>

<?php include 'layout_footer.php'; ?>