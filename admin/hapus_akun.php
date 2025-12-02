<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id']) || !isset($_SESSION['organisasi_id'])) {
    header("Location: login.php");
    exit;
}

$organisasi_id = $_SESSION['organisasi_id'];
$admin_id = $_SESSION['admin_id'];

$query = mysqli_query($conn, "SELECT nama FROM organisasi WHERE id = $organisasi_id");
$organisasi = mysqli_fetch_assoc($query);
$nama_organisasi = $organisasi['nama'];

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $konfirmasi_nama = trim($_POST['konfirmasi_nama']);

    if (strtolower($konfirmasi_nama) !== strtolower($nama_organisasi)) {
        $error = "Nama organisasi tidak cocok. Hapus dibatalkan.";
    } else {
        try {
            // Simpan log
            $log = date('Y-m-d H:i:s') . " - Admin ID $admin_id menghapus organisasi ID $organisasi_id ($nama_organisasi)\n";
            file_put_contents('../hapus_log.txt', $log, FILE_APPEND);

            // Nonaktifkan foreign key sementara
            mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");

            // Hapus semua vote terkait organisasi
            mysqli_query($conn, "
                DELETE v 
                FROM vote v 
                JOIN kandidat k ON v.kandidat_id = k.id 
                WHERE k.organisasi_id = $organisasi_id
            ");

            // Hapus semua kandidat organisasi
            mysqli_query($conn, "DELETE FROM kandidat WHERE organisasi_id = $organisasi_id");

            // Hapus semua pemilih organisasi
            mysqli_query($conn, "DELETE FROM pemilih WHERE organisasi_id = $organisasi_id");

            // Hapus semua admin organisasi (termasuk yang login)
            mysqli_query($conn, "DELETE FROM admin WHERE organisasi_id = $organisasi_id");

            // Terakhir hapus organisasi
            mysqli_query($conn, "DELETE FROM organisasi WHERE id = $organisasi_id");

            // Aktifkan lagi foreign key
            mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");

            // Hapus session dan logout
            session_destroy();
            header("Location: ../public/index.php?hapus=success");
            exit;
        } catch (Exception $e) {
            $error = "Gagal menghapus data: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Hapus Akun dan Data</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #004080;
            color: white;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        header img {
            height: 40px;
        }
        nav {
            display: none;
            flex-direction: column;
            background-color: #004080;
        }
        nav a {
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            border-top: 1px solid rgba(255,255,255,0.2);
        }
        .menu-toggle {
            font-size: 26px;
            cursor: pointer;
        }
        @media (min-width: 600px) {
            nav {
                display: flex !important;
                flex-direction: row;
            }
            .menu-toggle {
                display: none;
            }
        }

        .container {
            padding: 30px;
            max-width: 600px;
            margin: auto;
        }

        h2 {
            margin-bottom: 10px;
        }

        .warning {
            color: red;
            font-weight: bold;
        }

        .error {
            color: red;
            margin-top: 10px;
        }

        form {
            margin-top: 20px;
        }

        input[type="text"] {
            padding: 10px;
            width: 100%;
            margin-top: 10px;
            font-size: 16px;
        }

        button {
            padding: 10px 20px;
            background-color: red;
            color: white;
            border: none;
            font-size: 16px;
            margin-top: 20px;
            cursor: pointer;
        }

        button:hover {
            background-color: darkred;
        }
    </style>
    <script>
        function toggleMenu() {
            const nav = document.getElementById("menu");
            nav.style.display = nav.style.display === "flex" ? "none" : "flex";
        }

        function confirmDelete() {
            return confirm("Apakah kamu yakin ingin menghapus akun dan seluruh data secara permanen?");
        }
    </script>
</head>
<body>
    <header>
        <img src="../public/logo.png" alt="Logo">
        <div class="menu-toggle" onclick="toggleMenu()">☰</div>
        <nav id="menu">
            <a href="dashboard.php">Dashboard</a>
            <a href="hasil_vote.php">Hasil</a>
            <a href="kelola_pemilih.php">Pemilih</a>
            <a href="kelola_kandidat.php">Kandidat</a>
            <a href="hapus_akun.php">Hapus Akun</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="container">
        <h2>Konfirmasi Hapus Akun & Data</h2>
        <p class="warning">⚠️ Semua data organisasi, admin, kandidat, pemilih, dan vote akan dihapus permanen. Tindakan ini tidak bisa dibatalkan.</p>

        <p>Ketik nama organisasi "<strong><?= htmlspecialchars($nama_organisasi) ?></strong>" untuk konfirmasi:</p>

        <form method="post" onsubmit="return confirmDelete();">
            <input type="text" name="konfirmasi_nama" placeholder="Ketik nama organisasi di sini..." required>
            <?php if ($error): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <button type="submit">Ya, Hapus Akun dan Seluruh Data</button>
        </form>
    </div>
</body>
</html>