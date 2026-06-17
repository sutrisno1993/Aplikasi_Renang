<?= $this->include('admin/templates/header') ?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 fw-bold text-primary"><?= $title ?></h1>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Daftar Pembayaran Per Bulan</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" width="100%" cellspacing="0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Bulan</th>
                                    <th>Ringkasan Paket</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data_pembayaran as $row): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-primary"><?= $row['bulan_label'] ?></td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-2">
                                            <?php foreach ($row['per_paket'] as $paket): ?>
                                                <span class="badge bg-info text-dark">
                                                    <?= $paket['nama_les'] ?>: <?= $paket['jumlah_paket'] ?> paket
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('admin/report/pembayaran/detail/' . $row['bulan_val']) ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($data_pembayaran)): ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">Belum ada data pembayaran yang disetujui.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('admin/templates/footer') ?>