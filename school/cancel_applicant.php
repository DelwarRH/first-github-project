<?php
session_start();
require_once '../config/db.php';

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("UPDATE admissions SET status = 'cancelled' WHERE id = ? AND school_id = ?");
    $stmt->execute([$_GET['id'], $_SESSION['school_id']]);
    header("Location: pending_applicants.php");
}