<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$nama_admin = $_SESSION['admin_nama'] ?? 'Admin';
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Admin IPM</title>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Trebuchet MS", Helvetica, sans-serif;
    }

    body {
        background-color: #fffdf3;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* Header */
    header {
        background: linear-gradient(135deg, #FFD500, #E6A300);
        color: #333;
        padding: 15px 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    header img {
        width: 45px;
        height: 45px;
    }

    header h1 {
        font-size: 22px;
        font-weight: bold;
    }

    header .logout-btn {
        background: #333;
        color: #fff;
        padding: 8px 16px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        transition: background 0.3s;
    }

    header .logout-btn:hover {
        background: #555;
    }

    /* Container */
    .container {
        flex: 1;
        max-width: 1000px;
        margin: 40px auto;
        background: #ffffff;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        text-align: center;
    }

    h2 {
        color: #333;
        margin-bottom: 10px;
        font-size: 24px;
    }

    p {
        color: #555;
        margin-bottom: 25px;
    }

    /* Menu */
    .menu {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 25px;
    }

    .card {
        background: linear-gradient(135deg, #FFD500, #E6A300);
        width: 200px;
        padding: 25px 20px;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        text-align: center;
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .card a {
        text-decoration: none;
        color: #333;
        font-weight: bold;
        font-size: 15px;
        display: block;
    }

    .card a:hover {
        color: #000;
    }

    footer {
        text-align: center;
        padding: 20px;
        color: #777;
        background: #f9f9f9;
        border-top: 1px solid #eee;
        font-size: 14px;
    }

    /* Responsif */
    @media (max-width: 768px) {
        header h1 {
            font-size: 18px;
        }

        .menu {
            gap: 15px;
        }

        .card {
            width: 45%;
            padding: 20px;
        }
    }

    @media (max-width: 480px) {
        .card {
            width: 90%;
        }
    }
</style>
</head>
<body>

    <header>
        <div style="display:flex; align-items:center; gap:10px;">
            <img src="https://ipm.or.id/wp-content/uploads/2018/11/Logo-Ikatan-Pelajar-Muhammadiyah-Resmi.png" alt="Logo IPM">
            <h1>Dashboard Admin</h1>
        </div>
        <a href="logout.php" class="logout-btn">Logout</a>
    </header>

    <div class="container">
        <h2>Halo, <?php echo htmlspecialchars($nama_admin); ?> 👋</h2>
        <p>Selamat datang di sistem e-voting. Silakan pilih menu yang ingin Anda kelola:</p>

        <div class="menu">
            <div class="card"><a href="organisasi.php">📋 Data Organisasi</a></div>
            <div class="card"><a href="kelola_kandidat.php">🧑‍💼 Kelola Kandidat</a></div>
            <div class="card"><a href="kelola_pemilih.php">🗳️ Kelola Pemilih</a></div>
            <div class="card"><a href="hasil_vote.php">📊 Hasil Voting</a></div>
            <div class="card"><a href="hapus_akun.php">⚙️ Hapus Akun</a></div>
        </div>
    </div>

    <footer>
        &copy; <?php echo date('Y'); ?> E-Voting IPM | Dikelola Oleh Bidang Teknologi Informasi
    </footer>

</body>
</html>