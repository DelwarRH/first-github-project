<?php include 'layout_header.php'; ?>
<h5 class="section-title"><i class="fa fa-trophy text-warning"></i> শীর্ষ মেধা তালিকা (Top 10 Students)</h5>

<div class="row g-4 justify-content-center">
    <?php
    $stmt = $pdo->prepare("SELECT r.student_roll, SUM(r.marks) as total_marks, s.name, s.photo 
                       FROM results r JOIN students s ON r.student_roll = s.roll AND r.school_id = s.school_id
                       WHERE r.school_id = ? 
                       GROUP BY r.student_roll, s.name, s.photo 
                       ORDER BY total_marks DESC LIMIT 10");
$stmt->execute([$school_id]);
    $rank = 1;
    while($m = $stmt->fetch()):
    ?>
    <div class="col-md-4 col-lg-3">
        <div class="card border-0 shadow-lg rounded-4 text-center p-3 h-100" style="background: linear-gradient(135deg, #ffffff 0%, #f9f9f9 100%);">
            <div class="position-absolute top-0 start-0 m-2">
                <span class="badge bg-warning text-dark shadow-sm">Rank: <?php echo $rank++; ?></span>
            </div>
            <img src="../<?php echo $m['photo'] ?: 'uploads/logo.jpg'; ?>" class="rounded-circle mx-auto mb-3 border border-4 border-white shadow-sm" width="85" height="85" style="object-fit: cover;">
            <h6 class="fw-bold mb-1"><?php echo $m['name']; ?></h6>
            <p class="text-muted small mb-2">Roll: <?php echo $m['student_roll']; ?></p>
            <div class="bg-primary text-white py-1 rounded-pill fw-bold">Marks: <?php echo $m['total_marks']; ?></div>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<?php include 'layout_footer.php'; ?>