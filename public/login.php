<?php
include '../includes/db.php';
session_start();

$pesan = $_GET['pesan'] ?? '';
$alert = '';

if ($pesan === 'selesai') {
    $alert = '<div class="alert success">✅ Terima kasih, suara Anda sudah tercatat.</div>';
} elseif ($pesan === 'sudah_memilih') {
    $alert = '<div class="alert warning">⚠️ Anda sudah pernah memilih. Sistem hanya mengizinkan satu kali voting.</div>';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode = mysqli_real_escape_string($conn, $_POST['kode_akses']);
    $query = mysqli_query($conn, "SELECT * FROM pemilih WHERE kode_akses = '$kode'");
    $data = mysqli_fetch_assoc($query);

    if ($data) {
        if ($data['sudah_memilih'] == 1) {
            header("Location: login.php?pesan=sudah_memilih");
            exit;
        } else {
            $_SESSION['pemilih_id'] = $data['id'];
            $_SESSION['organisasi_id'] = $data['organisasi_id'];
            header("Location: vote.php");
            exit;
        }
    } else {
        $alert = '<div class="alert error">❌ Kode akses tidak ditemukan. Silakan periksa kembali.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Pemilih - E-Voting IPM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Trebuchet MS", Helvetica, sans-serif;
            background: radial-gradient(circle, rgba(255,208,155,1) 22%, rgba(255,240,133,1) 59%, rgba(252,180,84,1) 100%);
            display: flex;
            height: 100vh;
        }

        .left {
            flex: 1;
            background: linear-gradient(135deg, #FFD500, #E6A300);
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            color: #333;
            padding: 20px;
        }

        .left img {
            width: 100px;
            height: auto;
            margin-bottom: 20px;
            border-radius: 0 !important;
        }

        .left h1 {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
        }

        .right {
            flex: 1;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-box {
            width: 80%;
            max-width: 380px;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #E6A300;
        }

        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert.success { background-color: #e0f9e0; color: #006400; }
        .alert.warning { background-color: #fff3cd; color: #856404; }
        .alert.error { background-color: #f8d7da; color: #721c24; }

        input[type="text"] {
            width: 100%;
            padding: 12px;
            font-size: 15px;
            border: 1.5px solid #ccc;
            border-radius: 8px;
            outline: none;
            transition: 0.3s;
        }

        input[type="text"]:focus {
            border-color: #E6A300;
            box-shadow: 0 0 5px rgba(230, 163, 0, 0.3);
        }

        button {
            width: 100%;
            margin-top: 20px;
            padding: 12px;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            color: #333;
            background: linear-gradient(135deg, #FFD500, #E6A300);
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #E6A300, #D67E00);
        }

        @media (max-width: 768px) {
            body {
                flex-direction: column;
                height: auto;
            }
            .left {
                height: 200px;
            }
        }

    </style>
</head>
<body>
    <div class="left">
        <img src="https://ipm.or.id/wp-content/uploads/2018/11/Logo-Ikatan-Pelajar-Muhammadiyah-Resmi.png" alt="Logo IPM">
        <h1>E-Voting<br>Ikatan Pelajar Muhammadiyah</h1>
    </div>

    <div class="right">
        <div class="login-box">
            <h2>Login Pemilih</h2>
            <?= $alert ?>
            <form method="POST">
                <input type="text" name="kode_akses" placeholder="Masukkan Kode Akses" required>
                <button type="submit">Masuk</button>
            </form>
        </div>
    </div>

</body>
</html>