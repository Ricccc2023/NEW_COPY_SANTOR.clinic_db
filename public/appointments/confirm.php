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

    /* 1️⃣ Fetch appointment */
    $stmt = $pdo->prepare("
        SELECT a.*, d.full_name as doctor_name
        FROM appointments a
        LEFT JOIN doctors d ON d.id = a.doctor_id
        WHERE a.id=?
        LIMIT 1
    ");
    $stmt->execute([$id]);
    $appointment = $stmt->fetch();

    if (!$appointment) {
        $pdo->rollBack();
        header("Location:index.php");
        exit;
    }

    /* 2️⃣ Build SMS message */
    $date = date("F d, Y", strtotime($appointment['appointment_date']));
    $time = date("h:i A", strtotime($appointment['appointment_date']));

    $smsMessage = "Good day {$appointment['full_name']}! "
        . "Your appointment has been CONFIRMED.\n"
        . "Doctor: {$appointment['doctor_name']}\n"
        . "Date: {$date}\n"
        . "Time: {$time}\n\n"
        . "Please arrive 15 minutes early. Thank you!";

    /* 3️⃣ Send SMS */
    if (!empty($appointment['phone'])) {
        sms_textbee_send($appointment['phone'], $smsMessage);
    }

    /* 4️⃣ Convert to patient if not exists */
    $check = $pdo->prepare("SELECT id FROM patients WHERE appointment_id=?");
    $check->execute([$id]);

    if (!$check->fetch()) {

        $count = $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();
        $invoice = 'APP-' . date('Y') . '-' . str_pad($count + 1,4,'0',STR_PAD_LEFT);

        $insert = $pdo->prepare("
            INSERT INTO patients
            (appointment_id, full_name, age, address, phone, email,
             attending_doctor_id, date_admitted,
             professional_fee, invoice_number)
            VALUES (?,?,?,?,?,?,?,?,?,?)
        ");

        $insert->execute([
            $appointment['id'],
            $appointment['full_name'],
            $appointment['age'],
            $appointment['address'],
            $appointment['phone'],
            $appointment['email'],
            $appointment['doctor_id'],
            $appointment['appointment_date'],
            0.00,
            $invoice
        ]);
    }

    /* 5️⃣ Archive appointment */
    $archive = $pdo->prepare("
        INSERT INTO appointments_archive
        SELECT *, NOW()
        FROM appointments
        WHERE id=?
    ");
    $archive->execute([$id]);

    $pdo->prepare("DELETE FROM appointments WHERE id=?")->execute([$id]);

    $pdo->commit();

} catch (Exception $e) {
    $pdo->rollBack();
    die("Confirmation failed: " . $e->getMessage());
}

header("Location:index.php");
exit;