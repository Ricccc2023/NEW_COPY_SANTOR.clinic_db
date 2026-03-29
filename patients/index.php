<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$user = $_SESSION['user'];
$isAdmin  = $user['role'] === 'admin';
$isDoctor = $user['role'] === 'doctor';

if (!$isAdmin && !$isDoctor) {
    http_response_code(403);
    exit("Unauthorized access.");
}

/* ==================================================
FILTERS
================================================== */

$search       = trim($_GET['search'] ?? '');
$doctorFilter = (int)($_GET['doctor'] ?? 0);
$dateFilter   = $_GET['date'] ?? '';
$todayOnly    = isset($_GET['today']);
$order        = ($_GET['sort'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';

$params = [];

/* ==================================================
BASE QUERY (FIXED JOIN)
================================================== */

$sql = "
SELECT 
    p.*,
    d.full_name AS doctor_name,
    COALESCE(a.tests, ar.tests) AS tests,
    COALESCE(a.appointment_date, ar.appointment_date) AS appointment_date,
    COALESCE(a.appointment_time, ar.appointment_time) AS appointment_time
FROM patients p
LEFT JOIN doctors d 
    ON d.id = p.attending_doctor_id
LEFT JOIN appointments a 
    ON a.id = p.appointment_id
LEFT JOIN appointments_archive ar 
    ON ar.id = p.appointment_id
WHERE 1=1
";

/* ==================================================
ROLE RESTRICTION
================================================== */

if ($isDoctor) {
    $docStmt = $pdo->prepare("SELECT id FROM doctors WHERE user_id = ?");
    $docStmt->execute([$user['id']]);
    $doctor = $docStmt->fetch();

    if ($doctor) {
        $sql .= " AND p.attending_doctor_id = ?";
        $params[] = $doctor['id'];
    } else {
        $sql .= " AND 1=0";
    }
}

/* ==================================================
SEARCH
================================================== */

if ($search !== '') {
    $sql .= " AND (
        p.full_name LIKE ?
        OR p.email LIKE ?
        OR p.phone LIKE ?
    )";
    $like = "%$search%";
    array_push($params, $like, $like, $like);
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
    $sql .= " AND p.date_admitted = ?";
    $params[] = $dateFilter;
}

if ($todayOnly) {
    $sql .= " AND DATE(p.date_admitted) = CURDATE()";
}

$sql .= " ORDER BY p.date_admitted $order";

/* ==================================================
EXECUTE
================================================== */

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <h2>Patient Management</h2>
    <?php if ($isAdmin): ?>
        <a href="create.php" class="btn-add">+ Add Patient</a>
    <?php endif; ?>
</div>

<div class="card">

<form method="GET" class="filter-bar">

<input type="text" 
       name="search" 
       placeholder="Search patient"
       value="<?= htmlspecialchars($search) ?>">

<?php if ($isAdmin): ?>
<select name="doctor">
    <option value="">All Doctors</option>
    <?php foreach ($doctorList as $d): ?>
        <option value="<?= $d['id'] ?>" <?= $doctorFilter == $d['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($d['full_name']) ?>
        </option>
    <?php endforeach; ?>
</select>
<?php endif; ?>

<input type="date" name="date" value="<?= htmlspecialchars($dateFilter) ?>">

<label>
    <input type="checkbox" name="today" <?= $todayOnly ? 'checked' : '' ?>>
    Today
</label>

<select name="sort">
    <option value="DESC" <?= $order === 'DESC' ? 'selected' : '' ?>>Newest</option>
    <option value="ASC" <?= $order === 'ASC' ? 'selected' : '' ?>>Oldest</option>
</select>

<button class="btn-search">Filter</button>

</form>

<div class="table-wrap">
<table>
<thead>
<tr>
    <th>Name</th>
    <th>Sex</th>
    <th>Doctor</th>
    <th>Appointment</th>
    <th>Tests</th>
    <th>Date Admitted</th>
    <th>Fee</th>
    <?php if ($isAdmin): ?>
        <th>Actions</th>
    <?php endif; ?>
</tr>
</thead>

<tbody>

<?php if (!$patients): ?>
<tr>
    <td colspan="<?= $isAdmin ? 7 : 6 ?>">No patients found.</td>
</tr>
<?php else: ?>

<?php foreach ($patients as $p): ?>

<?php
/* =========================================
SAFE TEST DECODING (FIXED)
========================================= */

$tests = [];

if (!empty($p['tests'])) {

    // Try decode JSON
    $decoded = json_decode($p['tests'], true);

    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        $tests = $decoded;
    } else {
        // fallback: maybe stored as comma separated string
        $tests = explode(',', $p['tests']);
    }
}
?>

<tr>

<td>
    <strong><?= htmlspecialchars($p['full_name']) ?></strong><br>
    <small><?= htmlspecialchars($p['email']) ?></small>
</td>
<td><?= htmlspecialchars($p['sex']) ?></td>
<td><?= htmlspecialchars($p['doctor_name'] ?? '-') ?></td>

<td>
    <?= $p['appointment_date'] ?? '-' ?><br>
    <small><?= $p['appointment_time'] ?? '' ?></small>
</td>

<td>
    <?php if ($tests): ?>
        <?php foreach ($tests as $t): ?>
            <span class="badge">
                <?= htmlspecialchars(trim($t)) ?>
            </span>
        <?php endforeach; ?>
    <?php else: ?>
        -
    <?php endif; ?>
</td>

<td><?= htmlspecialchars($p['date_admitted']) ?></td>

<td>₱<?= number_format((float)$p['professional_fee'], 2) ?></td>

<?php if ($isAdmin): ?>
<td class="action-buttons">
    <a href="view.php?id=<?= $p['id'] ?>" class="btn-save btn-sm action-btn">View</a>
    <a href="edit.php?id=<?= $p['id'] ?>" class="btn-decline btn-sm action-btn">Edit</a>
</td>
<?php endif; ?>

</tr>

<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
</div>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../includes/layout.php';