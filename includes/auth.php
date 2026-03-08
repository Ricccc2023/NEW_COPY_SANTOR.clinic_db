<?php
// includes/auth.php
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

if (!isset($_SESSION['user'])) {
  header("Location: /clinic_db/public/login.php");
  exit;
}

function require_role(array $roles): void {
  $role = (string)($_SESSION['user']['role'] ?? '');
  if (!in_array($role, $roles, true)) {
    http_response_code(403);
    echo "Forbidden";
    exit;
  }
}
