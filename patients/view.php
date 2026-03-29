<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("
SELECT p.*, d.full_name AS doctor_name
FROM patients p
JOIN doctors d ON p.attending_doctor_id = d.id
WHERE p.id = ?
");
$stmt->execute([$id]);
$patient = $stmt->fetch();

if (!$patient) exit("Not found.");


/* ==============================
   GET PATIENT TEST RECORDS
================================ */

$stmt = $pdo->prepare("
SELECT 
    pt.id,
    pt.visit_id,
    pt.created_at,
    pt.result_date,
    pt.status,
    lt.name AS test_name
FROM patient_tests pt
JOIN lab_tests lt ON pt.lab_test_id = lt.id
WHERE pt.patient_id = ?
ORDER BY pt.id DESC
");

$stmt->execute([$id]);
$tests = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<div class="page-header">
<h2>Patient Details</h2>
<a href="index.php" class="btn-save btn-sm">Back</a>
</div>

<div class="card">
<p><b>Name:</b> <?= $patient['full_name'] ?></p>
<p><b>Date of Birth:</b> <?= $patient['date_of_birth'] ?></p>
<p><b>Sex:</b> <?= $patient['sex'] ?></p>
<p><b>Age:</b> <?= $patient['age'] ?></p>
<p><b>Doctor:</b> <?= $patient['doctor_name'] ?></p>
<p><b>Date Admitted:</b> <?= $patient['date_admitted'] ?></p>
<p><b>Professional Fee:</b> ₱<?= number_format($patient['professional_fee'],2) ?></p>
<p><b>Invoice:</b> <?= $patient['invoice_number'] ?></p>
</div>


<div class="card" style="margin-top:20px;">

<h3>Past Test Records</h3>

<?php if(empty($tests)): ?>

<p>No test records found.</p>

<?php else: ?>

<?php foreach($tests as $test): ?>

<div style="border:1px solid #ddd;margin-top:10px;border-radius:6px;overflow:hidden;">

<div 
onclick="toggleTest<?= $test['id'] ?>()" 
style="padding:12px;background:#f5f5f5;cursor:pointer;display:flex;justify-content:space-between;align-items:center;"
>

<div>

<b><?= htmlspecialchars($test['test_name']) ?></b><br>

<small>
<?= date("F d, Y", strtotime($test['created_at'])) ?>
</small>

</div>

<div>▼</div>

</div>


<div id="test<?= $test['id'] ?>" style="display:none;padding:15px;">

<table style="width:100%;border-collapse:collapse;">

<thead>
<tr style="background:#f0f0f0;">
<th style="padding:8px;border:1px solid #ddd;">Parameter</th>
<th style="padding:8px;border:1px solid #ddd;">Result</th>
<th style="padding:8px;border:1px solid #ddd;">Interpretation</th>
<th style="padding:8px;border:1px solid #ddd;">Meaning</th>
</tr>
</thead>

<tbody>

<?php

$stmt2 = $pdo->prepare("
SELECT *
FROM patient_test_results
WHERE patient_test_id = ?
");

$stmt2->execute([$test['id']]);
$results = $stmt2->fetchAll(PDO::FETCH_ASSOC);

if(empty($results)){
echo "<tr><td colspan='4'>No results yet.</td></tr>";
}

foreach($results as $r){

echo "<tr>";

echo "<td style='padding:8px;border:1px solid #ddd;'>".$r['parameter_name']."</td>";

echo "<td style='padding:8px;border:1px solid #ddd;'>".$r['result_value']."</td>";

echo "<td style='padding:8px;border:1px solid #ddd;'>".$r['interpretation']."</td>";

echo "<td style='padding:8px;border:1px solid #ddd;'>".$r['meaning']."</td>";

echo "</tr>";

}

?>

</tbody>

</table>

</div>

</div>


<script>

function toggleTest<?= $test['id'] ?>(){

var el = document.getElementById("test<?= $test['id'] ?>");

if(el.style.display === "none"){
el.style.display = "block";
}else{
el.style.display = "none";
}

}

</script>

<?php endforeach; ?>

<?php endif; ?>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../includes/layout.php';
?>