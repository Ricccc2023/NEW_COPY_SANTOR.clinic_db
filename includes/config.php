<?php
// includes/config.php
declare(strict_types=1);

/* =========================
   CORS CONFIGURATION
========================= */
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

session_start();

/* =========================
   DATABASE CONFIG
========================= */
$DB_HOST = 'localhost';
$DB_NAME = 'clinic_db';
$DB_USER = 'root';
$DB_PASS = ''; // set your password

try {
  $pdo = new PDO(
    "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
    $DB_USER,
    $DB_PASS,
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
  );
} catch (PDOException $e) {
  http_response_code(500);
  echo "Database connection failed.";
  exit;
}

/* =========================
   TEXTBEE SMS CONFIG
========================= */
define('TEXTBEE_API_BASE', 'https://api.textbee.dev/api/v1');
define('TEXTBEE_API_KEY',  '1ec52d95-f5b0-4c22-9963-9ce3ab55e7aa');
define('TEXTBEE_DEVICE_ID','698f21fa86ec1ea031282aa7');