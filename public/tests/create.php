<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_role(['admin','doctor']);

$parameterPresets = [
'CBC' => ['White Blood Cells','Red Blood Cells','Hemoglobin','Hematocrit','Platelets'],
'Urinalysis' => ['Color','Transparency','pH','Protein','Glucose'],
'Fecalysis' => ['Color','Consistency','Parasites','Occult Blood'],
'Chest Xray' => ['Findings','Impression'],
'ECG' => ['Heart Rate','Rhythm','Interpretation'],
'Physical Exam' => ['Blood Pressure','Heart Rate','Respiratory Rate','Temperature']
];

if($_SERVER['REQUEST_METHOD'] === 'POST') {

    $patient_id = (int) $_POST['patient_id'];
    $status = $_POST['status'] ?? 'Pending';
    $now = date('Y-m-d H:i:s');

    if(!empty($_POST['tests'])) {

        $pdo->beginTransaction();

        try {

            // ✅ CREATE NEW VISIT GROUP
            $visitInsert = $pdo->prepare("
                INSERT INTO patient_test_visits (patient_id, created_at)
                VALUES (?,?)
            ");
            $visitInsert->execute([$patient_id, $now]);

            $visit_id = $pdo->lastInsertId();

            foreach($_POST['tests'] as $lab_test_id) {

                $lab_test_id = (int) $lab_test_id;

                $testStmt = $pdo->prepare("SELECT id, name, default_fee FROM lab_tests WHERE id=?");
                $testStmt->execute([$lab_test_id]);
                $test = $testStmt->fetch();

                if(!$test) continue;

                $result_date = ($status === 'Completed') ? $now : null;
                $note = $_POST['notes'][$lab_test_id] ?? '';

                $insert = $pdo->prepare("
                    INSERT INTO patient_tests
                    (visit_id, patient_id, lab_test_id, status, result_date, notes, test_fee, created_at)
                    VALUES (?,?,?,?,?,?,?,?)
                ");

                $insert->execute([
                    $visit_id,
                    $patient_id,
                    $lab_test_id,
                    $status,
                    $result_date,
                    $note,
                    $test['default_fee'],
                    $now
                ]);

                $patient_test_id = $pdo->lastInsertId();

                if(isset($parameterPresets[$test['name']])) {
                    foreach($parameterPresets[$test['name']] as $param) {

                        $value = $_POST['value'][$lab_test_id][$param] ?? '';
                        $interp = $_POST['interpretation'][$lab_test_id][$param] ?? '';
                        $meaning = $_POST['meaning'][$lab_test_id][$param] ?? '';

                        $pdo->prepare("
                            INSERT INTO patient_test_results
                            (patient_test_id, parameter_name, result_value, interpretation, meaning)
                            VALUES (?,?,?,?,?)
                        ")->execute([
                            $patient_test_id,
                            $param,
                            $value,
                            $interp,
                            $meaning
                        ]);
                    }
                }
            }

            $pdo->commit();

        } catch(Exception $e) {
            $pdo->rollBack();
            die("Error saving tests.");
        }
    }

    header("Location: index.php");
    exit;
}

$patients = $pdo->query("SELECT id, full_name FROM patients ORDER BY full_name")->fetchAll();
$tests = $pdo->query("SELECT id, name FROM lab_tests ORDER BY name")->fetchAll();
ob_start();
?>
<!-- FORM SAME AS BEFORE -->
<?php ob_start(); ?>

<div class="page-header">
    <h2>Add Laboratory Tests</h2>
    <div class="page-action">
        <a href="index.php" class="btn-save btn-sm">Back</a>
    </div>
</div>

<div class="card">

<form method="POST">

    <!-- PATIENT -->
    <div class="form-group">
        <label><strong>Patient</strong></label>
        <select name="patient_id" class="form-control" required>
            <?php foreach($patients as $p): ?>
                <option value="<?= $p['id'] ?>">
                    <?= htmlspecialchars($p['full_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <h4>Select Tests</h4>

<?php foreach($tests as $t): ?>
    
<div class="test-container">

    <!-- CHECKBOX -->
    <div class="checkbox-area">
        <input type="checkbox"
               class="test-toggle"
               value="<?= $t['id'] ?>"
               id="check-<?= $t['id'] ?>"
               name="tests[]">
        <label for="check-<?= $t['id'] ?>">
            <?= htmlspecialchars($t['name']) ?>
        </label>
    </div>

    <!-- PARAMETERS (HIDDEN FIRST) -->
    <div class="test-parameters" id="params-<?= $t['id'] ?>">

        <table class="result-table">
            <thead>
                <tr>
                    <th>Parameter</th>
                    <th>Result</th>
                    <th>Status</th>
                    <th>Meaning</th>
                </tr>
            </thead>
            <tbody>
            <?php if(isset($parameterPresets[$t['name']])): ?>
                <?php foreach($parameterPresets[$t['name']] as $param): ?>
                    <tr>
                        <td><?= htmlspecialchars($t['name']) ?> - <?= htmlspecialchars($param) ?></td>
                        <td>
                            <textarea name="value[<?= $t['id'] ?>][<?= $param ?>]" 
                                      class="auto-expand"></textarea>
                        </td>
                        <td>
                            <textarea name="interpretation[<?= $t['id'] ?>][<?= $param ?>]" 
                                      class="auto-expand"></textarea>
                        </td>
                        <td>
                            <textarea name="meaning[<?= $t['id'] ?>][<?= $param ?>]" 
                                      class="auto-expand"></textarea>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>

        <!-- DOCTOR NOTE -->
        <div class="doctor-note">
            <strong>Doctor Note (<?= htmlspecialchars($t['name']) ?>)</strong>
            <textarea name="notes[<?= $t['id'] ?>]" class="auto-expand full-width"></textarea>
        </div>

    </div>

</div>

<?php endforeach; ?>

    <!-- STATUS -->
    <div class="form-group">
        <label>Status</label>
        <select name="status" class="form-control">
            <option value="Pending">Pending</option>
            <option value="Ongoing">Ongoing</option>
            <option value="Completed">Completed</option>
        </select>
    </div>

    <button type="submit" class="btn-save">Save</button>

</form>
</div>

<style>

/* FORM SPACING */
.form-group{
    margin-bottom:20px;
}

.form-control{
    width:100%;
    padding:8px;
    border:1px solid #ccc;
    border-radius:4px;
}

/* TEST CONTAINER */
.test-container {
    margin-bottom: 8px;   /* from 25px → 8px */
}

.checkbox-area {
    display: flex;
    align-items: center;
    gap: 6px;             /* mas maliit gap */
    margin-bottom: 4px;   /* bawas space */
}

.checkbox-area label {
    margin: 0;
    font-weight: normal;
}

.checkbox-area input[type="checkbox"] {
    margin: 0;
}

/* Optional: tighten Select Tests header */
h4 {
    margin-bottom: 10px;
}
/* PARAMETERS HIDDEN BY DEFAULT */
.test-parameters{
    display:none;
    margin-left:25px;
    background:#fff;
    padding:15px;
    border:1px solid #ddd;
    border-radius:6px;
    animation: fadeSlide .2s ease-in-out;
}

@keyframes fadeSlide{
    from{opacity:0; transform:translateY(-5px);}
    to{opacity:1; transform:translateY(0);}
}

/* TABLE */
.result-table{
    width:100%;
    border-collapse:collapse;
}

.result-table th,
.result-table td{
    border:1px solid #ddd;
    padding:8px;
}

.result-table th{
    background:#f4f4f4;
    text-align:left;
}

/* TEXTAREA */
.auto-expand{
    width:100%;
    min-height:35px;
    resize:none;
    padding:6px;
    border:1px solid #ccc;
    border-radius:4px;
}

.full-width{
    width:100%;
}

/* DOCTOR NOTE */
.doctor-note{
    margin-top:15px;
    padding:12px;
    background:#f9f9f9;
    border-left:4px solid #2c5f4f;
    border-radius:4px;
}

/* BUTTON */
.btn-save{
    margin-top:20px;
    background:#2c5f4f;
    color:white;
    padding:10px 20px;
    border:none;
    border-radius:6px;
    cursor:pointer;
}

.btn-save:hover{
    background:#23493c;
}
/* Smaller Status Select */
select[name="status"] {
    width: 100px;   /* pwede mo gawing 150px–250px */
}
</style>

<script>

/* TOGGLE PARAMETERS */
document.querySelectorAll('.test-toggle').forEach(cb=>{
    cb.addEventListener('change',function(){
        const box = document.getElementById('params-'+this.value);
        box.style.display = this.checked ? 'block' : 'none';
    });
});

/* AUTO EXPAND TEXTAREA */
document.querySelectorAll('.auto-expand').forEach(el=>{
    el.addEventListener('input', function(){
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
    });
});

</script>

<?php 
$content = ob_get_clean();
require_once '../../includes/layout.php';
?>