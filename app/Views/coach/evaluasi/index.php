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

    <div class="row">
        <!-- Assigned Students Column -->
        <div class="col-md-5 mb-4">
            <div class="card border-0 shadow-sm rounded-lg">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="mb-0 font-weight-bold"><i class="fas fa-users mr-2"></i>Anak Didik Anda</h5>
                    <p class="mb-0 small text-white-50">Siswa yang saat ini Anda bimbing di level aktif</p>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush rounded-bottom">
                        <?php if (empty($students)): ?>
                            <div class="text-center text-muted py-5 px-3">
                                <i class="fas fa-user-slash fs-1 text-light mb-3 d-block"></i>
                                Belum ada siswa yang ditugaskan kepada Anda. Hubungi Admin.
                            </div>
                        <?php else: ?>
                            <?php foreach ($students as $s): ?>
                                <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3">
                                    <div>
                                        <h6 class="font-weight-bold text-dark mb-1"><?= esc($s['nama']) ?></h6>
                                        <span class="badge badge-info rounded-pill px-2 py-1 small"><?= esc($s['nama_level'] ?? 'Belum di-set') ?></span>
                                    </div>
                                    <a href="<?= base_url('coach/evaluasi/input/' . $s['id']) ?>" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm">
                                        <i class="fas fa-edit mr-1"></i> Input Nilai
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Evaluations Submitted -->
        <div class="col-md-7 mb-4">
            <div class="card border-0 shadow-sm rounded-lg">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                    <div>
                        <h5 class="mb-0 font-weight-bold text-success"><i class="fas fa-history mr-2"></i>Riwayat Evaluasi</h5>
                        <p class="mb-0 small text-muted">Evaluasi perkembangan mingguan yang sudah Anda buat</p>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small uppercase font-weight-bold">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Anak</th>
                                    <th>Level</th>
                                    <th class="text-center">Kaki/Tangan/Nafas</th>
                                    <th class="text-center">Keberanian/Fokus</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                <?php if (empty($evaluations)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">Belum ada evaluasi mingguan yang Anda buat.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($evaluations as $ev): ?>
                                        <tr>
                                            <td class="text-nowrap font-weight-bold text-secondary"><?= date('d/m/Y', strtotime($ev['tanggal'])) ?></td>
                                            <td class="font-weight-bold text-dark"><?= esc($ev['nama_anak']) ?></td>
                                            <td><span class="badge badge-light text-secondary"><?= esc($ev['nama_level']) ?></span></td>
                                            <td class="text-center font-weight-bold text-primary">
                                                <?= $ev['teknik_kaki'] ?> / <?= $ev['teknik_tangan'] ?> / <?= $ev['teknik_pernapasan'] ?>
                                            </td>
                                            <td class="text-center font-weight-bold text-success">
                                                <?= $ev['keberanian'] ?> / <?= $ev['sikap_fokus'] ?>
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
