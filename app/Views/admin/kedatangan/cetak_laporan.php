<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .print-container { max-width: 800px; margin: 30px auto; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
        .header-report { text-align: center; border-bottom: 2px solid #4F46E5; padding-bottom: 20px; margin-bottom: 30px; }
        .logo-report { max-width: 100px; margin-bottom: 10px; }
        .table thead { background-color: #4F46E5; color: white; }
        .btn-wa { background-color: #25D366; color: white; border: none; }
        .btn-wa:hover { background-color: #128C7E; color: white; }
        @media print {
            .no-print { display: none; }
            body { background: white; }
            .print-container { box-shadow: none; margin: 0; width: 100%; max-width: 100%; }
        }
    </style>
</head>
<body>

<div class="container no-print mt-4 mb-2 text-center">
    <button onclick="window.print()" class="btn btn-dark shadow-sm">
        <i class="fas fa-print"></i> Cetak PDF / Print
    </button>
    <button onclick="sendToWhatsApp()" class="btn btn-wa shadow-sm ms-2">
        <i class="fab fa-whatsapp"></i> Kirim ke Group WA
    </button>
    <a href="<?= base_url('admin/kedatangan/edit-absensi/' . $jadwal['id']) ?>" class="btn btn-secondary shadow-sm ms-2">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="print-container">
    <div class="header-report">
        <img src="<?= base_url('logo.png') ?>" alt="Logo" class="logo-report">
        <h3 class="fw-bold mb-0">11 MARET SPORT CENTER</h3>
        <p class="text-muted mb-0">Les Private Renang - HR Swimming Family</p>
    </div>

    <div class="row mb-4">
        <div class="col-6">
            <h6 class="fw-bold text-uppercase text-primary small">Informasi Latihan</h6>
            <table class="table table-sm table-borderless">
                <tr><td width="100">Hari</td><td>: <?= [
                    'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 
                    'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
                ][date('l', strtotime($jadwal['tanggal']))] ?></td></tr>
                <tr><td>Tanggal</td><td>: <?= date('d F Y', strtotime($jadwal['tanggal'])) ?></td></tr>
                <tr><td>Waktu</td><td>: <?= date('H:i', strtotime($jadwal['jam_mulai'])) ?> - <?= date('H:i', strtotime($jadwal['jam_selesai'])) ?> WIB</td></tr>
            </table>
        </div>
        <div class="col-6 text-end">
            <h6 class="fw-bold text-uppercase text-primary small">Materi Latihan</h6>
            <p class="mb-0"><?= $jadwal['materi'] ?: '-' ?></p>
        </div>
    </div>

    <table class="table table-bordered align-middle">
        <thead>
            <tr class="text-center">
                <th width="50">No</th>
                <th>Nama Peserta</th>
                <th>Jenis Les</th>
                <th width="120">Pertemuan Ke-</th>
                <th width="120">Sisa Sesi</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($kehadiran)): ?>
                <tr><td colspan="5" class="text-center py-3">Tidak ada data kehadiran</td></tr>
            <?php else: ?>
                <?php $i = 1; foreach($kehadiran as $k): ?>
                <tr>
                    <td class="text-center"><?= $i++ ?></td>
                    <td>
                        <strong><?= $k['nama'] ?></strong>
                    </td>
                    <td><?= $k['nama_les'] ?: '-' ?></td>
                    <td class="text-center fw-bold text-primary"><?= $k['pertemuan_ke'] ?></td>
                    <td class="text-center fw-bold <?= $k['sisa_pertemuan'] <= 1 ? 'text-danger' : 'text-success' ?>"><?= $k['sisa_pertemuan'] ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="row mt-4">
        <div class="col-6">
            <div class="p-3 border rounded bg-light" style="width: 250px;">
                <h6 class="fw-bold text-primary small mb-2 text-uppercase">Ringkasan Kehadiran</h6>
                <div class="d-flex justify-content-between mb-1 small">
                    <span>Total Peserta:</span>
                    <span class="fw-bold"><?= $summary['total'] ?></span>
                </div>
                <div class="d-flex justify-content-between mb-1 small text-danger">
                    <span>Private:</span>
                    <span class="fw-bold"><?= $summary['private'] ?></span>
                </div>
                <div class="d-flex justify-content-between small text-primary">
                    <span>Reguler:</span>
                    <span class="fw-bold"><?= $summary['reguler'] ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5 row">
        <div class="col-8">
            <p class="small text-muted italic">
                * Laporan ini digenerate otomatis melalui Sistem Aplikasi Renang 11 Maret Sport Center.<br>
                * <strong>Catatan:</strong> Apabila terdapat ketidaksesuaian data, mohon segera menghubungi Admin (japri) untuk proses koreksi.
            </p>
        </div>
        <div class="col-4 text-center">
            <p class="mb-5 small fw-bold">Instruktur / Admin</p>
            <br>
            <p class="mt-4 mb-0 fw-bold border-top pt-2">HR Swimming Family</p>
        </div>
    </div>
</div>

<script>
function sendToWhatsApp() {
    let hari = "<?= [
        'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 
        'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
    ][date('l', strtotime($jadwal['tanggal']))] ?>";
    let tanggal = "<?= date('d/m/Y', strtotime($jadwal['tanggal'])) ?>";
    
    let text = "*LAPORAN LATIHAN RENANG*\n";
    text += "*11 MARET SPORT CENTER*\n\n";
    text += "Alhamdulillah, latihan hari ini telah selesai dilaksanakan pada:\n";
    text += "📅 Hari: " + hari + "\n";
    text += "🗓️ Tanggal: " + tanggal + "\n";
    text += "⏰ Waktu: <?= date('H:i', strtotime($jadwal['jam_mulai'])) ?> - <?= date('H:i', strtotime($jadwal['jam_selesai'])) ?> WIB\n";
    text += "🏊 Materi: <?= $jadwal['materi'] ?: '-' ?>\n\n";
    text += "*DAFTAR PESERTA LATIHAN:*\n";
    
    <?php $i = 1; foreach($kehadiran as $k): ?>
    text += "<?= $i++ ?>. *<?= $k['nama'] ?>* (<?= $k['nama_les'] ?>) - Ke-<?= $k['pertemuan_ke'] ?> (Sisa: *<?= $k['sisa_pertemuan'] ?>*)\n";
    <?php endforeach; ?>
    
    text += "\nTerima kasih atas semangat dan kerja kerasnya hari ini! Sampai jumpa di jadwal latihan berikutnya. 🙏😊\n\n";
    text += "--- _Pesan Otomatis Sistem_ ---\n";
    text += "_Kehadiran berdasarkan absensi kartu oleh admin dan absensi tatapmuka oleh coach._\n";
    text += "_*Catatan:* Apabila terdapat ketidaksesuaian data pada laporan di atas, mohon segera menghubungi (japri) Admin 08981274514 untuk proses koreksi. Terima kasih._";
    
    let encodedText = encodeURIComponent(text);
    window.open("https://wa.me/?text=" + encodedText, '_blank');
}
</script>

</body>
</html>
