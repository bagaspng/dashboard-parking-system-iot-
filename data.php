<?php
require_once "auth.php";   // <-- ini start session + fungsi RBAC (require_login, can_view_device, user_role)
require_once "db.php";

require_login();

// --- param ---
$limit  = isset($_GET['limit']) ? intval($_GET['limit']) : 50;
$limit  = ($limit < 10) ? 10 : (($limit > 500) ? 500 : $limit); // clamp 10..500

$device = $_GET['device'] ?? '';          // 'jarak1' | 'jarak2' | 'all'
$role   = user_role();

// --- validasi device & role ---
$allowedDevices = [];
if ($role === 'superadmin') {
  if ($device === 'jarak1' || $device === 'jarak2') $allowedDevices = [$device];
  else $allowedDevices = ['jarak1','jarak2']; // 'all' atau kosong
} else {
  // admin_jarak1 hanya jarak1, admin_jarak2 hanya jarak2
  if ($role === 'admin_jarak1') $allowedDevices = ['jarak1'];
  if ($role === 'admin_jarak2') $allowedDevices = ['jarak2'];
  // Jika user minta device lain, tetap paksa ke yang allowed
  if ($device && !in_array($device, $allowedDevices, true)) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['error'=>'forbidden']);
    exit;
  }
}

// --- query ---
header('Content-Type: application/json');

try {
  if (count($allowedDevices) === 1) {
    // Single device
    $dev = $allowedDevices[0];
    $stmt = $mysqli->prepare("SELECT device, status, jarak, waktu
                              FROM parking_log
                              WHERE device=?
                              ORDER BY waktu DESC
                              LIMIT ?");
    $stmt->bind_param('si', $dev, $limit);
  } else {
    // Multi device (superadmin 'all')
    $stmt = $mysqli->prepare("SELECT device, status, jarak, waktu
                              FROM parking_log
                              ORDER BY waktu DESC
                              LIMIT ?");
    $stmt->bind_param('i', $limit);
  }

  $stmt->execute();
  $res = $stmt->get_result();

  $rows = [];
  while ($row = $res->fetch_assoc()) {
    $rows[] = $row;
  }
  // balik supaya waktu naik (kiri->kanan)
  $rows = array_reverse($rows);

  echo json_encode($rows);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['error'=>'server_error']);
}
