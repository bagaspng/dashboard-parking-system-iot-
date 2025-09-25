<?php
require_once "db.php";

// Wajib login
function require_login() {
  if (empty($_SESSION['user'])) {
    header("Location: login.php");
    exit;
  }
}

function user_role() {
  return $_SESSION['user']['role'] ?? null;
}

function can_view_device($device) {
  $r = user_role();
  if ($r === 'superadmin') return true;
  if ($r === 'admin_jarak1' && $device === 'jarak1') return true;
  if ($r === 'admin_jarak2' && $device === 'jarak2') return true;
  return false;
}

// Wajib role tertentu
function require_role($roles = []) {
  if (!in_array(user_role(), $roles, true)) {
    http_response_code(403);
    exit("Forbidden");
  }
}
