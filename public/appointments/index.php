<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

require_role(['admin','doctor','staff']);

$user = $_SESSION['user'];
$isAdmin  = $user['role'] == 'admin';
$isDoctor = $user['role'] == 'doctor';
$isStaff  = $user['role'] == 'staff';

/* ==================================================
FILTERS
================================================== */

$search       = trim($_GET['search'] ?? '');
$doctorFilter = (int)($_GET['doctor'] ?? 0);
$dateFilter   = $_GET['date'] ?? '';
$statusFilter = $_GET['status'] ?? '';
$order        = ($_GET['sort'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';

$params = [];

/* ==================================================
BASE QUERY
================================================== */

$sql = "
SELECT a.*, d.full_name doctor
FROM appointments a
JOIN doctors d ON a.doctor_id = d.id
WHERE 1=1
";

/* ==================================================
ROLE RESTRICTION
================================================== */

if ($isDoctor) {
    $doc = $pdo->prepare("SELECT id FROM doctors WHERE user_id=?");
    $doc->execute([$user['id']]);
    $doctor = $doc->fetch();

    if ($doctor) {
        $sql .= " AND a.doctor_id = ?";
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
        a.full_name LIKE ?
        OR a.email LIKE ?
        OR a.phone LIKE ?
    )";

    $like = "%$search%";
    array_push($params, $like, $like, $like);
}

/* ==================================================
ADMIN DOCTOR FILTER
================================================== */

if ($isAdmin && $doctorFilter > 0) {
    $sql .= " AND a.doctor_id = ?";
    $params[] = $doctorFilter;
}

/* ==================================================
DATE FILTER
================================================== */

if ($dateFilter !== '') {
    $sql .= " AND a.appointment_date = ?";
    $params[] = $dateFilter;
}

/* ==================================================
STATUS FILTER
================================================== */

if ($statusFilter !== '') {
    $sql .= " AND a.status = ?";
    $params[] = $statusFilter;
}

/* ==================================================
ORDER
================================================== */

$sql .= " ORDER BY a.appointment_date $order";

/* ==================================================
EXECUTE
================================================== */

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

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
<div class="page-title">
<h2>Appointments</h2>
<p class="sub">Pending / Confirmed</p>
</div>

<?php if($isAdmin): ?>
<div class="page-action">
<a href="create.php" class="btn-add">+</a>
</div>
<?php endif; ?>
</div>

<div class="card">

<!-- ================= FILTER BAR ================= -->

<form method="GET" class="filter-bar">

<input type="text" 
       name="search" 
       placeholder="Search appointment"
       value="<?= htmlspecialchars($search) ?>">

<?php if ($isAdmin): ?>
<select name="doctor">
    <option value="">All Doctors</option>
    <?php foreach ($doctorList as $d): ?>
        <option value="<?= $d['id'] ?>" 
            <?= $doctorFilter == $d['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($d['full_name']) ?>
        </option>
    <?php endforeach; ?>
</select>
<?php endif; ?>

<input type="date" name="date" value="<?= htmlspecialchars($dateFilter) ?>">

<select name="status">
    <option value="">All Status</option>
    <option value="pending" <?= $statusFilter=='pending'?'selected':'' ?>>Pending</option>
    <option value="confirmed" <?= $statusFilter=='confirmed'?'selected':'' ?>>Confirmed</option>
    <option value="declined" <?= $statusFilter=='declined'?'selected':'' ?>>Declined</option>
</select>

<select name="sort">
    <option value="DESC" <?= $order === 'DESC' ? 'selected' : '' ?>>Newest</option>
    <option value="ASC" <?= $order === 'ASC' ? 'selected' : '' ?>>Oldest</option>
</select>

<button class="btn-search">Filter</button>

</form>

<!-- ================= TABLE ================= -->

<div class="table-wrap">
<table>
<tr>
<th>Name</th>
<th>Doctor</th>
<th>Date</th>
<th>Time</th>
<th>Tests</th>
<th>Status</th>
<th>Invoice</th>
<th>Action</th>
</tr>

<?php foreach($rows as $r): ?>
<tr>
<td>
<b><?= $r['full_name'] ?></b><br>
<small><?= $r['email'] ?></small>
</td>
<td><?= $r['doctor'] ?></td>
<td><?= $r['appointment_date'] ?></td>
<td><?= $r['appointment_time'] ?></td>
<td><?= implode(", ",json_decode($r['tests'],true)) ?></td>
<td><?= $r['status'] ?></td>
<td><?= $r['invoice_number'] ?? '-' ?></td>
<td>

<?php if($r['status']=='pending' && !$isStaff): ?>
<div class="actions">
    <a href="confirm.php?id=<?= $r['id'] ?>" class="btn-save btn-sm">Confirm</a>
    <a href="decline.php?id=<?= $r['id'] ?>" class="btn-decline btn-sm">Decline</a>
</div>
<?php endif; ?>

</td>
</tr>
<?php endforeach; ?>

</table>
</div>
</div>

<?php
$content=ob_get_clean();
require_once '../../includes/layout.php';