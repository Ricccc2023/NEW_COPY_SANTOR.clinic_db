<?php
require_once __DIR__ . "/../../includes/config.php";
require_once __DIR__ . "/../../includes/auth.php";

$role = $_SESSION['user']['role'] ?? '';

if (!in_array($role, ['admin','staff'])) {
    exit("Access denied.");
}

$doctor_id = isset($_GET['doctor_id']) ? (int)$_GET['doctor_id'] : 0;
$filter    = $_GET['filter'] ?? 'monthly';
$date      = $_GET['date'] ?? date('Y-m-d');
$deduction = isset($_GET['deduction']) ? (float)$_GET['deduction'] : 0;

if ($doctor_id <= 0) exit("Invalid doctor ID.");

/* GET DOCTOR */
$stmt = $pdo->prepare("
    SELECT d.id, u.full_name
    FROM doctors d
    INNER JOIN users u ON u.id = d.user_id
    WHERE d.id = ?
");
$stmt->execute([$doctor_id]);
$doctor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$doctor) exit("Doctor not found.");

/* DATE FILTER */
$whereClause = "";
$params = [$doctor_id];

if ($filter === 'daily') {
    $whereClause = "AND DATE(date_admitted)=?";
    $params[] = $date;
    $periodLabel = date("F d, Y", strtotime($date));
}
elseif ($filter === 'weekly') {
    $whereClause = "AND YEARWEEK(date_admitted,1)=YEARWEEK(?,1)";
    $params[] = $date;
    $start = date("M d", strtotime("monday this week", strtotime($date)));
    $end   = date("M d, Y", strtotime("sunday this week", strtotime($date)));
    $periodLabel = "$start - $end";
}
else {
    $whereClause = "AND YEAR(date_admitted)=YEAR(?) AND MONTH(date_admitted)=MONTH(?)";
    $params[] = $date;
    $params[] = $date;
    $periodLabel = date("F Y", strtotime($date));
}

/* FETCH DATA */
$stmt = $pdo->prepare("
    SELECT professional_fee
    FROM patients
    WHERE attending_doctor_id = ?
    $whereClause
");
$stmt->execute($params);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalPatients = count($records);
$totalFee = 0;
foreach ($records as $r) {
    $totalFee += $r['professional_fee'];
}

$sharePercent = 70;
$doctorShare = ($totalFee * $sharePercent) / 100;
$netPay = $doctorShare - $deduction;

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Payslip</title>

<style>
body{
    font-family: Arial, sans-serif;
    background:#fff;
}

.wrapper{
    width:520px;
    margin:20px auto;
    border:1px solid #000;
    padding:20px;
}

.header{
    display:flex;
    align-items:center;
    border-bottom:1px solid #000;
    padding-bottom:10px;
    margin-bottom:15px;
}

.logo{
    width:60px;
    height:60px;
    margin-right:15px;
}

.logo img{
    width:100%;
    height:100%;
    object-fit:contain;
}

.clinic-info{
    font-size:13px;
}

.clinic-info h2{
    margin:0;
    font-size:16px;
}

.payslip-title{
    text-align:center;
    font-weight:bold;
    margin:15px 0;
    font-size:14px;
}

.section{
    margin-bottom:15px;
}

.section-title{
    font-weight:bold;
    border-bottom:1px solid #000;
    font-size:12px;
    padding-bottom:4px;
    margin-bottom:6px;
}

table{
    width:100%;
    border-collapse:collapse;
    font-size:12px;
}

td{
    padding:4px 0;
}

.right{
    text-align:right;
}

.net{
    font-size:14px;
    font-weight:bold;
    border-top:2px solid #000;
    padding-top:6px;
}

.footer{
    margin-top:30px;
    font-size:12px;
}

.signature{
    margin-top:40px;
}
.print-green{
    background:#198754;
    color:#fff;
    border:none;
    padding:8px 16px;
    cursor:pointer;
    font-size:13px;
    font-weight:600;
}

.print-green:hover{
    background:#157347;
}
.print-btn{
    text-align:center;
    margin-bottom:15px;
}

@media print{
    .print-btn{
        display:none;
    }
    body{
        margin:0;
    }
}
</style>
</head>

<body>

<div class="print-btn">
    <button onclick="window.print()" class="print-green">
        🖨 Print Payslip
    </button>
</div>

<div class="wrapper">

    <div class="header">

        <div class="logo">
            <img src="/clinic_db/public/assets/logo.png" alt="Clinic Logo">
        </div>

        <div class="clinic-info">
            <h2>THE NEW SANTOR CLINIC & DIAGNOSTIC CENTER</h2>
            <div>Santor, Tanauan City Batangas</div>
            <div>Contact: 09854002367</div>
        </div>

    </div>

    <div class="payslip-title">
        PAYSLIP
    </div>

    <div class="section">
        <table>
            <tr>
                <td><b>Doctor:</b></td>
                <td><?= htmlspecialchars($doctor['full_name']) ?></td>
            </tr>
            <tr>
                <td><b>Period:</b></td>
                <td><?= $periodLabel ?></td>
            </tr>
            <tr>
                <td><b>Date Generated:</b></td>
                <td><?= date("M d, Y") ?></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">EARNINGS</div>
        <table>
            <tr>
                <td>Total Patients</td>
                <td class="right"><?= $totalPatients ?></td>
            </tr>
            <tr>
                <td>Total Professional Fees</td>
                <td class="right">₱<?= number_format($totalFee,2) ?></td>
            </tr>
            <tr>
                <td>Doctor Share (<?= $sharePercent ?>%)</td>
                <td class="right">₱<?= number_format($doctorShare,2) ?></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">DEDUCTIONS</div>
        <table>
            <tr>
                <td>Manual Deduction</td>
                <td class="right">₱<?= number_format($deduction,2) ?></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <table>
            <tr class="net">
                <td>NET PAY</td>
                <td class="right">₱<?= number_format($netPay,2) ?></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <div class="signature">
            ___________________________<br>
            Doctor Signature
        </div>
    </div>

</div>

</body>
</html>