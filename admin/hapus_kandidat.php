<?php
session_start();
include '../includes/db.php'; // pake db.php sesuai project kamu

// Pastikan admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Ambil data kandidat dulu (cek apakah ada fotonya)
    $result = mysqli_query($conn, "SELECT foto FROM kandidat WHERE id = $id");
    $data = mysqli_fetch_assoc($result);

    if ($data) {
        $foto = $data['foto'];

        // Hapus kandidat dari database
        $query = "DELETE FROM kandidat WHERE id = $id";
        if (mysqli_query($conn, $query)) {
            // Kalau ada foto dan file-nya ada di folder uploads/, hapus juga
            if (!empty($foto) && file_exists("../uploads/$foto")) {
                unlink("../uploads/$foto");
            }

            header("Location: kelola_kandidat.php?pesan=sukses_hapus");
            exit();
        } else {
            echo "Gagal menghapus kandidat: " . mysqli_error($conn);
        }
    } else {
        header("Location: kelola_kandidat.php?pesan=kandidat_tidak_ditemukan");
        exit();
    }
} else {
    header("Location: kelola_kandidat.php");
    exit();
}
?>