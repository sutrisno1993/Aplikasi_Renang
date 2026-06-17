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

<!-- Riwayat Pembayaran Content -->
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold text-primary">Riwayat Pembayaran Sukses</h5>
            <div class="d-flex gap-3 align-items-center">
                <form action="" method="get" class="d-flex align-items-center gap-2 mb-0" id="perPageForm">
                    <?php foreach (array_merge($filter, ['page' => null]) as $key => $value): ?>
                        <?php if ($value !== null && $key !== 'per_page' && $key !== 'page'): ?>
                            <input type="hidden" name="<?= esc($key) ?>" value="<?= esc($value) ?>">
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <label for="per_page" class="small fw-bold text-muted text-nowrap mb-0">Tampilkan:</label>
                    <select name="per_page" id="per_page" class="form-select form-select-sm rounded-pill border shadow-sm px-3" style="width: auto;" onchange="this.form.submit()">
                        <option value="50" <?= $perPage == '50' ? 'selected' : '' ?>>50 baris</option>
                        <option value="100" <?= $perPage == '100' ? 'selected' : '' ?>>100 baris</option>
                        <option value="200" <?= $perPage == '200' ? 'selected' : '' ?>>200 baris</option>
                        <option value="all" <?= $perPage == 'all' ? 'selected' : '' ?>>Semua data (All)</option>
                    </select>
                </form>
                <div class="form-check form-switch mt-1">
                    <input class="form-check-input" type="checkbox" id="toggleAksi">
                    <label class="form-check-input-label small fw-bold text-muted" for="toggleAksi">Mode Edit</label>
                </div>
                <button class="btn btn-outline-primary btn-sm rounded-pill px-3" type="button" data-bs-toggle="collapse" data-bs-target="#filterSection" aria-expanded="false" aria-controls="filterSection">
                    <i class="fas fa-filter me-2"></i>Filter Data
                </button>
                <a href="<?= base_url('admin/pembayaran/export-excel') . '?' . http_build_query($filter) ?>" class="btn btn-success btn-sm rounded-pill px-3">
                    <i class="fas fa-file-excel me-2"></i>Export Excel
                </a>
            </div>
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
            
            <!-- Filter Section (Collapsed by Default) -->
            <div class="collapse <?= (isset($_GET['tanggal_mulai']) || isset($_GET['nama_anak'])) ? 'show' : '' ?> mb-4" id="filterSection">
                <div class="card border-0 bg-light rounded-4 shadow-sm">
                    <div class="card-body p-4">
                        <form action="<?= base_url('admin/pembayaran/riwayat') ?>" method="get">
                            <input type="hidden" name="per_page" value="<?= $perPage ?>">
                            <div class="row">
                                <!-- Filter Tanggal -->
                                <div class="col-md-3 mb-3">
                                    <label for="tanggal_mulai" class="form-label fw-bold small text-uppercase">Tanggal Mulai</label>
                                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="form-control border-0 shadow-sm" value="<?= $filter['tanggal_mulai'] ?? '' ?>">
                                </div>
                                
                                <div class="col-md-3 mb-3">
                                    <label for="tanggal_selesai" class="form-label fw-bold small text-uppercase">Tanggal Selesai</label>
                                    <input type="date" id="tanggal_selesai" name="tanggal_selesai" class="form-control border-0 shadow-sm" value="<?= $filter['tanggal_selesai'] ?? '' ?>">
                                </div>
                                
                                <!-- Filter Metode Pembayaran -->
                                <div class="col-md-3 mb-3">
                                    <label for="metode" class="form-label fw-bold small text-uppercase">Metode Pembayaran</label>
                                    <select id="metode" name="metode" class="form-select border-0 shadow-sm">
                                        <option value="">Semua</option>
                                        <option value="transfer_bca" <?= ($filter['metode'] ?? '') == 'transfer_bca' ? 'selected' : '' ?>>Transfer BCA</option>
                                        <option value="transfer_bri" <?= ($filter['metode'] ?? '') == 'transfer_bri' ? 'selected' : '' ?>>Transfer BRI</option>
                                        <option value="transfer_mandiri" <?= ($filter['metode'] ?? '') == 'transfer_mandiri' ? 'selected' : '' ?>>Transfer Mandiri</option>
                                        <option value="cash" <?= ($filter['metode'] ?? '') == 'cash' ? 'selected' : '' ?>>Tunai</option>
                                    </select>
                                </div>
                                
                                <!-- Filter Nama Anak -->
                                <div class="col-md-3 mb-3">
                                    <label for="nama_anak" class="form-label fw-bold small text-uppercase">Nama Anak</label>
                                    <input type="text" id="nama_anak" name="nama_anak" class="form-control border-0 shadow-sm" placeholder="Masukkan nama anak..." value="<?= $filter['nama_anak'] ?? '' ?>">
                                </div>
                            </div>
                            
                            <div class="row">
                                <!-- Filter Jenis Les -->
                                <div class="col-md-3 mb-3">
                                    <label for="jenis_les" class="form-label fw-bold small text-uppercase">Jenis Les</label>
                                    <select id="jenis_les" name="jenis_les" class="form-select border-0 shadow-sm">
                                        <option value="">Semua</option>
                                        <?php 
                                        // Load model jenis les
                                        $jenisLesModel = new \App\Models\JenisLesModel();
                                        $jenisLesOptions = $jenisLesModel->findAll();
                                        
                                        foreach ($jenisLesOptions as $option): 
                                        ?>
                                            <option value="<?= $option['id'] ?>" <?= ($filter['jenis_les'] ?? '') == $option['id'] ? 'selected' : '' ?>>
                                                <?= $option['nama_les'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-12 mt-2">
                                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">Terapkan Filter</button>
                                    <a href="<?= base_url('admin/pembayaran/riwayat') ?>" class="btn btn-light rounded-pill px-4 shadow-sm border ms-2">Reset Filter</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover" id="tabelPembayaran">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID</th>
                            <th>Bukti</th>
                            <th>Tanggal</th>
                            <th>Nama Anak</th>
                            <th>Nama Orang Tua</th>
                            <th>Jenis Les</th>
                            <th>Jumlah Pertemuan</th>
                            <th>Total</th>
                            <th>Metode Pembayaran</th>
                            <th>Status</th>
                                    <th>Masa Berlaku</th>
                                    <th class="text-nowrap">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($pembayaran)): ?>
                                    <?php 
                                    $no = 1;
                                    if (isset($pager) && $perPage !== 'all') {
                                        $perPageNum = ($perPage === 'all') ? count($pembayaran) : (int)$perPage;
                                        $no = 1 + (($pager->getCurrentPage() - 1) * $perPageNum); 
                                    }
                                    foreach ($pembayaran as $p): 
                                    ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= $p['id'] ?></td>
                                            <td>
                                                <?php if (!empty($p['bukti_pembayaran'])) : ?>
                                                    <img src="<?= r2_url($p['bukti_pembayaran'], 'pembayaran') ?>" 
                                                         alt="Bukti" 
                                                         class="img-thumbnail bukti-img" 
                                                         style="width: 50px; height: 50px; object-fit: cover;"
                                                         onclick="zoomImage(this.src)">
                                                <?php else : ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('d-m-Y H:i', strtotime($p['tanggal'])) ?></td>
                                            <td><?= $p['nama_anak'] ?></td>
                                            <td><?= $p['nama_parent'] ?></td>
                                            <td>
                                                <?= esc($p['nama_les_snapshot'] ?? '-') ?>
                                            </td>
                                            <td><?= $p['jumlah_pertemuan'] ?> kali</td>
                                            <td>Rp <?= number_format($p['total'], 0, ',', '.') ?></td>
                                            <td>
                                                <?php 
                                                $metode = '';
                                                switch($p['metode_pembayaran']) {
                                                    case 'transfer_bca':
                                                        $metode = 'Transfer BCA';
                                                        break;
                                                    case 'transfer_bri':
                                                        $metode = 'Transfer BRI';
                                                        break;
                                                    case 'transfer_mandiri':
                                                        $metode = 'Transfer Mandiri';
                                                        break;
                                                    case 'cash':
                                                        $metode = 'Tunai';
                                                        break;
                                                    default:
                                                        $metode = $p['metode_pembayaran'];
                                                }
                                                echo $metode;
                                                ?>
                                            </td>
                                            <td>
                                                <?php if (($p['is_confirmed_boss'] ?? 0) == 1): ?>
                                                    <span class="badge bg-success" title="Sudah diverifikasi oleh Boss"><i class="fas fa-check-double me-1"></i> Valid</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark" title="Menunggu verifikasi mutasi dana oleh Boss"><i class="fas fa-clock me-1"></i> Pending Boss</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if(!empty($p['berlaku_sampai'])): ?>
                                                    <?= date('d-m-Y', strtotime($p['berlaku_sampai'])) ?>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-nowrap">
                                                    <div class="d-inline-flex gap-1">
                                                        <a href="<?= base_url('admin/pembayaran/detail/' . $p['id']) ?>" class="btn btn-sm btn-outline-info rounded-pill" title="Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <div class="action-buttons d-none">
                                                            <a href="<?= base_url('admin/pembayaran/edit/' . $p['id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill" title="Edit Data">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="<?= base_url('admin/pembayaran/cetak/' . $p['id']) ?>" class="btn btn-sm btn-outline-secondary rounded-pill" target="_blank" title="Cetak Kuitansi">
                                                                <i class="fas fa-print"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" onclick="confirmDelete(<?= $p['id'] ?>)" title="Hapus">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </td>
                                        </tr>
                                    <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="12" class="text-center">Tidak ada data pembayaran</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Control -->
            <?php if (isset($pager) && $perPage !== 'all'): ?>
                <div class="mt-4">
                    <?= $pager->links('default', 'bootstrap_full') ?>
                </div>
            <?php endif; ?>
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
                    <button type="button" class="btn btn-light btn-sm rounded-pill" data-bs-dismiss="modal">Tutup</button>
                    <a id="downloadImage" href="" target="_blank" class="btn btn-primary btn-sm rounded-pill ms-2">Buka di Tab Baru / Download</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tambahkan Script untuk menampilkan modal -->
<!-- Modal Reject Boss -->
<div class="modal fade" id="rejectModalBoss" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Tolak Verifikasi Dana (Boss)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectFormBoss" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Alasan Penolakan Dana</label>
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Contoh: Dana belum masuk / Nominal kurang" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Dana</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openRejectModalBoss(id) {
    document.getElementById('rejectFormBoss').action = "<?= base_url('admin/pembayaran/reject-boss/') ?>" + id;
    var myModal = new bootstrap.Modal(document.getElementById('rejectModalBoss'));
    myModal.show();
}

function zoomImage(imageUrl) {
    document.getElementById('modalImage').src = imageUrl;
    document.getElementById('downloadImage').href = imageUrl;
    var imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
    imageModal.show();
}
</script>

<script>
$(document).ready(function() {
    // Toggle Mode Edit
    $('#toggleAksi').on('change', function() {
        if ($(this).is(':checked')) {
            $('.action-buttons').removeClass('d-none').addClass('d-inline-flex');
        } else {
            $('.action-buttons').addClass('d-none').removeClass('d-inline-flex');
        }
    });

    // Inisialisasi DataTable tanpa pagination client-side (karena sudah server-side)
    var table = $('#tabelPembayaran').DataTable({
        "responsive": true,
        "order": [[0, "desc"]],
        "paging": false,
        "searching": false,
        "info": false,
        "language": {
            "zeroRecords": "Tidak ada data yang ditemukan"
        }
    });
});
</script>

<?= $this->include('admin/templates/footer') ?>