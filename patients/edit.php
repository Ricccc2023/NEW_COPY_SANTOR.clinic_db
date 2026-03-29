<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_role(['admin']);

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->execute([$id]);
$patient = $stmt->fetch();

if (!$patient) exit("Patient not found.");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $age = date_diff(date_create($_POST['date_of_birth']), date_create('today'))->y;

    $stmt = $pdo->prepare("
        UPDATE patients
        SET full_name=?, date_of_birth=?, sex=?, age=?, address=?, phone=?, email=?, attending_doctor_id=?, date_admitted=?, professional_fee=?
        WHERE id=?
    ");

            $stmt->execute([
            $_POST['full_name'],
            $_POST['date_of_birth'],
            $_POST['sex'],                 //
            $age,
            $_POST['address'],
            $_POST['phone'],
            $_POST['email'],
            $_POST['doctor_id'],
            $_POST['date_admitted'],
            $_POST['professional_fee'],
            $id
        ]);

    header("Location: index.php");
    exit;
}

$doctors = $pdo->query("SELECT id, full_name FROM doctors")->fetchAll();

ob_start();
?>

<div class="page-header">
<h2>Edit Patient</h2>
<a href="index.php" class="btn-save btn-sm">Back</a>
</div>

<div class="card">
<form method="POST" class="form-wrapper">

<div class="form-row">
<label>Name</label>
<input name="full_name" value="<?= htmlspecialchars($patient['full_name']) ?>">
</div>

<div class="form-row">
<label>Sex</label>
<select name="sex">
<option value="Male" <?= $patient['sex']=='Male'?'selected':'' ?>>Male</option>
<option value="Female" <?= $patient['sex']=='Female'?'selected':'' ?>>Female</option>
</select></div>

<div class="form-row">
<label>Age</label>
<input name="age" value="<?= $patient['age'] ?>">
</div>

<div class="form-row">
<label>Address</label>
<input name="address" value="<?= htmlspecialchars($patient['address']) ?>">
</div>

<div class="form-row">
<label>Phone</label>
<input name="phone" value="<?= htmlspecialchars($patient['phone']) ?>">
</div>

<div class="form-row">
<label>Email</label>
<input name="email" value="<?= htmlspecialchars($patient['email']) ?>">
</div>

<div class="form-row">
<label>Doctor</label>
<select name="doctor_id">
<?php foreach($doctors as $d): ?>
<option value="<?= $d['id'] ?>" <?= $d['id']==$patient['attending_doctor_id']?'selected':'' ?>>
<?= htmlspecialchars($d['full_name']) ?>
</option>
<?php endforeach; ?>
</select>
</div>

<div class="form-row">
<label>Date Admitted</label>
<input type="date" name="date_admitted" value="<?= $patient['date_admitted'] ?>">
</div>

<div class="form-row">
<label>Fee</label>
<input name="professional_fee" value="<?= $patient['professional_fee'] ?>">
</div>

<button class="btn-save">Update</button>

</form>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../includes/layout.php';