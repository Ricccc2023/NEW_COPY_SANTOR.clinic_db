<?php
require_once __DIR__ . "/../../includes/config.php";
require_once __DIR__ . "/../../includes/auth.php";

require_role(['staff','admin']); // staff allowed

$title = "Staff Dashboard";
$active = "dashboard";

require_once __DIR__ . "/../../includes/header.php";
require_once __DIR__ . "/../../includes/sidebar.php";
?>

<div class="main">
  <div class="topbar">
    <div>
      <div style="font-weight:800">Staff Dashboard</div>
      <div style="color:var(--muted);font-size:13px">Doctor/Staff access</div>
    </div>
  </div>

  <div class="page">
    <div class="card">
      <h2>Welcome</h2>
      <p class="sub">
        Logged in as <b><?= htmlspecialchars($_SESSION['user']['name'] ?? 'Staff') ?></b>
      </p>
      <p>This dashboard is for staff only.</p>
    </div>
  </div>
</div>

<?php require_once __DIR__ . "/../../includes/footer.php"; ?>
