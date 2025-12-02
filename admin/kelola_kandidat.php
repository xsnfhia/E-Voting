<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$organisasi_id = $_SESSION['organisasi_id'];

if (isset($_POST['update_kandidat'])) {
    $id = (int) $_POST['id'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);

    if (!empty($_FILES['foto']['name'])) {
        $foto = basename($_FILES['foto']['name']);
        $path = '../public/uploads/' . $foto;
        move_uploaded_file($_FILES['foto']['tmp_name'], $path);
        $update = "UPDATE kandidat SET nama='$nama', foto='$foto' WHERE id=$id AND organisasi_id=$organisasi_id";
    } else {
        $update = "UPDATE kandidat SET nama='$nama' WHERE id=$id AND organisasi_id=$organisasi_id";
    }

    mysqli_query($conn, $update);
    header('Location: kelola_kandidat.php');
    exit;
}

$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = (int) $_GET['edit'];
    $edit_result = mysqli_query($conn, "SELECT * FROM kandidat WHERE id=$edit_id AND organisasi_id=$organisasi_id");
    $edit_data = mysqli_fetch_assoc($edit_result);
}

$query = "SELECT * FROM kandidat WHERE organisasi_id = $organisasi_id";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kelola Kandidat - IPM</title>
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
        height: 38px;
        width: auto;
        object-fit: contain;
        border-radius: 0 !important;
        box-shadow: none !important;
    }

    .main-header h2 {
        margin: 0;
        font-size: 22px;
        font-weight: bold;
        color: #333;
    }

    .main-header .nav-links {
        display: flex;
        gap: 20px;
    }

    .main-header .nav-links a {
        color: #333;
        text-decoration: none;
        font-weight: bold;
        transition: 0.3s;
    }

    .main-header .nav-links a:hover,
    .main-header .nav-links a.active {
        color: #000;
        text-decoration: underline;
    }

    .menu-toggle {
        display: none;
        font-size: 26px;
        cursor: pointer;
    }

    @media screen and (max-width: 768px) {
        .menu-toggle {
            display: block;
        }

        .main-header .nav-links {
            display: none;
            flex-direction: column;
            background: #FFD500;
            position: absolute;
            top: 60px;
            right: 0;
            width: 40%;
            padding: 15px;
            border-radius: 0 0 10px 10px;
            box-shadow: -3px 5px 10px rgba(0,0,0,0.1);
        }

        .main-header .nav-links.active {
            display: flex;
        }
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

    h2, h3 {
        text-align: center;
        color: #E6A300;
        margin-bottom: 20px;
    }

    .action-bar {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }

    .add-btn {
        background: linear-gradient(135deg, #FFD500, #E6A300);
        color: #333;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 8px;
        font-weight: bold;
        transition: 0.3s;
    }

    .add-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(230, 163, 0, 0.3);
    }

    /* TABLE */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        border-radius: 10px;
        overflow: hidden;
    }

    th, td {
        padding: 12px;
        text-align: center;
        border-bottom: 1px solid #eee;
    }

    th {
        background-color: #FFD500;
        color: #333;
    }

    tr:nth-child(even) {
        background-color: #fffbea;
    }

    tr:hover {
        background-color: #fff3c4;
    }

    img {
        max-width: 80px;
        max-height: 80px;
        object-fit: cover;
        border-radius: 8px;
    }

    .action-btn {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 14px;
        text-decoration: none;
        color: white;
        font-weight: bold;
        display: inline-block;
        transition: 0.3s;
    }

    .edit-btn {
        background-color: #f9a825;
    }

    .edit-btn:hover {
        background-color: #E6A300;
    }

    .delete-btn {
        background-color: #e53935;
    }

    .delete-btn:hover {
        background-color: #e74c3c;
    }

    /* FORM */
    form {
        display: flex;
        flex-direction: column;
        gap: 12px;
        max-width: 400px;
        margin: 20px auto;
        background: #fffbea;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    }

    form label {
        font-weight: bold;
        color: #444;
    }

    form input[type="text"],
    form input[type="file"] {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 8px;
        transition: 0.3s;
    }

    form input[type="text"]:focus,
    form input[type="file"]:focus {
        border-color: #E6A300;
        box-shadow: 0 0 0 3px rgba(230, 163, 0, 0.25);
    }

    .save-btn {
        background: #007b55;
        color: white;
        padding: 10px;
        border: none;
        border-radius: 8px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }

    .save-btn:hover {
        background: #006644;
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

<header class="main-header">
    <div class="left">
        <img src="https://ipm.or.id/wp-content/uploads/2018/11/Logo-Ikatan-Pelajar-Muhammadiyah-Resmi.png" alt="Logo IPM">
        <h2>Kelola Kandidat IPM</h2>
    </div>
    <div class="menu-toggle" onclick="toggleMenu()">☰</div>
    <nav class="nav-links" id="navMenu">
        <a href="dashboard.php">Dashboard</a>
        <a href="organisasi.php">Organisasi</a>
        <a href="kelola_kandidat.php" class="active">Kandidat</a>
        <a href="kelola_pemilih.php">Pemilih</a>
        <a href="hasil_vote.php">Hasil Voting</a>
        <a href="logout.php" onclick="return confirm('Yakin ingin keluar?')">Logout</a>
    </nav>
</header>

<div class="container">
    <h2>Daftar Kandidat</h2>

    <div class="action-bar">
        <a href="tambah_kandidat.php" class="add-btn">+ Tambah Kandidat</a>
    </div>

    <?php if ($edit_data): ?>
        <h3>Edit Kandidat</h3>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $edit_data['id']; ?>">

            <label>Nama Kandidat:</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($edit_data['nama']); ?>" required>

            <label>Foto Kandidat:</label>
            <input type="file" name="foto">
            <?php if ($edit_data['foto']): ?>
                <img src="../public/uploads/<?= htmlspecialchars($edit_data['foto']); ?>" width="80">
            <?php endif; ?>

            <button type="submit" name="update_kandidat" class="save-btn">Simpan Perubahan</button>
        </form>
    <?php endif; ?>

    <table>
        <tr>
            <th>Foto</th>
            <th>Nama</th>
            <th>Aksi</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><img src="../public/uploads/<?= htmlspecialchars($row['foto']); ?>" alt="<?= htmlspecialchars($row['nama']); ?>"></td>
            <td><?= htmlspecialchars($row['nama']); ?></td>
            <td>
                <a href="kelola_kandidat.php?edit=<?= $row['id']; ?>" class="action-btn edit-btn">Edit</a>
                <a href="hapus_kandidat.php?id=<?= $row['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Yakin ingin menghapus kandidat ini?')">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<footer>
    &copy; <?= date('Y'); ?> E-Voting IPM | Dikelola Oleh Bidang Teknologi Informasi
</footer>

<script>
function toggleMenu() {
    const menu = document.getElementById('navMenu');
    menu.classList.toggle('active');
}
</script>

</body>
</html>