<?php
require_once __DIR__ . "/../../includes/config.php";
require_once __DIR__ . "/../../includes/auth.php";

require_role(['admin']);

$title  = "Add Accounts";
$active = "staff";

$errors = [];

function usernameExists($pdo, $username) {
    $chk = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :u");
    $chk->execute([':u' => $username]);
    return (int)$chk->fetchColumn() > 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $type      = $_POST['type'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $username  = trim($_POST['username'] ?? '');
    $password  = trim($_POST['password'] ?? '');

    // ================= VALIDATION =================
    if (!in_array($type, ['doctor', 'staff'])) {
        $errors[] = "Invalid account type.";
    }

    if ($full_name === '') $errors[] = "Full name is required.";
    if ($username === '')  $errors[] = "Username is required.";
    if ($password === '')  $errors[] = "Password is required.";

    if ($username !== '' && !preg_match('/^[a-zA-Z0-9_.-]{4,30}$/', $username)) {
        $errors[] = "Username must be 4-30 characters only.";
    }

    if (!$errors && usernameExists($pdo, $username)) {
        $errors[] = "Username already taken.";
    }

    // ================= INSERT =================
    if (!$errors) {
        try {

            $pdo->beginTransaction();

            // 🔐 HASH PASSWORD (VERY IMPORTANT — don’t store plain text)
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // 1️⃣ Insert into users table
            $ustmt = $pdo->prepare("
                INSERT INTO users (full_name, username, password, role)
                VALUES (:full_name, :username, :password, :role)
            ");

            $ustmt->execute([
                ':full_name' => $full_name,
                ':username'  => $username,
                ':password'  => $hashedPassword,
                ':role'      => $type   // doctor OR staff
            ]);

            $userId = $pdo->lastInsertId();

            // 2️⃣ ALWAYS insert into doctors table (for BOTH roles)
            $dstmt = $pdo->prepare("
                INSERT INTO doctors (full_name, phone, user_id)
                VALUES (:full_name, :phone, :user_id)
            ");

            $dstmt->execute([
                ':full_name' => $full_name,
                ':phone'     => $phone !== '' ? $phone : null,
                ':user_id'   => $userId
            ]);

            $pdo->commit();

            header("Location: index.php?success=1");
            exit;

        } catch (PDOException $e) {
            $pdo->rollBack();
            die("Database Error: " . $e->getMessage());
        }
    }
}

ob_start();
?>

<div class="page-header">
    <div class="page-title">
        <h2>Add Accounts</h2>
        <p class="sub">Create Doctor or Staff Login</p>
    </div>
</div>

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


<!-- ================= DOCTOR CARD ================= -->
<form method="post">
<input type="hidden" name="type" value="doctor">

<div class="card">
    <h3>Doctor Account</h3>
    <p class="sub">Create doctor with login access.</p>

    <div class="form-row">
        <label>Full Name *</label>
        <input type="text" name="full_name" required>
    </div>

    <div class="form-row">
        <label>Phone</label>
        <input type="text" name="phone" placeholder="09xx...">
    </div>

    <div class="form-row">
        <label>Username *</label>
        <input type="text" name="username" required>
    </div>

    <div class="form-row">
        <label>Password *</label>
        <input type="password" name="password" required>
    </div>

    <button class="btn-save" type="submit">
        Save Doctor
    </button>
</div>
</form>



<!-- ================= STAFF CARD ================= -->
<form method="post">
<input type="hidden" name="type" value="staff">

<div class="card" style="margin-top:20px;">
    <h3>Staff Account</h3>
    <p class="sub">Create regular staff login.</p>

    <div class="form-row">
        <label>Full Name *</label>
        <input type="text" name="full_name" required>
    </div>

    <div class="form-row">
        <label>Phone</label>
        <input type="text" name="phone" placeholder="09xx...">
    </div>

    <div class="form-row">
        <label>Username *</label>
        <input type="text" name="username" required>
    </div>

    <div class="form-row">
        <label>Password *</label>
        <input type="password" name="password" required>
    </div>

    <button class="btn-save" type="submit">
        Save Staff
    </button>
</div>
</form>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../../includes/layout.php";
?>