<?= $this->include('admin/templates/header') ?>

<?php 
$levelMap = [];
foreach ($levels as $l) {
    $levelMap[$l['id']] = $l['nama_level'];
}
?>

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

    <!-- Main Row -->
    <div class="row">
        <!-- Swimming Levels Column -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 fw-bold text-primary">Tingkatan Kurikulum</h5>
                        <p class="text-muted small mb-0">Kelola 7 level pembelajaran renang terstruktur</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="<?= base_url('admin/curriculum/ujian') ?>" class="btn btn-outline-primary rounded-pill px-3 shadow-sm">
                            <i class="fas fa-gavel me-1"></i> Ujian Kenaikan
                        </a>
                        <button class="btn btn-primary rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addLevelModal">
                            <i class="fas fa-plus me-1"></i> Tambah Level
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="80">Urutan</th>
                                    <th>Nama Level</th>
                                    <th>Deskripsi</th>
                                    <th width="120" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($levels)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Belum ada tingkatan level.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($levels as $l): ?>
                                        <tr>
                                            <td class="text-center"><span class="badge bg-secondary rounded-pill px-2 py-1"><?= esc($l['urutan']) ?></span></td>
                                            <td>
                                                <div class="fw-bold text-dark"><?= esc($l['nama_level']) ?></div>
                                            </td>
                                            <td>
                                                <small class="text-muted text-wrap d-block" style="max-width: 250px;"><?= esc($l['deskripsi']) ?></small>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-1">
                                                    <button class="btn btn-outline-warning btn-sm rounded-circle btn-edit-level" 
                                                            data-id="<?= $l['id'] ?>"
                                                            data-nama="<?= esc($l['nama_level']) ?>"
                                                            data-deskripsi="<?= esc($l['deskripsi']) ?>"
                                                            data-urutan="<?= $l['urutan'] ?>"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#editLevelModal">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <a href="<?= base_url('admin/curriculum/delete-level/' . $l['id']) ?>" 
                                                       class="btn btn-outline-danger btn-sm rounded-circle"
                                                       onclick="return confirm('Apakah Anda yakin ingin menghapus level ini?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
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

        <!-- Student Assignment Column -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold text-primary">Penugasan Level & Coach</h5>
                    <p class="text-muted small mb-0">Tentukan level kemampuan dan pelatih pendamping anak</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle table-hover" id="studentsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Anak</th>
                                    <th>Level Sekarang</th>
                                    <th>Coach Pendamping</th>
                                    <th width="80" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($students)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Belum ada data siswa aktif.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($students as $s): ?>
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark"><?= esc($s['nama']) ?></div>
                                                <small class="text-muted">Sisa Sesi: <?= esc($s['sisa_pertemuan']) ?></small>
                                            </td>
                                            <td>
                                                <?php if ($s['current_level_id']): ?>
                                                    <span class="badge bg-info text-dark rounded-pill px-2 py-1 fw-bold"><?= esc($s['nama_level']) ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-muted rounded-pill px-2 py-1">Belum di-set</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($s['assigned_coach_id']): ?>
                                                    <span class="badge bg-success rounded-pill px-2 py-1"><i class="fas fa-user-tie me-1"></i> <?= esc($s['nama_coach']) ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-muted rounded-pill px-2 py-1">Belum di-set</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm btn-assign-student"
                                                        data-id="<?= $s['id'] ?>"
                                                        data-nama="<?= esc($s['nama']) ?>"
                                                        data-level="<?= $s['current_level_id'] ?>"
                                                        data-coach="<?= $s['assigned_coach_id'] ?>"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#assignModal">
                                                    Atur
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
    </div>
</div>

<!-- =====================================================================
     MODALS SECTION
     ===================================================================== -->

<!-- Add Level Modal -->
<div class="modal fade" id="addLevelModal" tabindex="-1" aria-labelledby="addLevelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="<?= base_url('admin/curriculum/store-level') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header border-0 bg-light p-3">
                    <h5 class="modal-title fw-bold text-primary" id="addLevelModalLabel"><i class="fas fa-plus me-2"></i>Tambah Level Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="nama_level" class="form-label small fw-bold text-muted">Nama Level</label>
                        <input type="text" class="form-control rounded-3" id="nama_level" name="nama_level" required placeholder="Contoh: Level 1 : Water Introduction">
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label small fw-bold text-muted">Deskripsi Kemampuan</label>
                        <textarea class="form-control rounded-3" id="deskripsi" name="deskripsi" rows="3" placeholder="Deskripsikan fokus materi latihan pada level ini..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="urutan" class="form-label small fw-bold text-muted">Urutan Urutan Tampil</label>
                        <input type="number" class="form-control rounded-3" id="urutan" name="urutan" required min="1" value="<?= count($levels) + 1 ?>">
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Level Modal -->
<div class="modal fade" id="editLevelModal" tabindex="-1" aria-labelledby="editLevelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form id="editLevelForm" action="" method="post">
                <?= csrf_field() ?>
                <div class="modal-header border-0 bg-light p-3">
                    <h5 class="modal-title fw-bold text-warning" id="editLevelModalLabel"><i class="fas fa-edit me-2"></i>Ubah Level</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="edit_nama_level" class="form-label small fw-bold text-muted">Nama Level</label>
                        <input type="text" class="form-control rounded-3" id="edit_nama_level" name="nama_level" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_deskripsi" class="form-label small fw-bold text-muted">Deskripsi Kemampuan</label>
                        <textarea class="form-control rounded-3" id="edit_deskripsi" name="deskripsi" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_urutan" class="form-label small fw-bold text-muted">Urutan</label>
                        <input type="number" class="form-control rounded-3" id="edit_urutan" name="urutan" required min="1">
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4">Ubah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Modal (Level & Coach Assignment) -->
<div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="<?= base_url('admin/curriculum/assign') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="anak_id" id="assign_anak_id">
                <div class="modal-header border-0 bg-light p-3">
                    <h5 class="modal-title fw-bold text-primary" id="assignModalLabel"><i class="fas fa-user-edit me-2"></i>Atur Penugasan Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3 text-center">
                        <span class="text-muted d-block small">Mengatur data untuk anak:</span>
                        <h4 class="fw-bold text-dark mb-0" id="assign_student_name">-</h4>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label for="assign_level_id" class="form-label small fw-bold text-muted">Level Pembelajaran</label>
                        <select class="form-select rounded-3" id="assign_level_id" name="level_id">
                            <option value="">-- Pilih Level / Belum Di-set --</option>
                            <?php foreach ($levels as $l): ?>
                                <option value="<?= $l['id'] ?>"><?= esc($l['nama_level']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="assign_coach_id" class="form-label small fw-bold text-muted">Coach Pendamping Utama</label>
                        <select class="form-select rounded-3" id="assign_coach_id" name="coach_id">
                            <option value="">-- Pilih Coach / Belum Di-set --</option>
                            <?php foreach ($coaches as $c): ?>
                                <?php 
                                $coachLevels = [];
                                if (!empty($c['assigned_levels'])) {
                                    $ids = explode(',', $c['assigned_levels']);
                                    foreach ($ids as $id) {
                                        if (isset($levelMap[$id])) {
                                            $coachLevels[] = explode(':', $levelMap[$id])[0];
                                        }
                                    }
                                }
                                $levelsLabel = !empty($coachLevels) ? implode(', ', $coachLevels) : 'Belum Ditugaskan';
                                ?>
                                <option value="<?= $c['id'] ?>"><?= esc($c['nama']) ?> (Tanggung Jawab: <?= esc($levelsLabel) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Penugasan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Populate Level edit modal
    $('.btn-edit-level').click(function() {
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        var deskripsi = $(this).data('deskripsi');
        var urutan = $(this).data('urutan');
        
        $('#editLevelForm').attr('action', '<?= base_url('admin/curriculum/update-level/') ?>/' + id);
        $('#edit_nama_level').val(nama);
        $('#edit_deskripsi').val(deskripsi);
        $('#edit_urutan').val(urutan);
    });

    // Populate student assign modal
    $('.btn-assign-student').click(function() {
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        var level = $(this).data('level');
        var coach = $(this).data('coach');
        
        $('#assign_anak_id').val(id);
        $('#assign_student_name').text(nama);
        $('#assign_level_id').val(level);
        $('#assign_coach_id').val(coach);
    });

    // Student list DataTable
    $('#studentsTable').DataTable({
        "responsive": true,
        "language": {
            "search": "Cari Siswa:",
            "lengthMenu": "Tampilkan _MENU_ baris",
            "zeroRecords": "Tidak ada siswa aktif yang cocok ditemukan",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ siswa",
            "infoEmpty": "Menampilkan 0 sampai 0 dari 0 siswa"
        }
    });
});
</script>

<?= $this->include('admin/templates/footer') ?>
