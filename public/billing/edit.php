<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_role(['admin','doctor']);

if (!isset($_GET['id'])) {
    exit("Billing ID missing.");
}

$id = (int) $_GET['id'];

/* ===============================
   GET BILLING INFO
=============================== */
$stmt = $pdo->prepare("
    SELECT b.id, b.visit_id, b.payment_mode,
           p.full_name
    FROM billings b
    JOIN patient_test_visits v ON v.id = b.visit_id
    JOIN patients p ON p.id = v.patient_id
    WHERE b.id = ?
");
$stmt->execute([$id]);
$bill = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$bill) {
    exit("Billing not found.");
}

/* ===============================
   COMPUTE TOTAL (DISPLAY ONLY)
=============================== */
$stmt = $pdo->prepare("
    SELECT IFNULL(SUM(pt.test_fee),0)
    FROM patient_tests pt
    WHERE pt.visit_id = ?
");
$stmt->execute([$bill['visit_id']]);
$labTotal = (float)$stmt->fetchColumn();

$stmt = $pdo->prepare("
    SELECT p.professional_fee
    FROM patient_test_visits v
    JOIN patients p ON p.id = v.patient_id
    WHERE v.id = ?
");
$stmt->execute([$bill['visit_id']]);
$professionalFee = (float)$stmt->fetchColumn();

$total = $labTotal + $professionalFee;

/* ===============================
   UPDATE
=============================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $payment_mode = $_POST['payment_mode'];

    $stmt = $pdo->prepare("
        UPDATE billings
        SET payment_mode = ?
        WHERE id = ?
    ");
    $stmt->execute([$payment_mode, $id]);

    header("Location: index.php");
    exit;
}

ob_start();
?>

<div class="page-header">
    <div class="page-title">
        <h2>Edit Billing</h2>
        <p class="sub">Update payment information</p>
    </div>
</div>

<div class="card">

    <div class="form-wrapper">
        <form method="POST">

            <!-- Patient -->
                            <div class="form-row">
                    <label>Patient</label>
                    <div class="static-value">
                        <?= htmlspecialchars($bill['full_name']) ?>
                    </div>
                </div>

                <div class="form-row">
                    <label>Visit ID</label>
                    <div class="static-value">
                        #<?= $bill['visit_id'] ?>
                    </div>
                </div>

                <div class="form-row">
                    <label>Total (Auto Computed)</label>
                    <div class="static-value total-highlight">
                        ₱<?= number_format($total,2) ?>
                    </div>
                </div>

            <!-- Payment Mode -->
            <div class="form-row">
                <label for="payment_mode">Payment Mode</label>
                <select name="payment_mode" id="payment_mode" required>
                    <option value="Cash" <?= $bill['payment_mode']=='Cash'?'selected':'' ?>>Cash</option>
                    <option value="GCash" <?= $bill['payment_mode']=='GCash'?'selected':'' ?>>GCash</option>
                    <option value="Card" <?= $bill['payment_mode']=='Card'?'selected':'' ?>>Card</option>
                </select>
            </div>

            <!-- Submit -->
            <div class="form-row">
                <label></label>
                <button type="submit" class="btn-save">
                    Update Billing
                </button>
            </div>

        </form>
    </div>

</div>

<?php
$content = ob_get_clean();
require_once '../../includes/layout.php';
?>