<?php
require_once __DIR__ . "/../includes/config.php";

// If already logged in, go dashboard
if (isset($_SESSION['user'])) {
  header("Location: dashboard.php");
  exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = trim($_POST['password'] ?? '');

  if ($username === '' || $password === '') {
    $error = "Username and password are required.";
  } else {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch();

    //This version compares plain text password (no hashing)
    if ($user && $password === $user['password']) {
      $_SESSION['user'] = [
        'id'   => (int)$user['id'],
        'name' => $user['full_name'],
        'role' => $user['role'],
      ];
      $role = $_SESSION['user']['role'] ?? '';
    if ($role === 'staff') {
      header("Location: /clinic_db/public/dashboard.php");
    } else {
      header("Location: /clinic_db/public/dashboard.php");
    }
    exit;

      exit;
    } else {
      $error = "Invalid login credentials.";
    }
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Clinic Login</title>
  <link rel="stylesheet" href="/clinic_db/clinic/assets/css/clinic.css">
</head>
<body>

<!-- ===== SYSTEM BANNER (ADDED ONLY) ===== -->
<div class="topbar" style="text-align:center;">
    <div style="font-weight:700;">
        The New Santor Clinic and Diagnostic Center Web-Based Management System
    </div>
</div>
<!-- ======================================= -->

<div style="max-width:420px;margin:10vh auto;padding:0 14px;">
  <div class="card">

    <h2>Clinic Login</h2>
    <p class="sub">Please login to continue. </p>

    <?php if ($error): ?>
      <div class="card" style="border-color:rgba(197, 215, 230, 0.25); background:rgba(92, 128, 207, 0.06)">
        <b style="color:var(--danger)"><?= htmlspecialchars($error) ?></b>
      </div>
      <div style="height:10px"></div>
    <?php endif; ?>

    <form method="post">
      <div class="field">
        <label>Username</label>
        <input class="input" name="username" autocomplete="username" required>
      </div>

      <div style="height:10px"></div>

      <div class="field">
        <label>Password</label>
        <input type="password" class="input" name="password" autocomplete="current-password" required>
      </div>

      <div class="actions">
        <button class="btn-save" type="submit">Login</button>
      </div>
    </form>

  </div>
</div>
</body>
</html>
