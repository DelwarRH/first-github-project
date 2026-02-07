<?php 
include 'layout_header.php'; 

// পরিসংখ্যান সংগ্রহ (Quick Stats)
$stmt_stats = $pdo->prepare("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN gender = 'Male' THEN 1 ELSE 0 END) as boys,
    SUM(CASE WHEN gender = 'Female' THEN 1 ELSE 0 END) as girls,
    SUM(CASE WHEN is_stipend = 1 THEN 1 ELSE 0 END) as stipend
    FROM students WHERE school_id = ?");
$stmt_stats->execute([$school_id]);
$stats = $stmt_stats->fetch();

// ফিল্টার লজিক
$class_filter = $_GET['class'] ?? '';
$gender_filter = $_GET['gender'] ?? '';

$sql = "SELECT * FROM students WHERE school_id = ?";
$params = [$school_id];

if (!empty($class_filter)) {
    $sql .= " AND class = ?";
    $params[] = $class_filter;
}
if (!empty($gender_filter)) {
    $sql .= " AND gender = ?";
    $params[] = $gender_filter;
}
$sql .= " ORDER BY class, roll ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll();
?>

<div class="container-fluid no-print">
    <!-- কুইক স্ট্যাটাস কার্ডস -->
    <div class="row g-3 mb-4 mt-2">
        <div class="col-md-3">
            <div class="p-3 bg-white border-start border-4 border-primary shadow-sm rounded-3">
                <small class="text-muted fw-bold d-block">মোট শিক্ষার্থী</small>
                <h4 class="fw-black m-0 text-primary"><?php echo $stats['total']; ?></h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-3 bg-white border-start border-4 border-info shadow-sm rounded-3">
                <small class="text-muted fw-bold d-block">ছাত্র (Boys)</small>
                <h4 class="fw-black m-0 text-info"><?php echo $stats['boys'] ?? 0; ?></h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-3 bg-white border-start border-4 border-danger shadow-sm rounded-3">
                <small class="text-muted fw-bold d-block">ছাত্রী (Girls)</small>
                <h4 class="fw-black m-0 text-danger"><?php echo $stats['girls'] ?? 0; ?></h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-3 bg-white border-start border-4 border-success shadow-sm rounded-3">
                <small class="text-muted fw-bold d-block">উপবৃত্তি প্রাপ্ত</small>
                <h4 class="fw-black m-0 text-success"><?php echo $stats['stipend'] ?? 0; ?></h4>
            </div>
        </div>
    </div>

    <!-- ফিল্টার বক্স -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="small fw-bold">শ্রেণি অনুযায়ী দেখুন</label>
                    <select name="class" class="form-select form-select-sm shadow-sm">
                        <option value="">সকল শ্রেণি</option>
                        <option value="Six" <?php echo $class_filter == 'Six' ? 'selected' : ''; ?>>Class Six</option>
                        <option value="Ten" <?php echo $class_filter == 'Ten' ? 'selected' : ''; ?>>Class Ten</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold">লিঙ্গ</label>
                    <select name="gender" class="form-select form-select-sm shadow-sm">
                        <option value="">উভয়</option>
                        <option value="Male" <?php echo $gender_filter == 'Male' ? 'selected' : ''; ?>>ছাত্র (Male)</option>
                        <option value="Female" <?php echo $gender_filter == 'Female' ? 'selected' : ''; ?>>ছাত্রী (Female)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold shadow">ফিল্টার করুন</button>
                </div>
                <div class="col-md-2">
                    <button type="button" onclick="window.print()" class="btn btn-dark btn-sm w-100 fw-bold shadow">প্রিন্ট রিপোর্ট</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- মেইন টেবিল ডাটা -->
<div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-5">
    <div class="card-header bg-dark text-white p-3 text-center">
        <h5 class="m-0 fw-bold">প্রতিষ্ঠানের সকল শিক্ষার্থীর তথ্য বিবরণী</h5>
        <small class="opacity-75">রিপোর্ট তৈরির তারিখ: <?php echo date('d-M-Y'); ?></small>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 text-center" style="font-size: 13px;">
            <thead class="bg-light">
                <tr class="fw-bold text-muted border-bottom">
                    <th width="80">রোল</th>
                    <th>ছবি</th>
                    <th class="text-start">শিক্ষার্থীর নাম</th>
                    <th class="text-start">পিতার নাম</th>
                    <th>শ্রেণি</th>
                    <th>মোবাইল নম্বর</th>
                    <th>লিঙ্গ</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($students) > 0): foreach($students as $s): 
                    $photo_path = !empty($s['photo']) ? '../'.$s['photo'] : '../uploads/students/default.png';
                ?>
                <tr>
                    <td class="fw-bold">#<?php echo $s['roll']; ?></td>
                    <td><img src="<?php echo $photo_path; ?>" class="rounded border shadow-sm" width="35" height="40" style="object-fit: cover;"></td>
                    <td class="text-start fw-bold text-dark"><?php echo $s['name']; ?></td>
                    <td class="text-start"><?php echo $s['father']; ?></td>
                    <td><span class="badge bg-info text-dark"><?php echo $s['class']; ?></span></td>
                    <td class="fw-bold text-secondary"><?php echo $s['phone']; ?></td>
                    <td><?php echo ($s['gender'] == 'Male') ? 'ছাত্র' : 'ছাত্রী'; ?></td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="7" class="py-5 text-muted">এই ক্যাটাগরিতে কোনো শিক্ষার্থীর তথ্য পাওয়া যায়নি।</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    @media print {
        .no-print { display: none !important; }
        .dashboard-content { background-color: white !important; box-shadow: none !important; margin-top: 0 !important; }
        body { background-image: none !important; background-color: white !important; }
        .table { border: 1px solid #000 !important; }
    }
</style>

<?php include 'layout_footer.php'; ?>