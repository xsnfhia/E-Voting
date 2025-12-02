<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['pemilih_id']) || !isset($_SESSION['organisasi_id'])) {
    header('Location: login.php');
    exit;
}

$organisasi_id = $_SESSION['organisasi_id'];

$org_stmt = $conn->prepare("SELECT nama, max_pilihan FROM organisasi WHERE id = ?");
$org_stmt->bind_param("i", $organisasi_id);
$org_stmt->execute();
$org_data = $org_stmt->get_result()->fetch_assoc();
$nama_organisasi = htmlspecialchars($org_data['nama']);
$max_pilihan = (int)$org_data['max_pilihan'];

$stmt = $conn->prepare("SELECT * FROM kandidat WHERE organisasi_id = ?");
$stmt->bind_param("i", $organisasi_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Voting - <?= $nama_organisasi ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
    * { box-sizing: border-box; }

    body {
        margin: 0;
        font-family: "Trebuchet MS", Helvetica, sans-serif;
        background: #FFD09B;
        background: radial-gradient(circle, rgba(255,208,155,1) 22%, rgba(255,240,133,1) 59%, rgba(252,180,84,1) 100%);
        color: #222;
    }

    header {
        background-color: #FCB454;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    header h1 {
        margin: 0;
        color: #000;
        font-size: 22px;
        font-weight: bold;
        text-align : center;
    }

    header img {
        height: 40px;
        border-radius: 0 !important;
    }

    .container {
        max-width: 1000px;
        margin: 40px auto;
        background-color: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    h2 {
        text-align: center;
        color: #000;
        margin-bottom: 25px;
    }

    .kandidat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
    }

    .kandidat-card {
        cursor: pointer;
        border-radius: 10px;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .kandidat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.1);
    }

    .kandidat-card input[type="checkbox"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .card-inner {
        background-color: #fffdf9;
        border: 2px solid #FCB454;
        border-radius: 10px;
        padding: 14px;
        text-align: center;
        transition: border-color 0.2s, background-color 0.2s;
    }

    .kandidat-card input[type="checkbox"]:checked + .card-inner {
        border-color: #FFD09B;
        background: linear-gradient(180deg, rgba(255,243,205,1) 0%, rgba(255,230,180,1) 100%);
    }

    .card-inner img {
        width: 100%;
        aspect-ratio: 3 / 4;
        object-fit: cover;
        border-radius: 6px;
        margin-bottom: 10px;
    }

    .nama {
        font-weight: bold;
        color: #000;
        margin-bottom: 5px;
    }

    .asal {
        color: #444;
        font-size: 14px;
        margin-bottom: 8px;
    }

    .pilihan-indicator {
        display: inline-block;
        background-color: #FCB454;
        color: #000;
        padding: 7px 14px;
        border-radius: 999px;
        font-weight: bold;
        font-size: 13px;
    }

    .kandidat-card input[type="checkbox"]:checked + .card-inner .pilihan-indicator {
        background-color: #FFD09B;
    }

    .submit-btn {
        display: block;
        background-color: #FCB454;
        color: #000;
        font-weight: bold;
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        margin: 30px auto 15px;
        font-size: 17px;
        transition: background-color 0.2s;
    }

    .submit-btn:hover {
        background-color: #FFD09B;
    }

    .info {
        text-align: center;
        color: #444;
        font-size: 14px;
    }

    footer {
        text-align: center;
        padding: 20px;
        color: #888;
        margin-top: 60px;
        font-family: sans-serif;
    }
</style>
</head>
<body>

<header>
    <h1>Voting - <?= $nama_organisasi ?></h1>
    <img src="https://ipm.or.id/wp-content/uploads/2018/11/Logo-Ikatan-Pelajar-Muhammadiyah-Resmi.png" alt="Logo IPM">
</header>

<div class="container">
    <h2>Pilih Kandidat Terbaikmu!</h2>
    <form action="proses_vote.php" method="POST" onsubmit="return validateForm();">
        <div class="kandidat-grid">
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <label class="kandidat-card" tabindex="0">
                    <input type="checkbox" name="kandidat_id[]" value="<?= (int)$row['id'] ?>">
                    <div class="card-inner">
                        <img src="uploads/<?= htmlspecialchars($row['foto']) ?>" alt="Foto <?= htmlspecialchars($row['nama']) ?>">
                        <div class="nama"><?= htmlspecialchars($row['nama']) ?></div>
                        <div class="asal"><?= htmlspecialchars($row['asal_pimpinan']) ?></div>
                        <div class="pilihan-indicator">Klik untuk pilih</div>
                    </div>
                </label>
            <?php } ?>
        </div>

        <button type="submit" class="submit-btn">Selesai Memilih</button>
        <div class="info">
            Pilih <strong>tepat <?= $max_pilihan ?></strong> kandidat untuk organisasi <strong><?= $nama_organisasi ?></strong>.
        </div>
    </form>
</div>

<footer>
    &copy; <?= date('Y'); ?> E-Voting IPM | Dikelola Oleh Bidang Teknologi Informasi 
</footer>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const MAX_SELECTION = <?= $max_pilihan ?>;
    const checkboxes = Array.from(document.querySelectorAll('.kandidat-card input[type="checkbox"]'));

    function countChecked() {
        return checkboxes.filter(cb => cb.checked).length;
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', e => {
            const checked = countChecked();
            if (checked > MAX_SELECTION) {
                e.target.checked = false;
                alert("Kamu hanya boleh memilih TEPAT " + MAX_SELECTION + " kandidat.");
            }
        });

        const label = cb.closest('.kandidat-card');
        label.addEventListener('keydown', ev => {
            if (ev.key === ' ' || ev.key === 'Enter') {
                ev.preventDefault();
                cb.checked = !cb.checked;
                cb.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    });

    window.validateForm = function() {
        const checked = countChecked();
        if (checked !== MAX_SELECTION) {
            alert("Kamu harus memilih TEPAT " + MAX_SELECTION + " kandidat. Saat ini memilih " + checked + ".");
            return false;
        }
        return true;
    };
});
</script>
</body>
</html>