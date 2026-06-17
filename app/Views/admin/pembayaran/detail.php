<?= $this->include('admin/templates/header') ?>

<!-- Detail Pembayaran Content -->
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Detail Pembayaran</h5>
            <div>
                <a href="<?= base_url('admin/pembayaran/cetak/' . $pembayaran['id']) ?>" 
                   class="btn btn-info" 
                   target="_blank">
                    <i class="fas fa-print"></i> Cetak
                </a>
                <a href="<?= base_url('admin/pembayaran') ?>" 
                   class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">ID Pembayaran</th>
                            <td><?= $pembayaran['id'] ?></td>
                        </tr>
                        <tr>
                            <th>Nama Anak</th>
                            <td><?= $pembayaran['nama_anak'] ?></td>
                        </tr>
                        <tr>
                            <th>Nama Orang Tua</th>
                            <td><?= $pembayaran['nama_parent'] ?></td>
                        </tr>
                        <tr>
                            <th>Jenis Les</th>
                            <td><?= $pembayaran['nama_les'] ?? 'Tidak ada' ?></td>
                        </tr>
                        <tr>
                            <th>Tanggal</th>
                            <td><?= date('d-m-Y', strtotime($pembayaran['tanggal'])) ?></td>
                        </tr>
                        <tr>
                            <th>Jumlah Pertemuan</th>
                            <td><?= $pembayaran['jumlah_pertemuan'] ?> kali</td>
                        </tr>
                        <tr>
                            <th>Total Pembayaran</th>
                            <td>Rp <?= number_format($pembayaran['total'], 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <th>Metode Pembayaran</th>
                            <td><?= $pembayaran['metode_pembayaran'] ?></td>
                        </tr>
                        <tr>
                            <th>Berlaku Sampai</th>
                            <td>
                                <?php if ($pembayaran['status'] == 'success' && !empty($pembayaran['berlaku_sampai'])) : ?>
                                    <?= date('d-m-Y', strtotime($pembayaran['berlaku_sampai'])) ?>
                                <?php else : ?>
                                    <span class="text-muted">Belum disetujui</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <?php if ($pembayaran['status'] == 'pending') : ?>
                                    <span class="badge bg-warning">Pending</span>
                                <?php elseif ($pembayaran['status'] == 'success') : ?>
                                    <span class="badge bg-success">Diterima</span>
                                <?php else : ?>
                                    <span class="badge bg-danger">Ditolak</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Catatan</th>
                            <td><?= $pembayaran['catatan'] ?? '-' ?></td>
                        </tr>
                        <tr>
                            <th>Tanggal Dibuat</th>
                            <td><?= date('d-m-Y H:i', strtotime($pembayaran['created_at'])) ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h6 class="mb-0">Bukti Pembayaran</h6>
                        </div>
                        <div class="card-body text-center">
                            <?php if (!empty($pembayaran['bukti_pembayaran'])) : ?>
                                <img src="<?= r2_url($pembayaran['bukti_pembayaran'], 'bukti_pembayaran') ?>" alt="Bukti Pembayaran" class="img-fluid" style="max-height: 400px;">
                                <div class="mt-3">
                                    <a href="<?= r2_url($pembayaran['bukti_pembayaran'], 'bukti_pembayaran') ?>" class="btn btn-primary" target="_blank">
                                        <i class="fas fa-eye"></i> Lihat Gambar Asli
                                    </a>
                                </div>
                            <?php else : ?>
                                <div class="alert alert-warning">
                                    Tidak ada bukti pembayaran yang diunggah
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('admin/pembayaran') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                            <div>
                                <a href="<?= base_url('admin/pembayaran/approve/' . $pembayaran['id']) ?>" class="btn btn-success" onclick="return confirm('Apakah Anda yakin ingin menyetujui pembayaran ini?')">
                                    <i class="fas fa-check me-1"></i> Approve
                                </a>
                                <a href="<?= base_url('admin/pembayaran/reject/' . $pembayaran['id']) ?>" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menolak pembayaran ini?')">
                                    <i class="fas fa-times me-1"></i> Reject
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('admin/templates/footer') ?>