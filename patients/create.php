<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_role(['admin']);

$errors = [];
$doctors = $pdo->query("SELECT id, full_name FROM doctors ORDER BY full_name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $full_name     = trim($_POST['full_name']);
    $date_of_birth = $_POST['date_of_birth'];
    $sex           = $_POST['sex'];
    $address       = trim($_POST['address']);
    $phone         = trim($_POST['phone']);
    $email         = trim($_POST['email']);
    $doctor_id     = (int)$_POST['doctor_id'];
    $date_admitted = $_POST['date_admitted'];
    $fee           = (float)$_POST['professional_fee'];

    if (!$full_name || !$date_of_birth || !$sex || !$address || !$phone) {
        $errors[] = "All required fields must be filled.";
    }

    if (!$errors) {

        $age = date_diff(date_create($date_of_birth), date_create('today'))->y;

        $count = $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn() + 1;
        $invoice = 'INV-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        $stmt = $pdo->prepare("
            INSERT INTO patients
            (full_name, date_of_birth, sex, age, address, phone, email, attending_doctor_id, date_admitted, professional_fee, invoice_number)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $full_name,
            $date_of_birth,
            $sex,
            $age,
            $address,
            $phone,
            $email,
            $doctor_id,
            $date_admitted,
            $fee,
            $invoice
        ]);

        header("Location: index.php");
        exit;
    }
}
ob_start();
?>

<div class="page-header">
    <div class="page-title">
        <h2>Add Patient</h2>
        <p class="sub">Create New Patient Record</p>
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

<!-- ================= APPOINTMENT SELECT ================= -->

<div class="form-row">
<label>Select Appointment (Optional)</label>
<select name="appointment_id" id="appointmentSelect">
<option value="">-- None --</option>
<?php foreach($appointments as $a): ?>
<option value="<?= $a['id'] ?>"
        data-name="<?= htmlspecialchars($a['full_name']) ?>"
        data-dob="<?= $a['date_of_birth'] ?>"
        data-age="<?= $a['age'] ?>"
        data-address="<?= htmlspecialchars($a['address']) ?>"
        data-phone="<?= htmlspecialchars($a['phone']) ?>"
        data-email="<?= htmlspecialchars($a['email']) ?>"
        data-doctor="<?= $a['doctor_id'] ?>">
    <?= htmlspecialchars($a['full_name']) ?>
    (<?= $a['appointment_date'] ?> - <?= $a['doctor_name'] ?>)
</option>
<?php endforeach; ?>
</select>
</div>

<!-- ================= PATIENT INFO ================= -->

<div class="form-row">
<label>Full Name *</label>
<input type="text" name="full_name" id="full_name" required>
</div>

<div class="form-row">
        <label>Date of Birth *</label>
        <input type="date" name="date_of_birth" required>
        </div>

<div class="form-row">
            <label>Sex *</label>
            <select name="sex" required>
            <option value="">Select</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            </select>
            </div>

<div class="form-row">
<label>Age *</label>
<input type="number" name="age" id="age" required>
</div>

<div class="form-row">
<label>Address *</label>
<input type="text" name="address" id="address" required>
</div>

<div class="form-row">
<label>Phone *</label>
<input type="text" name="phone" id="phone" required>
</div>

<div class="form-row">
<label>Email</label>
<input type="email" name="email" id="email">
</div>

<div class="form-row">
<label>Doctor *</label>
<select name="doctor_id" id="doctor_id" required>
<?php foreach($doctors as $d): ?>
<option value="<?= $d['id'] ?>">
<?= htmlspecialchars($d['full_name']) ?>
</option>
<?php endforeach; ?>
</select>
</div>

<div class="form-row">
<label>Date Admitted *</label>
<input type="date" name="date_admitted" value="<?= date('Y-m-d') ?>" required>
</div>

<div class="form-row">
<label>Professional Fee *</label>
<input type="number" step="0.01" name="professional_fee" required>
</div>

<button type="submit" class="btn-save">Save Patient</button>

</form>
</div>

<script>
// Autofill patient info if appointment selected
const appointmentSelect = document.getElementById('appointmentSelect');

appointmentSelect.addEventListener('change', function(){
    const selected = this.options[this.selectedIndex];

    if (!this.value) return;

    document.getElementById('full_name').value = selected.dataset.name;
    document.getElementById('age').value = selected.dataset.age;
    document.getElementById('address').value = selected.dataset.address;
    document.getElementById('phone').value = selected.dataset.phone;
    document.getElementById('email').value = selected.dataset.email;
    document.getElementById('doctor_id').value = selected.dataset.doctor;
});
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../includes/layout.php';