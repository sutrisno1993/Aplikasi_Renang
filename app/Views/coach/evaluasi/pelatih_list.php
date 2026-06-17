<?= $this->extend('templates/coach') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-lg mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle mr-2 fs-5"></i>
                <div><?= session()->getFlashdata('success') ?></div>
            </div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-lg mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle mr-2 fs-5"></i>
                <div><?= session()->getFlashdata('error') ?></div>
            </div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Master Level Mapping Associative Array -->
    <?php 
    $levelMap = [];
    foreach ($levels as $l) {
        $levelMap[$l['id']] = $l['nama_level'];
    }
    ?>

    <div class="card border-0 shadow-sm rounded-lg">
        <div class="card-header bg-primary text-white py-3">
            <h5 class="mb-0 font-weight-bold"><i class="fas fa-user-shield mr-2"></i>Manajemen Tanggung Jawab Pelatih</h5>
            <p class="mb-0 small text-white-50">Sebagai Head Coach, Anda memiliki wewenang untuk mengatur pembagian tugas dan level tanggung jawab setiap pelatih pendamping</p>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small uppercase font-weight-bold">
                        <tr>
                            <th width="80">ID</th>
                            <th>Nama Pelatih</th>
                            <th>No. Telepon / WhatsApp</th>
                            <th>Pengalaman</th>
                            <th>Tanggung Jawab Level Didik</th>
                            <th width="100" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($coaches)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">Belum ada data pelatih terdaftar.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($coaches as $c): ?>
                            <tr>
                                <td class="text-muted font-weight-bold">#<?= $c['id'] ?></td>
                                <td>
                                    <span class="font-weight-bold text-dark d-block"><?= esc($c['nama']) ?></span>
                                    <span class="badge <?= $c['role'] === 'head_coach' ? 'badge-primary' : 'badge-secondary' ?> small">
                                        <?= $c['role'] === 'head_coach' ? 'Head Coach' : 'Coach' ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="font-weight-bold"><i class="fab fa-whatsapp text-success mr-1"></i><?= esc($c['telepon']) ?></span>
                                </td>
                                <td>
                                    <span class="text-muted font-weight-bold"><?= esc($c['pengalaman']) ?> Tahun</span>
                                </td>
                                <td>
                                    <?php 
                                    $assignedNames = [];
                                    if (!empty($c['assigned_levels'])) {
                                        $ids = explode(',', $c['assigned_levels']);
                                        foreach ($ids as $id) {
                                            if (isset($levelMap[$id])) {
                                                $nameParts = explode(':', $levelMap[$id]);
                                                $assignedNames[$id] = trim($nameParts[0]);
                                            }
                                        }
                                    }
                                    ?>
                                    <?php if (empty($assignedNames)): ?>
                                        <span class="badge badge-light border text-secondary px-2 py-1 rounded small">
                                            <i class="fas fa-exclamation-triangle text-warning mr-1"></i> Belum ditugaskan level
                                        </span>
                                    <?php else: ?>
                                        <div class="d-flex flex-wrap" style="gap: 4px;">
                                            <?php foreach ($assignedNames as $id => $name): ?>
                                                <span class="badge badge-info px-2 py-1 small rounded-pill">
                                                    <?= esc($name) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm"
                                            data-toggle="modal"
                                            data-target="#editResponsibilityModal<?= $c['id'] ?>">
                                        <i class="fas fa-tasks mr-1"></i> Atur Level
                                    </button>
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

<!-- Modal Edit Responsibility (Untuk setiap pelatih) -->
<?php foreach ($coaches as $c): ?>
<div class="modal fade" id="editResponsibilityModal<?= $c['id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg rounded-lg">
            <div class="modal-header bg-success text-white border-0 py-3">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-tasks mr-2"></i>Atur Tanggung Jawab Pelatih</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('coach/pelatih/update/' . $c['id']) ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold text-muted small d-block">Nama Pelatih</label>
                        <input type="text" class="form-control" value="<?= esc($c['nama']) ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold text-muted small d-block mb-2"><i class="fas fa-swimming-pool text-success mr-1"></i> Level Tanggung Jawab</label>
                        <div class="row g-2 p-3 bg-light rounded border">
                            <?php 
                            $curLevels = !empty($c['assigned_levels']) ? explode(',', $c['assigned_levels']) : [];
                            foreach ($levels as $l): 
                            ?>
                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="assigned_levels[]" value="<?= $l['id'] ?>" id="hc_level_edit_<?= $c['id'] ?>_<?= $l['id'] ?>" <?= in_array($l['id'], $curLevels) ? 'checked' : '' ?>>
                                    <label class="form-check-label small text-dark font-weight-bold" for="hc_level_edit_<?= $c['id'] ?>_<?= $l['id'] ?>">
                                        <?= esc(explode(':', $l['nama_level'])[0]) ?>
                                    </label>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <small class="form-text text-muted mt-2 d-block">Mencentang level akan mengizinkan pelatih ini untuk membimbing dan memberikan evaluasi perkembangan mingguan serta merekomendasikan ujian kenaikan tingkat bagi siswa yang berada di level tersebut.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm">Simpan Tugas</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?= $this->endSection() ?>
