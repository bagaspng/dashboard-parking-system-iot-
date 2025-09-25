<?php
require_once "auth.php";  // start session + fungsi RBAC
require_once "db.php";

require_login();

// hanya superadmin yang boleh lihat status TV
if (user_role() !== 'superadmin') {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'forbidden']);
    exit;
}

$res = $mysqli->query("SELECT status FROM tv_control ORDER BY id DESC LIMIT 1");
$row = $res ? $res->fetch_assoc() : null;

header('Content-Type: application/json');
if ($row) {
    echo json_encode(['status' => $row['status']]);
} else {
    echo json_encode(['status' => 'UNKNOWN']);
}
