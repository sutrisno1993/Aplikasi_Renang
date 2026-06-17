<?= $this->include('admin/templates/header') ?>

<!-- Dashboard Content -->
<div class="container-fluid">
    <div class="row">
        <?php if (session()->get('role') === 'boss'): ?>
            <!-- Card Dashboard Khusus Boss -->
            <div class="col-md-4 mb-4">
                <div class="card card-dashboard bg-primary text-white border-0 shadow">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <div class="icon">
                                    <i class="fas fa-hand-holding-usd"></i>
                                </div>
                            </div>
                            <div class="col-7">
                                <?php 
                                    $db = \Config\Database::connect();
                                    $unconfirmed = $db->table('pembayaran')->where('is_confirmed_boss', 0)->where('status !=', 'rejected')->countAllResults();
                                ?>
                                <div class="number"><?= $unconfirmed ?></div>
                                <div class="card-title">Perlu Verifikasi Dana</div>
                            </div>
                        </div>
                        <a href="<?= base_url('admin/pembayaran') ?>" class="stretched-link"></a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Card Dashboard Admin Biasa -->
            <div class="col-md-3 mb-4">
                <div class="card card-dashboard bg-primary text-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <div class="icon">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                            <div class="col-7">
                                <div class="number"><?= $totalAnak ?? 0 ?></div>
                                <div class="card-title">Total Siswa</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card card-dashboard bg-success text-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <div class="icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                            <div class="col-7">
                                <div class="number"><?= $siswaAktif ?? 0 ?></div>
                                <div class="card-title">Siswa Aktif</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card card-dashboard bg-warning text-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <div class="icon">
                                    <i class="fas fa-user-clock"></i>
                                </div>
                            </div>
                            <div class="col-7">
                                <div class="number"><?= count($siswaHampirExpired) ?></div>
                                <div class="card-title">Hampir Expired</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card card-dashboard bg-danger text-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <div class="icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                            <div class="col-7">
                                <div class="number"><?= $pendingPayments ?? 0 ?></div>
                                <div class="card-title">Menunggu Approval</div>
                            </div>
                        </div>
                        <a href="<?= base_url('admin/pembayaran') ?>" class="stretched-link"></a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Visual Analytics -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                        <div>
                            <h5 class="mb-1">Analitik Siswa</h5>
                            <div class="text-muted small">Ringkasan data siswa dan performa latihan 40 hari terakhir.</div>
                        </div>
                        <span class="badge bg-dark">Periode: <?= date('d M Y', strtotime($periodeSiswaAktifDari ?? date('Y-m-d'))) ?> - <?= date('d M Y', strtotime($periodeSiswaAktifSampai ?? date('Y-m-d'))) ?></span>
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-md-3">
                            <div class="p-3 rounded bg-light border h-100">
                                <div class="text-muted small">Total Siswa</div>
                                <div class="h4 mb-0 fw-bold"><?= $totalAnak ?? 0 ?></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 rounded bg-light border h-100">
                                <div class="text-muted small">Siswa Aktif (40 Hari)</div>
                                <div class="h4 mb-0 fw-bold text-success"><?= $siswaAktif ?? 0 ?></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 rounded bg-light border h-100">
                                <div class="text-muted small">Siswa Reguler</div>
                                <div class="h4 mb-0 fw-bold text-primary"><?= $siswaReguler ?? 0 ?></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 rounded bg-light border h-100">
                                <div class="text-muted small">Siswa Private</div>
                                <div class="h4 mb-0 fw-bold text-warning"><?= $siswaPrivate ?? 0 ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-lg-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Grafik Ringkasan Siswa</h6>
                </div>
                <div class="card-body">
                    <canvas id="grafikRingkasanSiswa" height="220"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Peringkat Siswa Paling Rajin Latihan</h6>
                </div>
                <div class="card-body">
                    <canvas id="grafikPeringkatRajin" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Siswa Perlu Tagihan -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-danger shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-exclamation-circle me-2"></i> Perlu Ditagih (Siswa Aktif & Kuota Habis)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Nama Siswa</th>
                                    <th>Nama Orang Tua</th>
                                    <th>Sisa Pertemuan</th>
                                    <th>Latihan Terakhir</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($siswaPerluTagihan)): ?>
                                    <?php foreach ($siswaPerluTagihan as $siswa): ?>
                                        <?php 
                                            // Format nomor WA (ganti awalan 0 menjadi 62)
                                            $noWa = preg_replace('/[^0-9]/', '', (string)$siswa['whatsapp']);
                                            if (substr($noWa, 0, 1) === '0') {
                                                $noWa = '62' . substr($noWa, 1);
                                            }
                                            $pesanWa = "Halo Bapak/Ibu " . $siswa['nama_parent'] . ", menginformasikan bahwa kuota les renang Ananda " . $siswa['nama_anak'] . " telah habis. Latihan terakhir tercatat pada " . date('d M Y', strtotime($siswa['last_latihan'])) . ". Mohon untuk melakukan perpanjangan administrasi paket. Terima kasih.";
                                            $linkWa = "https://wa.me/" . $noWa . "?text=" . urlencode($pesanWa);
                                        ?>
                                        <tr>
                                            <td><strong><?= $siswa['nama_anak'] ?></strong></td>
                                            <td><?= $siswa['nama_parent'] ?></td>
                                            <td><span class="badge bg-danger"><?= $siswa['sisa_pertemuan'] ?> Pertemuan</span></td>
                                            <td><?= date('d M Y', strtotime($siswa['last_latihan'])) ?></td>
                                            <td>
                                                <a href="<?= $linkWa ?>" target="_blank" class="btn btn-sm btn-success">
                                                    <i class="fab fa-whatsapp"></i> Kirim Pengingat
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-check-circle text-success mb-2" style="font-size: 24px;"></i><br>
                                            Semua tagihan siswa aktif sudah lunas / tidak ada yang jatuh tempo.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Siswa Hampir Expired (Waktu) -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-warning shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0"><i class="fas fa-clock me-2"></i> Hampir Expired (Batas Waktu < 12 Hari)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Nama Siswa</th>
                                    <th>Nama Orang Tua</th>
                                    <th>Sisa Pertemuan</th>
                                    <th>Tanggal Bayar Terakhir</th>
                                    <th>Sisa Waktu</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($siswaHampirExpired)): ?>
                                    <?php foreach ($siswaHampirExpired as $index => $siswa): ?>
                                        <?php 
                                            // Format nomor WA
                                            $noWa = preg_replace('/[^0-9]/', '', (string)$siswa['whatsapp']);
                                            if (substr($noWa, 0, 1) === '0') {
                                                $noWa = '62' . substr($noWa, 1);
                                            }
                                            
                                            $daysLeft = $siswa['days_left'];
                                            $sisaSesi = $siswa['sisa_sesi'];
                                            
                                            $pesanWa = "Halo Bapak/Ibu " . $siswa['nama_parent'] . ", menginformasikan bahwa masa aktif paket les renang Ananda " . $siswa['nama_anak'] . " akan segera berakhir dalam " . $daysLeft . " hari lagi (Meskipun sisa pertemuan masih ada " . $sisaSesi . "). Mohon untuk bersiap melakukan perpanjangan. Terima kasih.";
                                            $linkWa = "https://wa.me/" . $noWa . "?text=" . urlencode($pesanWa);
                                            
                                            $isHidden = $index >= 10;
                                        ?>
                                        <tr class="<?= $isHidden ? 'd-none additional-expired-rows' : '' ?>">
                                            <td><strong><?= $siswa['nama_anak'] ?></strong></td>
                                            <td><?= $siswa['nama_parent'] ?></td>
                                            <td><span class="badge bg-info"><?= $sisaSesi ?> Pertemuan</span></td>
                                            <td><?= date('d M Y', strtotime($siswa['tanggal'])) ?></td>
                                            <td><span class="badge bg-warning text-dark"><?= $daysLeft ?> Hari Lagi</span></td>
                                            <td>
                                                <a href="<?= $linkWa ?>" target="_blank" class="btn btn-sm btn-success">
                                                    <i class="fab fa-whatsapp"></i> Kirim Notifikasi
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-check-circle text-success mb-2" style="font-size: 24px;"></i><br>
                                            Tidak ada siswa yang paket waktunya hampir habis.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if (count($siswaHampirExpired) > 10): ?>
                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btnToggleExpired">
                                <i class="fas fa-chevron-down mr-1"></i> Lihat Semua (<?= count($siswaHampirExpired) ?>)
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Siswa Tidak Aktif (> 100 Hari) -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-secondary shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-user-slash me-2"></i> Siswa Tidak Aktif (> 100 Hari Tanpa Kegiatan)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Nama Siswa</th>
                                    <th>Nama Orang Tua</th>
                                    <th>Latihan Terakhir</th>
                                    <th>Pembayaran Terakhir</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="inactiveTableBody">
                                <?php if (!empty($siswaTidakAktif)): ?>
                                    <?php foreach ($siswaTidakAktif as $index => $siswa): ?>
                                        <?php 
                                            // Format nomor WA
                                            $noWa = preg_replace('/[^0-9]/', '', (string)$siswa['whatsapp']);
                                            if (substr($noWa, 0, 1) === '0') {
                                                $noWa = '62' . substr($noWa, 1);
                                            }
                                            $pesanWa = "Halo Bapak/Ibu " . $siswa['nama_parent'] . ", dari admin renang ingin mengkonfirmasi mengenai status kepesertaan Ananda " . $siswa['nama_anak'] . ". Karena sudah lebih dari 100 hari tidak ada aktivitas latihan maupun pembayaran, apakah Ananda masih ingin melanjutkan program les renang ini? Terima kasih.";
                                            $linkWa = "https://wa.me/" . $noWa . "?text=" . urlencode($pesanWa);
                                            
                                            $latihanTerakhir = !empty($siswa['last_latihan']) ? date('d M Y', strtotime($siswa['last_latihan'])) : 'Belum pernah';
                                            $pembayaranTerakhir = !empty($siswa['last_payment_date']) ? date('d M Y', strtotime($siswa['last_payment_date'])) : 'Belum pernah';
                                            
                                            $isHidden = $index >= 10;
                                        ?>
                                        <tr class="<?= $isHidden ? 'd-none additional-inactive-rows' : '' ?>">
                                            <td><strong><?= $siswa['nama_anak'] ?></strong></td>
                                            <td><?= $siswa['nama_parent'] ?></td>
                                            <td><?= $latihanTerakhir ?></td>
                                            <td><?= $pembayaranTerakhir ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?= $linkWa ?>" target="_blank" class="btn btn-sm btn-success">
                                                        <i class="fab fa-whatsapp"></i> Hubungi
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="if(confirm('Apakah Anda yakin ingin menghapus data siswa <?= $siswa['nama_anak'] ?> secara permanen?')) window.location.href='<?= base_url('admin/anak/delete/'.$siswa['id']) ?>';">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-check-circle text-success mb-2" style="font-size: 24px;"></i><br>
                                            Tidak ada siswa yang tidak aktif lebih dari 100 hari.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if (count($siswaTidakAktif) > 10): ?>
                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btnToggleInactive">
                                <i class="fas fa-chevron-down mr-1"></i> Lihat Semua (<?= count($siswaTidakAktif) ?>)
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Pembayaran Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Anak</th>
                                    <th>Tanggal</th>
                                    <th>Jumlah</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($pembayaranTerbaru)): ?>
                                    <?php foreach ($pembayaranTerbaru as $pembayaran): ?>
                                        <?php 
                                            $isRejectedByBoss = ($pembayaran['status_approval_bos'] ?? 0) == 2;
                                        ?>
                                        <tr class="<?= ($pembayaran['status'] == 'pending' && $isRejectedByBoss) ? 'table-danger' : '' ?>">
                                            <td><?= $pembayaran['id'] ?></td>
                                            <td><?= $pembayaran['nama_anak'] ?></td>
                                            <td><?= date('d-m-Y', strtotime($pembayaran['tanggal'])) ?></td>
                                            <td>Rp <?= number_format($pembayaran['total'], 0, ',', '.') ?></td>
                                            <td>
                                                <?php if ($pembayaran['status'] == 'diterima'): ?>
                                                    <span class="badge bg-success">Diterima</span>
                                                <?php elseif ($pembayaran['status'] == 'pending'): ?>
                                                    <?php if ($isRejectedByBoss): ?>
                                                        <span class="badge bg-danger" title="Ditolak Boss: <?= esc($pembayaran['catatan_tolak_bos'] ?? '') ?>">
                                                            Ditolak Boss
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning">Pending</span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Ditolak</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada data pembayaran terbaru</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Anak Terdaftar Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>Jenis Les</th>
                                    <th>Sisa Pertemuan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($anakTerbaru)): ?>
                                    <?php foreach ($anakTerbaru as $anak): ?>
                                        <tr>
                                            <td><?= $anak['id'] ?></td>
                                            <td><?= $anak['nama'] ?></td>
                                            <td><?= $anak['nama_les'] ?></td>
                                            <td><?= $anak['sisa_pertemuan'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada data anak terbaru</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Aksi Cepat</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="<?= base_url('admin/jenis-les/create') ?>" class="btn btn-primary w-100 py-3">
                                <i class="fas fa-plus-circle me-2"></i> Tambah Jenis Les
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="#" class="btn btn-success w-100 py-3">
                                <i class="fas fa-check-circle me-2"></i> Verifikasi Pembayaran
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="#" class="btn btn-info w-100 py-3 text-white">
                                <i class="fas fa-user-plus me-2"></i> Tambah Coach
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="#" class="btn btn-secondary w-100 py-3">
                                <i class="fas fa-file-alt me-2"></i> Laporan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        $('#btnToggleInactive').on('click', function() {
            var rows = $('.additional-inactive-rows');
            var btn = $(this);
            var isHidden = rows.first().hasClass('d-none');
            
            if (isHidden) {
                rows.removeClass('d-none');
                btn.html('<i class="fas fa-chevron-up mr-1"></i> Sembunyikan');
            } else {
                rows.addClass('d-none');
                btn.html('<i class="fas fa-chevron-down mr-1"></i> Lihat Semua (' + (rows.length + 10) + ')');
            }
        });

        $('#btnToggleExpired').on('click', function() {
            var rows = $('.additional-expired-rows');
            var btn = $(this);
            var isHidden = rows.first().hasClass('d-none');
            
            if (isHidden) {
                rows.removeClass('d-none');
                btn.html('<i class="fas fa-chevron-up mr-1"></i> Sembunyikan');
            } else {
                rows.addClass('d-none');
                btn.html('<i class="fas fa-chevron-down mr-1"></i> Lihat Semua (' + (rows.length + 10) + ')');
            }
        });
    });

    (function () {
        var ringkasan = <?= json_encode($grafikRingkasanSiswa ?? ['labels' => [], 'data' => []]) ?>;
        var peringkat = <?= json_encode($grafikPeringkatRajin ?? ['labels' => [], 'data' => []]) ?>;

        var elRingkasan = document.getElementById('grafikRingkasanSiswa');
        if (elRingkasan) {
            new Chart(elRingkasan, {
                type: 'bar',
                data: {
                    labels: ringkasan.labels || [],
                    datasets: [{
                        label: 'Jumlah Siswa',
                        data: ringkasan.data || [],
                        backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        }

        var elPeringkat = document.getElementById('grafikPeringkatRajin');
        if (elPeringkat) {
            var hasData = Array.isArray(peringkat.data) && peringkat.data.length > 0;
            new Chart(elPeringkat, {
                type: 'bar',
                data: {
                    labels: hasData ? peringkat.labels : ['Belum ada data'],
                    datasets: [{
                        label: 'Total Hadir',
                        data: hasData ? peringkat.data : [0],
                        backgroundColor: '#f97316',
                        borderRadius: 8
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        }
    })();
</script>

<?= $this->include('admin/templates/footer') ?>