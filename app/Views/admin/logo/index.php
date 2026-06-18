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

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 fw-bold text-primary">Kelola Logo Aplikasi</h5>
                <p class="text-muted small mb-0">Manajemen logo dan gambar aset yang tersimpan di cloud (Cloudflare R2)</p>
            </div>
            <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#tambahLogoModal">
                <i class="fas fa-plus me-1"></i> Tambah Logo
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-hover" id="logoTable">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th width="60">ID</th>
                            <th>Preview Gambar</th>
                            <th>Nama / Identifier</th>
                            <th>Path di Cloud</th>
                            <th>Tanggal Diunggah</th>
                            <th width="120" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logos)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada logo yang diunggah.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($logos as $logo): ?>
                            <tr>
                                <td class="text-muted fw-bold"><?= $logo['id'] ?></td>
                                <td>
                                    <?php
                                        // Gunakan helper r2_url untuk mendapatkan link gambar
                                        $urlGambar = !empty($logo['file_path']) ? r2_url($logo['file_path']) : '';
                                    ?>
                                    <?php if ($urlGambar): ?>
                                        <div class="bg-light p-2 rounded-3 border d-inline-block text-center" style="width: 120px; height: 80px;">
                                            <a href="<?= esc($urlGambar) ?>" target="_blank" title="Klik untuk melihat ukuran penuh">
                                                <img src="<?= esc($urlGambar) ?>" alt="<?= esc($logo['nama']) ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Tidak ada gambar</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark d-block"><?= esc($logo['nama']) ?></span>
                                </td>
                                <td>
                                    <code class="text-muted bg-light px-2 py-1 rounded"><?= esc($logo['file_path']) ?></code>
                                </td>
                                <td>
                                    <div class="small">
                                        <div class="fw-bold text-dark"><?= date('d M Y', strtotime($logo['created_at'])) ?></div>
                                        <div class="text-muted"><?= date('H:i', strtotime($logo['created_at'])) ?> WIB</div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-light border rounded-circle p-2 text-info me-1 shadow-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editLogoModal<?= $logo['id'] ?>"
                                            title="Edit Logo">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="<?= base_url('admin/logo/delete/' . $logo['id']) ?>" 
                                       class="btn btn-sm btn-light border rounded-circle p-2 text-danger shadow-sm" 
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus logo ini? File fisik di cloud juga akan ikut terhapus secara permanen.')"
                                       title="Hapus Logo">
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

<!-- Modal Tambah Logo -->
<div class="modal fade" id="tambahLogoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white border-0 py-3 rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="fas fa-image me-2"></i>Unggah Logo Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/logo/store') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="nama" class="form-label small fw-bold text-muted">Nama / Identifier Logo</label>
                        <input type="text" class="form-control rounded-3" id="nama" name="nama" placeholder="Contoh: Logo Utama, Background Banner, dll..." required>
                        <small class="form-text text-muted">Beri nama yang jelas agar mudah dicari.</small>
                    </div>
                    <div class="mb-3">
                        <label for="logo_file" class="form-label small fw-bold text-muted">Pilih File Gambar</label>
                        <input class="form-control rounded-3" type="file" id="logo_file" name="logo_file" accept="image/*" required>
                        <small class="form-text text-muted">Format yang didukung: JPG, PNG, GIF, WebP. File akan otomatis disimpan ke bucket <code>logo/</code>.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 rounded-bottom-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="fas fa-cloud-upload-alt me-1"></i> Unggah Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Logo -->
<?php foreach ($logos as $logo): ?>
<div class="modal fade" id="editLogoModal<?= $logo['id'] ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-info text-dark border-0 py-3 rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i>Edit Logo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/logo/update/' . $logo['id']) ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="nama<?= $logo['id'] ?>" class="form-label small fw-bold text-muted">Nama / Identifier Logo</label>
                        <input type="text" class="form-control rounded-3" id="nama<?= $logo['id'] ?>" 
                               name="nama" value="<?= esc($logo['nama']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted d-block">Gambar Saat Ini</label>
                        <?php $urlGambar = !empty($logo['file_path']) ? r2_url($logo['file_path']) : ''; ?>
                        <?php if ($urlGambar): ?>
                            <div class="bg-light p-2 rounded-3 border d-inline-block text-center mb-2" style="width: 150px; height: 100px;">
                                <img src="<?= esc($urlGambar) ?>" alt="Current Logo" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="logo_file<?= $logo['id'] ?>" class="form-label small fw-bold text-muted">Ganti File Gambar (Opsional)</label>
                        <input class="form-control rounded-3" type="file" id="logo_file<?= $logo['id'] ?>" name="logo_file" accept="image/*">
                        <small class="form-text text-warning"><i class="fas fa-exclamation-triangle me-1"></i> Biarkan kosong jika tidak ingin mengganti gambar. Mengunggah gambar baru akan <b>MENGHAPUS</b> file gambar lama di cloud secara permanen.</small>
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
    $('#logoTable').DataTable({
        "responsive": true,
        "order": [[4, "desc"]], // Urutkan berdasarkan tanggal terbaru
        "language": {
            "search": "Cari Logo:",
            "lengthMenu": "Tampilkan _MENU_ baris",
            "zeroRecords": "Tidak ada data logo yang cocok ditemukan",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ logo",
            "infoEmpty": "Menampilkan 0 sampai 0 dari 0 logo"
        }
    });
});
</script>

<?= $this->include('admin/templates/footer') ?>
