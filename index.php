<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Selamat Datang di E-Voting IPM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Trebuchet MS", Helvetica, sans-serif;
            background: radial-gradient(circle, #FFD500 25%, #E6A300 75%);
            color: #222;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }

        img {
            width: 120px;
            height: auto;
            margin-bottom: 20px;
            border-radius: 0 !important;
        }

        h1 {
            font-size: 28px;
            margin-bottom: 10px;
            color: #000;
        }

        p {
            font-size: 16px;
            margin-bottom: 40px;
            color: #333;
        }

        .button-container {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn {
            background: #ffefbcff;
            color: #000;
            font-weight: bold;
            padding: 14px 28px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        .btn:hover {
            background: linear-gradient(135deg, #E6A300, #D67E00);
            transform: translateY(-2px);
        }

        footer {
            position: fixed;
            bottom: 15px;
            width: 100%;
            text-align: center;
            font-size: 13px;
            color: #444;
        }

        @media (max-width: 480px) {
            h1 { font-size: 22px; }
            p { font-size: 14px; margin-bottom: 30px; }
            .btn { padding: 12px 22px; font-size: 14px; }
        }
    </style>
</head>
<body>

    <img src="https://ipm.or.id/wp-content/uploads/2018/11/Logo-Ikatan-Pelajar-Muhammadiyah-Resmi.png" alt="Logo IPM">
    <h1>Selamat Datang di Sistem E-Voting IPM</h1>
    <p>Pilih jenis login sesuai peran Anda:</p>

    <div class="button-container">
        <a href="admin/login.php"><button class="btn">Login Admin</button></a>
        <a href="public/login.php"><button class="btn">Login Pemilih</button></a>
    </div>

    <footer>
        &copy; <?= date('Y'); ?> E-Voting IPM | Dikelola Oleh Bidang Teknologi Informasi
    </footer>

</body>
</html>