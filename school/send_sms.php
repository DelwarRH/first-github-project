<?php 
session_start();
require_once '../config/db.php'; 

// সিকিউরিটি চেক
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['school', 'operator', 'teacher'])) {
    header("Location: ../login.php"); exit();
}

$school_id = $_SESSION['school_id'];
$user_name = $_SESSION['user_name'];

include 'layout_header.php'; // আপনার প্রতিষ্ঠানের লোগো ও নাম সংবলিত হেডার
?>

<style>
    body { background-color: #f4f7f6; }
    /* টপ গ্রিন বার */
    .sms-top-bar {
        background-color: #008000;
        color: white;
        padding: 8px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 3px solid #cc0000;
    }
    .btn-back-dashboard {
        background-color: #d9534f;
        color: white;
        border: none;
        padding: 4px 15px;
        border-radius: 4px;
        font-weight: bold;
        text-decoration: none;
        font-size: 13px;
    }
    .btn-back-dashboard:hover { background-color: #c9302c; color: white; }

    /* কার্ড ডিজাইন */
    .card-custom { border-radius: 8px; border: 1px solid #ccc; overflow: hidden; background: #fff; }
    .card-header-blue { background-color: #0d6efd; color: white; padding: 10px 15px; font-weight: bold; }
    .card-header-grey { background-color: #6c757d; color: white; padding: 10px 15px; font-weight: bold; }
    
    .form-label { font-weight: bold; font-size: 13px; color: #333; }
    .btn-send { background-color: #198754; color: white; font-weight: bold; border-radius: 4px; border: none; padding: 10px; width: 100%; }
    .btn-send:hover { background-color: #157347; }
    
    /* টেবিল ডিজাইন */
    .table-sms-history { font-size: 12px; }
    .table-sms-history thead { background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; }
    .char-count { font-size: 11px; color: #666; text-align: right; margin-top: 5px; }
</style>

<!-- এসএমএস প্যানেল হেডার -->
<div class="sms-top-bar no-print">
    <div class="fw-bold"><i class="fa fa-envelope me-2"></i> SEND SMS PANEL</div>
    <a href="dashboard.php" class="btn-back-dashboard">Back to Dashboard</a>
</div>

<div class="container-fluid mt-4">
    <div class="row g-4 justify-content-center">
        
        <!-- বাম পাশ: Compose SMS -->
        <div class="col-md-5">
            <div class="card-custom shadow-sm border-primary">
                <div class="card-header-blue">
                    <i class="fa fa-paper-plane me-2"></i> Compose SMS
                </div>
                <div class="card-body p-4">
                    <form action="process_sms.php" method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label">Send to Class (Bulk)</label>
                            <select name="class" class="form-select form-select-sm shadow-sm">
                                <option value="">--- Select Class ---</option>
                                <option value="Six">Class Six</option>
                                <option value="Seven">Class Seven</option>
                                <option value="Eight">Class Eight</option>
                                <option value="Nine">Class Nine</option>
                                <option value="Ten">Class Ten</option>
                            </select>
                            <small class="text-muted" style="font-size: 11px;">নির্দিষ্ট ক্লাসের সবাইকে পাঠাতে এটি সিলেক্ট করুন</small>
                        </div>

                        <div class="text-center my-2 fw-bold text-muted small">- OR -</div>

                        <div class="mb-3">
                            <label class="form-label">Specific Number (Single)</label>
                            <input type="number" name="custom_number" class="form-control form-control-sm" placeholder="017xxxxxxxx">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea name="message" id="msgArea" class="form-control" rows="5" maxlength="160" placeholder="Type your message here..." required></textarea>
                            <div class="char-count">Characters: <span id="charNum">0</span>/160</div>
                        </div>

                        <button type="submit" name="send_sms" class="btn-send">
                            <i class="fa fa-paper-plane me-2"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- ডান পাশ: Recent Sent SMS History -->
        <div class="col-md-7">
            <div class="card-custom shadow-sm border-secondary">
                <div class="card-header-grey">
                    <i class="fa fa-history me-2"></i> Recent Sent SMS History
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0 table-sms-history align-middle">
                            <thead class="text-center">
                                <tr>
                                    <th width="100">Date</th>
                                    <th width="120">Receiver</th>
                                    <th>Message</th>
                                    <th width="100">Sent By</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // ডাটাবেজ থেকে সর্বশেষ ১০টি পাঠানো মেসেজ আনা হচ্ছে
                                $stmt = $pdo->prepare("SELECT * FROM sms_logs WHERE school_id = ? ORDER BY id DESC LIMIT 10");
                                $stmt->execute([$school_id]);
                                $logs = $stmt->fetchAll();

                                if (count($logs) > 0) {
                                    foreach ($logs as $log) {
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo date('d-M h:i A', strtotime($log['created_at'])); ?></td>
                                    <td class="text-center fw-bold"><?php echo $log['receiver_number']; ?></td>
                                    <td class="px-3" style="line-height: 1.4; text-align: justify;"><?php echo $log['message']; ?></td>
                                    <td class="text-center"><?php echo $log['sent_by']; ?></td>
                                </tr>
                                <?php 
                                    }
                                } else {
                                    echo "<tr><td colspan='4' class='text-center py-4 text-muted'>কোনো এসএমএস রেকর্ড পাওয়া যায়নি।</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    // ক্যারেক্টার কাউন্টার স্ক্রিপ্ট
    const msgArea = document.getElementById('msgArea');
    const charNum = document.getElementById('charNum');

    msgArea.addEventListener('input', function() {
        charNum.textContent = this.value.length;
    });
</script>

<?php include 'layout_footer.php'; ?>