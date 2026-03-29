<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

require_role(['admin']);

$id = (int)($_GET['id'] ?? 0);

if (!$id) {
    header("Location: index.php");
    exit;
}

$admin_id = $_SESSION['user']['id'] ?? null;

/*
Validate admin exists in DB
*/
$checkAdmin = $pdo->prepare("SELECT id FROM users WHERE id = ?");
$checkAdmin->execute([$admin_id]);

if (!$checkAdmin->fetch()) {
    $admin_id = null; // fallback to NULL if missing
}

try {

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO patients_archive
        (full_name, date_of_birth, age, address, phone, email, attending_doctor_id,
         date_admitted, professional_fee, invoice_number,
         created_at, archived_by)
        SELECT
            full_name,
            date_of_birth,
            age,
            address,
            phone,
            email,
            attending_doctor_id,
            date_admitted,
            professional_fee,
            invoice_number,
            created_at,
            ?
        FROM patients
        WHERE id = ?
    ");

    $stmt->execute([$admin_id, $id]);

    $delete = $pdo->prepare("DELETE FROM patients WHERE id = ?");
    $delete->execute([$id]);

    $pdo->commit();

} catch (Exception $e) {
    $pdo->rollBack();
    die("Archive failed: " . $e->getMessage());
}

header("Location: index.php");
exit;