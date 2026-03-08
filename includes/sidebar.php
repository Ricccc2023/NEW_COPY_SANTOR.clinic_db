<?php
define('BASE_URL', '/clinic_db/public');

$role = $_SESSION['user']['role'] ?? '';
?>

<div class="sidebar">
  <div class="nav">

    <!-- DASHBOARD (ADMIN ONLY) -->
    <?php if ($role === 'admin'): ?>
      <a href="<?= BASE_URL ?>/dashboard.php"
         class="<?= ($active ?? '') === 'dashboard' ? 'active' : '' ?>">
        Dashboard
      </a>
    <?php endif; ?>


    <!-- DOCTOR ROLE MENU -->
    <?php if ($role === 'doctor'): ?>

      <a href="<?= BASE_URL ?>/doctors/index.php"
         class="<?= ($active ?? '') === 'doctors' ? 'active' : '' ?>">
        Doctors
      </a>

      <a href="<?= BASE_URL ?>/patients/index.php"
         class="<?= ($active ?? '') === 'patients' ? 'active' : '' ?>">
        Patients
      </a>

      <a href="<?= BASE_URL ?>/appointments/index.php"
         class="<?= ($active ?? '') === 'appointments' ? 'active' : '' ?>">
        Appointments
      </a>

      <a href="<?= BASE_URL ?>/tests/index.php"
         class="<?= ($active ?? '') === 'tests' ? 'active' : '' ?>">
        Laboratory
      </a>

    <?php endif; ?>


    <!-- STAFF ROLE MENU -->
    <?php if ($role === 'staff'): ?>

      <a href="<?= BASE_URL ?>/appointments/index.php"
         class="<?= ($active ?? '') === 'appointments' ? 'active' : '' ?>">
        Appointments
      </a>

      <a href="<?= BASE_URL ?>/billing/index.php"
         class="<?= ($active ?? '') === 'billing' ? 'active' : '' ?>">
        Billing
      </a>

      <a href="<?= BASE_URL ?>/payroll/index.php"
         class="<?= ($active ?? '') === 'payroll' ? 'active' : '' ?>">
        Payroll
      </a>

    <?php endif; ?>


    <!-- ADMIN FULL MENU -->
    <?php if ($role === 'admin'): ?>

      <a href="<?= BASE_URL ?>/doctors/index.php"
         class="<?= ($active ?? '') === 'doctors' ? 'active' : '' ?>">
        Doctors
      </a>

      <a href="<?= BASE_URL ?>/patients/index.php"
         class="<?= ($active ?? '') === 'patients' ? 'active' : '' ?>">
        Patients
      </a>

      <a href="<?= BASE_URL ?>/tests/index.php"
         class="<?= ($active ?? '') === 'tests' ? 'active' : '' ?>">
        Laboratory
      </a>

      <a href="<?= BASE_URL ?>/billing/index.php"
         class="<?= ($active ?? '') === 'billing' ? 'active' : '' ?>">
        Billing
      </a>

      <a href="<?= BASE_URL ?>/appointments/index.php"
         class="<?= ($active ?? '') === 'appointments' ? 'active' : '' ?>">
        Appointments
      </a>

      <a href="<?= BASE_URL ?>/payroll/index.php"
         class="<?= ($active ?? '') === 'payroll' ? 'active' : '' ?>">
        Payroll
      </a>

    <?php endif; ?>

  </div>

  <div class="nav power">
    <a href="<?= BASE_URL ?>/logout.php" class="logout-btn">
      Logout
    </a>
  </div>
</div>