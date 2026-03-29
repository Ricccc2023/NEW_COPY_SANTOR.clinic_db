<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_role(['admin','staff']);

if (!isset($_GET['visit_id'])) {
    exit;
}

$visit_id = (int) $_GET['visit_id'];

/* ==============================
   Soft Archive Billing
============================== */

$stmt = $pdo->prepare("
    UPDATE billings
    SET is_archived = 1
    WHERE visit_id = ?
");
$stmt->execute([$visit_id]);

exit;