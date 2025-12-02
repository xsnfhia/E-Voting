<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$organisasi_id = $_SESSION['organisasi_id'];

// Hapus pemilih
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];

    // Cek apakah pemilih sudah voting
    $cekVote = mysqli_query($conn, "SELECT * FROM vote WHERE pemilih_id = $id");

    if (mysqli_num_rows($cekVote) > 0) {
        // Hapus suara dulu
        mysqli_query($conn, "DELETE FROM vote WHERE pemilih_id = $id");
        mysqli_query($conn, "DELETE FROM pemilih WHERE id = $id AND organisasi_id = $organisasi_id");
        echo "<script>alert('Pemilih beserta suaranya berhasil dihapus.'); window.location='kelola_pemilih.php';</script>";
        exit;
    } else {
        // Kalau belum voting, langsung hapus
        mysqli_query($conn, "DELETE FROM pemilih WHERE id = $id AND organisasi_id = $organisasi_id");
        echo "<script>alert('Pemilih berhasil dihapus.'); window.location='kelola_pemilih.php';</script>";
        exit;
    }
}

$query = "SELECT * FROM pemilih WHERE organisasi_id = $organisasi_id ORDER BY nama ASC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kelola Pemilih - IPM</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
    * {
        box-sizing: border-box;
    }

    html, body {
        height: 100%;
    }

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
        color: #000;
        text-decoration: underline;
    }

    nav a.active {
        color: #006400;
    }

    @media (max-width: 768px) {
        .menu-toggle {
            display: block;
        }

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

        nav.active {
            display: flex;
        }
    }

    /* CONTAINER */
    .container {
        flex: 1; /* ini kuncinya biar footer turun ke bawah */
        max-width: 900px;
        margin: 40px auto;
        padding: 25px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    }

    h2 {
        text-align: center;
        color: #E6A300;
        margin-bottom: 20px;
    }

    .top-actions {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 20px;
    }

    .btn {
        display: inline-block;
        background: linear-gradient(135deg, #FFD500, #E6A300);
        color: #333;
        font-weight: bold;
        padding: 10px 18px;
        text-decoration: none;
        border-radius: 6px;
        transition: 0.3s;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(230, 163, 0, 0.3);
    }

    /* TABLE */
    table {
        width: 100%;
        border-collapse: collapse;
        overflow: hidden;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    }

    thead {
        background-color: #FFD500;
        color: #333;
        font-weight: bold;
    }

    th, td {
        padding: 12px 14px;
        text-align: center;
        border-bottom: 1px solid #eee;
        font-size: 15px;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .action-btn {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 14px;
        text-decoration: none;
        color: white;
        margin: 0 3px;
        transition: 0.2s;
    }

    .edit-btn {
        background-color: #2980b9;
    }

    .edit-btn:hover {
        background-color: #3498db;
    }

    .delete-btn {
        background-color: #e53935;
    }

    .delete-btn:hover {
        background-color: #e74c3c;
    }

    /* FOOTER */
    footer {
        text-align: center;
        padding: 20px;
        color: #777;
        background: #f9f9f9;
        border-top: 1px solid #eee;
        font-size: 14px;
        margin-top: auto; /* ini yang bikin footer selalu di bawah */
    }

</style>
</head>

<body>

<header>
    <div class="left">
        <img src="https://ipm.or.id/wp-content/uploads/2018/11/Logo-Ikatan-Pelajar-Muhammadiyah-Resmi.png" alt="Logo IPM">
        <h2>Kelola Pemilih IPM</h2>
    </div>
    <div class="menu-toggle" onclick="toggleMenu()">☰</div>
    <nav id="navMenu">
        <a href="dashboard.php">Dashboard</a>
        <a href="organisasi.php">Organisasi</a>
        <a href="kelola_kandidat.php">Kandidat</a>
        <a href="kelola_pemilih.php" class="active">Pemilih</a>
        <a href="hasil_vote.php">Hasil Voting</a>
        <a href="logout.php" onclick="return confirm('Yakin ingin keluar?')">Logout</a>
    </nav>
</header>

<div class="container">
    <div class="top-actions">
        <a href="tambah_pemilih.php" class="btn">+ Tambah Pemilih</a>
    </div>

    <h2>Daftar Pemilih</h2>

    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Kode Akses</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($pemilih = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?= htmlspecialchars($pemilih['nama']); ?></td>
                <td><?= htmlspecialchars($pemilih['kode_akses']); ?></td>
                <td><?= $pemilih['sudah_memilih'] ? '✅ Sudah Memilih' : '❌ Belum Memilih'; ?></td>
                <td>
                    <a href="tambah_pemilih.php?id=<?= $pemilih['id']; ?>" class="action-btn edit-btn">Edit</a>
                    <a href="kelola_pemilih.php?hapus=<?= $pemilih['id']; ?>"
                       class="action-btn delete-btn"
                       onclick="return confirm('<?= $pemilih['sudah_memilih'] ? 'Pemilih ini sudah melakukan voting. Hapus beserta suaranya?' : 'Yakin ingin menghapus pemilih ini?' ?>')">
                       Hapus
                    </a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<footer>&copy; <?= date('Y'); ?> E-Voting IPM | Dikelola Oleh Bidang Teknologi Informasi</footer>

<script>
function toggleMenu() {
    document.getElementById('navMenu').classList.toggle('active');
}
</script>

</body>
</html>