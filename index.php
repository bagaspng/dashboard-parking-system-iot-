<?php /* /parking/index.php */ ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Parking Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    body { font-family: sans-serif; margin: 24px; }
    .card { border: 1px solid #ddd; border-radius: 12px; padding:16px; margin-bottom: 16px; }
    .status-pill { display:inline-block; padding:6px 12px; border-radius:999px; color:#fff; font-weight:600; }
    .pill-penuh{ background:#e02424; }
    .pill-waspada{ background:#f59e0b; }
    .pill-kosong{ background:#10b981; }
    canvas { max-width: 100%; height: 380px; }
    .muted { color:#777; font-size: 12px; }
  </style>
</head>
<body>
  <h2>📊 Parking Ultrasonic Dashboard</h2>

  <div class="card">
    <div id="last-status">Memuat status terbaru...</div>
    <div class="muted">Auto-refresh tiap 3 detik</div>
  </div>

  <div class="card">
  <h3>Kontrol TV</h3>
  <form action="tv.php" method="post">
    <button type="submit" style="padding:10px 20px;font-size:16px;">
      Toggle TV ON/OFF
    </button>
  </form>
</div>


  <div class="card">
    <h3>Grafik Jarak (cm)</h3>
    <canvas id="chart"></canvas>
  </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const elLast = document.getElementById('last-status');
const ctx = document.getElementById('chart').getContext('2d');

const chart = new Chart(ctx, {
  type: 'line',
  data: {
    labels: [],
    datasets: [{
      label: 'Jarak (cm)',
      data: [],
      tension: 0.25,
      borderWidth: 2,
      pointRadius: 2
    }]
  },
  options: {
    animation: false,
    scales: {
      x: { title: { display: true, text: 'Waktu' } },
      y: { beginAtZero: true, title: { display: true, text: 'cm' } }
    },
    plugins: {
      legend: { display: true }
    }
  }
});

function pill(status) {
  const s = status.toUpperCase();
  if (s === 'COlUSSION') return '<span class="status-pill pill-penuh">PENUH (<=10cm)</span>';
  if (s === 'WASPADA') return '<span class="status-pill pill-waspada">WASPADA (15–20cm)</span>';
  return '<span class="status-pill pill-kosong">KOSONG (>20cm)</span>';
}

async function refresh() {
  try {
    const r = await fetch('data.php?limit=50', { cache: 'no-store' });
    const data = await r.json();

    const labels = data.map(d => new Date(d.waktu).toLocaleTimeString());
    const values = data.map(d => parseFloat(d.jarak));

    chart.data.labels = labels;
    chart.data.datasets[0].data = values;
    chart.update();

    if (data.length) {
      const last = data[data.length - 1];
      elLast.innerHTML = `
        Status terbaru: ${pill(last.status)} &nbsp; | &nbsp;
        Jarak: <b>${parseFloat(last.jarak).toFixed(2)} cm</b> &nbsp; | &nbsp;
        Waktu: <b>${new Date(last.waktu).toLocaleString()}</b>
      `;
    } else {
      elLast.textContent = 'Belum ada data.';
    }
  } catch (e) {
    elLast.textContent = 'Gagal memuat data.';
  }
}

refresh();
setInterval(refresh, 3000);
</script>
</body>
</html>
