<?php include('../includes/db.php'); ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Jadwal Pemilihan</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <h2>DAFTAR JADWAL PEMILIHAN</h2>
  <table>
    <thead>
      <tr>
        <th>Nama Pemilihan</th>
        <th>Tanggal Mulai</th>
        <th>Tanggal Selesai</th>
        <th>Status</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $result = $conn->query("SELECT * FROM jadwal ORDER BY mulai DESC");
      date_default_timezone_set("Asia/Jakarta");
      $today = date("Y-m-d");

      while ($row = $result->fetch_assoc()) {
        $status = 'Selesai';
        if ($today < $row['mulai']) {
          $status = 'Belum Mulai';
        } elseif ($today <= $row['selesai']) {
          $status = 'Sedang Berlangsung';
        }

        echo "<tr>
                <td>{$row['nama_pemilihan']}</td>
                <td>{$row['mulai']}</td>
                <td>{$row['selesai']}</td>
                <td>$status</td>
                <td><a href='detail.php?id={$row['id']}'>Lihat Detail</a></td>
              </tr>";
      }
      ?>
    </tbody>
  </table>
</body>
</html>