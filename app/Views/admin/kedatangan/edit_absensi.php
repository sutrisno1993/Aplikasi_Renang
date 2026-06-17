<?= $this->include('admin/templates/header') ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                Edit Absensi - <?= date('d-m-Y', strtotime($jadwal['tanggal'])) ?>
                (<?= date('H:i', strtotime($jadwal['jam_mulai'])) ?> - <?= date('H:i', strtotime($jadwal['jam_selesai'])) ?>)
            </h5>
            <div>
                <a href="<?= base_url('admin/kedatangan/cetak-laporan/' . $jadwal['id']) ?>" class="btn btn-success">
                    <i class="fas fa-file-invoice"></i> Buat Laporan (Print/WA)
                </a>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahPesertaModal">
                    <i class="fas fa-user-plus"></i> Tambah Anak Manual
                </button>
                <a href="<?= base_url('admin/kedatangan/edit') ?>" class="btn btn-secondary">
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
            
            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> <strong>Penting:</strong> 
                Menghapus anak dari daftar ini akan <strong>menambah</strong> sisa pertemuan mereka (+1). 
                Menambahkan anak secara manual akan <strong>mengurangi</strong> sisa pertemuan mereka (-1).
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-striped" id="editAbsensiTable">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">ID</th>
                            <th>Nama Anak</th>
                            <th>Nama Panggilan</th>
                            <th>Status</th>
                            <th>Sisa Pertemuan</th>
                            <th>Catatan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($kehadiran)): ?>
                            <tr>
                                <td colspan="8" class="text-center">Belum ada peserta yang diabsen hadir</td>
                            </tr>
                        <?php else: ?>
                            <?php $i = 1; foreach($kehadiran as $k): ?>
                            <tr>
                                <td class="text-center"><?= $i++ ?></td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">#<?= $k['anak_id'] ?></span>
                                </td>
                                <td><?= $k['nama'] ?></td>
                                <td><?= $k['nama_panggilan'] ?></td>
                                <td>
                                    <span class="badge bg-success"><?= ucfirst($k['status_kehadiran']) ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark">Pkt:<?= $k['paket_ke'] ?> Ke-<?= $k['pertemuan_ke'] ?> (Sisa: <?= $k['sisa_pertemuan_display'] ?>)</span>
                                </td>
                                <td><small class="text-muted"><?= $k['catatan'] ?></small></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $k['id'] ?>, '<?= $k['nama'] ?>')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
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

<!-- Modal Tambah Peserta -->
<div class="modal fade" id="tambahPesertaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Anak Manual (Selesai)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/kedatangan/save-edit-absensi') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <input type="hidden" name="jadwal_id" value="<?= $jadwal['id'] ?>">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Cari Anak (Berdasarkan ID atau Nama Panggilan)</label>
                        <?php if (!empty($allowed_jenis_les_ids) && empty($jenis_les_filter_warning)): ?>
                            <div class="small text-muted mb-2">
                                Hanya menampilkan anak dengan jenis les yang sesuai jadwal ini.
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($jenis_les_filter_warning)): ?>
                            <div class="alert alert-warning small py-2">
                                Tidak ada anak yang cocok filter jenis les jadwal. Daftar ditampilkan semua (kecuali yang sudah absen).
                            </div>
                        <?php endif; ?>
                        <select class="form-select select2" name="anak_id" required style="width: 100%">
                            <option value="">-- Ketik ID atau Nama Panggilan --</option>
                            <?php foreach($semua_anak as $a): ?>
                                <option value="<?= $a['id'] ?>" 
                                        data-id="<?= $a['id'] ?>" 
                                        data-panggilan="<?= strtolower($a['nama_panggilan'] ?? '') ?>"
                                        data-nama="<?= strtolower($a['nama'] ?? '') ?>">
                                    #<?= $a['id'] ?> - <?= $a['nama'] ?> (Panggilan: <?= $a['nama_panggilan'] ?? '-' ?>) | [<?= $a['nama_les'] ?? 'Tanpa Paket' ?>] | Sisa: <?= $a['sisa_pertemuan_display'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea name="catatan" class="form-control" rows="2" placeholder="Alasan penambahan manual..."></textarea>
                    </div>
                    <div class="alert alert-info small">
                        <i class="fas fa-info-circle"></i> Menambahkan anak akan otomatis memotong 1 sisa pertemuan.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan & Potong Pertemuan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->include('admin/templates/footer') ?>

<!-- Tambahkan Select2 untuk dropdown pencarian yang lebih baik -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $('.select2').select2({
        dropdownParent: $('#tambahPesertaModal'),
        matcher: function(params, data) {
            // Jika tidak ada term pencarian, tampilkan semua data
            if ($.trim(params.term) === '') {
                return data;
            }

            // Skip jika data tidak memiliki text
            if (typeof data.text === 'undefined') {
                return null;
            }

            var term = params.term.toLowerCase();
            var text = data.text.toLowerCase();
            
            // Ambil data dari elemen option asli
            var id = $(data.element).data('id') ? $(data.element).data('id').toString().toLowerCase() : '';
            var panggilan = $(data.element).data('panggilan') ? $(data.element).data('panggilan').toString().toLowerCase() : '';
            var nama = $(data.element).data('nama') ? $(data.element).data('nama').toString().toLowerCase() : '';

            // Cocokkan term dengan teks, ID, nama panggilan, atau nama lengkap
            if (text.indexOf(term) > -1 || id.indexOf(term) > -1 || panggilan.indexOf(term) > -1 || nama.indexOf(term) > -1) {
                return data;
            }

            return null;
        }
    });

    $('#editAbsensiTable').DataTable();
});

function confirmDelete(id, nama) {
    if (confirm('Apakah Anda yakin ingin menghapus absensi ' + nama + '? \n\nTindakan ini akan MENAMBAH 1 sisa pertemuan anak tersebut.')) {
        window.location.href = '<?= base_url('admin/kedatangan/delete-edit-absensi/') ?>' + id;
    }
}
</script>
