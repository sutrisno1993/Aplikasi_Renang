<?= $this->include('admin/templates/header') ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Detail Jadwal</h5>
            <div>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editJadwalModal">
                    <i class="fas fa-edit"></i> Edit Jadwal
                </button>
                <a href="<?= base_url('admin/jadwal') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <?php if(session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if(session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Tanggal</th>
                            <td><?= isset($jadwal['tanggal']) ? date('d-m-Y', strtotime($jadwal['tanggal'])) : 'Tidak ada data' ?></td>
                        </tr>
                        <tr>
                            <th>Waktu</th>
                            <td><?= isset($jadwal['jam_mulai']) && isset($jadwal['jam_selesai']) ? $jadwal['jam_mulai'] . ' - ' . $jadwal['jam_selesai'] : 'Tidak ada data' ?></td>
                        </tr>
                        <tr>
                            <th>Jenis Les</th>
                            <td>
                                <?php 
                                if(isset($jadwal['jenis_les_names'])): 
                                    $jenis_les_array = explode(',', $jadwal['jenis_les_names']);
                                    foreach($jenis_les_array as $les): ?>
                                        <span class="badge bg-info me-1"><?= $les ?></span>
                                <?php 
                                    endforeach;
                                endif; 
                                ?>
                            </td>
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
                
                <!-- Tambahkan section untuk daftar siswa -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h6 class="mb-0">Daftar Siswa Terdaftar</h6>
                        </div>
                        <div class="card-body">
                            <?php if(isset($jadwal['schedule_students']) && !empty($jadwal['schedule_students'])): ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Siswa</th>
                                                <th>Nama Panggilan</th>
                                                <th>Jenis Les</th>
                                                <th>Sisa Pertemuan</th>
                                                <th>Status</th>
                                                <th>Catatan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $no = 1; foreach($jadwal['schedule_students'] as $student): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><?= $student['nama_anak'] ?? '-' ?></td>
                                                <td><?= $student['nama_panggilan'] ?? '-' ?></td>
                                                <td><?= $student['jenis_les_nama'] ?? '-' ?></td>
                                                <td><?= $student['sisa_pertemuan'] ?? '0' ?></td>
                                                <td>
                                                    <?php if($student['status'] == 'hadir'): ?>
                                                        <span class="badge bg-success">Hadir</span>
                                                    <?php elseif($student['status'] == 'tidak_hadir'): ?>
                                                        <span class="badge bg-danger">Tidak Hadir</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning">Belum Diabsen</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= $student['catatan'] ?? '-' ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    Belum ada siswa yang terdaftar pada jadwal ini.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Jadwal -->
<div class="modal fade" id="editJadwalModal" tabindex="-1" aria-labelledby="editJadwalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editJadwalModalLabel">Edit Jadwal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('admin/jadwal/update/' . $jadwal['id']) ?>" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= $jadwal['tanggal'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="jam_mulai" class="form-label">Jam Mulai</label>
                        <input type="time" class="form-control" id="jam_mulai" name="jam_mulai" value="<?= $jadwal['jam_mulai'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="jam_selesai" class="form-label">Jam Selesai</label>
                        <input type="time" class="form-control" id="jam_selesai" name="jam_selesai" value="<?= $jadwal['jam_selesai'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="jenis_les" class="form-label">Jenis Les</label>
                        <select class="form-select" id="jenis_les" name="jenis_les[]" multiple required>
                            <?php foreach($jenis_les as $les): ?>
                                <option value="<?= $les['id'] ?? '' ?>" 
                                    <?= isset($jadwal['jenis_les_ids']) && in_array($les['id'] ?? '', explode(',', $jadwal['jenis_les_ids'])) ? 'selected' : '' ?>>
                                    <?= $les['nama'] ?? 'Tidak ada nama' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="materi" class="form-label">Materi</label>
                        <textarea class="form-control" id="materi" name="materi" required><?= $jadwal['materi'] ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="kapasitas" class="form-label">Kapasitas</label>
                        <input type="number" class="form-control" id="kapasitas" name="kapasitas" value="<?= $jadwal['kapasitas'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="aktif" <?= $jadwal['status'] == 'aktif' ? 'selected' : '' ?>>Aktif</option>
                            <option value="nonaktif" <?= $jadwal['status'] == 'nonaktif' ? 'selected' : '' ?>>Tidak Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->include('admin/templates/footer') ?>