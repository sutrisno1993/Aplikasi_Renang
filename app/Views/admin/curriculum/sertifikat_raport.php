<?= $this->include('admin/templates/header') ?>

<div class="container-fluid py-4">
    <!-- Notifications -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2 fs-5"></i>
                <div><?= session()->getFlashdata('success') ?></div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-2 fs-5"></i>
                <div><?= session()->getFlashdata('error') ?></div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0 fw-bold text-primary">Sertifikat & Raport Kelulusan</h5>
            <p class="text-muted small mb-0">Cari dan cetak sertifikat kelulusan beserta raport evaluasi kenaikan tingkat siswa</p>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-hover" id="sertifikatTable">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Siswa</th>
                            <th>Tingkat Kelulusan</th>
                            <th>Nomor Sertifikat</th>
                            <th width="280" class="text-center">Aksi Dokumen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($studentCerts)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Belum ada sertifikat/raport terbit untuk siswa.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($studentCerts as $item): ?>
                                <?php foreach ($item['certs'] as $c): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark"><?= esc($item['student']['nama']) ?></div>
                                            <small class="text-muted">Panggilan: <?= esc($item['student']['nama_panggilan']) ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-success rounded-pill px-3 py-1 fw-bold">
                                                <?= esc($c['nama_level']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <code class="text-dark bg-light px-2 py-1 rounded small border fw-bold"><?= esc($c['nomor_sertifikat']) ?></code>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= base_url('admin/curriculum/certificate/print/' . $item['student']['id'] . '/' . $c['level_id']) ?>" target="_blank" class="btn btn-sm btn-primary rounded-pill px-3 me-2 shadow-sm">
                                                <i class="fas fa-certificate me-1"></i> Cetak Sertifikat
                                            </a>
                                            <a href="<?= base_url('admin/curriculum/raport/print/' . $item['student']['id'] . '/' . $c['level_id']) ?>" target="_blank" class="btn btn-sm btn-outline-info rounded-pill px-3 shadow-sm">
                                                <i class="fas fa-file-invoice me-1"></i> Cetak Raport
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#sertifikatTable').DataTable({
        "responsive": true,
        "language": {
            "search": "Cari Data:",
            "lengthMenu": "Tampilkan _MENU_ baris",
            "zeroRecords": "Tidak ada data sertifikat/raport yang cocok ditemukan",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data"
        }
    });
});
</script>

<?= $this->include('admin/templates/footer') ?>
