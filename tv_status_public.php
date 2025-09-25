<?php
require_once "db.php";
$res = $mysqli->query("SELECT status FROM tv_control ORDER BY id DESC LIMIT 1");
$row = $res ? $res->fetch_assoc() : null;

header('Content-Type: application/json');
echo json_encode(['status' => $row['status'] ?? 'OFF']);
