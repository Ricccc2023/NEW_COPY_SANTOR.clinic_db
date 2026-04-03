<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/auth.php";

$title = "Doctors";
$active = "doctors";

$loggedUser = $_SESSION['user'] ?? null;
$role = $loggedUser['role'] ?? '';
$userId = $loggedUser['id'] ?? 0;

/* ============================
   HANDLE TOGGLE (ADMIN ONLY)
============================ */

if (isset($_GET['toggle']) && $role === 'admin') {

    $id = (int) $_GET['toggle'];

    /* GET CURRENT STATUS */

    $stmt = $pdo->prepare("
        SELECT is_available 
        FROM doctors 
        WHERE id = ?
    ");

    $stmt->execute([$id]);
    $doctor = $stmt->fetch();

    if ($doctor) {

        $newStatus = $doctor['is_available'] ? 0 : 1;

        /* UPDATE AVAILABILITY */

        $stmt = $pdo->prepare("
            UPDATE doctors
            SET is_available = ?
            WHERE id = ?
        ");

        $stmt->execute([$newStatus, $id]);

        /* RECORD ATTENDANCE ONLY WHEN OPEN */

        if ($newStatus == 1) {

            $today = date('Y-m-d');

            $stmt = $pdo->prepare("
                INSERT INTO doctor_attendance
                (doctor_id, attendance_date, status)
                VALUES (?, ?, 'present')
                ON DUPLICATE KEY UPDATE status='present'
            ");

            $stmt->execute([$id, $today]);
        }
    }

    header("Location: index.php");
    exit;
}

/* ============================
   FETCH DOCTORS BASED ON ROLE
============================ */

$q = trim($_GET['q'] ?? '');

if ($role === 'admin') {

    $stmt = $pdo->prepare("
        SELECT 
            d.id,
            u.full_name,
            LOWER(u.role) AS role,
            d.phone,
            d.is_available,
            u.created_at
        FROM doctors d
        INNER JOIN users u ON u.id = d.user_id
        WHERE u.full_name LIKE :q
        ORDER BY d.id DESC
    ");

    $stmt->execute([':q' => "%{$q}%"]);
    $doctors = $stmt->fetchAll();

} else {

    $stmt = $pdo->prepare("
        SELECT 
            d.id,
            u.full_name,
            LOWER(u.role) AS role,
            d.phone,
            d.is_available,
            u.created_at
        FROM doctors d
        INNER JOIN users u ON u.id = d.user_id
        WHERE u.id = :user_id
        LIMIT 1
    ");

    $stmt->execute([':user_id' => $userId]);
    $doctors = $stmt->fetchAll();
}

ob_start();
?>

<h2>Doctors</h2>


<?php if ($role === 'admin'): ?>

<div class="card" style="margin-bottom:20px;">

<form method="get" style="display:flex; gap:10px; flex-wrap:wrap;">

<input
type="text"
name="q"
value="<?= htmlspecialchars($q) ?>"
placeholder="Search doctor name..."
class="input"
style="max-width:300px;"
>

<button class="btn-search" type="submit">
Search
</button>

<a class="btn-add"
href="create.php"
title="Add Doctor">
+
</a>

</form>

</div>

<?php endif; ?>

<div class="card">

<h3 style="margin-bottom:10px;">
Doctor List
</h3>

<p style="color:#666;margin-bottom:15px;">
Total: <?= count($doctors) ?>
</p>

<div class="table-wrap">

<table>

<thead>

<tr>
<th>Full Name</th>
<th>Role</th>
<th>Phone</th>
<th>Availability</th>
<th>Created</th>
<th style="width:220px;">Actions</th>
</tr>

</thead>

<tbody>

<?php foreach ($doctors as $d): ?>

<tr>

<td>
<b><?= htmlspecialchars($d['full_name']) ?></b>
</td>

<td>

<span style="color:#0d6efd;font-weight:600;">
<?= htmlspecialchars(ucfirst($d['role'])) ?>
</span>

</td>

<td>
<?= htmlspecialchars($d['phone'] ?? '-') ?>
</td>

<td>

<?php if ($d['is_available']): ?>

<span style="color:green;font-weight:600;">
Available
</span>

<?php else: ?>

<span style="color:red;font-weight:600;">
Unavailable
</span>

<?php endif; ?>

</td>

<td>
<?= htmlspecialchars($d['created_at']) ?>
</td>

<td>

<div class="actions">

<?php if ($role === 'admin' || ($role === 'doctor' && $d['id'])): ?>

<a class="btn-save btn-sm"
href="view.php?id=<?= $d['id'] ?>">
View
</a>

<?php endif; ?>

<?php if ($role === 'admin'): ?>

<a class="btn-save btn-sm"
href="edit.php?id=<?= $d['id'] ?>">
Edit
</a>

<?php if ($d['is_available']): ?>

<a class="btn-decline btn-sm"
href="?toggle=<?= (int)$d['id'] ?>"
onclick="return confirm('Mark this doctor as Unavailable?')">

Close

</a>

<?php else: ?>

<a class="btn-save btn-sm"
href="?toggle=<?= (int)$d['id'] ?>"
onclick="return confirm('Mark this doctor as Available?')">

Open

</a>

<?php endif; ?>

<a class="btn-decline btn-sm"
href="archive.php?id=<?= (int)$d['id'] ?>"
onclick="return confirm('Archive this doctor?')">

Archive

</a>

<?php endif; ?>

</div>

</td>

</tr>

<?php endforeach; ?>

<?php if (count($doctors) === 0): ?>

<tr>
<td colspan="6" style="color:#999;">
No doctors found.
</td>
</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../includes/layout.php";
?>