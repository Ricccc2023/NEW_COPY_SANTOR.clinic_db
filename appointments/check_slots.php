<?php
require_once '../includes/config.php';

header('Content-Type: application/json');

$doctor = isset($_GET['doctor']) ? (int)$_GET['doctor'] : 0;
$date   = $_GET['date'] ?? '';

if (!$doctor || !$date) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT appointment_time
    FROM appointments
    WHERE doctor_id = ?
      AND appointment_date = ?
");

$stmt->execute([$doctor, $date]);

$booked = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode($booked);