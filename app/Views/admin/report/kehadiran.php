<?= $this->include('admin/templates/header') ?>

<div class="container-fluid">
    <div class="row mb-4">
        <?php foreach($stat as $s): ?>
        <?php 
            $color = 'primary';
            if($s['status_kehadiran'] == 'hadir') $color = 'success';
            if($s['status_kehadiran'] == 'izin') $color = 'warning';
            if($s['status_kehadiran'] == 'tidak_hadir') $color = 'danger';
        ?>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-uppercase small fw-bold text-muted"><?= ucfirst(str_replace('_', ' ', $s['status_kehadiran'])) ?></h6>
                    <h2 class="fw-bold text-<?= $color ?>"><?= $s['jumlah'] ?></h2>
                    <p class="mb-0 small">Siswa</p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Filter -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body bg-light rounded-4">
            <form action="" method="get" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Dari Tanggal</label>
                    <input type="date" name="tgl_mulai" class="form-control" value="<?= $filter['tgl_mulai'] ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Sampai Tanggal</label>
                    <input type="date" name="tgl_selesai" class="form-control" value="<?= $filter['tgl_selesai'] ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Tampilkan Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0 fw-bold">Riwayat Kehadiran Detail</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Tanggal</th>
                            <th>Nama Siswa</th>
                            <th>Paket Les</th>
                            <th>Status</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($kehadiran as $k): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold"><?= date('d/m/Y', strtotime($k['tanggal'])) ?></div>
                                <small class="text-muted"><?= date('H:i', strtotime($k['jam_mulai'])) ?> WIB</small>
                            </td>
                            <td class="fw-bold"><?= $k['nama_anak'] ?></td>
                            <td><?= $k['nama_les'] ?></td>
                            <td>
                                <?php 
                                    $badge = 'secondary';
                                    if($k['status_kehadiran'] == 'hadir') $badge = 'success';
                                    if($k['status_kehadiran'] == 'izin') $badge = 'warning';
                                    if($k['status_kehadiran'] == 'tidak_hadir') $badge = 'danger';
                                ?>
                                <span class="badge bg-<?= $badge ?>"><?= ucfirst(str_replace('_', ' ', $k['status_kehadiran'])) ?></span>
                            </td>
                            <td><small class="text-muted"><?= $k['catatan'] ?: '-' ?></small></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($kehadiran)): ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada data kehadiran</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->include('admin/templates/footer') ?>
