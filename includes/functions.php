<?php
// লেটার গ্রেড বের করার ফাংশন
function getGrade($marks) {
    if ($marks >= 80) return "A+";
    elseif ($marks >= 70) return "A";
    elseif ($marks >= 60) return "A-";
    elseif ($marks >= 50) return "B";
    elseif ($marks >= 40) return "C";
    elseif ($marks >= 33) return "D";
    else return "F";
}

// গ্রেড পয়েন্ট (GP) বের করার ফাংশন
function getPoint($marks) {
    if ($marks >= 80) return 5.00;
    elseif ($marks >= 70) return 4.00;
    elseif ($marks >= 60) return 3.50;
    elseif ($marks >= 50) return 3.00;
    elseif ($marks >= 40) return 2.00;
    elseif ($marks >= 33) return 1.00;
    else return 0.00;
}
?>