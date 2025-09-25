<?php
require_once "auth.php";  // start session + fungsi RBAC
require_once "db.php";

require_login();

// hanya superadmin yang boleh toggle TV
if (user_role() !== 'superadmin') {
    http_response_code(403);
    exit("Forbidden");
}

// ambil status terakhir
$res = $mysqli->query("SELECT status FROM tv_control ORDER BY id DESC LIMIT 1");
$row = $res ? $res->fetch_assoc() : null;
$current = $row ? $row['status'] : 'OFF';

// tentukan status baru
$newStatus = ($current === 'ON') ? 'OFF' : 'ON';

// simpan ke tabel
$stmt = $mysqli->prepare("INSERT INTO tv_control (status) VALUES (?)");
$stmt->bind_param('s', $newStatus);
$stmt->execute();
$stmt->close();

// redirect balik ke dashboard
header("Location: index.php");
exit;
