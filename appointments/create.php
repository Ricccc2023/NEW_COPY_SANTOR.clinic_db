<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

require_role(['admin']);

$testsList = ["CBC","Urinalysis","Fecalysis","Chest Xray","ECG","Physical Exam"];

$timeSlots = [
"7:00-8:00 AM",
"8:00-9:00 AM",
"9:00-10:00 AM",
"10:00-11:00 AM",
"11:00-12:00 PM"
];

$doctors = $pdo->query("
    SELECT id, full_name 
    FROM doctors 
    ORDER BY full_name ASC
")->fetchAll();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $full_name = trim($_POST['full_name'] ?? '');
    $age       = (int)($_POST['age'] ?? 0);
    $address   = trim($_POST['address'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $doctor_id = (int)($_POST['doctor'] ?? 0);
    $date      = $_POST['date'] ?? '';
    $time      = $_POST['time'] ?? '';
    $tests     = $_POST['tests'] ?? [];

    if (!$full_name || !$age || !$address || !$phone || !$doctor_id || !$date || !$time) {
        $errors[] = "All required fields must be filled.";
    }

    /*
    Check slot availability
    */
    if (empty($errors)) {
        $slot = $pdo->prepare("
            SELECT id FROM appointments
            WHERE appointment_date = ?
              AND appointment_time = ?
              AND doctor_id = ?
        ");
        $slot->execute([$date, $time, $doctor_id]);

        if ($slot->fetch()) {
            $errors[] = "Time slot already taken.";
        }
    }

    /*
    Insert as PENDING only
    */
    if (empty($errors)) {

        try {

            $stmt = $pdo->prepare("
                INSERT INTO appointments
                (full_name, age, address, email, phone,
                 doctor_id, appointment_date, appointment_time,
                 tests, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
            ");

            $stmt->execute([
                $full_name,
                $age,
                $address,
                $email,
                $phone,
                $doctor_id,
                $date,
                $time,
                json_encode($tests)
            ]);

            header("Location: index.php");
            exit;

        } catch (Exception $e) {
            $errors[] = "Failed to create appointment.";
        }
    }
}

ob_start();
?>

<div class="page-header">
    <div class="page-title">
        <h2>Manual Appointment</h2>
        <p class="sub">Admin Appointment Entry (Pending)</p>
    </div>
    <div class="page-action">
        <a href="index.php" class="btn-save btn-sm">Back</a>
    </div>
</div>

<div class="card">

<?php if ($errors): ?>
<div class="error-box">
    <b>Error:</b><br>
    <?= implode('<br>', $errors) ?>
</div>
<?php endif; ?>

<form method="POST" class="form-wrapper">

<div class="form-row">
<label>Full Name *</label>
<input name="full_name" required>
</div>

<div class="form-row">
<label>Age *</label>
<input type="number" name="age" required>
</div>

<div class="form-row">
<label>Address *</label>
<input name="address" required>
</div>

<div class="form-row">
<label>Email *</label>
<input type="email" name="email" required>
</div>

<div class="form-row">
<label>Phone *</label>
<input name="phone" required>
</div>

<div class="form-row">
<label>Doctor *</label>
<select name="doctor" required>
<?php foreach ($doctors as $d): ?>
<option value="<?= $d['id'] ?>">
<?= htmlspecialchars($d['full_name']) ?>
</option>
<?php endforeach; ?>
</select>
</div>

<div class="form-row">
<label>Date *</label>
<input type="date" name="date" required>
</div>

<div class="form-row">
<label>Time *</label>
<select name="time" required>
<?php foreach ($timeSlots as $t): ?>
<option value="<?= $t ?>"><?= $t ?></option>
<?php endforeach; ?>
</select>
</div>

<div class="form-row">
    <label>Tests Needed</label>

    <div class="tests-list">
        <?php foreach ($testsList as $t): ?>
            <label class="test-item">
                <input type="checkbox" name="tests[]" value="<?= $t ?>">
                <?= $t ?>
            </label>
        <?php endforeach; ?>
    </div>
</div>

<button class="btn-save">Create Appointment</button>

</form>

</div>

<?php
$content = ob_get_clean();
require_once '../includes/layout.php';