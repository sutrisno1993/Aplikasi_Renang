<?= $this->extend('templates/parent') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Riwayat Latihan</h5>
            <div>
                <a href="<?= base_url('parent/dashboard') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <?php foreach ($riwayat_per_anak as $anak_id => $data): ?>
                <div class="mb-4">
                    <h5 class="border-bottom pb-2"><?= $data['nama_anak'] ?></h5>
                    <?php if (empty($data['riwayat'])): ?>
                        <div class="alert alert-info">
                            Belum ada riwayat latihan untuk <?= $data['nama_anak'] ?>.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Waktu</th>
                                        <th>Pelatih</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $total_latihan = count($data['riwayat']);
                                    foreach($data['riwayat'] as $index => $r): 
                                        $nomor_latihan = $total_latihan - $index;
                                    ?>
                                    <tr>
                                        <td><strong>Latihan Renang <?= $nomor_latihan ?></strong></td>
                                        <td>
                                            <?php 
                                                $hari = date('l', strtotime($r['tanggal']));
                                                $hari_indo = [
                                                    'Sunday' => 'Minggu',
                                                    'Monday' => 'Senin',
                                                    'Tuesday' => 'Selasa',
                                                    'Wednesday' => 'Rabu',
                                                    'Thursday' => 'Kamis',
                                                    'Friday' => 'Jumat',
                                                    'Saturday' => 'Sabtu'
                                                ];
                                                echo $hari_indo[$hari] . ', ' . date('d-m-Y', strtotime($r['tanggal']));
                                            ?>
                                        </td>
                                        <td><?= $r['jam_mulai'] ? date('H:i', strtotime($r['jam_mulai'])) : '' ?> - <?= $r['jam_selesai'] ? date('H:i', strtotime($r['jam_selesai'])) : '' ?></td>
                                        <td><?= $r['nama_coach'] ?? '-' ?></td>
                                        <td><?= $r['catatan'] ?? '-' ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>