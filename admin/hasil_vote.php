<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$organisasi_id = (int)$_SESSION['organisasi_id'];

// 🔹 Query kandidat & suara
$query = "
    SELECT k.nama, COUNT(v.id) AS jumlah_suara 
    FROM kandidat k 
    LEFT JOIN vote v ON k.id = v.kandidat_id AND v.organisasi_id = $organisasi_id
    WHERE k.organisasi_id = $organisasi_id 
    GROUP BY k.id
    ORDER BY jumlah_suara DESC
";
$result = mysqli_query($conn, $query);

// 🔹 Query suara tidak sah
$query_invalid = "
    SELECT COUNT(v.id) AS jumlah_tidak_sah 
    FROM vote v 
    LEFT JOIN kandidat k ON v.kandidat_id = k.id 
    WHERE v.organisasi_id = $organisasi_id
      AND (v.kandidat_id IS NULL OR k.organisasi_id != $organisasi_id)
";
$result_invalid = mysqli_query($conn, $query_invalid);
$invalid_vote = mysqli_fetch_assoc($result_invalid)['jumlah_tidak_sah'] ?? 0;

$labels = [];
$data = [];
$backgroundColors = [];

$colorPalette = [
    '#FFD500', '#E6A300', '#FFD500', '#E6A300',
    '#FFD500', '#E6A300', '#FFD500', '#E6A300'
];

$i = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $labels[] = $row['nama'];
    $data[] = (int)$row['jumlah_suara'];
    $backgroundColors[] = $colorPalette[$i % count($colorPalette)];
    $i++;
}

if ($invalid_vote > 0) {
    $labels[] = 'Suara Tidak Sah';
    $data[] = (int)$invalid_vote;
    $backgroundColors[] = '#777';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Hasil Voting - IPM</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    * { box-sizing: border-box; }
    html, body { height: 100%; }

    body {
        margin: 0;
        font-family: "Trebuchet MS", Helvetica, sans-serif;
        background-color: #fffdf3;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    /* HEADER */
    header {
        background: linear-gradient(135deg, #FFD500, #E6A300);
        padding: 15px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    header img {
        height: 38px;
        width: auto;
        object-fit: contain;
        border-radius: 0 !important;
    }

    header h2 {
        margin: 0;
        color: #333;
        font-size: 22px;
        font-weight: bold;
    }

    .menu-toggle {
        display: none;
        font-size: 26px;
        cursor: pointer;
    }

    nav {
        display: flex;
        gap: 20px;
    }

    nav a {
        text-decoration: none;
        color: #333;
        font-weight: bold;
        transition: 0.3s;
    }

    nav a:hover {
        text-decoration: underline;
        color: #000;
    }

    nav a.active {
        color: #006400;
    }

    @media (max-width: 768px) {
        .menu-toggle { display: block; }
        nav {
            display: none;
            flex-direction: column;
            background-color: #FFD500;
            position: absolute;
            top: 60px;
            right: 0;
            width: 40%;
            padding: 15px;
            border-radius: 0 0 10px 10px;
            box-shadow: -3px 5px 10px rgba(0,0,0,0.1);
        }
        nav.active { display: flex; }
    }

    /* CONTAINER */
    .container {
        flex: 1;
        max-width: 900px;
        margin: 40px auto;
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    }

    h2 {
        text-align: center;
        color: #E6A300;
        margin-bottom: 25px;
    }

    .chart-container {
        position: relative;
        width: 100%;
        height: 400px;
    }

    canvas {
        width: 100% !important;
        height: auto !important;
    }

    /* TABLE */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 30px;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }

    th, td {
        padding: 12px;
        text-align: center;
        border-bottom: 1px solid #eee;
        font-size: 15px;
    }

    th {
        background: #FFD500;
        color: #333;
    }

    tr:nth-child(even) {
        background: #fafafa;
    }

    .back-btn {
        display: inline-block;
        text-decoration: none;
        color: #E6A300;
        font-weight: bold;
        margin-top: 25px;
        transition: 0.2s;
    }

    .back-btn:hover {
        text-decoration: underline;
    }

    footer {
        text-align: center;
        padding: 20px;
        color: #777;
        background: #f9f9f9;
        border-top: 1px solid #eee;
        font-size: 14px;
        margin-top: auto;
    }
</style>
</head>
<body>

<header>
    <div class="left">
        <img src="https://ipm.or.id/wp-content/uploads/2018/11/Logo-Ikatan-Pelajar-Muhammadiyah-Resmi.png" alt="Logo IPM">
        <h2>Hasil Voting</h2>
    </div>
    <div class="menu-toggle" onclick="toggleMenu()">☰</div>
    <nav id="navMenu">
        <a href="dashboard.php">Dashboard</a>
        <a href="organisasi.php">Organisasi</a>
        <a href="kelola_kandidat.php">Kandidat</a>
        <a href="kelola_pemilih.php">Pemilih</a>
        <a href="hasil_vote.php" class="active">Hasil Vote</a>
        <a href="logout.php" onclick="return confirm('Yakin ingin keluar?')">Logout</a>
    </nav>
</header>

<div class="container">
    <h2>Diagram Hasil Voting</h2>
    <div class="chart-container">
        <canvas id="voteChart"></canvas>
    </div>

    <h2>Rekapitulasi Suara</h2>
    <table>
        <tr>
            <th>Nama Kandidat</th>
            <th>Jumlah Suara</th>
        </tr>
        <?php
        mysqli_data_seek($result, 0);
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr><td>" . htmlspecialchars($row['nama']) . "</td><td>" . (int)$row['jumlah_suara'] . "</td></tr>";
        }
        if ($invalid_vote > 0) {
            echo "<tr><td><em>Suara Tidak Sah</em></td><td><strong>$invalid_vote</strong></td></tr>";
        }
        ?>
    </table>

    <a href="dashboard.php" class="back-btn">← Kembali ke Dashboard</a>
</div>

<footer>&copy; <?= date('Y'); ?> E-Voting IPM | Dikelola Oleh Bidang Teknologi Informasi</footer>

<script>
const ctx = document.getElementById('voteChart').getContext('2d');

// 🎨 Bikin gradasi dinamis untuk setiap batang
const gradients = [];
const labels = <?= json_encode($labels); ?>;
for (let i = 0; i < labels.length; i++) {
    const gradient = ctx.createLinearGradient(0, 0, 600, 0);
    gradient.addColorStop(0, '#D67E00');   // Oranye tua
    gradient.addColorStop(1, '#FFD500');   // Kuning IPM
    gradients.push(gradient);
}

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Jumlah Suara',
            data: <?= json_encode($data); ?>,
            backgroundColor: gradients,
            borderRadius: 8, // sudut batang agak melengkung
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        scales: {
            x: { beginAtZero: true },
            y: { ticks: { autoSkip: false } }
        },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(0,0,0,0.7)',
                titleColor: '#fff',
                bodyColor: '#fff'
            }
        }
    }
});

function toggleMenu() {
    document.getElementById('navMenu').classList.toggle('active');
}
</script>
</body>
</html>