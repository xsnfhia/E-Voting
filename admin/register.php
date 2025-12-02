<?php
include '../includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nama_organisasi = trim($_POST['organisasi']);
    $max_pilihan = intval($_POST['max_pilihan']);
    $kode_izin = trim($_POST['kode_izin']);

    if ($max_pilihan <= 0) {
        $error = '<div class="alert error">❌ Jumlah formatur harus lebih dari 0.</div>';
    } else {
        $cekKode = mysqli_query($conn, "SELECT kode FROM kode_izin WHERE aktif = 1 LIMIT 1");
        $rowKode = mysqli_fetch_assoc($cekKode);

        if (!$rowKode || $kode_izin !== $rowKode['kode']) {
            $error = '<div class="alert error">❌ Kode izin salah atau tidak aktif.</div>';
        } else {
            $cek = mysqli_query($conn, "SELECT id FROM organisasi WHERE nama = '$nama_organisasi'");
            if (mysqli_num_rows($cek) > 0) {
                $org = mysqli_fetch_assoc($cek);
                $organisasi_id = $org['id'];
            } else {
                $insertOrg = mysqli_query($conn, "INSERT INTO organisasi (nama, max_pilihan) VALUES ('$nama_organisasi', $max_pilihan)");
                if ($insertOrg) {
                    $organisasi_id = mysqli_insert_id($conn);
                } else {
                    $error = '<div class="alert error">❌ Gagal menambahkan organisasi.</div>';
                }
            }

            if (!$error) {
                $query = "INSERT INTO admin (nama, username, password, organisasi_id) 
                          VALUES ('$nama', '$username', '$password', '$organisasi_id')";

                if (mysqli_query($conn, $query)) {
                    header("Location: login.php?register=success");
                    exit;
                } else {
                    $error = '<div class="alert error">❌ Gagal mendaftar.</div>';
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Register Admin IPM</title>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Trebuchet MS", Helvetica, sans-serif;
    }

    body {
        display: flex;
        height: 100vh;
        background: #fefcf5;
    }

    /* --- Panel kiri (branding IPM) --- */
    .left-panel {
        flex: 1;
        background: linear-gradient(135deg, #FFD500, #E6A300);
        color: #333;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        padding: 40px;
    }

    .left-panel img {
        width: 100px;
        margin-bottom: 25px;
    }

    .left-panel h1 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 10px;
        color: #333;
    }

    .left-panel p {
        max-width: 340px;
        font-size: 15px;
        opacity: 0.9;
        color: #333;
    }

    /* --- Panel kanan (form register) --- */
    .right-panel {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        background: #ffffff;
        padding: 40px;
    }

    .register-card {
        width: 100%;
        max-width: 420px;
        padding: 40px 35px;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        background: #fff;
    }

    h2 {
        text-align: center;
        margin-bottom: 25px;
        color: #E6A300;
        font-weight: 700;
    }

    .alert {
        padding: 12px;
        border-radius: 10px;
        font-size: 14px;
        margin-bottom: 20px;
        animation: fadeIn 0.3s ease;
    }

    .alert.error {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
    }

    .form-group {
        margin-bottom: 18px;
    }

    input[type="text"],
    input[type="password"],
    input[type="number"] {
        width: 100%;
        padding: 12px 15px;
        font-size: 15px;
        border: 1px solid #ccc;
        border-radius: 8px;
        outline: none;
        transition: 0.3s;
        background-color: #fafafa;
    }

    input:focus {
        border-color: #E6A300;
        box-shadow: 0 0 0 3px rgba(230, 163, 0, 0.25);
        background-color: #fff;
    }

    button {
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg, #FFD500, #E6A300);
        border: none;
        border-radius: 8px;
        color: #333;
        font-weight: 700;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    button:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 15px rgba(230, 163, 0, 0.3);
    }

    .login-link {
        margin-top: 18px;
        font-size: 14px;
        text-align: center;
    }

    .login-link a {
        color: #E6A300;
        text-decoration: none;
        font-weight: 600;
    }

    .login-link a:hover {
        text-decoration: underline;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-5px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* --- Responsif untuk HP --- */
    @media (max-width: 768px) {
        body {
            flex-direction: column;
        }

        .left-panel {
            flex: none;
            height: 200px;
            padding: 20px;
        }

        .left-panel img {
            width: 100px;
            margin-bottom: 10px;
        }

        .left-panel h1 {
            font-size: 1.4rem;
        }

        .right-panel {
            flex: none;
            padding: 20px;
        }

        .register-card {
            padding: 30px 20px;
        }
    }
</style>
</head>
<body>
    <div class="left-panel">
        <img src="https://ipm.or.id/wp-content/uploads/2018/11/Logo-Ikatan-Pelajar-Muhammadiyah-Resmi.png" alt="Logo IPM">
        <h1>Buat Akun Admin Pemilihan Anda</h1>
        <p>Daftarkan organisasi Anda dan kelola sistem e-voting dengan mudah, aman, dan profesional.</p>
    </div>

    <div class="right-panel">
        <div class="register-card">
            <h2>Register Admin</h2>

            <?= $error ?>

            <form method="POST">
                <div class="form-group">
                    <input type="text" name="nama" placeholder="Nama Lengkap" required>
                </div>

                <div class="form-group">
                    <input type="text" name="username" placeholder="Username" required>
                </div>

                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>

                <div class="form-group">
                    <input type="text" name="organisasi" placeholder="Nama Organisasi" required>
                </div>

                <div class="form-group">
                    <input type="number" name="max_pilihan" placeholder="Jumlah Formatur" min="1" required>
                </div>

                <div class="form-group">
                    <input type="text" name="kode_izin" placeholder="Kode Izin Admin Utama" required>
                </div>

                <button type="submit">Daftar</button>
            </form>

            <div class="login-link">
                Sudah punya akun? <a href="login.php">Login di sini</a>
            </div>
        </div>
    </div>
</body>
</html>