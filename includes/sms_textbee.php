<?php
declare(strict_types=1);

require_once __DIR__ . '/sms_helpers.php';
require_once __DIR__ . '/config.php';

function sms_textbee_send(string $to, string $message): array
{
    $to = normalize_ph_e164($to);

    if ($to === '' || strlen($to) < 12) {
        return ['ok' => false, 'error' => 'Invalid phone'];
    }

    if (trim($message) === '') {
        return ['ok' => false, 'error' => 'Empty message'];
    }

    $url = TEXTBEE_API_BASE . "/gateway/devices/" . TEXTBEE_DEVICE_ID . "/send-sms";

    $payload = json_encode([
        'recipients' => [$to],
        'message' => $message,
    ]);

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'x-api-key: ' . TEXTBEE_API_KEY,
        ],
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_TIMEOUT => 20,
    ]);

    $body = curl_exec($ch);
    $error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($body === false) {
        return ['ok' => false, 'error' => $error];
    }

    if ($httpCode < 200 || $httpCode >= 300) {
        return ['ok' => false, 'error' => "HTTP $httpCode", 'response' => $body];
    }

    return ['ok' => true, 'response' => $body];
}