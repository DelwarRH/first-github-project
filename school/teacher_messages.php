<?php
session_start();
require_once '../config/db.php';
if (!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit(); }
include '../includes/header.php';

$user_id = $_SESSION['user_id'];
$school_id = $_SESSION['school_id'];
?>

<div class="container py-4">
    <div class="row g-0 shadow-lg rounded-4 overflow-hidden bg-white" style="height: 85vh;">
        <!-- বাম পাশে চ্যাট লিস্ট -->
        <div class="col-md-4 border-end d-none d-md-block bg-light">
            <div class="p-3 bg-white border-bottom fw-bold text-primary">
                <i class="fa fa-comments me-2"></i> শিক্ষার্থী ইনবক্স
            </div>
            <div class="overflow-auto" style="height: 75vh;">
                <?php
                $stmt = $pdo->prepare("SELECT * FROM students WHERE school_id = ?");
                $stmt->execute([$school_id]);
                while($stu = $stmt->fetch()):
                    $s_img = $stu['photo'] ? '../'.$stu['photo'] : 'https://i.pravatar.cc/150?u='.$stu['id'];
                ?>
                <div class="d-flex align-items-center p-3 border-bottom cursor-pointer hover-chat-item" onclick="loadChat('<?php echo $stu['id']; ?>', '<?php echo $stu['name']; ?>', '<?php echo $s_img; ?>')">
                    <img src="<?php echo $s_img; ?>" class="rounded-circle me-3" width="45" height="45">
                    <div>
                        <div class="fw-bold small"><?php echo $stu['name']; ?></div>
                        <small class="text-muted">Class: <?php echo $stu['class']; ?> | Roll: <?php echo $stu['roll']; ?></small>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- ডান পাশে চ্যাট এরিয়া -->
        <div class="col-md-8 d-flex flex-column" id="mainChatArea">
            <div class="p-3 border-bottom bg-white d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <img id="activeChatImg" src="" class="rounded-circle me-3 d-none" width="40" height="40">
                    <h6 id="activeChatName" class="fw-bold m-0 text-dark">কাউকে নির্বাচন করুন</h6>
                </div>
                <!-- কলিং বাটনসমূহ -->
                <div id="callButtons" class="d-none">
                    <button class="btn btn-light text-primary rounded-circle me-2" onclick="startCall('audio')"><i class="fa fa-phone"></i></button>
                    <button class="btn btn-light text-success rounded-circle me-2" onclick="startCall('video')"><i class="fa fa-video"></i></button>
                    <button class="btn btn-light text-danger rounded-circle"><i class="fa fa-info-circle"></i></button>
                </div>
            </div>

            <!-- মেসেজ লিস্ট -->
            <div class="flex-grow-1 p-4 overflow-auto bg-light" id="messageDisplay" style="background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png');">
                <div class="text-center mt-5">
                    <i class="fa fa-paper-plane fa-4x text-muted opacity-25"></i>
                    <p class="text-muted mt-3">নিরাপদ চ্যাট ও ভিডিও কলের মাধ্যমে পাঠদান সহজ করুন।</p>
                </div>
            </div>

            <!-- চ্যাট ইনপুট -->
            <div class="p-3 bg-white border-top d-none" id="inputArea">
                <form id="chatForm" class="d-flex gap-2">
                    <label class="btn btn-light rounded-circle"><i class="fa fa-image text-success"></i><input type="file" hidden></label>
                    <input type="text" class="form-control rounded-pill border-0 bg-light" placeholder="একটি মেসেজ লিখুন...">
                    <button class="btn btn-primary rounded-circle"><i class="fa fa-paper-plane"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ভিডিও কল মোডাল -->
<div class="modal fade" id="callModal" data-bs-backdrop="static">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content bg-dark">
            <div id="meet" style="height: 100vh;"></div>
            <button class="btn btn-danger position-absolute bottom-0 start-50 translate-middle-x mb-4 rounded-pill px-5" onclick="endCall()">কল শেষ করুন</button>
        </div>
    </div>
</div>

<script src="https://meet.jit.si/external_api.js"></script>
<script>
let api = null;

function loadChat(id, name, img) {
    document.getElementById('activeChatName').innerText = name;
    document.getElementById('activeChatImg').src = img;
    document.getElementById('activeChatImg').classList.remove('d-none');
    document.getElementById('callButtons').classList.remove('d-none');
    document.getElementById('inputArea').classList.remove('d-none');
    document.getElementById('messageDisplay').innerHTML = '<div class="text-center small text-muted">চ্যাট হিস্টোরি লোড হচ্ছে...</div>';
}

function startCall(type) {
    const myModal = new bootstrap.Modal(document.getElementById('callModal'));
    myModal.show();
    
    const domain = "meet.jit.si";
    const options = {
        roomName: "Astha_School_Room_" + Math.floor(Math.random() * 10000),
        width: "100%",
        height: "100%",
        parentNode: document.querySelector('#meet'),
        configOverwrite: { startWithAudioMuted: (type === 'video'), startWithVideoMuted: (type === 'audio') },
        interfaceConfigOverwrite: { TOOLBAR_BUTTONS: ['microphone', 'camera', 'hangup', 'chat', 'tileview'] }
    };
    api = JitsiMeetExternalAPI(domain, options);
}

function endCall() {
    if(api) api.dispose();
    location.reload();
}
</script>

<style>
    .hover-chat-item:hover { background: #e4e6eb; transition: 0.2s; }
    .cursor-pointer { cursor: pointer; }
</style>

<?php include '../includes/footer.php'; ?>