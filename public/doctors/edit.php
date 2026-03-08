<?php
require_once __DIR__ . "/../../includes/config.php";
require_once __DIR__ . "/../../includes/auth.php";

$title  = "Edit Doctor";
$active = "doctors";

/* ADMIN ONLY */
if (($_SESSION['user']['role'] ?? '') !== 'admin') {
    header("Location: /clinic_db/public/dashboard.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: /clinic_db/public/doctors/index.php");
    exit;
}

/* FETCH DOCTOR + USER */
$stmt = $pdo->prepare("
    SELECT 
        d.id,
        d.phone,
        d.is_available,
        u.id AS user_id,
        u.full_name,
        u.username,
        u.password
    FROM doctors d
    INNER JOIN users u ON u.id = d.user_id
    WHERE d.id = :id
");
$stmt->execute([':id' => $id]);
$doctor = $stmt->fetch();

if (!$doctor) {
    header("Location: /clinic_db/public/doctors/index.php");
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $full_name    = trim($_POST['full_name'] ?? '');
    $username     = trim($_POST['username'] ?? '');
    $password     = trim($_POST['password'] ?? '');
    $phone        = trim($_POST['phone'] ?? '');
    $is_available = isset($_POST['is_available']) ? 1 : 0;

    if ($full_name === '') {
        $errors[] = "Full Name is required.";
    }

    if ($username === '') {
        $errors[] = "Username is required.";
    }

    /* CHECK USERNAME DUPLICATE */
    $checkUser = $pdo->prepare("
        SELECT id FROM users 
        WHERE username = :username 
        AND id != :user_id
    ");
    $checkUser->execute([
        ':username' => $username,
        ':user_id'  => $doctor['user_id']
    ]);

    if ($checkUser->fetch()) {
        $errors[] = "Username already exists.";
    }

    if (!$errors) {

        $pdo->beginTransaction();

        try {

            /* UPDATE USERS TABLE (NO HASHING) */
            if ($password !== '') {
                $stmtUser = $pdo->prepare("
                    UPDATE users
                    SET full_name = :full_name,
                        username  = :username,
                        password  = :password
                    WHERE id = :user_id
                ");

                $stmtUser->execute([
                    ':full_name' => $full_name,
                    ':username'  => $username,
                    ':password'  => $password, // plain text
                    ':user_id'   => $doctor['user_id']
                ]);
            } else {
                $stmtUser = $pdo->prepare("
                    UPDATE users
                    SET full_name = :full_name,
                        username  = :username
                    WHERE id = :user_id
                ");

                $stmtUser->execute([
                    ':full_name' => $full_name,
                    ':username'  => $username,
                    ':user_id'   => $doctor['user_id']
                ]);
            }

            /* UPDATE DOCTORS TABLE */
            $stmtDoctor = $pdo->prepare("
                UPDATE doctors
                SET phone = :phone,
                    is_available = :is_available
                WHERE id = :id
            ");

            $stmtDoctor->execute([
                ':phone'        => $phone ?: null,
                ':is_available' => $is_available,
                ':id'           => $id
            ]);

            $pdo->commit();

            header("Location: /clinic_db/public/doctors/index.php");
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "Something went wrong. Please try again.";
        }
    }
}

ob_start();
?>

<div class="page-header">
    <div class="page-title">
        <h2>Edit Doctor</h2>
    </div>

    <div class="page-action">
        <a href="/clinic_db/public/doctors/index.php" class="btn btn-primary">
            Back
        </a>
    </div>
</div>

<div class="card">
    <h3>Doctor Account & Details</h3>
    <p class="sub">Update the information below.</p>

    <?php if ($errors): ?>
        <div class="card" style="border:1px solid #dc3545;background:#ffeaea;margin-bottom:15px;">
            <b style="color:#dc3545;">Please fix:</b>
            <ul style="margin:10px 0 0 18px;">
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" class="form-wrapper">

        <div class="form-row">
            <label>Full Name *</label>
            <input type="text" name="full_name"
                value="<?= htmlspecialchars($_POST['full_name'] ?? $doctor['full_name']) ?>"
                required>
        </div>

        <div class="form-row">
            <label>Username *</label>
            <input type="text" name="username"
                value="<?= htmlspecialchars($_POST['username'] ?? $doctor['username']) ?>"
                required>
        </div>

        <div class="form-row">
            <label>New Password (leave blank if no change)</label>

            <div style="position:relative;">
                <input type="password" name="password" id="passwordField"
                       style="width:100%; padding-right:35px;">

                <span id="togglePassword"
                      style="position:absolute; right:10px; top:50%; transform:translateY(-50%);
                             cursor:pointer; font-size:14px;">
                    👁
                </span>
            </div>
        </div>

        <div class="form-row">
            <label>Phone</label>
            <input type="text" name="phone"
                value="<?= htmlspecialchars($_POST['phone'] ?? ($doctor['phone'] ?? '')) ?>">
        </div>

        <div class="form-row">
            <label>
                <input type="checkbox" name="is_available"
                <?= ($doctor['is_available'] ?? 1) ? 'checked' : '' ?>>
                Available for Booking
            </label>
        </div>

        <div class="actions">
            <button type="submit" class="btn-save">
                Update Doctor
            </button>
        </div>

    </form>
</div>

<script>
document.getElementById("togglePassword").addEventListener("click", function() {
    const input = document.getElementById("passwordField");
    if (input.type === "password") {
        input.type = "text";
        this.textContent = "🙈";
    } else {
        input.type = "password";
        this.textContent = "👁";
    }
});
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../../includes/layout.php";
?>