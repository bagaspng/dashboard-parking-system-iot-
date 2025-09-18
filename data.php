<?php
require_once "db.php";

// Ambil 50 data terbaru
$limit = isset($_GET['limit']) ? max(10, intval($_GET['limit'])) : 50;
$res = $mysqli->query("SELECT status, jarak, waktu FROM parking_log ORDER BY waktu DESC LIMIT ".$limit);

$data = [];
while ($row = $res->fetch_assoc()) {
  $data[] = $row;
}
// urutkan naik berdasarkan waktu supaya grafik ke kanan
$data = array_reverse($data);

header('Content-Type: application/json');
echo json_encode($data);
