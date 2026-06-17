<?= $this->include('admin/templates/header') ?>

<div class="container-fluid">
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-5 text-center">
            <div class="mb-4">
                <i class="fas fa-tools text-warning" style="font-size: 80px;"></i>
            </div>
            <h2 class="fw-bold text-dark mb-3"><?= $title ?></h2>
            <p class="text-muted fs-5 mb-4">Fitur ini sedang dalam pengembangan khusus untuk akses Boss.</p>
            <div class="badge bg-light text-warning border border-warning px-4 py-2 fs-6 rounded-pill">
                <i class="fas fa-hourglass-half me-2"></i> Ready Soon
            </div>
            <div class="mt-5">
                <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-primary rounded-pill px-5 py-2">
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->include('admin/templates/footer') ?>
