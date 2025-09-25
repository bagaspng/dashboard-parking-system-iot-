<?php
// Konfigurasi database
$host = "localhost";
$user = "root";
$pass = "";               // default XAMPP kosong
$db   = "db_parking";

// Buat koneksi
$mysqli = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($mysqli->connect_errno) {
    http_response_code(500);
    die("Database connection failed: " . $mysqli->connect_error);
}

// Set charset aman
$mysqli->set_charset("utf8mb4");

// Mulai session sekali di sini
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Optional: aktifkan error reporting saat development
// mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
