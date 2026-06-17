<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Aplikasi Renang - Jadwal Latihan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background: #f4f7f6; }
        header { background: #dd4814; color: white; padding: 2rem; text-align: center; }
        nav { background: #333; padding: 10px; text-align: center; }
        nav a { color: white; margin: 0 15px; text-decoration: none; font-weight: bold; }
        section { max-width: 800px; margin: 20px auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .schedule-card { border-left: 5px solid #dd4814; padding-left: 20px; margin-bottom: 30px; }
        .schedule-day { font-weight: bold; color: #dd4814; font-size: 1.2rem; display: block; }
        .terms-box { background: #fff3e0; border: 1px solid #ff9800; padding: 20px; border-radius: 5px; }
        .btn-register { display: inline-block; background: #dd4814; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 20px; }
        footer { text-align: center; padding: 20px; font-size: 0.9rem; color: #666; }
    </style>
</head>
<body>

<header>
    <h1>Aplikasi Manajemen Renang</h1>
    <p>Informasi Jadwal & Pendaftaran Anggota Baru</p>
</header>

<nav>
    <a href="<?= base_url() ?>">Home</a>
    <a href="<?= base_url('auth') ?>">Login Admin</a>
    <a href="<?= base_url('parent/login') ?>">Login Orang Tua</a>
    <a href="<?= base_url('coach/login') ?>">Login Pelatih</a>
</nav>

<section>
    <div class="schedule-card">
        <h2>📅 Jadwal Rutin Latihan</h2>
        <p><span class="schedule-day">Jumat Sore</span> Jam 15.30 s/d 18.00 WIB</p>
        <p><span class="schedule-day">Minggu Pagi</span> Mulai Jam 07.30 WIB</p>
    </div>

    <div class="terms-box">
        <h3>⚠️ Syarat dan Ketentuan Pendamping</h3>
        <ul>
            <li>Pendamping boleh masuk maksimal 1 orang.</li>
            <li>Lebih dari 1 orang akan dikenakan biaya tiket kolam.</li>
            <li>Pendamping tidak diperbolehkan ikut berenang.</li>
            <li>Jika pendamping ikut berenang, maka akan dikenakan biaya tiket kolam.</li>
            <li><strong>Mendaftar berarti menyetujui seluruh syarat dan ketentuan.</strong></li>
        </ul>
    </div>

    <div style="text-align: center;">
        <a href="<?= base_url('parent/register') ?>" class="btn-register">Daftar Sekarang</a>
    </div>
</section>

<footer>
    &copy; <?= date('Y') ?> Aplikasi Renang. Seluruh hak cipta dilindungi.
</footer>

</body>
</html>