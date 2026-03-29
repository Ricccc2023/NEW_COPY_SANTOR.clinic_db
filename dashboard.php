<?php
require_once __DIR__ . "/includes/config.php";
require_once __DIR__ . "/includes/auth.php";

$title  = "Dashboard";
$active = "dashboard";

/* =====================================================
   BASIC COUNTS
===================================================== */

$doctorCount   = (int)$pdo->query("SELECT COUNT(*) FROM doctors")->fetchColumn();
$archivedCount = (int)$pdo->query("SELECT COUNT(*) FROM doctors_archive")->fetchColumn();
$userCount     = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

/* =====================================================
   MODE SWITCH (weekly / monthly / yearly)
===================================================== */

$mode = $_GET['mode'] ?? 'weekly';

switch($mode){

    case 'monthly':
        $gross = $pdo->query("
            SELECT IFNULL(SUM(pt.test_fee + p.professional_fee),0)
            FROM patient_test_visits v
            JOIN patient_tests pt ON pt.visit_id = v.id
            JOIN patients p ON p.id = v.patient_id
            WHERE MONTH(v.created_at)=MONTH(CURDATE())
            AND YEAR(v.created_at)=YEAR(CURDATE())
        ")->fetchColumn();
        $label = "Gross This Month";
    break;

    case 'yearly':
        $gross = $pdo->query("
            SELECT IFNULL(SUM(pt.test_fee + p.professional_fee),0)
            FROM patient_test_visits v
            JOIN patient_tests pt ON pt.visit_id = v.id
            JOIN patients p ON p.id = v.patient_id
            WHERE YEAR(v.created_at)=YEAR(CURDATE())
        ")->fetchColumn();
        $label = "Gross This Year";
    break;

    default:
        $gross = $pdo->query("
            SELECT IFNULL(SUM(pt.test_fee + p.professional_fee),0)
            FROM patient_test_visits v
            JOIN patient_tests pt ON pt.visit_id = v.id
            JOIN patients p ON p.id = v.patient_id
            WHERE YEARWEEK(v.created_at,1)=YEARWEEK(CURDATE(),1)
        ")->fetchColumn();
        $label = "Gross This Week";
}

ob_start();
?>

<div class="stats-grid">

    <div class="card" style="flex:1; min-width:200px;">
        <h2>Active Doctors</h2>
        <h2><?= $doctorCount ?></h2>
    </div>

    <div class="card" style="flex:1; min-width:200px;">
        <h2>Archived Doctors</h2>
        <h2><?= $archivedCount ?></h2>
    </div>

    <div class="card" style="flex:1; min-width:200px;">
        <h2>Total Users</h2>
        <h2><?= $userCount ?></h2>
    </div>

</div>

<br>

<!-- ================================
     ANALYTICS SECTION
================================ -->

<div class="card">

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
        <h3>Gross Analytics</h3>

        <form method="GET" style="display:flex; gap:10px;">
            <select name="mode" onchange="this.form.submit()">
                <option value="weekly"  <?= $mode=='weekly'?'selected':'' ?>>Weekly</option>
                <option value="monthly" <?= $mode=='monthly'?'selected':'' ?>>Monthly</option>
                <option value="yearly"  <?= $mode=='yearly'?'selected':'' ?>>Yearly</option>
            </select>
        </form>
    </div>

    <div style="padding:20px; background:#f8f9fa; border:1px solid #e5e5e5;">
        <h4><?= $label ?></h4>
        <h1 style="color:#1f4e46;">
            ₱<?= number_format($gross,2) ?>
        </h1>
    </div>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/includes/layout.php";
require_once __DIR__ . "/includes/footer.php";
?>