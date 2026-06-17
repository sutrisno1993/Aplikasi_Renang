<!DOCTYPE html>
<html>
<head>
    <title>Bukti Pembayaran</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 10px;
            width: 80mm; /* Ukuran kertas thermal standard */
        }
        .text-center {
            text-align: center;
        }
        .header {
            margin-bottom: 10px;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        .info {
            font-size: 12px;
            margin: 5px 0;
        }
        .footer {
            margin-top: 10px;
            font-size: 10px;
        }
        .signature-space {
            margin-top: 20px;
            text-align: right;
            padding-right: 10px;
        }
        .signature-line {
            margin-top: 40px;  /* Ruang untuk cap dan tanda tangan */
            border-top: 1px solid #000;
            width: 150px;
            display: inline-block;
        }
        .signature-name {
            font-size: 12px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header text-center">
        <h3 style="margin: 0;">BUKTI PEMBAYARAN</h3>
        <p style="margin: 5px 0;">Les Renang</p>
    </div>
    
    <div class="divider"></div>
    
    <div class="info">
        <p>No: #<?= $pembayaran['id'] ?></p>
        <p>Tanggal: <?= date('d/m/Y H:i', strtotime($pembayaran['created_at'])) ?></p>
        <p>Nama Anak: <?= $pembayaran['nama_anak'] ?></p>
        <p>Wali: <?= $pembayaran['nama_parent'] ?></p>
        <p>Jenis Les: <?= $pembayaran['nama_les'] ?></p>
    </div>
    
    <div class="divider"></div>
    
    <div class="info">
        <p>Jumlah Pertemuan: <?= $pembayaran['jumlah_pertemuan'] ?> kali</p>
        <p>Total Pembayaran: Rp <?= number_format($pembayaran['total'], 0, ',', '.') ?></p>
        <p>Metode Pembayaran: <?= $pembayaran['metode_pembayaran'] ?></p>
        <p>Status: <?= ucfirst($pembayaran['status']) ?></p>
    </div>
    
    <div class="divider"></div>
    
    <div class="footer text-center">
        <p>Terima kasih atas kepercayaan Anda</p>
        <?php if (!empty($pembayaran['berlaku_sampai'])): ?>
        <p>Berlaku hingga: <?= date('d/m/Y', strtotime($pembayaran['berlaku_sampai'])) ?></p>
        <?php endif; ?>
    </div>

    <div class="signature-space">
        <p>Petugas</p>
        <div class="signature-line"></div>
        <div class="signature-name">(_____________)</div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>