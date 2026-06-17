<?= $this->extend('templates/parent') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Detail Jadwal Les</h4>
                    <a href="<?= base_url('parent/dashboard') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <!-- Informasi Jadwal -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2 mb-3">Informasi Jadwal</h5>
                            <table class="table">
                                <tr>
                                    <th width="30%">Tanggal</th>
                                    <td>
                                        <?php 
                                        $hari = date('l', strtotime($jadwal['tanggal']));
                                        $hari_indo = [
                                            'Sunday' => 'Minggu',
                                            'Monday' => 'Senin',
                                            'Tuesday' => 'Selasa',
                                            'Wednesday' => 'Rabu',
                                            'Thursday' => 'Kamis',
                                            'Friday' => 'Jumat',
                                            'Saturday' => 'Sabtu'
                                         ];
                                         echo $hari_indo[$hari] . ', ' . date('d-m-Y', strtotime($jadwal['tanggal']));
                                         ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Waktu</th>
                                    <td><?= date('H:i', strtotime($jadwal['jam_mulai'])) ?> - <?= date('H:i', strtotime($jadwal['jam_selesai'])) ?></td>
                                </tr>
                                <tr>
                                    <th>Materi</th>
                                    <td><?= $jadwal['materi'] ?></td>
                                </tr>
                                <tr>
                                    <th>Kapasitas</th>
                                    <td><?= $jadwal['kapasitas'] ?> orang</td>
                                </tr>
                                <tr>
                                    <th>Jenis Latihan</th>
                                    <td><span class="badge bg-info"><?= ucfirst($jadwal['jenis_latihan']) ?></span></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <?php if($jadwal['status'] == 'aktif'): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Tidak Aktif</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <!-- Coach yang Bertugas -->
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2 mb-3">Coach yang Bertugas</h5>
                            <div class="list-group">
                                <?php foreach($coaches as $coach): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user-tie text-primary me-2"></i>
                                            <div>
                                                <strong><?= $coach['nama'] ?></strong>
                                                <br>
                                                <small class="text-muted">Keahlian: <?= $coach['keahlian'] ?></small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Peserta Terdaftar -->
                    <div class="row">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 mb-3">Peserta Terdaftar</h5>
                            <?php if(!empty($peserta)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>Jenis Les</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($peserta as $index => $p): ?>
                                                <tr>
                                                    <td><?= $index + 1 ?></td>
                                                    <td><?= $p['nama_anak'] ?></td>
                                                    <td><span class="badge bg-info"><?= $p['nama_les'] ?></span></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Belum ada peserta terdaftar
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<!-- Di bagian atas file, ganti script Bootstrap -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Tambahkan script di bagian bawah file -->
<script>
$(document).ready(function() {
    // Handler untuk tombol close modal
    $('.close').click(function() {
        $('#daftarAnakModal').modal('hide');
    });
    
    // Handler untuk tombol Batal
    $('[data-dismiss="modal"]').click(function() {
        $('#daftarAnakModal').modal('hide');
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>