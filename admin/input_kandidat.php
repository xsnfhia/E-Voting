<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Input Kandidat</title>
  <link rel="stylesheet" href="../public/css/style.css">
</head>

<body>
  <h2>Input Kandidat</h2>
  <form action="proses_input_kandidat.php" method="POST" enctype="multipart/form-data">
    <label>Nama Kandidat:</label><br>
    <input type="text" name="nama" required><br><br>

    <label>Visi:</label><br>
    <textarea name="visi" rows="3" required></textarea><br><br>

    <label>Misi:</label><br>
    <textarea name="misi" rows="3" required></textarea><br><br>

    <label>Foto (opsional):</label><br>
    <input type="file" name="foto"><br><br>

    <button type="submit">Simpan</button>
  </form>
</body>
</html>