<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$pesan = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $asal = trim($_POST['asal_pimpinan'] ?? '');
    $organisasi_id = $_SESSION['organisasi_id'];

    if (!$nama || !$asal) {
        $pesan = '<div class="alert error">❌ Semua kolom harus diisi!</div>';
    } elseif (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        $pesan = '<div class="alert error">❌ Harap upload foto kandidat.</div>';
    } else {
        $foto_tmp = $_FILES['foto']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($ext, $allowed) && getimagesize($foto_tmp)) {
            $foto_name = time() . '_' . uniqid() . '.' . $ext;
            $dest = '../public/uploads/' . $foto_name;

            if (move_uploaded_file($foto_tmp, $dest)) {
                $stmt = $conn->prepare("INSERT INTO kandidat (nama, asal_pimpinan, foto, organisasi_id) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("sssi", $nama, $asal, $foto_name, $organisasi_id);

                if ($stmt->execute()) {
                    $pesan = '<div class="alert success">✅ Kandidat berhasil ditambahkan!</div>';
                } else {
                    $pesan = '<div class="alert error">❌ Gagal menyimpan ke database.</div>';
                }
                $stmt->close();
            } else {
                $pesan = '<div class="alert error">❌ Gagal mengupload foto.</div>';
            }
        } else {
            $pesan = '<div class="alert error">❌ Format foto tidak valid (jpg/png/gif).</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Tambah Kandidat - IPM</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
    * { box-sizing: border-box; }

    body {
        margin: 0;
        font-family: "Trebuchet MS", Helvetica, sans-serif;
        background-color: #fffdf3;
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
        max-width: 600px;
        margin: 40px auto;
        padding: 30px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    }

    h2 {
        text-align: center;
        color: #E6A300;
        margin-bottom: 20px;
    }

    form label {
        display: block;
        margin-top: 15px;
        margin-bottom: 5px;
        color: #444;
        font-weight: bold;
    }

    input[type="text"],
    input[type="file"] {
        width: 100%;
        padding: 10px;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 15px;
        transition: 0.3s;
    }

    input:focus {
        border-color: #E6A300;
        box-shadow: 0 0 0 3px rgba(230, 163, 0, 0.25);
        outline: none;
    }

    .btn {
        margin-top: 25px;
        background: linear-gradient(135deg, #FFD500, #E6A300);
        color: #333;
        padding: 12px 20px;
        border: none;
        border-radius: 8px;
        font-weight: bold;
        width: 100%;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(230, 163, 0, 0.3);
    }

    .alert {
        padding: 12px;
        border-radius: 8px;
        margin-top: 20px;
        font-weight: bold;
        text-align: center;
    }

    .success {
        background-color: #e9fbe9;
        color: #007b55;
    }

    .error {
        background-color: #ffe6e6;
        color: #cc0000;
    }

    .back-btn {
        display: block;
        margin-top: 20px;
        text-align: center;
        color: #E6A300;
        text-decoration: none;
        font-weight: bold;
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
        margin-top: 60px;
        font-size: 14px;
    }
</style>
</head>

<body>

<header>
    <div class="left">
        <img src="https://ipm.or.id/wp-content/uploads/2018/11/Logo-Ikatan-Pelajar-Muhammadiyah-Resmi.png" alt="Logo IPM">
        <h2>Tambah Kandidat IPM</h2>
    </div>
    <div class="menu-toggle" onclick="toggleMenu()">☰</div>
    <nav id="navMenu">
        <a href="dashboard.php">Dashboard</a>
        <a href="kelola_kandidat.php">Kandidat</a>
        <a href="kelola_pemilih.php">Pemilih</a>
        <a href="hasil_vote.php">Hasil Voting</a>
        <a href="logout.php" onclick="return confirm('Yakin ingin keluar?')">Logout</a>
    </nav>
</header>

<div class="container">
    <h2>Tambah Kandidat</h2>

    <?= $pesan ?>

    <form method="POST" enctype="multipart/form-data">
        <label for="nama">Nama Kandidat:</label>
        <input type="text" name="nama" id="nama" required>

        <label for="asal_pimpinan">Asal Pimpinan:</label>
        <input type="text" name="asal_pimpinan" id="asal_pimpinan" required>

        <label for="foto">Foto Kandidat:</label>
        <input type="file" name="foto" id="foto" accept="image/*" required>

        <button type="submit" class="btn">Tambah Kandidat</button>
    </form>

    <a href="kelola_kandidat.php" class="back-btn">← Kembali ke Daftar Kandidat</a>
</div>

<footer>
    &copy; <?= date('Y'); ?> E-Voting IPM | Dikelola Oleh Bidang Teknologi Informasi
</footer>

<script>
function toggleMenu() {
    document.getElementById('navMenu').classList.toggle('active');
}
</script>

</body>
</html>