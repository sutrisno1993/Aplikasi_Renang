<?= $this->include('admin/templates/header') ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Kedatangan (Jadwal Selesai)</h5>
        </div>
        <div class="card-body">
            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body bg-light rounded">
                    <form action="<?= base_url('admin/kedatangan/edit') ?>" method="get" class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold text-primary"><i class="fas fa-calendar-day me-1"></i> Pilih Tanggal Latihan</label>
                            <input type="date" name="tanggal" class="form-control form-control-lg border-0 shadow-sm" value="<?= $filter['tanggal'] ?? '' ?>">
                        </div>
                        <div class="col-md-4 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary btn-lg flex-grow-1 shadow-sm">
                                <i class="fas fa-search me-1"></i> Cari Jadwal
                            </button>
                            <a href="<?= base_url('admin/kedatangan/edit') ?>" class="btn btn-secondary btn-lg shadow-sm">
                                <i class="fas fa-undo"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Menampilkan jadwal yang sudah berstatus <strong>Selesai</strong>. Gunakan menu ini untuk mengoreksi absensi anak yang terlewat.
            </div>

            <div class="table-responsive">
                <table class="table table-hover" id="jadwalTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Hari</th>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Materi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($jadwal)) : ?>
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada jadwal yang selesai</td>
                            </tr>
                        <?php else : ?>
                            <?php $i = 1; foreach($jadwal as $j): ?>
                            <?php 
                                $hari = [
                                    'Sunday' => 'Minggu',
                                    'Monday' => 'Senin',
                                    'Tuesday' => 'Selasa',
                                    'Wednesday' => 'Rabu',
                                    'Thursday' => 'Kamis',
                                    'Friday' => 'Jumat',
                                    'Saturday' => 'Sabtu'
                                ];
                                $namaHari = $hari[date('l', strtotime($j['tanggal']))];
                            ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= $namaHari ?></td>
                                <td><?= date('d-m-Y', strtotime($j['tanggal'])) ?></td>
                                <td><?= date('H:i', strtotime($j['jam_mulai'])) ?> - <?= date('H:i', strtotime($j['jam_selesai'])) ?></td>
                                <td><?= $j['materi'] ?></td>
                                <td>
                                    <span class="badge bg-secondary">Selesai</span>
                                </td>
                                <td>
                                    <a href="<?= base_url('admin/kedatangan/edit-absensi/' . $j['id']) ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i> Edit Absensi
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

<?= $this->include('admin/templates/footer') ?>

<script>
$(document).ready(function() {
    $('#jadwalTable').DataTable({
        "order": [[2, "desc"]]
    });
});
</script>
