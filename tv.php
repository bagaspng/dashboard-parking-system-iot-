<?php
require_once "db.php";

$res = $mysqli->query("SELECT * FROM tv_control ORDER BY id DESC LIMIT 1");
$row = $res->fetch_assoc();
$current = $row ? $row['status'] : 'OFF';

$newStatus = ($current === 'ON') ? 'OFF' : 'ON';
$mysqli->query("INSERT INTO tv_control (status) VALUES ('$newStatus')");

header("Location: index.php");
exit;
