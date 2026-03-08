<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$role    = $_SESSION['user']['role'];
$user_id = $_SESSION['user']['id'];

$doctor_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($doctor_id <= 0) {
    exit("Invalid doctor ID.");
}

/* ===========================
   SECURITY CHECK
=========================== */

if ($role === 'doctor') {

    $stmtCheck = $pdo->prepare("
        SELECT id FROM doctors WHERE id = ? AND user_id = ?
    ");

    $stmtCheck->execute([$doctor_id, $user_id]);

    if (!$stmtCheck->fetch()) {
        exit("Unauthorized access.");
    }
}

/* ===========================
   GET DOCTOR INFO
=========================== */

$stmt = $pdo->prepare("
SELECT d.*, u.full_name
FROM doctors d
INNER JOIN users u ON u.id = d.user_id
WHERE d.id = ?
");

$stmt->execute([$doctor_id]);
$doctor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$doctor) {
    exit("Doctor not found.");
}

/* ===========================
   FILTER
=========================== */

$filter = $_GET['filter'] ?? 'daily';
$date   = $_GET['date'] ?? date('Y-m-d');

$whereClause = "";
$params = [$doctor_id];

if ($filter === 'daily') {

    $whereClause = "AND DATE(date_admitted) = ?";
    $params[] = $date;
}
elseif ($filter === 'weekly') {

    $whereClause = "AND YEARWEEK(date_admitted,1)=YEARWEEK(?,1)";
    $params[] = $date;
}
elseif ($filter === 'monthly') {

    $whereClause = "AND YEAR(date_admitted)=YEAR(?) 
                    AND MONTH(date_admitted)=MONTH(?)";

    $params[] = $date;
    $params[] = $date;
}

/* ===========================
   GET PATIENTS
=========================== */

$stmt = $pdo->prepare("
SELECT *
FROM patients
WHERE attending_doctor_id = ?
$whereClause
ORDER BY date_admitted DESC
");

$stmt->execute($params);
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ===========================
   TOTAL INCOME
=========================== */

$totalIncome = 0;

foreach ($patients as $p) {
    $totalIncome += $p['professional_fee'];
}

/* ===========================
   CALENDAR DATA
=========================== */

$month = date('m', strtotime($date));
$year  = date('Y', strtotime($date));
$monthName = date('F', strtotime($date));

$daysInMonth = cal_days_in_month(CAL_GREGORIAN,$month,$year);
$firstDay = date('w', strtotime("$year-$month-01"));

/* ===========================
   GET ATTENDANCE
=========================== */

$stmt = $pdo->prepare("
SELECT attendance_date
FROM doctor_attendance
WHERE doctor_id = ?
AND MONTH(attendance_date) = ?
AND YEAR(attendance_date) = ?
");

$stmt->execute([$doctor_id,$month,$year]);

$attendance = [];

while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

$attendance[$row['attendance_date']] = true;

}

ob_start();
?>

<div class="page-header">

<div class="page-title">
<h2><?= htmlspecialchars($doctor['full_name']) ?></h2>
<p class="sub">Professional Earnings Overview</p>
</div>

<div class="page-action">
<a href="index.php" class="btn-decline">Back</a>
</div>

</div>

<div style="display:flex; gap:20px; align-items:flex-start;">

<div style="flex:1;">

<div class="card" style="margin-bottom:20px;">

<form method="get" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">

<input type="hidden" name="id" value="<?= $doctor_id ?>">

<select name="filter">

<option value="daily" <?= $filter=='daily'?'selected':'' ?>>Daily</option>
<option value="weekly" <?= $filter=='weekly'?'selected':'' ?>>Weekly</option>
<option value="monthly" <?= $filter=='monthly'?'selected':'' ?>>Monthly</option>

</select>

<input type="date" name="date" value="<?= htmlspecialchars($date) ?>">

<button type="submit" class="btn-save">Filter</button>

</form>

</div>

<div class="card">

<h3>Total Professional Income</h3>

<h2 style="color:#198754;">
₱<?= number_format($totalIncome,2) ?>
</h2>

</div>

</div>

<div style="width:350px;">

<div class="card">

<h3 style="margin-bottom:10px;">
Attendance — <?= $monthName ?> <?= $year ?>
</h3>

<table style="text-align:center;">

<thead>

<tr>
<th>Sun</th>
<th>Mon</th>
<th>Tue</th>
<th>Wed</th>
<th>Thu</th>
<th>Fri</th>
<th>Sat</th>
</tr>

</thead>

<tbody>

<tr>

<?php

$counter = 0;

for($i=0;$i<$firstDay;$i++){

echo "<td></td>";
$counter++;

}

for($d=1;$d<=$daysInMonth;$d++){

$dateCheck = "$year-$month-".str_pad($d,2,'0',STR_PAD_LEFT);

$color = "#ffffff";
$text  = "#000";

if(isset($attendance[$dateCheck])){

$color = "#198754";
$text  = "#fff";

}

echo "<td style='background:$color;color:$text;'>$d</td>";

$counter++;

if($counter % 7 == 0){

echo "</tr><tr>";

}

}

?>

</tr>

</tbody>

</table>

</div>

</div>

</div>

<div class="card">

<table>

<thead>

<tr>
<th>Patient Name</th>
<th>Age</th>
<th>Phone</th>
<th>Date Admitted</th>
<th>Professional Fee</th>
</tr>

</thead>

<tbody>

<?php if(empty($patients)): ?>

<tr>
<td colspan="5">No records found for selected filter.</td>
</tr>

<?php else: ?>

<?php foreach($patients as $p): ?>

<tr>

<td><?= htmlspecialchars($p['full_name']) ?></td>
<td><?= (int)$p['age'] ?></td>
<td><?= htmlspecialchars($p['phone']) ?></td>
<td><?= htmlspecialchars($p['date_admitted']) ?></td>
<td>₱<?= number_format($p['professional_fee'],2) ?></td>

</tr>

<?php endforeach; ?>

<?php endif; ?>

</tbody>

</table>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../includes/layout.php';
?>