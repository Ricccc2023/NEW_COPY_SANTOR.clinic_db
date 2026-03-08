<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_role(['admin','doctor']);

if (!isset($_GET['visit_id'])) {
    exit("Visit ID missing.");
}

$visit_id = (int) $_GET['visit_id'];

/* ==============================
GET VISIT + PATIENT INFO
============================== */
$stmt = $pdo->prepare("
    SELECT 
        v.created_at AS visit_date,

        p.id AS patient_id,
        p.full_name,
        p.age,
        p.sex,
        p.date_of_birth,
        p.phone,
        p.address,
        p.date_admitted,
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

/* ==============================
GET TESTS + PARAMETERS + NOTES
============================== */
$stmt = $pdo->prepare("
    SELECT 
        pt.id AS patient_test_id,
        pt.notes,
        lt.name AS test_name,
        ptr.parameter_name,
        ptr.result_value,
        ptr.interpretation,
        ptr.meaning
    FROM patient_tests pt
    JOIN lab_tests lt ON lt.id = pt.lab_test_id
    LEFT JOIN patient_test_results ptr 
        ON ptr.patient_test_id = pt.id
    WHERE pt.visit_id = ?
    ORDER BY lt.name, ptr.id
");
$stmt->execute([$visit_id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ==============================
GROUP RESULTS
============================== */
$grouped = [];
$allNotes = [];

foreach ($rows as $r) {

    $grouped[$r['test_name']][] = $r;

    if (!empty($r['notes']) && !in_array($r['notes'], $allNotes)) {
        $allNotes[] = $r['notes'];
    }
}

$doctorNotes = implode("\n\n", $allNotes);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Laboratory Service Report</title>

<style>
body{
    font-family: Arial, sans-serif;
    margin: 0;
}
.report-container{
    width: 800px;
    margin: auto;
    padding: 20px;
}

/* ===== HEADER ===== */
.header{
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.header-left{
    display: flex;
    align-items: center;
}

.header-left img{
    width: 70px;
    height: 70px;
    margin-right: 15px;
}

.clinic-info h2{
    margin: 0;
    font-size: 18px;
}

.clinic-info small{
    font-size: 12px;
}

.report-title{
    text-align: right;
}

.report-title h1{
    font-size: 18px; /* smaller title */
    margin: 0;
    letter-spacing: 1px;
}

/* ===== PATIENT INFO ===== */
.patient-info{
    margin-top:20px;
}

.patient-info table{
    width:100%;
    border-collapse:collapse;
}

.patient-info td{
    padding:6px 8px;   /* konting horizontal spacing */
    font-size:13px;
    vertical-align:top;
}

/* ===== TEST TABLE ===== */
.test-title{
    font-weight:bold;
    margin-top:25px;
    font-size:14px;
}

.test-table{
    margin-top:5px;
    width:100%;
    border-collapse: collapse;
}

.test-table th,
.test-table td{
    border:1px solid #000;
    padding:6px;
    font-size:12px;
    text-align:left;
}

/* ===== FOOTER ===== */
.footer{
    margin-top:40px;
}

.signature{
    margin-top:40px;
}

@media print{
    .no-print{
        display:none;
    }
}
</style>
</head>

<body>

<div class="report-container">

<!-- ================= HEADER ================= -->

<div class="header">

    <div class="header-left">
        <!-- CHANGE PATH NG LOGO DEPENDE SA SYSTEM MO -->
        <img src="logo.png" alt="Clinic Logo">

        <div class="clinic-info">
    <h2>
        The New Santor Clinic and Diagnostic<br>
        <span class="sub-title">
            Center Web-Based Management System
        </span>
    </h2>

    <small>Santor, Tanauan, Philippines, 4232</small><br>
    <small>0985-400-2367</small>
</div>
    </div>

    <div class="report-title">
        <h1>LABORATORY SERVICE REPORT</h1>
    </div>

</div>

<hr>

<!-- ================= PATIENT INFO ================= -->

<div class="patient-info">
<table>
<tr>
    <td><strong>Patient Name:</strong> <?= htmlspecialchars($data['full_name']) ?></td>
<td><strong>Patient ID:</strong> <?= $data['patient_id'] ?></td>
<td><strong>Age:</strong> <?= $data['age'] ?></td>
</tr>

<tr>
    <td><strong>Sex:</strong> <?= $data['sex'] ?></td>
<td><strong>Date of Birth:</strong> <?= $data['date_of_birth'] ?></td>
<td><strong>Phone:</strong> <?= $data['phone'] ?></td>
</tr>

<tr>
    <td><strong>Address:</strong> <?= htmlspecialchars($data['address']) ?></td>
<td><strong>Doctor:</strong> <?= htmlspecialchars($data['doctor_name']) ?></td>
<td><strong>Visit Date:</strong> <?= $data['visit_date'] ?></td>
</tr>
</table>
</div>

<!-- ================= TEST RESULTS ================= -->

<?php foreach($grouped as $testName => $params): ?>

    <div class="test-title">
        <?= htmlspecialchars($testName) ?>
    </div>

    <table class="test-table" style="margin-top:5px; margin-bottom:25px;">
        <tr>
            <th style="width:20%">TEST NAME</th>
            <th style="width:25%">PARAMETER</th>
            <th style="width:20%">RESULT</th>
            <th style="width:15%">STATUS</th>
            <th style="width:20%">MEANING</th>
        </tr>

        <?php foreach($params as $p): ?>
            <tr>
                <td><?= htmlspecialchars($testName) ?></td>
                <td><?= htmlspecialchars($p['parameter_name'] ?? '-') ?></td>
                <td><?= htmlspecialchars($p['result_value'] ?? '-') ?></td>
                <td><?= htmlspecialchars($p['interpretation'] ?? '-') ?></td>
                <td><?= htmlspecialchars($p['meaning'] ?? '-') ?></td>
            </tr>
        <?php endforeach; ?>

    </table>

<?php endforeach; ?>


<!-- ================= DOCTOR NOTE ================= -->

<div class="footer">
    <h4>Doctor Note:</h4>

    <div style="border:1px solid #000; padding:10px; min-height:80px;">
        <?= nl2br(htmlspecialchars($doctorNotes ?? '')) ?>
    </div>

    <div class="signature">
        _________________________________<br>

        Physician: <strong><?= htmlspecialchars($data['doctor_name']) ?></strong>
    </div>
</div>

<br>

<div class="no-print">
    <button onclick="window.print()">🖨 Print</button>
</div>

</div>

</body>
</html>