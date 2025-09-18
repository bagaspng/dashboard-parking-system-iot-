<?php
$host = "localhost";
$user = "root";
$pass = "";     // default XAMPP
$db   = "db_parking";

$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_errno) {
  http_response_code(500);
  die("DB connection failed: " . $mysqli->connect_error);
}
$mysqli->set_charset("utf8mb4");
