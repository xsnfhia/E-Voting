<?php
session_start();
include '../includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM admin WHERE username = '$username'";
    $result = mysqli_query($conn, $query);
    $admin = mysqli_fetch_assoc($result);

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_nama'] = $admin['nama'];
        $_SESSION['organisasi_id'] = $admin['organisasi_id'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = '<div class="alert error">❌ Username atau password salah.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login Admin IPM</title>
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
        font-weight: bold;
        margin-bottom: 10px;
        color: #333;
    }

    .left-panel p {
        max-width: 340px;
        font-size: 15px;
        opacity: 0.9;
        color: #333;
    }

    /* --- Panel kanan (form login) --- */
    .right-panel {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        background: #ffffff;
        padding: 40px;
    }

    .login-card {
        width: 100%;
        max-width: 380px;
        padding: 40px 35px;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        background: #fff;
    }

    h2 {
        text-align: center;
        margin-bottom: 25px;
        color: #E6A300;
        font-weight: bold;
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
    input[type="password"] {
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
        font-weight: bold;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    button:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 15px rgba(230, 163, 0, 0.3);
    }

    .register-link {
        margin-top: 18px;
        font-size: 14px;
        text-align: center;
    }

    .register-link a {
        color: #E6A300;
        text-decoration: none;
        font-weight: bold;
    }

    .register-link a:hover {
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

        .login-card {
            padding: 30px 20px;
        }
    }
</style>
</head>
<body>
    <div class="left-panel">
        <img src="https://ipm.or.id/wp-content/uploads/2018/11/Logo-Ikatan-Pelajar-Muhammadiyah-Resmi.png" alt="Logo IPM">
        <h1>Selamat Datang di Portal Login Admin</h1>
        <p>Login untuk mengelola dan memantau proses e-voting dengan aman dan efisien.</p>
    </div>

    <div class="right-panel">
        <div class="login-card">
            <h2>Login Admin</h2>
            <?= $error ?>
            <form method="POST">
                <div class="form-group">
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit">Masuk</button>
            </form>
            <div class="register-link">
                Belum punya akun? <a href="register.php">Daftar di sini</a>
            </div>
        </div>
    </div>
</body>
</html>
