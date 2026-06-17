<?= $this->include('admin/templates/header') ?>

<div class="container-fluid py-4">
    <!-- Flash Notifications -->
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2 fs-5"></i>
                <div><?= session()->getFlashdata('success') ?></div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-2 fs-5"></i>
                <div><?= session()->getFlashdata('error') ?></div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Master Level Mapping Associative Array -->
    <?php 
    $levelMap = [];
    foreach ($levels as $l) {
        $levelMap[$l['id']] = $l['nama_level'];
    }
    ?>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 fw-bold text-primary">Daftar Pelatih (Coach)</h5>
                <p class="text-muted small mb-0">Kelola informasi pelatih, hak akses, dan pembagian tugas level tanggung jawab</p>
            </div>
            <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#tambahCoachModal">
                <i class="fas fa-plus me-1"></i> Tambah Pelatih
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-hover" id="coachTable">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th width="60">ID</th>
                            <th>Nama Pelatih</th>
                            <th>Email</th>
                            <th>No. Telepon</th>
                            <th>Pengalaman</th>
                            <th>Tanggung Jawab Level</th>
                            <th width="120" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($coaches)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Belum ada data pelatih terdaftar.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($coaches as $coach): ?>
                            <tr>
                                <td class="text-muted fw-bold"><?= $coach['id'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded-circle p-2 me-2 text-primary">
                                            <i class="fas fa-user-tie"></i>
                                        </div>
                                        <div>
                                            <span class="fw-bold text-dark d-block"><?= esc($coach['nama']) ?></span>
                                            <span class="badge <?= $coach['role'] === 'head_coach' ? 'bg-primary' : 'bg-secondary' ?> rounded-pill small">
                                                <?= $coach['role'] === 'head_coach' ? 'Head Coach' : 'Coach' ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td><?= esc($coach['email']) ?></td>
                                <td>
                                    <span class="fw-bold text-dark"><i class="fab fa-whatsapp text-success me-1"></i><?= esc($coach['telepon']) ?></span>
                                </td>
                                <td>
                                    <span class="fw-bold text-secondary"><?= esc($coach['pengalaman']) ?> Tahun</span>
                                </td>
                                <td>
                                    <?php 
                                    $assignedNames = [];
                                    if (!empty($coach['assigned_levels'])) {
                                        $ids = explode(',', $coach['assigned_levels']);
                                        foreach ($ids as $id) {
                                            if (isset($levelMap[$id])) {
                                                $nameParts = explode(':', $levelMap[$id]);
                                                $assignedNames[$id] = trim($nameParts[0]);
                                            }
                                        }
                                    }
                                    ?>
                                    <?php if (empty($assignedNames)): ?>
                                        <span class="badge bg-light text-secondary border px-2 py-1 rounded-3 small">
                                            <i class="fas fa-exclamation-triangle text-warning me-1"></i> Belum ditugaskan
                                        </span>
                                    <?php else: ?>
                                        <div class="d-flex flex-wrap gap-1">
                                            <?php foreach ($assignedNames as $id => $name): ?>
                                                <span class="badge bg-info text-dark rounded-pill px-2 py-1 small">
                                                    <?= esc($name) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-light border rounded-circle p-2 text-info me-1 shadow-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editCoachModal<?= $coach['id'] ?>"
                                            title="Edit Data Pelatih">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="<?= base_url('admin/coach/delete/' . $coach['id']) ?>" 
                                       class="btn btn-sm btn-light border rounded-circle p-2 text-danger shadow-sm" 
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus pelatih ini? Siswa yang dibimbing oleh pelatih ini akan kehilangan bimbingan pelatih.')"
                                       title="Hapus Pelatih">
                                        <i class="fas fa-trash"></i>
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
</div>

<!-- Modal Tambah Coach -->
<div class="modal fade" id="tambahCoachModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white border-0 py-3 rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="fas fa-user-plus me-2"></i>Tambah Pelatih Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/coach/save') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="nama" class="form-label small fw-bold text-muted">Nama Lengkap</label>
                        <input type="text" class="form-control rounded-3" id="nama" name="nama" placeholder="Masukkan nama pelatih..." required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label small fw-bold text-muted">Alamat Email</label>
                        <input type="email" class="form-control rounded-3" id="email" name="email" placeholder="contoh: pelatih@gmail.com" required>
                    </div>
                    <div class="mb-3">
                        <label for="telepon" class="form-label small fw-bold text-muted">No. Telepon / WhatsApp</label>
                        <input type="text" class="form-control rounded-3" id="telepon" name="telepon" placeholder="628xxxxxxxxxx" required>
                    </div>
                    <div class="mb-3">
                        <label for="alamat" class="form-label small fw-bold text-muted">Alamat Tempat Tinggal</label>
                        <textarea class="form-control rounded-3" id="alamat" name="alamat" rows="2" placeholder="Masukkan alamat lengkap..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="pengalaman" class="form-label small fw-bold text-muted">Pengalaman Mengajar (Tahun)</label>
                        <input type="number" class="form-control rounded-3" id="pengalaman" name="pengalaman" min="0" placeholder="Contoh: 3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small d-block fw-bold text-muted mb-2"><i class="fas fa-swimmer text-primary me-1"></i> Tanggung Jawab Level Didik</label>
                        <div class="row g-2 p-2 bg-light rounded-3 border">
                            <?php foreach ($levels as $l): ?>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="assigned_levels[]" value="<?= $l['id'] ?>" id="level_add_<?= $l['id'] ?>">
                                    <label class="form-check-label small text-dark fw-semibold" for="level_add_<?= $l['id'] ?>">
                                        <?= esc(explode(':', $l['nama_level'])[0]) ?>
                                    </label>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <small class="form-text text-muted">Pelatih hanya diperkenankan membimbing dan mengevaluasi siswa pada level-level yang dicentang.</small>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label small fw-bold text-muted">Password Akun</label>
                        <input type="password" class="form-control rounded-3" id="password" name="password" minlength="6" placeholder="Minimal 6 karakter..." required>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 rounded-bottom-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">Simpan Pelatih</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Coach -->
<?php foreach ($coaches as $coach): ?>
<div class="modal fade" id="editCoachModal<?= $coach['id'] ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-info text-dark border-0 py-3 rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="fas fa-user-edit me-2"></i>Edit Informasi Pelatih</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/coach/update/' . $coach['id']) ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="nama<?= $coach['id'] ?>" class="form-label small fw-bold text-muted">Nama Lengkap</label>
                        <input type="text" class="form-control rounded-3" id="nama<?= $coach['id'] ?>" 
                               name="nama" value="<?= esc($coach['nama']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email<?= $coach['id'] ?>" class="form-label small fw-bold text-muted">Alamat Email</label>
                        <input type="email" class="form-control rounded-3" id="email<?= $coach['id'] ?>" 
                               name="email" value="<?= esc($coach['email']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="telepon<?= $coach['id'] ?>" class="form-label small fw-bold text-muted">No. Telepon / WhatsApp</label>
                        <input type="text" class="form-control rounded-3" id="telepon<?= $coach['id'] ?>" 
                               name="telepon" value="<?= esc($coach['telepon']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="alamat<?= $coach['id'] ?>" class="form-label small fw-bold text-muted">Alamat Tempat Tinggal</label>
                        <textarea class="form-control rounded-3" id="alamat<?= $coach['id'] ?>" 
                                  name="alamat" rows="2" required><?= esc($coach['alamat']) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="pengalaman<?= $coach['id'] ?>" class="form-label small fw-bold text-muted">Pengalaman Mengajar (Tahun)</label>
                        <input type="number" class="form-control rounded-3" id="pengalaman<?= $coach['id'] ?>" 
                               name="pengalaman" value="<?= esc($coach['pengalaman']) ?>" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small d-block fw-bold text-muted mb-2"><i class="fas fa-swimmer text-info me-1"></i> Tanggung Jawab Level Didik</label>
                        <div class="row g-2 p-2 bg-light rounded-3 border">
                            <?php 
                            $curLevels = !empty($coach['assigned_levels']) ? explode(',', $coach['assigned_levels']) : [];
                            foreach ($levels as $l): 
                            ?>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="assigned_levels[]" value="<?= $l['id'] ?>" id="level_edit_<?= $coach['id'] ?>_<?= $l['id'] ?>" <?= in_array($l['id'], $curLevels) ? 'checked' : '' ?>>
                                    <label class="form-check-label small text-dark fw-semibold" for="level_edit_<?= $coach['id'] ?>_<?= $l['id'] ?>">
                                        <?= esc(explode(':', $l['nama_level'])[0]) ?>
                                    </label>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <small class="form-text text-muted">Pelatih hanya diperkenankan membimbing dan mengevaluasi siswa pada level-level yang dicentang.</small>
                    </div>
                    <div class="mb-3">
                        <label for="password<?= $coach['id'] ?>" class="form-label small fw-bold text-muted">Password Baru (Opsional)</label>
                        <input type="password" class="form-control rounded-3" id="password<?= $coach['id'] ?>" name="password" minlength="6" placeholder="Masukkan jika ingin mengubah password...">
                        <small class="form-text text-muted">Kosongkan jika tidak ingin merubah password akun pelatih.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 rounded-bottom-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info rounded-pill px-4 shadow-sm text-dark fw-bold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<script>
$(document).ready(function() {
    $('#coachTable').DataTable({
        "responsive": true,
        "language": {
            "search": "Cari Pelatih:",
            "lengthMenu": "Tampilkan _MENU_ baris",
            "zeroRecords": "Tidak ada data pelatih yang cocok ditemukan",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ pelatih",
            "infoEmpty": "Menampilkan 0 sampai 0 dari 0 pelatih"
        }
    });
});
</script>

<?= $this->include('admin/templates/footer') ?>