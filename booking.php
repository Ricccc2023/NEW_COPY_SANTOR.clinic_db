<?php
require_once __DIR__ . '/../includes/config.php';

$testsList = ["CBC","Urinalysis","Fecalysis","Chest Xray","ECG","Physical Exam"];

$timeSlots = [
"7:00-8:00 AM",
"8:00-9:00 AM",
"9:00-10:00 AM",
"10:00-11:00 AM",
"11:00-12:00 PM"
];

/* ==========================================
   ONLY AVAILABLE DOCTORS
   (exclude non-doctors + unavailable)
========================================== */

$stmtDoctors = $pdo->prepare("
    SELECT d.id, u.full_name
    FROM doctors d
    INNER JOIN users u ON d.user_id = u.id
    WHERE u.role = 'doctor'
    AND d.is_available = 1
    ORDER BY u.full_name
");
$stmtDoctors->execute();
$doctors = $stmtDoctors->fetchAll(PDO::FETCH_ASSOC);

$errors = [];

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $full_name     = trim($_POST['full_name'] ?? '');
    $date_of_birth = $_POST['date_of_birth'] ?? '';
    $sex           = $_POST['sex'] ?? '';
    $age           = (int)($_POST['age'] ?? 0);
    $address       = trim($_POST['address'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $phone         = trim($_POST['phone'] ?? '');
    $doctor        = isset($_POST['doctor']) ? (int)$_POST['doctor'] : 0;
    $date          = $_POST['date'] ?? '';
    $time          = $_POST['time'] ?? '';
    $tests         = json_encode($_POST['tests'] ?? []);

    // ================= VALIDATION =================
    if(!$full_name || !$date_of_birth || !$sex || !$address || !$phone){
        $errors[] = "All required fields must be filled.";
    }

    if(!$doctor){
        $errors[] = "Please select a doctor.";
    }

    if(!$date || !$time){
        $errors[] = "Appointment date and time are required.";
    }

    /* EXTRA SECURITY CHECK:
       Ensure selected doctor is still available */
    if($doctor){
        $checkDoctor = $pdo->prepare("
            SELECT id FROM doctors 
            WHERE id = ? AND is_available = 1
        ");
        $checkDoctor->execute([$doctor]);

        if(!$checkDoctor->fetch()){
            $errors[] = "Selected doctor is no longer available.";
        }
    }

    // ================= INSERT =================
    if(!$errors){

        $stmt = $pdo->prepare("
            INSERT INTO appointments
            (full_name, date_of_birth, sex, age, address, email, phone, doctor_id, appointment_date, appointment_time, tests)
            VALUES(?,?,?,?,?,?,?,?,?,?,?)
        ");

        $stmt->execute([
            $full_name,
            $date_of_birth,
            $sex,
            $age,
            $address,
            $email,
            $phone,
            $doctor,
            $date,
            $time,
            $tests
        ]);

        header("Location: thankyou.php");
        exit;
    }
}
ob_start();
?>

<style>

/* Layout Wrapper */
.page-wrapper {
    display: flex;
    align-items: flex-start;
    justify-content: flex-start;
    gap: 25px;
    padding: 30px;
}

/* MAIN APPOINTMENT CARD */
.card {
    width: 1000px;
    padding: 10px;
    border: 1px solid #ddd;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);


}

/* Remove rounded corners */
.card,
.side-card,
input,
select,
.btn-save {
    border-radius: 0 !important;
}

/* SIDE INSTRUCTION CARD */
.side-card {
    width: 280px;
    background: #f1f1f1;
    padding: 18px;
    border: 1px solid #ddd;
}

/* Headings */
h2 {
    margin-bottom: 3px;
}

.sub-text {
    color: #666;
    font-size: 14px;
    margin-bottom: 15px;
}

/* FORM GRID */
.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.full-width {
    grid-column: 1 / -1;
}

label {
    font-size: 13px;
    margin-bottom: 3px;
    font-weight: 600;
}

input, select {
    padding: 6px 8px;
    font-size: 13px;
    border: 1px solid #ccc;
    background: rgba(255,255,255,0.95);
}

/* Checkbox */
.checkbox-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 6px;
    font-size: 13px;
}

/* Button */
.btn-save {
    background: #1f4e46;
    color: #fff;
    border: none;
    padding: 8px 14px;
    cursor: pointer;
}

.btn-save:hover {
    background: #1f4e46;
}

/* Error box */
.error-box {
    background: #ffe6e6;
    color: #b30000;
    padding: 8px;
    margin-bottom: 12px;
    font-size: 13px;
    border: 1px solid #ffcccc;
}
</style>


<div class="page-wrapper">

    <!-- LEFT: MAIN APPOINTMENT CARD -->
    <div class="card">

        <h2>Add Appointment</h2>
        <div class="sub-text">Create New Appointment Record</div>

        <?php if($errors): ?>
            <div class="error-box">
                <?= implode("<br>", $errors) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="form-grid">

                <div class="form-group">
                    <label>Full Name *</label>
                    <input name="full_name" required>
                </div>

                <div class="form-group">
                    <label>Date of Birth *</label>
                    <input type="date" name="date_of_birth" required>
                </div>

                <div class="form-group">
                    <label>Sex *</label>
                    <select name="sex" required>
                        <option value="">Select</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Age *</label>
                    <input type="number" name="age" required>
                </div>

            
                <div class="form-group">
                    <label>Address *</label>
                    <input name="address" required>
                </div>

                <div class="form-group">
                    <label>Phone *</label>
                    <input name="phone" required>
                </div>

                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" required>
                </div>

                <div class="form-group">
                        <label>Doctor *</label>
                        <select name="doctor" required>
                            <option value="" disabled selected>-- Select Doctor --</option>
                            
                            <?php foreach($doctors as $d): ?>
                                <option value="<?= $d['id'] ?>">
                                    <?= htmlspecialchars($d['full_name']) ?>
                                </option>
                            <?php endforeach; ?>
                            
                        </select>
                    </div>

                <div class="form-group">
                    <label>Date *</label>
                    <input type="date" name="date" required>
                </div>

                <div class="form-group">
                    <label>Time *</label>
                    <select name="time" required>
                        <?php foreach($timeSlots as $t): ?>
                            <option value="<?= $t ?>"><?= $t ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label>Tests (if needed)</label>
                    <div class="checkbox-grid">
                        <?php foreach($testsList as $test): ?>
                            <label>
                                <input type="checkbox" name="tests[]" value="<?= $test ?>">
                                <?= $test ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                            
                <div class="form-group full-width">
                    <button type="submit" class="btn-save">
                        Save Appointment
                    </button>
                </div>

            </div>
        </form>
    </div>


    <!-- RIGHT: SMALL INSTRUCTION CARD -->
    <div class="side-card">

        <h3 style="margin-bottom:8px;">Booking Instructions</h3>

        <p style="font-size:13px; margin-bottom:10px;">
            Please follow the guidelines below before confirming your appointment.
        </p>

        <ul style="font-size:13px; padding-left:18px; line-height:1.5;">
            <li>Appointments must be booked at least <b>1 day in advance</b>.</li>
            <li>Maximum of <b>1 active booking per patient</b>.</li>
            <li>Please arrive <b>15 minutes early</b> on appointment day.</li>
            <li>Bring valid ID and previous medical records (if any).</li>
            <li>Late arrivals beyond 20 minutes may be rescheduled.</li>
        </ul>

        <hr style="margin:12px 0;">

        <h4 style="margin-bottom:6px;">Limitations</h4>

        <ul style="font-size:13px; padding-left:18px; line-height:1.5;">
            <li>Walk-ins are subject to availability.</li>
            <li>Emergency cases are prioritized.</li>
            <li>Selected tests may require fasting.</li>
        </ul>

    </div>

</div>

<script>
const doctorSelect = document.querySelector('select[name="doctor"]');
const dateInput = document.querySelector('input[name="date"]');
const timeSelect = document.querySelector('select[name="time"]');

function checkSlots() {
    const doctor = doctorSelect.value;
    const date = dateInput.value;
    if (!doctor || !date) return;

    fetch(`appointments/check_slots.php?doctor=${doctor}&date=${date}`)
    .then(res => res.json())
    .then(data => {
        [...timeSelect.options].forEach(opt => {
            opt.disabled = false;
            if (data.includes(opt.value)) {
                opt.disabled = true;
            }
        });
    });
}

doctorSelect.addEventListener('change', checkSlots);
dateInput.addEventListener('change', checkSlots);
</script>

<?php
$content = ob_get_clean();
$hideSidebar = true;
require_once __DIR__ . "/../includes/layout.php";