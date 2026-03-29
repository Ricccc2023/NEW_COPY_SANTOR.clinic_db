<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

require_role(['admin','staff']);

$user = $_SESSION['user'];
$role = $user['role'];
$isAdmin = $role === 'admin';

/* ===============================
   FILTERS
=============================== */

$search = $_GET['search'] ?? '';
$dateFilter = $_GET['date'] ?? '';
$params = [];

/* ===============================
   MAIN BILLING QUERY
   (BASED ON LAB VISITS)
=============================== */

$sql = "
SELECT 
    v.id AS visit_id,
    v.created_at,
    p.full_name,
    p.professional_fee,
    SUM(pt.test_fee) AS lab_total,
    (SUM(pt.test_fee) + p.professional_fee) AS total
FROM patient_test_visits v
JOIN patients p ON p.id = v.patient_id
JOIN patient_tests pt ON pt.visit_id = v.id
WHERE 1=1
";

/* SEARCH */

if ($search !== '') {
    $sql .= " AND p.full_name LIKE ?";
    $params[] = "%$search%";
}

/* DATE FILTER */

if ($dateFilter !== '') {
    $sql .= " AND DATE(v.created_at) = ?";
    $params[] = $dateFilter;
}

$sql .= "
GROUP BY v.id, p.full_name, v.created_at, p.professional_fee
ORDER BY v.created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$bills = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ===============================
   SALES SUMMARY (REAL-TIME)
=============================== */

$daily = $pdo->query("
SELECT IFNULL(SUM(pt.test_fee + p.professional_fee),0)
FROM patient_test_visits v
JOIN patient_tests pt ON pt.visit_id = v.id
JOIN patients p ON p.id = v.patient_id
WHERE DATE(v.created_at) = CURDATE()
")->fetchColumn();

$weekly = $pdo->query("
SELECT IFNULL(SUM(pt.test_fee + p.professional_fee),0)
FROM patient_test_visits v
JOIN patient_tests pt ON pt.visit_id = v.id
JOIN patients p ON p.id = v.patient_id
WHERE YEARWEEK(v.created_at,1)=YEARWEEK(CURDATE(),1)
")->fetchColumn();

$monthly = $pdo->query("
SELECT IFNULL(SUM(pt.test_fee + p.professional_fee),0)
FROM patient_test_visits v
JOIN patient_tests pt ON pt.visit_id = v.id
JOIN patients p ON p.id = v.patient_id
WHERE MONTH(v.created_at)=MONTH(CURDATE())
AND YEAR(v.created_at)=YEAR(CURDATE())
")->fetchColumn();

ob_start();
?>

<div class="card">
    <div class="card-header">
        <h3>Billing Dashboard (Real-Time)</h3>
    </div>

    <div style="display:flex; gap:15px; flex-wrap:wrap;">
        <div class="card" style="flex:1; min-width:200px;">
            <h4>Daily</h4>
            <b>₱<?= number_format($daily,2) ?></b>
        </div>

        <div class="card" style="flex:1; min-width:200px;">
            <h4>Weekly</h4>
            <b>₱<?= number_format($weekly,2) ?></b>
        </div>

        <div class="card" style="flex:1; min-width:200px;">
            <h4>Monthly</h4>
            <b>₱<?= number_format($monthly,2) ?></b>
        </div>
    </div>
</div>

<div class="card">

    <div class="card-header">
        <h2>Billing Records (Auto Generated)</h2>
    </div>

    <form method="GET" class="filter-bar">

        <input type="text"
               name="search"
               placeholder="Search patient"
               value="<?= htmlspecialchars($search) ?>">

        <input type="date"
               name="date"
               value="<?= htmlspecialchars($dateFilter) ?>">

        <button class="btn-search">Filter</button>

    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Visit ID</th>
                    <th>Patient</th>
                    <th>Lab Total</th>
                    <th>Professional Fee</th>
                    <th>Total</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

            <?php if ($bills): ?>
                <?php foreach ($bills as $bill): ?>
                    <tr>
                        <td>#<?= $bill['visit_id'] ?></td>
                        <td><?= htmlspecialchars($bill['full_name']) ?></td>
                        <td>₱<?= number_format($bill['lab_total'],2) ?></td>
                        <td>₱<?= number_format($bill['professional_fee'],2) ?></td>
                        <td><b>₱<?= number_format($bill['total'],2) ?></b></td>
                        <td><?= date('M d, Y', strtotime($bill['created_at'])) ?></td>
                        <td>
                            <a href="../prints/print_billing.php?visit_id=<?= $bill['visit_id'] ?>"
                            class="action-btn action-secondary"
                            target="_blank">
                            Print
                            </a>
                            <a href="edit.php?visit_id=<?= $bill['visit_id'] ?>"
                                class="btn-save">
                                Edit
                                </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No billing data available.</td>
                </tr>
            <?php endif; ?>

            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once '../includes/layout.php';
?>