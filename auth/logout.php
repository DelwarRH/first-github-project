<?php
session_start();
session_destroy();
header("Location: ../index.php"); // সাকসেসফুলি ইনডেক্সে নিয়ে যাবে
exit();
?>