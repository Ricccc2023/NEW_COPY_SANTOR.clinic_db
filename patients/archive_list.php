<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

require_role(['admin']);

$stmt = $pdo->query("
    SELECT 
        pa.*, 
        d.full_name AS doctor_name,
        u.full_name AS archived_by_name
    FROM patients_archive pa
    LEFT JOIN doctors d ON pa.attending_doctor_id = d.id
    LEFT JOIN users u ON pa.archived_by = u.id
    ORDER BY pa.archived_at DESC
");

$patients = $stmt->fetchAll();

ob_start();
?>

<div class="page-header">
    <div class="page-title">
        <h2>Archived Patients</h2>
        <p class="sub">Historical Patient Records</p>
    </div>
    <div class="page-action">
        <a href="index.php" class="btn-save btn-sm">Back</a>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Sex</th>
                    <th>Doctor</th>
                    <th>Admitted</th>
                    <th>Fee</th>
                    <th>Invoice</th>
                    <th>Archived At</th>
                    <th>Archived By</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

            <?php if (empty($patients)): ?>
                <tr>
                    <td colspan="8">No archived patients found.</td>
                </tr>
            <?php else: ?>

                <?php foreach ($patients as $p): ?>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($p['full_name']) ?></strong><br>
                        <strong><?= htmlspecialchars($p['sex']) ?></strong><br>
                        <strong><?= htmlspecialchars($p['date_of_birth']) ?></strong><br>
                        <small><?= htmlspecialchars($p['email'] ?? '') ?></small>
                    </td>

                    <td><?= htmlspecialchars($p['doctor_name'] ?? 'N/A') ?></td>

                    <td><?= htmlspecialchars($p['date_admitted']) ?></td>

                    <td>₱<?= number_format($p['professional_fee'], 2) ?></td>

                    <td><?= htmlspecialchars($p['invoice_number'] ?? '-') ?></td>

                    <td><?= htmlspecialchars($p['archived_at']) ?></td>

                    <td><?= htmlspecialchars($p['archived_by_name'] ?? 'Unknown') ?></td>

                    <td>
                        <a href="restore.php?id=<?= $p['id'] ?>" 
                           class="btn-save btn-sm"
                           onclick="return confirm('Restore this patient?');">
                           Restore
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
require_once __DIR__ . '/../includes/layout.php';