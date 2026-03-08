<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_role(['admin','doctor']);

/* ===================================
   GET VISITS (LIKE YOUR TEST INDEX)
=================================== */

$sql = "
SELECT 
    v.id AS visit_id,
    p.full_name,
    COUNT(pt.id) AS total_tests,
    v.created_at
FROM patient_test_visits v
INNER JOIN patients p ON p.id = v.patient_id
LEFT JOIN patient_tests pt ON pt.visit_id = v.id
GROUP BY v.id, p.full_name, v.created_at
ORDER BY v.created_at DESC
";

$visits = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);


/* ===================================
   SAVE BILLING
=================================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $visit_id = (int) $_POST['visit_id'];
    $payment_mode = $_POST['payment_mode'];

    if (!$visit_id || !$payment_mode) {
        $error = "All fields are required.";
    } else {

        /* DUPLICATE CHECK */
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM billings 
            WHERE visit_id = ?
        ");
        $stmt->execute([$visit_id]);

        if ($stmt->fetchColumn() > 0) {
            $error = "Billing already exists for this visit.";
        } else {

            /* COMPUTE TOTAL HERE */
            // lab total query
            // professional fee query
            // $total calculation

            /* INSERT BILLING */
            $stmt = $pdo->prepare("
                INSERT INTO billings 
                (visit_id, total, payment_mode, created_at)
                VALUES (?, ?, ?, NOW())
            ");
            $stmt->execute([
                $visit_id,
                $total,
                $payment_mode
            ]);

            header("Location: index.php");
            exit;
        }
    }
}

ob_start();
?>

<div class="page-header">
    <div class="header-row">
        <div class="title-section">
            <h2>Create Billing</h2>
            <p class="sub">Generate billing for completed laboratory visit</p>
        </div>

        <a href="index.php" class="btn-save btn-sm back-btn">Back</a>
    </div>
</div>

<div class="card">

    <?php if (!empty($error)): ?>
        <div class="error-box">
            <b>Error:</b> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="form-wrapper">
        <form method="POST">

            <!-- Laboratory Visit -->
            <div class="form-row">
                <label for="visit_id">Laboratory Visit</label>
                <select name="visit_id" id="visit_id" required>
                    <option value="">-- Select Visit --</option>

                    <?php foreach ($visits as $v): ?>
                        <option value="<?= $v['visit_id'] ?>">
                            <?= htmlspecialchars($v['full_name']) ?> 
                            | <?= $v['total_tests'] ?> tests 
                            | <?= $v['created_at'] ?>
                        </option>
                    <?php endforeach; ?>

                </select>
            </div>

            <!-- Payment Mode -->
            <div class="form-row">
                <label for="payment_mode">Payment Mode</label>
                <select name="payment_mode" id="payment_mode" required>
                    <option value="">-- Select Mode --</option>
                    <option value="Cash">Cash</option>
                    <option value="GCash">GCash</option>
                    <option value="Card">Card</option>
                </select>
            </div>

            <!-- Submit -->
            <div class="form-row">
                <label></label>
                <button type="submit" class="btn-save">
                    Create Billing
                </button>
            </div>

        </form>
    </div>

</div>

<?php
$content = ob_get_clean();
require_once '../../includes/layout.php';
?>