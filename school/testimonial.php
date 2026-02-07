<?php include 'layout_header.php'; ?>
<h5 class="section-title">শিক্ষার্থী প্রশংসাপত্র (Testimonial)</h5>

<form method="GET" class="row g-2 mb-4 bg-light p-3 border rounded no-print">
    <div class="col-md-5"><input type="number" name="roll" class="form-control" placeholder="শিক্ষার্থীর রোল নম্বর" required></div>
    <div class="col-md-4"><select name="class" class="form-select" required><option value="Ten">Ten</option><option value="Nine">Nine</option></select></div>
    <div class="col-md-2"><button type="submit" class="btn btn-dark w-100">খুঁজুন</button></div>
</form>

<?php if(isset($_GET['roll'])): 
    $stmt = $pdo->prepare("SELECT * FROM students WHERE roll=? AND class=? AND school_id=?");
    $stmt->execute([$_GET['roll'], $_GET['class'], $school_id]);
    $s = $stmt->fetch();
    if($s):
?>
    <div class="card p-5 border-5 border-double bg-white text-dark mx-auto shadow" style="max-width: 800px; font-family: serif; border: 10px double #000;">
        <div class="text-center mb-5">
            <img src="<?php echo $logo_url; ?>" width="80" class="mb-3">
            <h1 class="fw-bold m-0" style="font-size: 35px;"><?php echo $_SESSION['user_name']; ?></h1>
            <p class="fw-bold m-0">স্থাপিত: ১৯৯৫ ইং | তালা, সাতক্ষীরা</p>
            <h4 class="mt-4 border-bottom d-inline-block px-5 pb-1 fw-bold">প্রশংসাপত্র</h4>
        </div>
        <div style="font-size: 18px; line-height: 2.2; text-align: justify;">
            এই মর্মে প্রত্যয়ন করা যাচ্ছে যে, <b class="border-bottom border-dark px-2"><?php echo $s['name']; ?></b>, 
            পিতা: <b class="border-bottom border-dark px-2"><?php echo $s['father']; ?></b>, 
            রোল নং- <b class="border-bottom border-dark px-2"><?php echo $s['roll']; ?></b>, 
            শ্রেণি- <b class="border-bottom border-dark px-2"><?php echo $s['class']; ?></b>। 
            সে এই বিদ্যালয়ের একজন নিয়মিত শিক্ষার্থী ছিল। আমার জানামতে সে কোন রাষ্ট্রবিরোধী বা সমাজবিরোধী কাজে লিপ্ত ছিল না। 
            আমি তার ভবিষ্যৎ জীবনের উত্তরোত্তর সাফল্য ও সমৃদ্ধি কামনা করি।
        </div>
        <div class="mt-5 pt-5 d-flex justify-content-between fw-bold">
            <div class="border-top border-dark px-4 pt-1">শ্রেণি শিক্ষকের স্বাক্ষর</div>
            <div class="border-top border-dark px-4 pt-1">প্রধান শিক্ষকের স্বাক্ষর</div>
        </div>
    </div>
    <div class="text-center mt-4 no-print"><button onclick="window.print()" class="btn btn-primary px-5"><i class="fa fa-print"></i> প্রিন্ট</button></div>
<?php else: echo "<p class='text-danger text-center'>শিক্ষার্থী পাওয়া যায়নি!</p>"; endif; endif; ?>

<?php include 'layout_footer.php'; ?>