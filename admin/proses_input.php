<?php
include '../includes/db.php';

$nama = $_POST['nama'];
$visi = $_POST['visi'];
$misi = $_POST['misi'];
$organisasi_id = $_POST['organisasi_id'];


$foto = $_FILES['foto']['name'];
$tmp = $_FILES['foto']['tmp_name'];
$folder = '../public/uploads/' . $foto;

if(move_uploaded_file($tmp, $folder)) {
    $query = "INSERT INTO kandidat (nama, visi, misi, foto, organisasi_id)
              VALUES ('$nama', '$visi', '$misi', '$foto', '$organisasi_id')";
    $result = mysqli_query($conn, $query);

    if($result){
        echo "<script>alert('Kandidat berhasil ditambahkan');window.location='input_kandidat.php';</script>";
    } else {
        echo "Gagal input kandidat: " . mysqli_error($conn);
    }
} else {
    echo "Upload foto gagal!";
}
?>
