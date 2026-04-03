<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/auth.php";

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

/* =============================
   ROLE ACCESS CONTROL
   Allow: admin, staff
============================= */

$role = $_SESSION['user']['role'] ?? '';

if (!in_array($role, ['admin', 'staff'])) {
    http_response_code(403);
    exit("Access denied.");
}

$title = "Payroll";
$active = "payroll";

/* ============================
   FETCH DOCTORS
============================ */

$stmt = $pdo->query("
    SELECT d.id, u.full_name
    FROM doctors d
    INNER JOIN users u ON u.id = d.user_id
    ORDER BY u.full_name
");

$doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

$selected_month = $_GET['month'] ?? date('m');
$selected_year  = $_GET['year'] ?? date('Y');

ob_start();
?>

<div class="page-header">
    <div class="page-title">
        <h2>Payroll Management</h2>
        
    </div>
</div>

<div class="card" style="margin-bottom:20px;">
    <form method="get" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
        
        <label>Month:</label>
        <select name="month">
            <?php for($m=1; $m<=12; $m++): ?>
                <option value="<?= sprintf('%02d',$m) ?>" 
                    <?= $selected_month == sprintf('%02d',$m) ? 'selected' : '' ?>>
                    <?= date("F", mktime(0,0,0,$m,1)) ?>
                </option>
            <?php endfor; ?>
        </select>

        <label>Year:</label>
        <select name="year">
            <?php for($y=date('Y'); $y>=2024; $y--): ?>
                <option value="<?= $y ?>" 
                    <?= $selected_year == $y ? 'selected' : '' ?>>
                    <?= $y ?>
                </option>
            <?php endfor; ?>
        </select>

        <button type="submit" class="btn-save">
            Filter
        </button>
    </form>
</div>

<div class="card">
    <h3 style="margin-bottom:10px;">Doctor Payroll List</h3>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Doctor Name</th>
                    <th>Payroll Period</th>
                    <th style="width:180px;">Action</th>
                </tr>
            </thead>
            <tbody>

            <?php if(empty($doctors)): ?>
                <tr>
                    <td colspan="3">No doctors found.</td>
                </tr>
            <?php else: ?>

                <?php foreach($doctors as $d): ?>
                    <tr>
                        <td><b><?= htmlspecialchars($d['full_name']) ?></b></td>
                        <td>
                            <?= date("F", mktime(0,0,0,$selected_month,1)) ?>
                            <?= htmlspecialchars($selected_year) ?>
                        </td>

                        <td>
                            <a href="view.php?doctor_id=<?= $d['id'] ?>&month=<?= $selected_month ?>&year=<?= $selected_year ?>" 
                               class="btn-save btn-sm">
                                Generate
                            </a>

                            <a href="print.php?doctor_id=<?= $d['id'] ?>&month=<?= $selected_month ?>&year=<?= $selected_year ?>" 
                               class="btn-decline btn-sm">
                                Print
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>

            <?php endif; ?>

            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../includes/layout.php";
?>