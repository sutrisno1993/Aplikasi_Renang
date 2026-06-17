<?= $this->include('admin/templates/header') ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Riwayat Kedatangan</h5>
            <div>
                <a href="<?= base_url('admin/kedatangan') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
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

            <!-- Form Filter -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Filter Data</h6>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('admin/kedatangan/riwayat') ?>" method="get">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="nama_anak" class="form-label">Nama Anak</label>
                                <input type="text" id="nama_anak" name="nama_anak" class="form-control" placeholder="Cari nama anak..." value="<?= $filter['nama_anak'] ?? '' ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="form-control" value="<?= $filter['tanggal_mulai'] ?? '' ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                                <input type="date" id="tanggal_selesai" name="tanggal_selesai" class="form-control" value="<?= $filter['tanggal_selesai'] ?? '' ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="status_kehadiran" class="form-label">Status Kehadiran</label>
                                <select id="status_kehadiran" name="status_kehadiran" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="hadir" <?= ($filter['status_kehadiran'] ?? '') == 'hadir' ? 'selected' : '' ?>>Hadir</option>
                                    <option value="tidak_hadir" <?= ($filter['status_kehadiran'] ?? '') == 'tidak_hadir' ? 'selected' : '' ?>>Tidak Hadir</option>
                                    <option value="izin" <?= ($filter['status_kehadiran'] ?? '') == 'izin' ? 'selected' : '' ?>>Izin</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary btn-sm me-2">
                                    <i class="fas fa-filter"></i> Terapkan Filter
                                </button>
                                <a href="<?= base_url('admin/kedatangan/riwayat') ?>" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-sync"></i> Reset Filter
                                </a>
                                <a href="<?= base_url('admin/kedatangan/export-riwayat') ?>" class="btn btn-success btn-sm ms-2">
                                    <i class="fas fa-file-excel"></i> Export Excel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover" id="riwayatKehadiranTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Nama Anak</th>
                            <th>Status Kehadiran</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($kehadiran)) : ?>
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data riwayat</td>
                            </tr>
                        <?php else : ?>
                            <?php $i = 1; foreach($kehadiran as $k): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= $k['nama_hari'] ?>, <?= date('d-m-Y', strtotime($k['tanggal'])) ?></td>
                                <td><?= date('H:i', strtotime($k['jam_mulai'])) ?> - <?= date('H:i', strtotime($k['jam_selesai'])) ?></td>
                                <td><?= $k['nama_anak'] ?></td>
                                <td>
                                    <?php
                                    $badge_class = 'bg-primary';
                                    if($k['status_kehadiran'] == 'hadir') $badge_class = 'bg-success';
                                    elseif($k['status_kehadiran'] == 'tidak_hadir') $badge_class = 'bg-danger';
                                    elseif($k['status_kehadiran'] == 'izin') $badge_class = 'bg-warning';
                                    ?>
                                    <span class="badge <?= $badge_class ?>"><?= ucfirst(str_replace('_', ' ', $k['status_kehadiran'])) ?></span>
                                </td>
                                <td><?= $k['catatan'] ?? '-' ?></td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-info btn-sm btn-detail" 
                                                data-id="<?= $k['id'] ?>" 
                                                data-anak="<?= $k['nama_anak'] ?>" 
                                                data-hari="<?= $k['nama_hari'] ?>"
                                                data-tanggal="<?= date('d-m-Y', strtotime($k['tanggal'])) ?>"
                                                data-waktu="<?= date('H:i', strtotime($k['jam_mulai'])) ?> - <?= date('H:i', strtotime($k['jam_selesai'])) ?>"
                                                data-status="<?= $k['status_kehadiran'] ?>"
                                                data-catatan="<?= $k['catatan'] ?? '-' ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <?php if(session()->get('role') == 'admin'): ?>
                                        <a href="<?= base_url('admin/kedatangan/edit-kehadiran/' . $k['id']) ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php endif; ?>
                                        
                                        <!-- Tombol Laporan WA -->
                                        <?php if($k['status_kehadiran'] == 'hadir'): ?>
                                            <a href="<?= base_url('admin/kedatangan/kirim-wa-riwayat/' . $k['id'] . '/ortu') ?>" target="_blank" class="btn btn-success btn-sm" title="Kirim ke Orang Tua">
                                                <i class="fab fa-whatsapp"></i> Ortu
                                            </a>
                                            <a href="<?= base_url('admin/kedatangan/kirim-wa-riwayat/' . $k['id'] . '/group') ?>" target="_blank" class="btn btn-primary btn-sm" title="Kirim ke Group">
                                                <i class="fab fa-whatsapp"></i> Grup
                                            </a>
                                        <?php endif; ?>
                                    </div>
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

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Kehadiran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="30%">Nama Anak</td>
                        <td width="5%">:</td>
                        <td id="detail-anak"></td>
                    </tr>
                    <tr>
                        <td>Tanggal</td>
                        <td>:</td>
                        <td id="detail-tanggal"></td>
                    </tr>
                    <tr>
                        <td>Waktu</td>
                        <td>:</td>
                        <td id="detail-waktu"></td>
                    </tr>
                    <tr>
                        <td>Status Kehadiran</td>
                        <td>:</td>
                        <td id="detail-status"></td>
                    </tr>
                    <tr>
                        <td>Catatan</td>
                        <td>:</td>
                        <td id="detail-catatan"></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?= $this->include('admin/templates/footer') ?>

<script>
$(document).ready(function() {
    // Inisialisasi DataTable dengan fitur dasar saja
    $('#riwayatKehadiranTable').DataTable({
        "responsive": true,
        "pageLength": 25,
        "language": {
            "search": "Pencarian:",
            "lengthMenu": "Tampilkan _MENU_ data per halaman",
            "zeroRecords": "Tidak ada data yang ditemukan",
            "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
            "infoEmpty": "Tidak ada data yang tersedia",
            "infoFiltered": "(difilter dari _MAX_ total data)",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Selanjutnya",
                "previous": "Sebelumnya"
            }
        }
    });
    
    // Setup Modal Detail
    $('.btn-detail').on('click', function() {
        var anak = $(this).data('anak');
        var tanggal = $(this).data('tanggal');
        var waktu = $(this).data('waktu');
        var status = $(this).data('status');
        var catatan = $(this).data('catatan');
        var hari = $(this).data('hari'); // Tambahkan data hari
        
        // Format status
        var statusText = status.charAt(0).toUpperCase() + status.slice(1);
        statusText = statusText.replace('_', ' ');
        
        // Set badge untuk status
        var badgeClass = 'badge bg-primary';
        if(status == 'hadir') badgeClass = 'badge bg-success';
        else if(status == 'tidak_hadir') badgeClass = 'badge bg-danger';
        else if(status == 'izin') badgeClass = 'badge bg-warning';
        
        $('#detail-anak').text(anak);
        $('#detail-tanggal').text(hari + ', ' + tanggal); // Tambahkan hari ke tanggal
        $('#detail-waktu').text(waktu);
        $('#detail-status').html('<span class="' + badgeClass + '">' + statusText + '</span>');
        $('#detail-catatan').text(catatan);
        
        $('#detailModal').modal('show');
    });
});
</script>