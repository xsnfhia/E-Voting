<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$organisasi_id = $_SESSION['organisasi_id'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];


    $foto = '';
    if ($_FILES['foto']['name']) {
        $target_dir = "../uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir);
        }
        $foto = $target_dir . basename($_FILES["foto"]["name"]);
        move_uploaded_file($_FILES["foto"]["tmp_name"], $foto);
    }

    $insert = mysqli_query($conn, "INSERT INTO kandidat (nama, foto, organisasi_id)
                VALUES ('$nama', '$foto', '$organisasi_id')");

    $message = $insert ? "<p style='color:green'>Kandidat berhasil ditambahkan.</p>" 
                       : "<p style='color:red'>Gagal menambahkan kandidat: " . mysqli_error($conn) . "</p>";
}

$kandidat = mysqli_query($conn, "SELECT * FROM kandidat WHERE organisasi_id = $organisasi_id");
?>

<h2>Kelola Kandidat</h2>
<?= $message ?>
<form method="POST" enctype="multipart/form-data">
    <input type="text" name="nama" placeholder="Nama Kandidat" required><br><br>
    <input type="file" name="foto"><br><br>
    <input type="submit" value="Tambah Kandidat">
</form>

<hr>
<h3>Daftar Kandidat</h3>
<ul>
<?php while($row = mysqli_fetch_assoc($kandidat)) { ?>
    <li>
        <strong><?php echo $row['nama']; ?></strong><br>
        <?php if ($row['foto']): ?>
            <img src="<?php echo $row['foto']; ?>" width="100"><br>
        <?php endif; ?>
    </li>
<?php } ?>
</ul>

<a href="dashboard.php">← Kembali ke Dashboard</a>