<?= $this->include('admin/templates/header') ?>

<style>
    .btn-xs {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        line-height: 1.5;
        border-radius: 0.2rem;
    }
    .action-cell .btn {
        margin-bottom: 2px !important;
    }
</style>

<!-- Daftar Anak Content -->
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold text-primary">Daftar Anak</h5>
            <div class="d-flex align-items-center gap-3">
                <form action="" method="get" class="d-flex align-items-center gap-2 mb-0" id="perPageForm">
                    <?php foreach (array_merge($filters, ['page' => null]) as $key => $value): ?>
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
                <button class="btn btn-outline-primary btn-sm rounded-pill px-3" type="button" data-bs-toggle="collapse" data-bs-target="#filterSection" aria-expanded="false" aria-controls="filterSection">
                    <i class="fas fa-filter me-2"></i>Filter Data
                </button>
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
            <div class="collapse <?= (isset($_GET['jenis_kelamin']) || isset($_GET['nama'])) ? 'show' : '' ?> mb-4" id="filterSection">
                <div class="card border-0 bg-light rounded-4 shadow-sm">
                    <div class="card-body p-4">
                        <form action="" method="get">
                            <input type="hidden" name="per_page" value="<?= $perPage ?>">
                            <div class="row">
                                <!-- Filter Jenis Kelamin -->
                                <div class="col-md-3 mb-3">
                                    <label for="jenis_kelamin" class="form-label fw-bold small text-uppercase">Jenis Kelamin</label>
                                    <select name="jenis_kelamin" id="jenis_kelamin" class="form-select border-0 shadow-sm">
                                        <option value="">Semua</option>
                                        <option value="L" <?= ($filters['jenis_kelamin'] == 'L' ? 'selected' : '') ?>>Laki-laki</option>
                                        <option value="P" <?= ($filters['jenis_kelamin'] == 'P' ? 'selected' : '') ?>>Perempuan</option>
                                    </select>
                                </div>
                                
                                <!-- Filter Jenis Les -->
                                <div class="col-md-3 mb-3">
                                    <label for="jenis_les" class="form-label fw-bold small text-uppercase">Jenis Les</label>
                                    <select name="jenis_les" id="jenis_les" class="form-select border-0 shadow-sm">
                                        <option value="">Semua</option>
                                        <?php foreach ($jenis_les as $les): ?>
                                            <option value="<?= $les['id'] ?>" <?= ($filters['jenis_les'] == $les['id'] ? 'selected' : '') ?>>
                                                <?= $les['nama_les'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <!-- Filter Sisa Pertemuan -->
                                <div class="col-md-3 mb-3">
                                    <label for="sisa_pertemuan" class="form-label fw-bold small text-uppercase">Sisa Pertemuan</label>
                                    <select name="sisa_pertemuan" id="sisa_pertemuan" class="form-select border-0 shadow-sm">
                                        <option value="">Semua</option>
                                        <option value="kurang2" <?= ($filters['sisa_pertemuan'] == 'kurang2' ? 'selected' : '') ?>>Kurang dari 2</option>
                                        <option value="2sampai5" <?= ($filters['sisa_pertemuan'] == '2sampai5' ? 'selected' : '') ?>>2 sampai 5</option>
                                        <option value="lebih5" <?= ($filters['sisa_pertemuan'] == 'lebih5' ? 'selected' : '') ?>>Lebih dari 5</option>
                                    </select>
                                </div>
                                
                                <!-- Filter ID Anak -->
                                <div class="col-md-3 mb-3">
                                    <label for="anak_id" class="form-label fw-bold small text-uppercase">Cari ID Anak</label>
                                    <input type="number" name="anak_id" id="anak_id" class="form-control border-0 shadow-sm" placeholder="ID (contoh: 72)" value="<?= $filters['anak_id'] ?? '' ?>">
                                </div>

                                <!-- Filter Nama Lengkap -->
                                <div class="col-md-3 mb-3">
                                    <label for="nama" class="form-label fw-bold small text-uppercase">Cari Nama Lengkap</label>
                                    <input type="text" name="nama" id="nama" class="form-control border-0 shadow-sm" placeholder="Masukkan nama lengkap..." value="<?= $filters['nama'] ?? '' ?>">
                                </div>

                                <!-- Filter Nama Panggilan -->
                                <div class="col-md-3 mb-3">
                                    <label for="panggilan" class="form-label fw-bold small text-uppercase">Cari Nama Panggilan</label>
                                    <input type="text" name="panggilan" id="panggilan" class="form-control border-0 shadow-sm" placeholder="Masukkan nama panggilan..." value="<?= $filters['panggilan'] ?? '' ?>">
                                </div>
                                
                                <!-- Toggle Filter Aktif 50 Hari -->
                                <div class="col-md-3 mb-3 d-flex align-items-end">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="aktif_50_hari" id="aktif_50_hari" <?= ($filters['aktif_50_hari'] == 'on' ? 'checked' : '') ?>>
                                        <label class="form-check-input-label fw-bold small" for="aktif_50_hari">Aktif 50 Hari Terakhir</label>
                                    </div>
                                </div>

                                <!-- Toggle Filter Tidak Aktif 100 Hari -->
                                <div class="col-md-3 mb-3 d-flex align-items-end">
                                    <div class="form-check form-switch mb-2 text-danger">
                                        <input class="form-check-input" type="checkbox" name="tidak_aktif_100_hari" id="tidak_aktif_100_hari" <?= ($filters['tidak_aktif_100_hari'] == 'on' ? 'checked' : '') ?>>
                                        <label class="form-check-input-label fw-bold small" for="tidak_aktif_100_hari">Tidak Aktif > 100 Hari</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">Terapkan Filter</button>
                                    <a href="<?= base_url('admin/anak') ?>" class="btn btn-light rounded-pill px-4 shadow-sm border ms-2">Reset Filter</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover" id="tabelAnak">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Nama Panggilan</th>
                            <th>Orang Tua</th>
                            <th>Jenis Les</th>
                            <th>Sisa Pertemuan</th>
                            <th>Aktivitas Terakhir</th>
                            <th>Berlaku Sampai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($anak)): ?>
                            <?php foreach ($anak as $a): ?>
                                <tr>
                                    <td><?= $a['id'] ?></td>
                                    <td><?= esc($a['nama']) ?></td>
                                    <td><?= $a['nama_panggilan'] ?? '-' ?></td>
                                    <td><?= $a['nama_parent'] ?></td>
                                    <td><?= $a['nama_les'] ?></td>
                                    <td>
                                        <span class="badge bg-<?= $a['sisa_pertemuan'] < 2 ? 'danger' : 'primary' ?> text-white">
                                            <?= $a['sisa_pertemuan_display'] ?> kali
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($a['last_activity'] == '1970-01-01'): ?>
                                            <span class="text-muted small">Belum ada kegiatan</span>
                                        <?php else: ?>
                                            <span class="small">
                                                <?= date('d-m-Y', strtotime($a['last_activity'])) ?>
                                                <?php 
                                                    $days = floor((time() - strtotime($a['last_activity'])) / (60 * 60 * 24));
                                                    if ($days > 0) {
                                                        $color = ($days > 100) ? 'danger' : (($days > 50) ? 'warning' : 'success');
                                                        echo " <br><span class='badge bg-$color'>($days hari lalu)</span>";
                                                    }
                                                ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                        // Cek berlaku sampai dari pembayaran terakhir
                                        if(!empty($a['berlaku_sampai'])): 
                                            echo date('d-m-Y', strtotime($a['berlaku_sampai']));
                                        else: 
                                            echo '-';
                                        endif; 
                                        ?>
                                    </td>
                                    <td class="action-cell">
                                        <a href="<?= base_url('admin/anak/detail/' . $a['id']) ?>" class="btn btn-xs btn-info text-white" title="Detail">
                                            <i class="fas fa-eye fa-sm"></i>
                                        </a>
                                        <a href="<?= base_url('admin/anak/edit/' . $a['id']) ?>" class="btn btn-xs btn-warning text-white" title="Edit">
                                            <i class="fas fa-edit fa-sm"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-xs btn-danger btn-hapus-anak" 
                                                data-id="<?= $a['id'] ?>" 
                                                data-nama="<?= esc($a['nama']) ?>"
                                                title="Hapus">
                                            <i class="fas fa-trash fa-sm"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center">Tidak ada data anak</td>
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

<script>
$(document).ready(function() {
    // Inisialisasi DataTable tanpa pagination client-side (karena sudah server-side)
    var table = $('#tabelAnak').DataTable({
        "responsive": true,
        "order": [[0, "desc"]],
        "paging": false,
        "searching": false,
        "info": false,
        "language": {
            "zeroRecords": "Tidak ada data yang ditemukan"
        }
    });
    
    // Filter Jenis Kelamin
    $('#filterJenisKelamin').on('change', function() {
        var jenisKelamin = $(this).val();
        // Nama: 1, Nama Panggilan: 2, Orang Tua: 3, Jenis Les: 4, Sisa: 5
    });
    
    // Filter Jenis Les
    $('#filterJenisLes').on('change', function() {
        var jenisLes = $(this).val();
        table.column(4).search(jenisLes).draw();
    });
    
    // Filter Sisa Pertemuan
    $('#filterSisaPertemuan').on('change', function() {
        var sisaPertemuan = $(this).val();
        
        // Custom filtering function untuk sisa pertemuan
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            if (sisaPertemuan === '') {
                return true; // Tampilkan semua jika tidak ada filter
            }
            
            // Kolom sisa pertemuan index 5
            var text = data[5] || "";
            var matches = text.match(/\d+/);
            if (!matches) return false;
            
            var pertemuan = parseInt(matches[0]); 
            
            if (sisaPertemuan === 'kurang2' && pertemuan < 2) {
                return true;
            } else if (sisaPertemuan === '2sampai5' && pertemuan >= 2 && pertemuan <= 5) {
                return true;
            } else if (sisaPertemuan === 'lebih5' && pertemuan > 5) {
                return true;
            }
            
            return false;
        });
        
        table.draw();
        
        // Hapus custom filtering function setelah digunakan
        $.fn.dataTable.ext.search.pop();
    });
    
    // Filter Nama
    $('#filterNama').on('keyup', function() {
        var nama = $(this).val();
        table.column(1).search(nama).draw();
    });

    // Auto-submit saat toggle aktif 50 hari berubah
    $('#aktif_50_hari').on('change', function() {
        if ($(this).is(':checked')) {
            $('#tidak_aktif_100_hari').prop('checked', false);
        }
        $(this).closest('form').submit();
    });

    // Auto-submit saat toggle tidak aktif 100 hari berubah
    $('#tidak_aktif_100_hari').on('change', function() {
        if ($(this).is(':checked')) {
            $('#aktif_50_hari').prop('checked', false);
        }
        $(this).closest('form').submit();
    });
    
    // Reset Filter
    $('#resetFilter').on('click', function() {
        $('#filterJenisKelamin').val('');
        $('#filterJenisLes').val('');
        $('#filterSisaPertemuan').val('');
        $('#filterNama').val('');
        
        table.search('').columns().search('').draw();
    });

    // Fitur Hapus Anak dengan Konfirmasi Berlapis
    $(document).on('click', '.btn-hapus-anak', function() {
        const id = $(this).data('id');
        const nama = $(this).data('nama');

        // Konfirmasi Pertama
        Swal.fire({
            title: 'Hapus Data Anak?',
            text: `Apakah Anda yakin ingin menghapus data "${nama}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Lanjut!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Konfirmasi Kedua (Berlapis)
                Swal.fire({
                    title: 'Konfirmasi Terakhir',
                    text: `Data "${nama}" dan semua riwayat (pembayaran/jadwal) serta foto di Cloudflare akan dihapus PERMANEN. Tindakan ini tidak bisa dibatalkan!`,
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'YA, HAPUS SEKARANG!',
                    cancelButtonText: 'Pikirkan Lagi'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Tampilkan loading saat proses hapus
                        Swal.fire({
                            title: 'Sedang Menghapus...',
                            text: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Redirect ke URL hapus
                        window.location.href = `<?= base_url('admin/anak/delete') ?>/${id}`;
                    }
                });
            }
        });
    });
});
</script>

<?= $this->include('admin/templates/footer') ?>