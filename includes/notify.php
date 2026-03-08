<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/sms_textbee.php';

/**
 * Send professional result-ready notification
 */
function send_result_notification(int $patientId, ?string $customMessage = null): array
{
    global $pdo;

    try {

        /* 1️⃣ Get patient information */
        $stmt = $pdo->prepare("
            SELECT id, full_name, phone
            FROM patients
            WHERE id = ?
            LIMIT 1
        ");
        $stmt->execute([$patientId]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$patient) {
            return ['ok' => false, 'error' => 'Patient not found'];
        }

        if (empty($patient['phone'])) {
            return ['ok' => false, 'error' => 'Patient has no phone number'];
        }

        /* 2️⃣ Professional default message */
        $message = $customMessage;

        if (!$message) {
            $message =
                "Good day {$patient['full_name']},\n\n" .
                "Your medical results are now available.\n" .
                "You may visit the clinic to review and claim them at your convenience.\n\n" .
                "For inquiries, please contact our clinic.\n\n" .
                "Thank you.";
        }

        /* 3️⃣ Send SMS */
        $smsResult = sms_textbee_send($patient['phone'], $message);

        /* 4️⃣ Log notification */
        $log = $pdo->prepare("
            INSERT INTO notification_logs
            (patient_id, phone, message, status, response)
            VALUES (?,?,?,?,?)
        ");

        $log->execute([
            $patient['id'],
            $patient['phone'],
            $message,
            $smsResult['ok'] ? 'sent' : 'failed',
            json_encode($smsResult)
        ]);

        return $smsResult;

    } catch (Exception $e) {
        return ['ok' => false, 'error' => $e->getMessage()];
    }
}