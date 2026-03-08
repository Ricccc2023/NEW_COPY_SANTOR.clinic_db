<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_role(['admin','doctor']);

$visit_id = $_GET['visit_id'] ?? 0;

if(!$visit_id){
    header("Location:index.php");
    exit;
}

/* ===============================
   LOAD VISIT + PATIENT
=============================== */
$visitStmt = $pdo->prepare("
    SELECT v.*, p.full_name, p.id AS patient_id
    FROM patient_test_visits v
    JOIN patients p ON p.id = v.patient_id
    WHERE v.id = ?
");
$visitStmt->execute([$visit_id]);
$visit = $visitStmt->fetch(PDO::FETCH_ASSOC);

if(!$visit){
    header("Location:index.php");
    exit;
}

/* ===============================
   LOAD TESTS PER VISIT
=============================== */
$testsStmt = $pdo->prepare("
    SELECT pt.*, lt.name AS test_name
    FROM patient_tests pt
    JOIN lab_tests lt ON lt.id = pt.lab_test_id
    WHERE pt.visit_id = ?
    ORDER BY pt.created_at DESC
");
$testsStmt->execute([$visit_id]);
$tests = $testsStmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<div class="page-header">
            <h2><?= htmlspecialchars($visit['full_name']) ?> - Laboratory Records</h2>
                <a href="index.php" class="btn-save btn-sm">Back</a>
</div>

<a href="../prints/print_results.php?visit_id=<?= $visit_id ?>" 
   target="_blank"
   class="btn-save">
   🖨 Print Result
</a>
<a href="../prints/print_billing.php?visit_id=<?= $visit_id ?> 
   target="_blank class="btn-save">
   💰 Print Billing
</a>

<a href="../../includes/notify_result.php?id=<?= $visit['patient_id'] ?>  
   target="_blank class="btn-save">
   Notify Result Ready
</a>

<div class="card">

<?php if(!$tests): ?>
<p>No laboratory records found.</p>
<?php endif; ?>

<?php foreach($tests as $test): ?>

<div class="section-box">

    <!-- TEST HEADER -->
    <div style="margin-bottom:10px;">
        <h3 style="margin-bottom:4px;">
            <?= htmlspecialchars($test['test_name']) ?>
        </h3>
        <span style="font-size:12px;color:#777;">
            Status: <?= htmlspecialchars($test['status']) ?>
        </span><br>
        <span style="font-size:12px;color:#777;">
            Test Date: <?= htmlspecialchars($test['created_at']) ?>
        </span>
    </div>

    <!-- LOAD PARAMETERS -->
    <?php
    $resStmt = $pdo->prepare("
        SELECT *
        FROM patient_test_results
        WHERE patient_test_id = ?
    ");
    $resStmt->execute([$test['id']]);
    $results = $resStmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <table class="result-table">
        <tr>
            <th>Parameter</th>
            <th>Result</th>
            <th>Status</th>
            <th>Meaning</th>
        </tr>

        <?php foreach($results as $r): ?>
        <tr>
            <td><?= htmlspecialchars($r['parameter_name']) ?></td>
            <td><?= htmlspecialchars($r['result_value']) ?></td>
            <td><?= htmlspecialchars($r['interpretation']) ?></td>
            <td><?= htmlspecialchars($r['meaning']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- DOCTOR NOTE PER TEST -->
    <?php if(!empty($test['notes'])): ?>
    <div style="
        margin-top:15px;
        padding:12px;
        background:#f9f9f9;
        border-left:4px solid #2c5f4f;
        border-radius:4px;
    ">
        <strong>Doctor Note</strong>
        <p style="margin-top:8px; white-space:pre-line;">
            <?= htmlspecialchars($test['notes']) ?>
        </p>
    </div>
    <?php endif; ?>

</div>

<hr style="margin:30px 0; border:0; border-top:1px solid #eee;">

<?php endforeach; ?>

</div>

<?php
$content = ob_get_clean();
require_once '../../includes/layout.php';