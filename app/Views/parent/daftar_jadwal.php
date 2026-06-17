<?= $this->extend('templates/parent') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <!-- Header dengan Icon -->
            <div class="text-center mb-4">
                <i class="fas fa-calendar-check fa-3x text-primary mb-2"></i>
                <h2 class="fw-bold">Daftar Jadwal Les</h2>
            </div>

            <div class="card">
                <!-- Detail Jadwal -->
                <div class="card-header">
                    <h4 class="mb-0 fs-2"><i class="fas fa-info-circle me-2"></i>Detail Jadwal</h4>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="card">
                            <div class="card-body fs-5">
                                <?php 
                                    $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                    $namaHari = $hari[date('w', strtotime($jadwal['tanggal']))];
                                ?>
                                <p><i class="fas fa-calendar-alt me-2 text-primary"></i> Hari: <?= $namaHari ?></p>
                                <p><i class="fas fa-calendar me-2 text-primary"></i> Tanggal: <?= date('d-m-Y', strtotime($jadwal['tanggal'])) ?></p>
                                <p><i class="fas fa-clock me-2 text-primary"></i> Waktu: <?= $jadwal['jam_mulai'] ?> - <?= $jadwal['jam_selesai'] ?></p>
                                <p><i class="fas fa-book me-2 text-primary"></i> Materi: <?= $jadwal['materi'] ?></p>
                                <p><i class="fas fa-swimming-pool me-2 text-primary"></i> Jenis Les: <?= $jadwal['jenis_les_names'] ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Daftar Coach -->
                    <div class="mb-4">
                        <h4 class="fs-2 mb-3"><i class="fas fa-chalkboard-teacher me-2 text-primary"></i>Coach yang akan mengajar</h4>
                        <div class="card">
                            <div class="card-body">
                                <?php if(empty($coaches)): ?>
                                    <p class="text-muted fs-5">Belum ada coach yang ditugaskan</p>
                                <?php else: ?>
                                    <div class="list-group">
                                        <?php foreach($coaches as $coach): ?>
                                            <div class="list-group-item fs-5">
                                                <i class="fas fa-user-tie me-2 text-primary"></i>
                                                <span class="fw-bold"><?= $coach['nama'] ?></span>
                                                <small class="text-muted ms-2">(<?= $coach['keahlian'] ?>)</small>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Form Pendaftaran -->
                    <form action="<?= base_url('parent/jadwal/proses-daftar') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="jadwal_id" value="<?= $jadwal['id'] ?>">
                        
                        <div class="form-group mb-4">
                            <h4 class="fs-2 mb-3"><i class="fas fa-child me-2 text-primary"></i>Pilih Anak</h4>
                            <?php if(empty($anak)): ?>
                                <div class="alert alert-info fs-5">
                                    Tidak ada anak yang dapat didaftarkan untuk jadwal ini. Pastikan anak memiliki jenis les yang sesuai dan masih memiliki sisa pertemuan.
                                </div>
                            <?php else: ?>
                                <div class="list-group">
                                    <?php foreach($anak as $a): ?>
                                        <label class="list-group-item d-flex justify-content-between align-items-center fs-5">
                                            <div>
                                                <input type="radio" name="anak_id" value="<?= $a['id'] ?>" class="form-check-input me-2" required>
                                                <span class="fw-bold"><?= $a['nama'] ?></span>
                                                <small class="text-muted d-block">Jenis Les: <?= $a['nama_les'] ?></small>
                                            </div>
                                            <span class="badge bg-info fs-6">Sisa: <?= $a['sisa_pertemuan'] ?> pertemuan</span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="<?= base_url('parent/dashboard') ?>" class="btn btn-secondary btn-lg">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg" <?= empty($anak) ? 'disabled' : '' ?>>
                                <i class="fas fa-check"></i> Daftar Sekarang
                            </button>
                        </div>
                    </form>

                    <!-- Daftar Peserta -->
                    <div class="mt-4">
                        <h4 class="fs-2 mb-3"><i class="fas fa-users me-2 text-primary"></i>Peserta yang Sudah Terdaftar</h4>
                        <div class="card">
                            <div class="card-body">
                                <?php if(empty($peserta)): ?>
                                    <p class="text-muted fs-5">Belum ada peserta yang terdaftar</p>
                                <?php else: ?>
                                    <div class="list-group">
                                        <?php foreach($peserta as $p): ?>
                                            <div class="list-group-item fs-5">
                                                <i class="fas fa-user me-2 text-primary"></i>
                                                <span class="fw-bold"><?= $p['nama_anak'] ?></span>
                                                <small class="text-muted ms-2">(<?= $p['nama_les'] ?>)</small>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>