<?php
require_once "auth.php";  // start session + RBAC helpers
require_once "db.php";
require_login();
$role = user_role();
$username = $_SESSION['user']['username'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Parking Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    body { font-family: sans-serif; margin: 24px; }
    header { display:flex; justify-content: space-between; align-items:center; margin-bottom: 12px; }
    .muted { color:#777; font-size: 12px; }
    .card { border: 1px solid #ddd; border-radius: 12px; padding:16px; margin-bottom: 16px; }
    .status-pill { display:inline-block; padding:6px 12px; border-radius:999px; color:#fff; font-weight:600; }
    .pill-penuh{ background:#e02424; }
    .pill-waspada{ background:#f59e0b; }
    .pill-kosong{ background:#10b981; }
    canvas { max-width: 100%; height: 360px; }
    button { padding:10px 16px; font-size:14px; border-radius:8px; border:1px solid #ccc; background:#fff; cursor:pointer; }
    button:hover { background:#f6f6f6; }
  </style>
</head>
<body>
  <header>
    <div>
      <h2>📊 Parking Ultrasonic Dashboard</h2>
      <div class="muted">Login sebagai: <b><?=htmlspecialchars($username)?></b> (<?=htmlspecialchars($role)?>)</div>
    </div>
    <div><a href="logout.php">Logout</a></div>
  </header>

  <?php if ($role==='superadmin' || $role==='admin_jarak1'): ?>
  <div class="card">
    <h3>Status Terbaru — <code>jarak1</code></h3>
    <div id="last-status-jarak1" class="muted">Memuat...</div>
    <div class="muted">Auto-refresh tiap 3 detik</div>
  </div>

  <div class="card">
    <h3>Grafik Jarak (cm) — jarak1</h3>
    <canvas id="chart-jarak1"></canvas>
  </div>
  <?php endif; ?>

  <?php if ($role==='superadmin' || $role==='admin_jarak2'): ?>
  <div class="card">
    <h3>Status Terbaru — <code>jarak2</code></h3>
    <div id="last-status-jarak2" class="muted">Memuat...</div>
    <div class="muted">Auto-refresh tiap 3 detik</div>
  </div>

  <div class="card">
    <h3>Grafik Jarak (cm) — jarak2</h3>
    <canvas id="chart-jarak2"></canvas>
  </div>
  <?php endif; ?>

  <?php if ($role==='superadmin'): ?>
  <div class="card">
    <h3>Kontrol TV</h3>
    <form action="tv_toggle.php" method="post" style="margin-bottom:8px;">
      <button type="submit">Toggle TV ON/OFF</button>
    </form>
    <div id="tvStat" class="muted">Status TV: memuat...</div>
  </div>
  <?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function pill(status) {
  const s = (status || '').toUpperCase();
  if (s === 'PENUH')   return '<span class="status-pill pill-penuh">PENUH (≤10 cm)</span>';
  if (s === 'WASPADA') return '<span class="status-pill pill-waspada">WASPADA (15–20 cm)</span>';
  return '<span class="status-pill pill-kosong">KOSONG (>20 cm)</span>';
}

function makeChart(canvasId) {
  const ctx = document.getElementById(canvasId).getContext('2d');
  return new Chart(ctx, {
    type: 'line',
    data: { labels: [], datasets: [{ label: 'Jarak (cm)', data: [], tension: 0.25, borderWidth: 2, pointRadius: 2 }] },
    options: {
      animation: false,
      scales: {
        x: { title: { display: true, text: 'Waktu' } },
        y: { beginAtZero: true, title: { display: true, text: 'cm' } }
      },
      plugins: { legend: { display: true } }
    }
  });
}

async function fetchData(device, limit=50) {
  const r = await fetch(`data.php?device=${device}&limit=${limit}`, { cache: 'no-store' });
  if (!r.ok) throw new Error('HTTP '+r.status);
  return r.json();
}

async function refreshOne(device, chart, lastElId) {
  try {
    const data = await fetchData(device, 50);
    const labels = data.map(d => new Date(d.waktu).toLocaleTimeString());
    const values = data.map(d => parseFloat(d.jarak));
    chart.data.labels = labels;
    chart.data.datasets[0].data = values;
    chart.update();

    const lastEl = document.getElementById(lastElId);
    if (data.length) {
      const last = data[data.length - 1];
      lastEl.innerHTML = `
        Status: ${pill(last.status)} &nbsp; | &nbsp;
        Jarak: <b>${parseFloat(last.jarak).toFixed(2)} cm</b> &nbsp; | &nbsp;
        Waktu: <b>${new Date(last.waktu).toLocaleString()}</b>
      `;
    } else {
      lastEl.textContent = 'Belum ada data.';
    }
  } catch (e) {
    const lastEl = document.getElementById(lastElId);
    if (lastEl) lastEl.textContent = 'Gagal memuat data.';
    console.error(e);
  }
}

<?php if ($role==='superadmin' || $role==='admin_jarak1'): ?>
const chart1 = makeChart('chart-jarak1');
refreshOne('jarak1', chart1, 'last-status-jarak1');
setInterval(() => refreshOne('jarak1', chart1, 'last-status-jarak1'), 3000);
<?php endif; ?>

<?php if ($role==='superadmin' || $role==='admin_jarak2'): ?>
const chart2 = makeChart('chart-jarak2');
refreshOne('jarak2', chart2, 'last-status-jarak2');
setInterval(() => refreshOne('jarak2', chart2, 'last-status-jarak2'), 3000);
<?php endif; ?>

<?php if ($role==='superadmin'): ?>
async function refreshTV(){
  try{
    const r = await fetch('tv_status.php', { cache: 'no-store' });
    const j = await r.json();
    document.getElementById('tvStat').textContent = 'Status TV: ' + (j?.status || '-');
  }catch(e){
    document.getElementById('tvStat').textContent = 'Status TV: gagal memuat';
  }
}
refreshTV();
setInterval(refreshTV, 5000);
<?php endif; ?>
</script>
</body>
</html>
