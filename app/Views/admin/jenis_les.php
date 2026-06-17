<?= $this->include('admin/templates/header') ?>

<!-- Jenis Les Content -->
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Jenis Les</h5>
            <a href="<?= base_url('admin/jenis-les/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> Tambah Jenis Les
            </a>
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
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Les</th>
                            <th>Harga Paket</th>
                            <th>Owner Share</th>
                            <th>Coach Share</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($jenisLes)) : ?>
                            <?php foreach ($jenisLes as $les) : ?>
                                <tr>
                                    <td><?= $les['id'] ?></td>
                                    <td class="fw-bold"><?= $les['nama_les'] ?></td>
                                    <td>Rp <?= number_format($les['harga'], 0, ',', '.') ?></td>
                                    <td class="text-success">Rp <?= number_format($les['earn_owner'], 0, ',', '.') ?></td>
                                    <td class="text-primary">Rp <?= number_format($les['earn_coach'], 0, ',', '.') ?></td>
                                    <td><small><?= $les['keterangan'] ?></small></td>
                                    <td>
                                        <a href="<?= base_url('admin/jenis-les/edit/' . $les['id']) ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('admin/jenis-les/delete/' . $les['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus jenis les ini?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data jenis les</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->include('admin/templates/footer') ?>