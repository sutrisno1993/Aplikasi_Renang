<?= $this->include('admin/templates/header') ?>

<div class="container-fluid">
    <!-- Summary Cards -->
    <div class="row">
        <div class="col-md-4">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <h6 class="text-uppercase small fw-bold">Total Pendapatan</h6>
                    <h2 class="fw-bold mb-0">Rp <?= number_format($summary['total_pendapatan'] ?? 0, 0, ',', '.') ?></h2>
                    <p class="mb-0 small opacity-75">Berdasarkan filter tanggal</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-gradient-success text-white">
                <div class="card-body">
                    <h6 class="text-uppercase small fw-bold">Bagian Owner (Maret)</h6>
                    <h2 class="fw-bold mb-0">Rp <?= number_format($summary['total_owner'] ?? 0, 0, ',', '.') ?></h2>
                    <p class="mb-0 small opacity-75">Net Profit Owner</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-gradient-warning text-white">
                <div class="card-body">
                    <h6 class="text-uppercase small fw-bold">Bagian Pelatih</h6>
                    <h2 class="fw-bold mb-0">Rp <?= number_format($summary['total_coach'] ?? 0, 0, ',', '.') ?></h2>
                    <p class="mb-0 small opacity-75">Total Komisi Pelatih</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body bg-light rounded-4">
            <form action="" method="get" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Mulai Tanggal</label>
                    <input type="date" name="tgl_mulai" class="form-control" value="<?= $filter['tgl_mulai'] ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Sampai Tanggal</label>
                    <input type="date" name="tgl_selesai" class="form-control" value="<?= $filter['tgl_selesai'] ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="fas fa-sync"></i> Update Laporan
                    </button>
                    <button type="button" onclick="window.print()" class="btn btn-dark">
                        <i class="fas fa-print"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0 fw-bold">Rincian Transaksi Selesai</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Tanggal</th>
                            <th>Nama Anak</th>
                            <th>Jenis Paket</th>
                            <th class="text-end">Total Bayar</th>
                            <th class="text-end text-success">Owner</th>
                            <th class="text-end text-primary">Coach</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pembayaran as $p): ?>
                        <tr>
                            <td class="ps-4"><?= date('d/m/Y', strtotime($p['tanggal'])) ?></td>
                            <td class="fw-bold"><?= $p['nama_anak'] ?></td>
                            <td><span class="badge bg-info text-dark"><?= $p['nama_les'] ?></span></td>
                            <td class="text-end fw-bold">Rp <?= number_format($p['total'], 0, ',', '.') ?></td>
                            <td class="text-end text-success">Rp <?= number_format($p['earn_owner'], 0, ',', '.') ?></td>
                            <td class="text-end text-primary">Rp <?= number_format($p['earn_coach'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($pembayaran)): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">Tidak ada transaksi pada periode ini</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->include('admin/templates/footer') ?>
