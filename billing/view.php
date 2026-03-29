<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_role(['admin','staff']);

if (!isset($_GET['visit_id'])) {
    exit("Visit ID missing.");
}

$visit_id = (int) $_GET['visit_id'];

/* ===============================
GET VISIT + PATIENT + DOCTOR
=============================== */

$stmt = $pdo->prepare("
    SELECT 
        v.id AS visit_id,
        v.created_at AS visit_date,

        p.id AS patient_id,
        p.full_name,
        p.date_admitted,
        p.professional_fee,
        p.invoice_number,

        d.full_name AS doctor_name

    FROM patient_test_visits v
    JOIN patients p ON p.id = v.patient_id
    LEFT JOIN doctors d ON d.id = p.attending_doctor_id
    WHERE v.id = ?
");
$stmt->execute([$visit_id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    exit("Visit not found.");
}

/* ===============================
GET TESTS PER VISIT
=============================== */
if ($stmt->rowCount() === 0) {
    http_response_code(400);
    exit;
}
$stmt = $pdo->prepare("
    SELECT lt.name, pt.test_fee
    FROM patient_tests pt
    JOIN lab_tests lt ON lt.id = pt.lab_test_id
    WHERE pt.visit_id = ?
");
$stmt->execute([$visit_id]);
$tests = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ===============================
COMPUTATIONS
=============================== */

$totalLab = 0;
$testNames = [];

foreach ($tests as $t) {
    $fee = (float)$t['test_fee'];
    $totalLab += $fee;
    $testNames[] = $t['name'] . " (₱" . number_format($fee,2) . ")";
}

$labSummary = implode(", ", $testNames);

/* Extra charges (default 0) */
$supplies = 0;
$drugs = 0;
$misc = 0;
$procedure = 0;

$subTotal = $totalLab + $supplies + $drugs + $misc + $procedure;

$professionalFee = (float)$data['professional_fee'];
$overallSubtotal = $subTotal + $professionalFee;
$total = $overallSubtotal;
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Billing Receipt</title>

<style>
body{ font-family:Arial; margin:0; }
.container{ width:800px; margin:auto; padding:20px; }

.header{ display:flex; justify-content:space-between; align-items:center; }
.left{ display:flex; align-items:center; }
.left img{ width:70px; margin-right:15px; }

.clinic-info h2{ margin:0; font-size:18px; line-height:1.3; }
.clinic-info small{ font-size:12px; }

.title{ text-align:right; }
.title h1{ font-size:18px; margin:0; }

table{ width:100%; border-collapse:collapse; margin-top:20px; }
td, th{ border:1px solid #000; padding:8px; font-size:13px; }

.section-title{
    font-weight:bold;
    background:#eee;
}

.right{ text-align:right; }

.signature{
    margin-top:50px;
}

@media print{
    .no-print{ display:none; }
}
</style>
</head>

<body>
<div class="container">

<!-- HEADER -->

<div class="header">
    <div class="left">
        <img src="logo.png" alt="Clinic Logo">

        <div class="clinic-info">
            <h2>
                The New Santor Clinic and Diagnostic<br>
                <span style="font-weight:normal;font-size:15px;">
                Center Web-Based Management System
                </span>
            </h2>
            <small>Santor, Tanauan, Philippines, 4232</small><br>
            <small>0985-400-2367</small>
        </div>
    </div>

    <div class="title">
        <h1>BILLING RECEIPT</h1>
        <small>Invoice #: <?= htmlspecialchars($patient['invoice_number'] ?? '-') ?></small>
    </div>
</div>

<hr>

<!-- PATIENT INFO -->

<table>
<tr>
<td><strong>Patient:</strong> <?= htmlspecialchars($data['full_name']) ?></td>
<td><strong>Doctor:</strong> <?= htmlspecialchars($data['doctor_name'] ?? '-') ?></td>
</tr>
<tr>
<td><strong>Date Admitted:</strong> <?= htmlspecialchars($data['date_admitted']) ?></td>
<td><strong>Patient ID:</strong> <?= $data['patient_id'] ?></td>
</tr>
<tr>
<td><strong>Visit Date:</strong> <?= htmlspecialchars($data['visit_date']) ?></td>
<td><strong>Invoice #:</strong> <?= htmlspecialchars($data['invoice_number'] ?? '-') ?></td>
</tr>
</table>

<!-- BILLING TABLE -->

<table>

<tr class="section-title">
<td colspan="2">Charges</td>
</tr>

<tr>
<td>
<strong>Laboratory & Diagnostics</strong><br>
<small><?= $labSummary ?: 'No tests recorded' ?></small>
</td>
<td class="right">₱<?= number_format($totalLab,2) ?></td>
</tr>

<tr>
<td>Supplies</td>
<td class="right">₱<?= number_format($supplies,2) ?></td>
</tr>

<tr>
<td>Drugs & Medicines</td>
<td class="right">₱<?= number_format($drugs,2) ?></td>
</tr>

<tr class="section-title">
<td colspan="2">Others: pls. specify</td>
</tr>

<tr>
<td>Miscellaneous</td>
<td class="right">₱<?= number_format($misc,2) ?></td>
</tr>

<tr>
<td>Procedure</td>
<td class="right">₱<?= number_format($procedure,2) ?></td>
</tr>

<tr>
<td><strong>Subtotal</strong></td>
<td class="right"><strong>₱<?= number_format($subTotal,2) ?></strong></td>
</tr>

<tr class="section-title">
<td colspan="2">Professional Fee/s</td>
</tr>

<tr>
<td>Doctor Professional Fee</td>
<td class="right">₱<?= number_format($professionalFee,2) ?></td>
</tr>

<tr>
<td><strong>Subtotal</strong></td>
<td class="right"><strong>₱<?= number_format($overallSubtotal,2) ?></strong></td>
</tr>

<tr>
<td><strong>TOTAL</strong></td>
<td class="right"><strong>₱<?= number_format($total,2) ?></strong></td>
</tr>

</table>

<!-- SIGNATURE -->

<div class="signature">
<br><br>
_____________________________<br>
Accountant / Authorized Personnel
</div>

<br>


</div>
</body>
</html>