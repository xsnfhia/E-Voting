<?php
session_start();
include '../includes/db.php';

// Cek apakah user login & session lengkap
if (!isset($_SESSION['pemilih_id']) || !isset($_SESSION['organisasi_id'])) {
    header('Location: login.php');
    exit;
}

$pemilih_id = intval($_SESSION['pemilih_id']);
$organisasi_id = intval($_SESSION['organisasi_id']);
$kandidat_ids = isset($_POST['kandidat_id']) ? $_POST['kandidat_id'] : [];

// Pastikan kandidat_id berbentuk array
if (!is_array($kandidat_ids)) {
    die("Terjadi kesalahan pada input.");
}

// 🔹 Ambil batas maksimal pilihan dari tabel organisasi
$org_query = $conn->prepare("SELECT max_pilihan FROM organisasi WHERE id = ?");
$org_query->bind_param("i", $organisasi_id);
$org_query->execute();
$org_result = $org_query->get_result();
$org_data = $org_result->fetch_assoc();

if (!$org_data) {
    die("Organisasi tidak ditemukan.");
}

$max_pilihan = intval($org_data['max_pilihan']);

// 🔹 Validasi jumlah pilihan sesuai organisasi
$total_pilihan = count($kandidat_ids);
if ($total_pilihan === 0) {
    die("Anda belum memilih kandidat.");
}
if ($total_pilihan < $max_pilihan) {
    die("Anda harus memilih tepat $max_pilihan kandidat. Anda baru memilih $total_pilihan kandidat.");
}
if ($total_pilihan > $max_pilihan) {
    die("Anda hanya boleh memilih tepat $max_pilihan kandidat. Anda memilih $total_pilihan kandidat.");
}

// 🔹 Cek apakah pemilih sudah pernah memilih
$cek = $conn->prepare("SELECT sudah_memilih FROM pemilih WHERE id = ?");
$cek->bind_param("i", $pemilih_id);
$cek->execute();
$data = $cek->get_result()->fetch_assoc();

if (!$data) {
    die("Pemilih tidak ditemukan.");
}
if ($data['sudah_memilih'] == 1) {
    session_unset();
    session_destroy();
    header("Location: login.php?pesan=sudah_memilih");
    exit;
}

// 🔹 Masukkan suara ke tabel vote (gunakan organisasi_id juga)
$stmt = $conn->prepare("INSERT INTO vote (pemilih_id, kandidat_id, organisasi_id, waktu_vote) VALUES (?, ?, ?, NOW())");
foreach ($kandidat_ids as $kandidat_id) {
    $kandidat_id = intval($kandidat_id);
    $stmt->bind_param("iii", $pemilih_id, $kandidat_id, $organisasi_id);
    $stmt->execute();
}

// 🔹 Update status pemilih
$update = $conn->prepare("UPDATE pemilih SET sudah_memilih = 1 WHERE id = ?");
$update->bind_param("i", $pemilih_id);
$update->execute();

// 🔹 Hapus session agar tidak bisa voting lagi
session_unset();
session_destroy();

// 🔹 Redirect ke halaman sukses
header("Location: login.php?pesan=selesai");
exit;
?>