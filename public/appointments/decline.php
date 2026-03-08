<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/sms_textbee.php';

require_role(['admin','doctor']);

$id = (int)($_GET['id'] ?? 0);

if (!$id) {
    header("Location:index.php");
    exit;
}

try {

    $pdo->beginTransaction();

    /* 1️⃣ Get appointment details */
    $stmt = $pdo->prepare("
        SELECT * FROM appointments WHERE id=? LIMIT 1
    ");
    $stmt->execute([$id]);
    $appointment = $stmt->fetch();

    if (!$appointment) {
        $pdo->rollBack();
        header("Location:index.php");
        exit;
    }

    /* 2️⃣ Send SMS */
    $message = "Good day {$appointment['full_name']}, "
        . "we regret to inform you that your appointment request "
        . "on " . date("F d, Y h:i A", strtotime($appointment['appointment_date']))
        . " has been DECLINED.\n\n"
        . "Please book another to reschedule. Thank you.";

    if (!empty($appointment['phone'])) {
        sms_textbee_send($appointment['phone'], $message);
    }

    /* 3️⃣ Archive */
    $archive = $pdo->prepare("
        INSERT INTO appointments_archive
        SELECT *, NOW()
        FROM appointments
        WHERE id=?
    ");
    $archive->execute([$id]);

    $pdo->prepare("DELETE FROM appointments WHERE id=?")
        ->execute([$id]);

    $pdo->commit();

} catch (Exception $e) {
    $pdo->rollBack();
    die("Decline failed: " . $e->getMessage());
}

header("Location:index.php");
exit;