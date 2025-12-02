<?php
session_start();
include '../includes/db.php';

// Cek login
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Ambil data untuk edit jika ada parameter edit
$editData = null;
if (isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM organisasi WHERE id = $editId");
    $editData = mysqli_fetch_assoc($result);
}

// Proses tambah/update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $max_pilihan = (int) $_POST['max_pilihan']; 
    $logo = null;

    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $filename = time() . '_' . basename($_FILES['logo']['name']);
        $target = '../uploads/' . $filename;
        if (move_uploaded_file($_FILES['logo']['tmp_name'], $target)) {
            $logo = $filename;
        }
    }

    if (isset($_POST['id_edit'])) {
        $id_edit = (int) $_POST['id_edit'];
        if ($logo) {
            $query = "UPDATE organisasi SET nama = '$nama', logo = '$logo', max_pilihan = $max_pilihan WHERE id = $id_edit";
        } else {
            $query = "UPDATE organisasi SET nama = '$nama', max_pilihan = $max_pilihan WHERE id = $id_edit";
        }
    } else {
        $query = "INSERT INTO organisasi (nama, logo, max_pilihan) VALUES ('$nama', " . ($logo ? "'$logo'" : "NULL") . ", $max_pilihan)";
    }

    mysqli_query($conn, $query);
    header('Location: organisasi.php');
    exit;
}

// Proses hapus
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    
    $logoQuery = mysqli_query($conn, "SELECT logo FROM organisasi WHERE id = $id");
    $logoData = mysqli_fetch_assoc($logoQuery);
    if ($logoData && $logoData['logo']) {
        $logoPath = '../uploads/' . $logoData['logo'];
        if (file_exists($logoPath)) {
            unlink($logoPath);
        }
    }

    mysqli_query($conn, "DELETE FROM organisasi WHERE id = $id");
    header('Location: organisasi.php');
    exit;
}

// Ambil semua data organisasi
$organisasi = mysqli_query($conn, "SELECT * FROM organisasi");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kelola Organisasi - IPM</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Trebuchet MS", Helvetica, sans-serif;
    }

    body {
        background-color: #fffdf3;
    }

    /* HEADER */
    .main-header {
        background: linear-gradient(135deg, #FFD500, #E6A300);
        color: #333;
        padding: 12px 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .main-header .left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .main-header img {
        height: 45px;
    }

    .main-header h2 {
        margin: 0;
        font-size: 22px;
        font-weight: bold;
        color: #333;
    }

    .nav-links {
        display: flex;
        gap: 20px;
    }

    .nav-links a {
        text-decoration: none;
        color: #333;
        font-weight: bold;
        transition: color 0.2s;
    }

    .nav-links a:hover, 
    .nav-links a.active {
        color: #000;
        text-decoration: underline;
    }

    .menu-toggle {
        display: none;
        font-size: 26px;
        cursor: pointer;
    }

    /* CONTAINER */
    .container {
        max-width: 1000px;
        margin: 40px auto;
        background: #ffffff;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }

    h2 {
        text-align: center;
        color: #E6A300;
        margin-bottom: 20px;
    }

    form {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 15px;
        margin-bottom: 30px;
    }

    input[type="text"], input[type="file"], input[type="number"] {
        width: 80%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 8px;
        transition: 0.3s;
    }

    input[type="text"]:focus,
    input[type="file"]:focus,
    input[type="number"]:focus {
        border-color: #E6A300;
        box-shadow: 0 0 0 3px rgba(230, 163, 0, 0.25);
    }

    input[type="submit"] {
        background: linear-gradient(135deg, #FFD500, #E6A300);
        color: #333;
        font-weight: bold;
        border: none;
        border-radius: 8px;
        padding: 10px 25px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    input[type="submit"]:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(230, 163, 0, 0.3);
    }

    /* TABLE */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 25px;
        border-radius: 10px;
        overflow: hidden;
    }

    th {
        background: #FFD500;
        color: #333;
        padding: 12px;
        text-align: center;
    }

    td {
        border: 1px solid #eee;
        padding: 12px;
        text-align: center;
    }

    tr:nth-child(even) {
        background-color: #fffbea;
    }

    img.logo {
        height: 45px;
        border-radius: 6px;
    }

    .action-btn {
        display: inline-block;
        padding: 7px 14px;
        border-radius: 6px;
        color: white;
        font-weight: bold;
        text-decoration: none;
        font-size: 13px;
        transition: 0.3s;
    }

    .edit-btn {
        background-color: #2980b9;
    }
    .edit-btn:hover {
        background-color: #3498db;
    }

    .delete-btn {
        background-color: #c0392b;
    }
    .delete-btn:hover {
        background-color: #e74c3c;
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

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .menu-toggle { display: block; }
        .nav-links {
            display: none;
            flex-direction: column;
            position: absolute;
            top: 60px;
            right: 0;
            background: #FFD500;
            width: 50%;
            padding: 15px;
            border-radius: 0 0 10px 10px;
        }
        .nav-links.active { display: flex; }
    }
</style>
</head>
<body>

<header class="main-header">
    <div class="left">
        <img src="https://ipm.or.id/wp-content/uploads/2018/11/Logo-Ikatan-Pelajar-Muhammadiyah-Resmi.png" alt="Logo IPM">
        <h2>Kelola Organisasi IPM</h2>
    </div>
    <div class="menu-toggle" onclick="toggleMenu()">☰</div>
    <nav class="nav-links" id="navMenu">
        <a href="dashboard.php">Dashboard</a>
        <a href="organisasi.php" class="active">Organisasi</a>
        <a href="kelola_kandidat.php">Kandidat</a>
        <a href="kelola_pemilih.php">Pemilih</a>
        <a href="hasil_vote.php">Hasil Voting</a>
        <a href="logout.php" onclick="return confirm('Yakin ingin keluar?')">Logout</a>
    </nav>
</header>

<div class="container">
    <h2><?= $editData ? 'Edit Organisasi' : 'Tambah Organisasi' ?></h2>
    <form method="POST" enctype="multipart/form-data">
        <?php if ($editData): ?>
            <input type="hidden" name="id_edit" value="<?= htmlspecialchars($editData['id']) ?>">
        <?php endif; ?>
        <input type="text" name="nama" placeholder="Nama Organisasi" required value="<?= $editData ? htmlspecialchars($editData['nama']) : '' ?>">
        <input type="number" name="max_pilihan" placeholder="Jumlah Formatur (misal: 9)" min="1" required value="<?= $editData ? htmlspecialchars($editData['max_pilihan']) : '' ?>">
        <input type="file" name="logo" accept="image/*" <?= $editData ? '' : 'required' ?>>
        <input type="submit" value="<?= $editData ? 'Update Organisasi' : 'Tambah Organisasi' ?>">
    </form>

    <h2>Daftar Organisasi</h2>
    <table>
        <tr>
            <th>Nama Organisasi</th>
            <th>Jumlah Formatur</th>
            <th>Logo</th>
            <th>Aksi</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($organisasi)) : ?>
            <tr>
                <td><?= htmlspecialchars($row['nama']) ?></td>
                <td><?= htmlspecialchars($row['max_pilihan']) ?></td>
                <td>
                    <?php if ($row['logo']) : ?>
                        <img src="../uploads/<?= htmlspecialchars($row['logo']) ?>" alt="Logo" class="logo">
                    <?php else : ?>
                        <em>Tidak ada</em>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="organisasi.php?edit=<?= $row['id'] ?>" class="action-btn edit-btn">Edit</a>
                    <a href="organisasi.php?hapus=<?= $row['id'] ?>" class="action-btn delete-btn" onclick="return confirm('Hapus organisasi ini?')">Hapus</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<footer>&copy; <?= date('Y') ?> E-Voting IPM | Dikelola Oleh Bidang Teknologi Informasi</footer>

<script>
function toggleMenu() {
    document.getElementById("navMenu").classList.toggle("active");
}
</script>

</body>
</html>