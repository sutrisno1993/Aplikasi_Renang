<?= $this->include('admin/templates/header') ?>

<style>
.bukti-img {
    cursor: pointer;
    transition: transform 0.2s;
}
.bukti-img:hover {
    opacity: 0.8;
}
</style>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 fw-bold text-primary"><?= $title ?></h1>
        <a href="<?= base_url('admin/report/pembayaran') ?>" class="btn btn-dark btn-sm rounded-pill px-3 shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="row">
        <!-- Summary Cards -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card bg-primary text-white border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1 opacity-75">Total Pendapatan</div>
                            <div class="h3 mb-0 fw-bold">Rp <?= number_format($summary['total_pendapatan'] ?? 0, 0, ',', '.') ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card bg-success text-white border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1 opacity-75">Bagian Kolam (Owner)</div>
                            <div class="h3 mb-0 fw-bold">Rp <?= number_format($summary['total_owner'] ?? 0, 0, ',', '.') ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card bg-warning text-white border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1 opacity-75">Bagian Coach</div>
                            <div class="h3 mb-0 fw-bold">Rp <?= number_format($summary['total_coach'] ?? 0, 0, ',', '.') ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Riwayat Pembayaran - <?= date('F Y', strtotime($bulan . '-01')) ?></h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" width="100%" cellspacing="0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Tanggal</th>
                                    <th>Bukti</th>
                                    <th>Nama Siswa</th>
                                    <th>Jenis Les</th>
                                    <th class="text-end">Total Bayar</th>
                                    <th class="text-end text-success">Bagian Kolam</th>
                                    <th class="text-end text-primary">Bagian Coach</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pembayaran as $row): ?>
                                <tr>
                                    <td class="ps-4"><?= date('d/m/Y H:i', strtotime($row['tanggal'])) ?></td>
                                    <td>
                                        <?php if (!empty($row['bukti_pembayaran'])) : ?>
                                            <img src="<?= r2_url($row['bukti_pembayaran'], 'pembayaran') ?>" 
                                                 alt="Bukti" 
                                                 class="img-thumbnail bukti-img" 
                                                 style="width: 50px; height: 50px; object-fit: cover;"
                                                 onclick="zoomImage(this.src)">
                                        <?php else : ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-bold"><?= $row['nama_anak'] ?></td>
                                    <td><span class="badge bg-info text-dark"><?= $row['nama_les'] ?></span></td>
                                    <td class="text-end fw-bold text-dark">Rp <?= number_format($row['total'], 0, ',', '.') ?></td>
                                    <td class="text-end text-success fw-bold">Rp <?= number_format($row['earn_owner'], 0, ',', '.') ?></td>
                                    <td class="text-end text-primary fw-bold">Rp <?= number_format($row['earn_coach'], 0, ',', '.') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="bg-light fw-bold">
                                <tr>
                                    <td colspan="4" class="text-end ps-4">TOTAL BULAN INI:</td>
                                    <td class="text-end text-dark">Rp <?= number_format($summary['total_pendapatan'] ?? 0, 0, ',', '.') ?></td>
                                    <td class="text-end text-success">Rp <?= number_format($summary['total_owner'] ?? 0, 0, ',', '.') ?></td>
                                    <td class="text-end text-primary">Rp <?= number_format($summary['total_coach'] ?? 0, 0, ',', '.') ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tambahkan Modal untuk menampilkan gambar -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body text-center p-0">
                <img id="modalImage" src="" alt="Bukti Pembayaran" class="img-fluid rounded shadow-lg" style="max-height: 90vh;">
                <div class="mt-3">
                    <button type="button" class="btn btn-light btn-sm rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                    <a id="downloadImage" href="" target="_blank" class="btn btn-primary btn-sm rounded-pill ms-2 px-4">Buka di Tab Baru / Download</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function zoomImage(imageUrl) {
    document.getElementById('modalImage').src = imageUrl;
    document.getElementById('downloadImage').href = imageUrl;
    var imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
    imageModal.show();
}
</script>

<?= $this->include('admin/templates/footer') ?>