<?= $this->include('admin/templates/header') ?>

<!-- Tambah Jenis Les Content -->
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0">Tambah Jenis Les</h5>
        </div>
        <div class="card-body">
            <?php if (session()->has('errors')) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        <?php foreach (session('errors') as $error) : ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <form action="<?= base_url('admin/jenis-les/store') ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="mb-3">
                    <label for="nama_les" class="form-label">Nama Les</label>
                    <input type="text" class="form-control" id="nama_les" name="nama_les" value="<?= old('nama_les') ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="harga" class="form-label">Harga Paket (Rp)</label>
                    <input type="number" class="form-control" id="harga" name="harga" value="<?= old('harga') ?>" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="earn_owner" class="form-label text-success fw-bold">Bagi Hasil Owner (Rp)</label>
                        <input type="number" class="form-control border-success" id="earn_owner" name="earn_owner" value="<?= old('earn_owner', 0) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="earn_coach" class="form-label text-primary fw-bold">Bagi Hasil Coach (Rp)</label>
                        <input type="number" class="form-control border-primary" id="earn_coach" name="earn_coach" value="<?= old('earn_coach', 0) ?>" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <textarea class="form-control" id="keterangan" name="keterangan" rows="4" required><?= old('keterangan') ?></textarea>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="<?= base_url('admin/jenis-les') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->include('admin/templates/footer') ?>