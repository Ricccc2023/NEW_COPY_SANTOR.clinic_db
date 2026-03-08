<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';

require_role(['admin']);

$id = (int)$_GET['id'];

$pdo->beginTransaction();

$stmt = $pdo->prepare("
INSERT INTO patients
(full_name, age, address, phone, email, attending_doctor_id, date_admitted,
professional_fee, invoice_number, created_at)
SELECT full_name, age, address, phone, email, attending_doctor_id, date_admitted,
professional_fee, invoice_number, created_at
FROM patients_archive WHERE id = ?
");
$stmt->execute([$id]);

$delete = $pdo->prepare("DELETE FROM patients_archive WHERE id = ?");
$delete->execute([$id]);

$pdo->commit();

header("Location: archive_list.php");
exit;