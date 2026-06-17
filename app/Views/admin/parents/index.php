<?= $this->include('admin/templates/header') ?>

<div class="container-fluid">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold text-primary">Manajemen Orang Tua</h5>
            <form action="" method="get" class="d-flex align-items-center gap-2 mb-0" id="perPageForm">
                <label for="per_page" class="small fw-bold text-muted text-nowrap mb-0">Tampilkan:</label>
                <select name="per_page" id="per_page" class="form-select form-select-sm rounded-pill border shadow-sm px-3" style="width: auto;" onchange="this.form.submit()">
                    <option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10 baris</option>
                    <option value="25" <?= $perPage == 25 ? 'selected' : '' ?>>25 baris</option>
                    <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50 baris</option>
                    <option value="100" <?= $perPage == 100 ? 'selected' : '' ?>>100 baris</option>
                </select>
            </form>
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
                <table class="table table-hover align-middle" id="tabelParents">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Nama Orang Tua</th>
                            <th>No. WhatsApp</th>
                            <th>Nama Anak (Panggilan)</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1 + (($pager->getCurrentPage() - 1) * $perPage); foreach ($parents as $p) : ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td class="fw-bold"><?= esc($p['nama']) ?></td>
                                <td>
                                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $p['whatsapp']) ?>" target="_blank" class="text-decoration-none">
                                        <i class="fab fa-whatsapp text-success me-1"></i> <?= esc($p['whatsapp']) ?>
                                    </a>
                                </td>
                                <td>
                                    <?php if ($p['nama_anak']) : ?>
                                        <?php 
                                            $anaks = explode(', ', $p['nama_anak']);
                                            foreach($anaks as $a) :
                                        ?>
                                            <span class="badge bg-info text-dark mb-1"><?= esc($a) ?></span>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <span class="text-muted small italic">Belum ada data anak</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="confirmDelete(<?= $p['id'] ?>, '<?= esc($p['nama']) ?>')">
                                        <i class="fas fa-trash me-1"></i> Hapus
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Control -->
            <?php if (isset($pager)): ?>
                <div class="mt-4">
                    <?= $pager->links('default', 'bootstrap_full') ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#tabelParents').DataTable({
            "responsive": true,
            "paging": false,
            "searching": false,
            "info": false,
            "language": {
                "zeroRecords": "Tidak ada data orang tua ditemukan"
            }
        });
    });

    function confirmDelete(id, nama) {
        if (confirm('Apakah Anda yakin ingin menghapus data orang tua "' + nama + '"?')) {
            window.location.href = '<?= base_url('admin/parents/delete') ?>/' + id;
        }
    }
</script>

<?= $this->include('admin/templates/footer') ?>
