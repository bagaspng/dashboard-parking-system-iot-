<?php
require_once "db.php";
$res = $mysqli->query("SELECT status FROM tv_control ORDER BY id DESC LIMIT 1");
$row = $res->fetch_assoc();
header('Content-Type: application/json');
echo json_encode($row);
