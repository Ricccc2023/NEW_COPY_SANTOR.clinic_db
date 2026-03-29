<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/auth.php";

if (($_SESSION['user']['role'] ?? '') !== 'admin') {
    header("Location: index.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: index.php");
    exit;
}

try {

    $pdo->beginTransaction();

    // 1️⃣ Get doctor record
    $stmt = $pdo->prepare("SELECT * FROM doctors WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $doctor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$doctor) {
        throw new Exception("Doctor not found.");
    }

    // 2️⃣ Insert into archive table (MATCHING YOUR TABLE STRUCTURE)
    $archiveStmt = $pdo->prepare("
        INSERT INTO doctors_archive 
        (id, full_name, phone, created_at, user_id, archived_at, archived_by)
        VALUES 
        (:id, :full_name, :phone, :created_at, :user_id, NOW(), :archived_by)
    ");

    $archiveStmt->execute([
        ':id' => $doctor['id'],
        ':full_name' => $doctor['full_name'],
        ':phone' => $doctor['phone'],
        ':created_at' => $doctor['created_at'],
        ':user_id' => $doctor['user_id'], // if exists in doctors table
        ':archived_by' => $_SESSION['user']['id']
    ]);

    // 3️⃣ Delete from main table
    $deleteStmt = $pdo->prepare("DELETE FROM doctors WHERE id = :id");
    $deleteStmt->execute([':id' => $id]);

    $pdo->commit();

    $_SESSION['success'] = "Doctor archived successfully.";

} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $_SESSION['error'] = "Archive failed: " . $e->getMessage();
}

header("Location: index.php");
exit;