<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

require_role(['admin','staff']);

/* ===============================
   VALIDATE INPUT
=============================== */
if (!isset($_GET['visit_id']) || !is_numeric($_GET['visit_id'])) {
    exit("Invalid visit reference.");
}

$visit_id = (int) $_GET['visit_id'];

/* ===============================
   FETCH VISIT + PATIENT
=============================== */
$stmt = $pdo->prepare("
    SELECT 
        v.id AS visit_id,
        p.id AS patient_id,
        p.full_name,
        p.professional_fee
    FROM patient_test_visits v
    JOIN patients p ON p.id = v.patient_id
    WHERE v.id = ?
");
$stmt->execute([$visit_id]);
$visit = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$visit) {
    exit("Visit not found.");
}

/* ===============================
   FETCH OR CREATE BILLING
=============================== */
$stmt = $pdo->prepare("SELECT * FROM billings WHERE visit_id = ?");
$stmt->execute([$visit_id]);
$billing = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$billing) {
    $stmt = $pdo->prepare("
        INSERT INTO billings (visit_id, payment_mode)
        VALUES (?, 'Cash')
    ");
    $stmt->execute([$visit_id]);

    $stmt = $pdo->prepare("SELECT * FROM billings WHERE visit_id = ?");
    $stmt->execute([$visit_id]);
    $billing = $stmt->fetch(PDO::FETCH_ASSOC);
}

/* ===============================
   LAB TOTAL
=============================== */
$stmt = $pdo->prepare("
    SELECT IFNULL(SUM(test_fee),0)
    FROM patient_tests
    WHERE visit_id = ?
");
$stmt->execute([$visit_id]);
$labTotal = (float)$stmt->fetchColumn();

/* ===============================
   HANDLE UPDATE
=============================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $payment_mode = $_POST['payment_mode'] ?? 'Cash';
    $professional_fee = $_POST['professional_fee'] ?? '0';

    // Clean numeric input
    $professional_fee = floatval(preg_replace('/[^0-9.]/', '', $professional_fee));

    if ($professional_fee < 0) {
        $professional_fee = 0;
    }

    // Update patient fee
    $stmt = $pdo->prepare("
        UPDATE patients
        SET professional_fee = ?
        WHERE id = ?
    ");
    $stmt->execute([$professional_fee, $visit['patient_id']]);

    // Update billing
    $stmt = $pdo->prepare("
        UPDATE billings
        SET payment_mode = ?
        WHERE visit_id = ?
    ");
    $stmt->execute([$payment_mode, $visit_id]);

    header("Location: index.php");
    exit;
}

/* ===============================
   TOTAL
=============================== */
$professionalFee = (float)$visit['professional_fee'];
$total = $labTotal + $professionalFee;

ob_start();
?>

<div class="page-header">
    <div class="page-title">
        <h2>Edit Billing</h2>
        <p class="sub">Update billing and payment details</p>
    </div>
</div>

<div class="card">

    <div class="form-wrapper">
        <form method="POST">

            <!-- Patient -->
            <div class="form-row">
                <label>Patient Name</label>
                <div class="static-value">
                    <?= htmlspecialchars($visit['full_name']) ?>
                </div>
            </div>

            <!-- Visit -->
            <div class="form-row">
                <label>Visit ID</label>
                <div class="static-value">
                    #<?= $visit['visit_id'] ?>
                </div>
            </div>

            <!-- Lab Total -->
            <div class="form-row">
                <label>Lab Total</label>
                <div class="static-value">
                    ₱<?= number_format($labTotal,2) ?>
                </div>
            </div>

            <!-- Professional Fee (NO ARROWS) -->
            <div class="form-row">
                <label for="professional_fee">Professional Fee</label>
                <input 
                    type="text"
                    inputmode="decimal"
                    name="professional_fee"
                    id="professional_fee"
                    value="<?= number_format($professionalFee,2,'.','') ?>"
                    required
                    class="input-clean"
                >
            </div>

            <!-- Total -->
            <div class="form-row">
                <label>Total Amount</label>
                <div class="static-value total-highlight">
                    ₱<?= number_format($total,2) ?>
                </div>
            </div>

            <!-- Payment -->
            <div class="form-row">
                <label for="payment_mode">Payment Mode</label>
                <select name="payment_mode" id="payment_mode" required>
                    <option value="Cash" <?= $billing['payment_mode']=='Cash'?'selected':'' ?>>Cash</option>
                    <option value="GCash" <?= $billing['payment_mode']=='GCash'?'selected':'' ?>>GCash</option>
                    <option value="Card" <?= $billing['payment_mode']=='Card'?'selected':'' ?>>Card</option>
                </select>
            </div>

            <!-- Submit -->
            <div class="form-row">
                <label></label>
                <button type="submit" class="btn-save">
                    Save Changes
                </button>
            </div>

        </form>
    </div>

</div>

<style>
/* 🔥 Remove number input arrows (fallback if changed later) */
input[type=number]::-webkit-inner-spin-button, 
input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
input[type=number] {
    -moz-appearance: textfield;
}

/* Clean text input style */
.input-clean {
    width: 100%;
    padding: 10px;
    font-size: 15px;
}
</style>

<?php
$content = ob_get_clean();
require_once '../includes/layout.php';
?>