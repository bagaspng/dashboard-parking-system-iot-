<?php
// POST: device, token, status, jarak
require_once "db.php";

// mapping token untuk tiap device (bisa simpan di table juga kalau mau)
$TOKENS = [
    'jarak1' => 'tokensecret1',
    'jarak2' => 'tokensecret2'
];

$device = $_POST['device'] ?? '';
$token  = $_POST['token']  ?? '';
$status = isset($_POST['status']) ? trim($_POST['status']) : '';
$jarak  = isset($_POST['jarak'])  ? floatval($_POST['jarak']) : null;

// validasi dasar
if ($status === '' || $jarak === null) {
    http_response_code(400);
    echo "Bad Request";
    exit;
}
if (!isset($TOKENS[$device]) || $token !== $TOKENS[$device]) {
    http_response_code(401);
    echo "Unauthorized";
    exit;
}

// simpan ke DB
$stmt = $mysqli->prepare("INSERT INTO parking_log (device, status, jarak) VALUES (?, ?, ?)");
$stmt->bind_param('ssd', $device, $status, $jarak);

if ($stmt->execute()) {
    echo "OK";
} else {
    http_response_code(500);
    echo "Insert failed: " . $stmt->error;
}
$stmt->close();
