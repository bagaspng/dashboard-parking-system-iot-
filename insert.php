<?php
// POST: status, jarak
require_once "db.php";

$status = isset($_POST['status']) ? trim($_POST['status']) : "";
$jarak  = isset($_POST['jarak'])  ? floatval($_POST['jarak']) : null;

if ($status === "" || $jarak === null) {
  http_response_code(400);
  echo "Bad Request";
  exit;
}

$stmt = $mysqli->prepare("INSERT INTO parking_log (status, jarak) VALUES (?, ?)");
$stmt->bind_param("sd", $status, $jarak);

if ($stmt->execute()) {
  echo "OK";
} else {
  http_response_code(500);
  echo "Insert failed";
}
$stmt->close();
