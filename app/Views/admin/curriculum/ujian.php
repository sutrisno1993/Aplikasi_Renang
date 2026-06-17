<?= $this->include('admin/templates/header') ?>

<div class="container-fluid py-4">
    <!-- Notifications -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2 fs-5"></i>
                <div><?= session()->getFlashdata('success') ?></div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-2 fs-5"></i>
                <div><?= session()->getFlashdata('error') ?></div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Recommended/Pending Exams List Column -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 fw-bold text-primary">Daftar Ujian Kenaikan Tingkat</h5>
                        <p class="text-muted small mb-0">Kelola pengajuan rekomendasi dan hasil ujian kenaikan tingkat</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle table-hover" id="examsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Nama Siswa</th>
                                    <th>Tingkat</th>
                                    <th>Penguji</th>
                                    <th class="text-center">Status Kelulusan</th>
                                    <th width="120" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($exams)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">Belum ada riwayat ujian kenaikan tingkat.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($exams as $ex): ?>
                                        <tr>
                                            <td><?= date('d/m/Y', strtotime($ex['tanggal'])) ?></td>
                                            <td class="fw-bold text-dark"><?= esc($ex['nama_anak']) ?></td>
                                            <td>
                                                <span class="badge bg-light text-secondary border"><?= esc($ex['level_asal_nama']) ?></span>
                                                <i class="fas fa-arrow-right mx-1 text-muted small"></i>
                                                <span class="badge bg-info text-dark"><?= esc($ex['level_tujuan_nama']) ?></span>
                                            </td>
                                            <td class="fw-bold"><i class="fas fa-user-tie text-muted me-1"></i> <?= esc($ex['nama_examiner'] ?? 'Belum ditentukan') ?></td>
                                            <td class="text-center">
                                                <?php if ($ex['status_kelulusan'] === 'pending'): ?>
                                                    <span class="badge bg-warning text-dark rounded-pill px-2 py-1"><i class="fas fa-clock me-1"></i> Pending</span>
                                                <?php elseif ($ex['status_kelulusan'] === 'lulus'): ?>
                                                    <span class="badge bg-success rounded-pill px-2 py-1"><i class="fas fa-check-circle me-1"></i> Lulus</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger rounded-pill px-2 py-1"><i class="fas fa-times-circle me-1"></i> Gagal</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($ex['status_kelulusan'] === 'pending'): ?>
                                                    <a href="<?= base_url('admin/curriculum/ujian/evaluasi/' . $ex['id']) ?>" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                                                        <i class="fas fa-gavel me-1"></i> Nilai
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted small">Sudah Dinilai</span>
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

        <!-- Schedule / Recommend Exam Column -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold text-primary">Daftar Ujian Baru</h5>
                    <p class="text-muted small mb-0">Jadwalkan ujian kenaikan tingkat langsung oleh Admin</p>
                </div>
                <form action="<?= base_url('admin/curriculum/ujian/rekomendasi') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="tanggal" class="form-label small fw-bold text-muted">Tanggal Ujian (Serempak)</label>
                            <input type="date" class="form-select rounded-3" name="tanggal" id="tanggal" value="<?= esc($tanggal_ujian) ?>" required>
                            <div class="mt-2 p-2 bg-light border rounded-3 small">
                                <span class="d-block text-secondary font-weight-bold mb-1"><i class="fas fa-calendar-alt text-primary me-1"></i> Lookback Absensi (3 Bulan):</span>
                                <span class="text-dark fw-bold"><?= date('d/m/Y', strtotime($tanggal_ujian . ' -3 months')) ?></span> 
                                <span class="text-muted">s.d.</span> 
                                <span class="text-dark fw-bold"><?= date('d/m/Y', strtotime($tanggal_ujian)) ?></span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="anak_id" class="form-label small fw-bold text-muted">Siswa Ujian</label>
                            <select class="form-select rounded-3" name="anak_id" id="anak_id" required>
                                <option value="">-- Pilih Siswa --</option>
                                <?php foreach ($students as $s): ?>
                                    <option value="<?= $s['id'] ?>" <?= !$s['is_eligible'] ? 'disabled style="color: #999;"' : '' ?>>
                                        <?= esc($s['nama']) ?> (<?= esc($s['nama_level'] ?? 'Belum Di-set') ?> | Sesi: <?= $s['attended_sessions'] ?>/<?= $s['min_sessions'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted d-block mt-1">Siswa yang berwarna abu-abu belum memenuhi syarat jumlah minimal sesi latihan (<?= $min_sessions ?> kali) di level saat ini.</small>
                        </div>
                        <div class="mb-3">
                            <label for="examiner_id" class="form-label small fw-bold text-muted">Coach Penguji</label>
                            <select class="form-select rounded-3" name="examiner_id" id="examiner_id" required>
                                <option value="">-- Pilih Penguji --</option>
                                <?php foreach ($coaches as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= esc($c['nama']) ?> (<?= esc($c['role'] === 'head_coach' ? 'Head Coach' : 'Coach') ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-0 py-3 text-end rounded-bottom-4">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">Jadwalkan Ujian</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#examsTable').DataTable({
        "responsive": true,
        "order": [[0, "desc"]],
        "language": {
            "search": "Cari Ujian:",
            "lengthMenu": "Tampilkan _MENU_ baris",
            "zeroRecords": "Tidak ada data ujian yang cocok ditemukan",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ ujian",
            "infoEmpty": "Menampilkan 0 sampai 0 dari 0 ujian"
        }
    });

    $('#tanggal').on('change', function() {
        var selectedDate = $(this).val();
        window.location.href = '<?= base_url("admin/curriculum/ujian") ?>?tanggal=' + selectedDate;
    });
});
</script>

<?= $this->include('admin/templates/footer') ?>
