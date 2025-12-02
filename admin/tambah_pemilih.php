<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$organisasi_id = $_SESSION['organisasi_id'];
$error = '';
$sukses = '';
$isEdit = isset($_GET['id']);
$nama = '';
$nba = '';

function generateKodeAkses($length = 8) {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%&*?';
    return substr(str_shuffle($chars), 0, $length);
}

if ($isEdit) {
    $id = (int)$_GET['id'];
    $result = mysqli_query($conn, "SELECT * FROM pemilih WHERE id = $id AND organisasi_id = $organisasi_id");
    if ($row = mysqli_fetch_assoc($result)) {
        $nama = $row['nama'];
        $nba = $row['nba'];
    } else {
        $error = "Data pemilih tidak ditemukan.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $nba = trim($_POST['nba']);

    if ($nama === '' || $nba === '') {
        $error = 'Nama dan NBA tidak boleh kosong.';
    } elseif (!ctype_digit($nba)) {
        $error = 'Nomor Baku Anggota (NBA) harus berupa angka.';
    } else {
        if ($isEdit) {
            $stmt = mysqli_prepare($conn, "UPDATE pemilih SET nama = ?, nba = ? WHERE id = ? AND organisasi_id = ?");
            mysqli_stmt_bind_param($stmt, 'ssii', $nama, $nba, $id, $organisasi_id);
            mysqli_stmt_execute($stmt);

            if (mysqli_stmt_affected_rows($stmt) >= 0) {
                $sukses = "Data pemilih berhasil diperbarui.";
            } else {
                $error = "Gagal memperbarui data.";
            }
        } else {
            $kode_akses = generateKodeAkses();
            $check = mysqli_query($conn, "SELECT id FROM pemilih WHERE kode_akses = '$kode_akses'");
            while (mysqli_num_rows($check) > 0) {
                $kode_akses = generateKodeAkses();
                $check = mysqli_query($conn, "SELECT id FROM pemilih WHERE kode_akses = '$kode_akses'");
            }

            $stmt = mysqli_prepare($conn, "INSERT INTO pemilih (nama, nba, kode_akses, sudah_memilih, organisasi_id) VALUES (?, ?, ?, 0, ?)");
            mysqli_stmt_bind_param($stmt, 'sssi', $nama, $nba, $kode_akses, $organisasi_id);
            mysqli_stmt_execute($stmt);

            if (mysqli_stmt_affected_rows($stmt) > 0) {
                $sukses = "Pemilih berhasil ditambahkan dengan kode akses: <strong>$kode_akses</strong>";
                $nama = '';
                $nba = '';
            } else {
                $error = "Gagal menambahkan pemilih.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title><?= $isEdit ? 'Edit Pemilih' : 'Tambah Pemilih' ?> - IPM</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        border-radius: 0 !important;
        object-fit: contain;
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
        max-width: 600px;
        margin: 40px auto;
        background: white;
        padding: 30px;
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
        color: #333;
        font-weight: bold;
    }

    input[type="text"],
    input[type="number"] {
        width: 100%;
        padding: 10px 12px;
        border-radius: 6px;
        border: 1px solid #ccc;
    }

    .btn {
        margin-top: 25px;
        width: 100%;
        background: linear-gradient(135deg, #FFD500, #E6A300);
        color: #333;
        font-weight: bold;
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(230,163,0,0.3);
    }

    .alert {
        padding: 12px;
        border-radius: 6px;
        margin-top: 20px;
        font-weight: bold;
    }
    .success { background-color: #e0f9e0; color: #006400; }
    .error { background-color: #ffe6e6; color: #cc0000; }

    .back-btn {
        display: block;
        margin-top: 20px;
        text-align: center;
        color: #E6A300;
        text-decoration: none;
        font-weight: bold;
    }

    .back-btn:hover { text-decoration: underline; }

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
        <h2><?= $isEdit ? 'Edit Pemilih' : 'Tambah Pemilih' ?></h2>
    </div>
    <div class="menu-toggle" onclick="toggleMenu()">☰</div>
    <nav id="navMenu">
        <a href="dashboard.php">Dashboard</a>
        <a href="kelola_pemilih.php" class="active">Pemilih</a>
        <a href="hasil_vote.php">Hasil Voting</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="container">
    <h2><?= $isEdit ? 'Edit Pemilih' : 'Tambah Pemilih' ?></h2>

    <?php if ($error): ?>
        <div class="alert error"><?= $error; ?></div>
    <?php elseif ($sukses): ?>
        <div class="alert success"><?= $sukses; ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="nama">Nama Pemilih:</label>
        <input type="text" name="nama" id="nama" required value="<?= htmlspecialchars($nama) ?>">

        <label for="nba">Nomor Baku Anggota (NBA):</label>
        <input type="number" name="nba" id="nba" required value="<?= htmlspecialchars($nba) ?>">

        <button type="submit" class="btn"><?= $isEdit ? 'Simpan Perubahan' : 'Tambah Pemilih' ?></button>
    </form>

    <a href="kelola_pemilih.php" class="back-btn">← Kembali ke Daftar Pemilih</a>
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