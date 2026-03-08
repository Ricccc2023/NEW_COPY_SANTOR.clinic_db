<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/notify.php';

require_role(['admin','doctor']);

$id = (int)($_GET['id'] ?? 0);

if (!$id) {
    header("Location: ../patients/index.php");
    exit;
}

$result = send_result_notification($id);

header("Location: /clinic_db/public/patients/index.php?notify=" . ($result['ok'] ? 'success' : 'error'));
exit;