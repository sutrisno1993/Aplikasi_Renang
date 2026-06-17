<?= $this->include('admin/templates/header') ?>

<div class="container-fluid">
    <div class="row">
        <!-- Form Pengaturan -->
        <div class="col-md-5">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-cog me-2"></i>Konfigurasi Aplikasi</h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('admin/settings/update') ?>" method="post">
                        <?= csrf_field() ?>
                        <?php foreach($settings as $s): ?>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-uppercase small text-muted"><?= str_replace('_', ' ', $s['key']) ?></label>
                            <?php if($s['key'] == 'registration_fee'): ?>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0">Rp</span>
                                    <input type="number" name="settings[<?= $s['key'] ?>]" class="form-control border-0 bg-light" value="<?= $s['value'] ?>">
                                </div>
                            <?php else: ?>
                                <input type="text" name="settings[<?= $s['key'] ?>]" class="form-control border-0 bg-light" value="<?= $s['value'] ?>">
                            <?php endif; ?>
                            <div class="form-text small"><?= $s['description'] ?></div>
                        </div>
                        <?php endforeach; ?>
                        
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-3">
                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>

            <!-- Card Baru: Maintenance -->
            <div class="card shadow-sm border-0 rounded-4 mt-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-tools me-2"></i>Pemeliharaan Data</h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted">Gunakan fitur ini untuk mensinkronisasi ulang sisa pertemuan seluruh siswa berdasarkan riwayat pembayaran dan kehadiran terbaru (Rumus: Pembayaran x 4 - Kehadiran).</p>
                    <a href="<?= base_url('admin/settings/sync-all') ?>" class="btn btn-warning w-100 py-2 fw-bold rounded-3 text-dark" onclick="return confirm('Apakah Anda yakin ingin melakukan sinkronisasi ulang seluruh data siswa? Proses ini mungkin memakan waktu.')">
                        <i class="fas fa-sync me-2"></i>Sinkronisasi Ulang Semua Siswa
                    </a>
                </div>
            </div>
        </div>

        <!-- Log Aktivitas -->
        <div class="col-md-7">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-history me-2"></i>Log Aktivitas Admin</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 600px;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light sticky-top">
                                <tr>
                                    <th class="ps-4">Waktu</th>
                                    <th>Admin</th>
                                    <th>Aksi</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($logs as $l): ?>
                                <tr>
                                    <td class="ps-4 small">
                                        <div class="fw-bold"><?= date('d/m/Y', strtotime($l['created_at'])) ?></div>
                                        <div class="text-muted"><?= date('H:i', strtotime($l['created_at'])) ?> WIB</div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border"><?= $l['admin_nama'] ?: 'System' ?></span></td>
                                    <td><span class="badge bg-primary-light text-primary"><?= $l['action'] ?></span></td>
                                    <td class="small text-muted"><?= $l['description'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('admin/templates/footer') ?>
