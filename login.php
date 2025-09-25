<?php
require_once "db.php";

$err = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $u = trim($_POST['username'] ?? '');
  $p = $_POST['password'] ?? '';

  $stmt = $mysqli->prepare("SELECT id, username, pass_hash, role FROM users WHERE username=?");
  $stmt->bind_param('s', $u);
  $stmt->execute();
  $res = $stmt->get_result();
  $user = $res->fetch_assoc();

  if ($user && password_verify($p, $user['pass_hash'])) {
    $_SESSION['user'] = [
      'id'       => $user['id'],
      'username' => $user['username'],
      'role'     => $user['role']
    ];

    // catat jejak login
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $stmt2 = $mysqli->prepare("UPDATE users SET last_login_ts=NOW(), last_login_ip=? WHERE id=?");
    $stmt2->bind_param('si', $ip, $user['id']);
    $stmt2->execute();

    $stmt3 = $mysqli->prepare("INSERT INTO login_audit (user_id, ip_addr, user_agent) VALUES (?, ?, ?)");
    $stmt3->bind_param('iss', $user['id'], $ip, $ua);
    $stmt3->execute();

    header("Location: index.php"); exit;
  } else {
    $err = "Username atau password salah.";
  }
}
?>
<!doctype html>
<html lang="id"><head>
<meta charset="utf-8"><title>Login Dashboard</title>
<style>
body{font-family:sans-serif;display:flex;min-height:100vh;align-items:center;justify-content:center;background:#fafafa}
form{background:#fff;border:1px solid #ddd;border-radius:12px;padding:20px;min-width:320px}
input,button{width:100%;padding:10px;margin:6px 0;font-size:14px}
.err{color:#c00;margin-bottom:8px}
</style></head><body>
<form method="post">
  <h3 style="margin-top:0">Login Dashboard</h3>
  <?php if($err): ?><div class="err"><?=htmlspecialchars($err)?></div><?php endif; ?>
  <input name="username" placeholder="username" required>
  <input type="password" name="password" placeholder="password" required>
  <button>Masuk</button>
</form>
</body></html>
