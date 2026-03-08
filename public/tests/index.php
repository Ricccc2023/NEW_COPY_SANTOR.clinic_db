<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_role(['admin','doctor']);

/* ==================================================
INITIALIZE FILTERS
================================================== */

$search       = $_GET['search'] ?? '';
$dateFilter   = $_GET['date'] ?? '';
$todayOnly    = isset($_GET['today']);
$doctorFilter = $_GET['doctor'] ?? 0;
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
/* ==================================================
BASE QUERY
================================================== */

$sql = "
SELECT 
    v.id AS visit_id,
    p.full_name,
    COUNT(pt.id) AS total_tests,
    v.created_at,
    p.attending_doctor_id
FROM patient_test_visits v
INNER JOIN patients p ON p.id = v.patient_id
INNER JOIN patient_tests pt ON pt.visit_id = v.id
WHERE 1=1
";

$params = [];

/* ==================================================
SEARCH FILTER
================================================== */

if ($search !== '') {
    $sql .= " AND p.full_name LIKE ?";
    $params[] = "%$search%";
}

/* ==================================================
ADMIN DOCTOR FILTER
================================================== */

if ($isAdmin && $doctorFilter > 0) {
    $sql .= " AND p.attending_doctor_id = ?";
    $params[] = $doctorFilter;
}

/* ==================================================
DATE FILTER
================================================== */

if ($dateFilter !== '') {
    $sql .= " AND DATE(v.created_at) = ?";
    $params[] = $dateFilter;
}

if ($todayOnly) {
    $sql .= " AND DATE(v.created_at) = CURDATE()";
}

/* ==================================================
GROUP & ORDER
================================================== */

$sql .= "
GROUP BY v.id, p.full_name, v.created_at, p.attending_doctor_id
ORDER BY v.created_at DESC
";

/* ==================================================
EXECUTE
================================================== */

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ==================================================
ADMIN DOCTOR LIST
================================================== */

$doctorList = [];
if ($isAdmin) {
    $doctorList = $pdo->query("
        SELECT id, full_name 
        FROM doctors 
        ORDER BY full_name
    ")->fetchAll(PDO::FETCH_ASSOC);
}

ob_start();
?>

<div class="page-header">
    <h2>Laboratory Records</h2>
    <div class="page-action">
        <a href="create.php" class="btn-add">+ Add Test</a>
    </div>
</div>

<!-- ================= FILTER FORM ================= -->

<div class="card" style="margin-bottom:15px;">
<form method="GET">
    <input type="text" name="search" placeholder="Search patient..."
        value="<?= htmlspecialchars($search) ?>">

    <input type="date" name="date"
        value="<?= htmlspecialchars($dateFilter) ?>">

    <label>
        <input type="checkbox" name="today" <?= $todayOnly ? 'checked' : '' ?>>
        Today
    </label>

    <?php if ($isAdmin): ?>
        <select name="doctor">
            <option value="0">All Doctors</option>
            <?php foreach ($doctorList as $doc): ?>
                <option value="<?= $doc['id'] ?>"
                    <?= ($doctorFilter == $doc['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($doc['full_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    <?php endif; ?>

    <button class="btn-search">Filter</button>
</form>
</div>

<!-- ================= TABLE ================= -->

<div class="card">
<table>
<tr>
    <th>Patient</th>
    <th>Total Tests</th>
    <th>Date Created</th>
    <th>Action</th>
</tr>

<?php foreach($rows as $r): ?>
<tr>
    <td><?= htmlspecialchars($r['full_name']) ?></td>
    <td><?= $r['total_tests'] ?></td>
    <td><?= $r['created_at'] ?></td>
    <td>
        <a href="view.php?visit_id=<?= $r['visit_id'] ?>" class="btn-save">
            View
        </a>
    </td>
</tr>
<?php endforeach; ?>

<?php if(empty($rows)): ?>
<tr>
    <td colspan="4" style="text-align:center;">No records found.</td>
</tr>
<?php endif; ?>

</table>
</div>

<?php
$content = ob_get_clean();
require_once '../../includes/layout.php';