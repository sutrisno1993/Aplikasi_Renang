<?= $this->extend('templates/coach') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2 fs-5"></i>
                <div><?= session()->getFlashdata('success') ?></div>
            </div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-2 fs-5"></i>
                <div><?= session()->getFlashdata('error') ?></div>
            </div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Head Coach Panel (Only for Head Coach) -->
    <?php if ($coach['role'] === 'head_coach'): ?>
        <div class="card border-0 shadow-sm rounded-lg mb-4">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="mb-0 font-weight-bold"><i class="fas fa-user-shield mr-2"></i>Evaluasi Ujian Kenaikan (Head Coach)</h5>
                <p class="mb-0 small text-white-50">Menilai pengajuan rekomendasi kenaikan tingkat dari pelatih pendamping</p>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small uppercase font-weight-bold">
                            <tr>
                                <th>Tanggal Pengajuan</th>
                                <th>Nama Anak</th>
                                <th>Rekomendasi Tingkat</th>
                                <th>Pelatih Pengaju</th>
                                <th class="text-center" width="150">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pendingExams)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Belum ada pengajuan rekomendasi ujian yang tertunda (pending).</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($pendingExams as $pe): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($pe['tanggal'])) ?></td>
                                        <td class="font-weight-bold text-dark"><?= esc($pe['nama_anak']) ?></td>
                                        <td>
                                            <span class="badge badge-light border text-primary"><?= esc($pe['level_asal_nama']) ?></span>
                                            <i class="fas fa-arrow-right mx-1 small text-muted"></i>
                                            <span class="badge badge-primary"><?= esc($pe['level_tujuan_nama']) ?></span>
                                        </td>
                                        <td class="font-weight-bold"><i class="fas fa-user-tie text-muted mr-1"></i> <?= esc($pe['nama_coach']) ?></td>
                                        <td class="text-center">
                                            <a href="<?= base_url('coach/ujian/evaluasi/' . $pe['id']) ?>" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                                                <i class="fas fa-gavel mr-1"></i> Nilai Ujian
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Recommending form -->
        <div class="col-md-5 mb-4">
            <div class="card border-0 shadow-sm rounded-lg">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="mb-0 font-weight-bold"><i class="fas fa-file-signature mr-2"></i>Rekomendasi Naik Level</h5>
                    <p class="mb-0 small text-white-50">Kirim rekomendasi ujian kenaikan tingkat ke Head Coach</p>
                </div>
                <form action="<?= base_url('coach/ujian/rekomendasi') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label for="tanggal" class="form-label font-weight-bold small text-muted">Tanggal Ujian (Serempak)</label>
                            <input type="date" class="form-control" name="tanggal" id="tanggal" value="<?= esc($tanggal_ujian) ?>" required>
                            <div class="mt-2 p-2 bg-light border rounded small">
                                <span class="d-block text-secondary font-weight-bold mb-1"><i class="fas fa-calendar-alt text-success mr-1"></i> Lookback Absensi (3 Bulan):</span>
                                <span class="text-dark font-weight-bold"><?= date('d/m/Y', strtotime($tanggal_ujian . ' -3 months')) ?></span> 
                                <span class="text-muted">s.d.</span> 
                                <span class="text-dark font-weight-bold"><?= date('d/m/Y', strtotime($tanggal_ujian)) ?></span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="anak_id" class="form-label font-weight-bold small text-muted">Pilih Siswa</label>
                            <select class="form-control" name="anak_id" id="anak_id" required>
                                <option value="">-- Pilih Siswa Didik --</option>
                                <?php foreach ($students as $s): ?>
                                    <option value="<?= $s['id'] ?>" <?= !$s['is_eligible'] ? 'disabled style="color: #999;"' : '' ?>>
                                        <?= esc($s['nama']) ?> (<?= esc($s['nama_level'] ?? 'No Level') ?> | Sesi: <?= $s['attended_sessions'] ?>/<?= $s['min_sessions'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Siswa yang berwarna abu-abu belum memenuhi syarat jumlah minimal sesi latihan (<?= $min_sessions ?> kali) di level saat ini.</small>
                            <small class="form-text text-muted">Siswa yang muncul adalah anak didik yang secara sah ditugaskan kepada Anda oleh Admin.</small>
                        </div>
                    </div>
                    <div class="card-footer bg-light p-3 text-right">
                        <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm">Kirim Pengajuan</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tanggalInput = document.getElementById('tanggal');
            if (tanggalInput) {
                tanggalInput.addEventListener('change', function() {
                    var selectedDate = this.value;
                    window.location.href = '<?= base_url("coach/ujian") ?>?tanggal=' + selectedDate;
                });
            }
        });
        </script>

        <!-- My Recommendations History -->
        <div class="col-md-7 mb-4">
            <div class="card border-0 shadow-sm rounded-lg">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                    <div>
                        <h5 class="mb-0 font-weight-bold text-success"><i class="fas fa-history mr-2"></i>Riwayat Pengajuan Ujian</h5>
                        <p class="mb-0 small text-muted">Daftar rekomendasi ujian yang pernah Anda daftarkan</p>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small uppercase font-weight-bold">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Anak</th>
                                    <th>Level Dituju</th>
                                    <th class="text-center">Status Kelulusan</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                <?php if (empty($myRecommendations)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-5">Belum ada pengajuan rekomendasi ujian.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($myRecommendations as $mr): ?>
                                        <tr>
                                            <td><?= date('d/m/Y', strtotime($mr['tanggal'])) ?></td>
                                            <td class="font-weight-bold text-dark"><?= esc($mr['nama_anak']) ?></td>
                                            <td><span class="badge badge-info"><?= esc($mr['level_tujuan_nama']) ?></span></td>
                                            <td class="text-center">
                                                <?php if ($mr['status_kelulusan'] === 'pending'): ?>
                                                    <span class="badge badge-warning rounded-pill px-2 py-1"><i class="fas fa-clock mr-1"></i> Menunggu Ujian</span>
                                                <?php elseif ($mr['status_kelulusan'] === 'lulus'): ?>
                                                    <span class="badge badge-success rounded-pill px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Lulus Naik Level</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger rounded-pill px-2 py-1"><i class="fas fa-times-circle mr-1"></i> Tidak Lulus</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
