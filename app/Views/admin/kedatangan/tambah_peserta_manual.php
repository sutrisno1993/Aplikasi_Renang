<?= $this->include('admin/templates/header') ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                Tambah Peserta Manual - Jadwal <?= date('d-m-Y', strtotime($jadwal['tanggal'])) ?>
                (<?= date('H:i', strtotime($jadwal['jam_mulai'])) ?> - <?= date('H:i', strtotime($jadwal['jam_selesai'])) ?>)
            </h5>
            <div>
                <a href="<?= base_url('admin/kedatangan/absensi/' . $jadwal['id']) ?>" class="btn btn-secondary">
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

            <div class="row">
                <div class="col-md-8">
                    <form action="<?= base_url('admin/kedatangan/tambah-peserta-manual') ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="jadwal_id" value="<?= $jadwal['id'] ?>">
                        
                        <div class="mb-3">
                            <label for="search_id" class="form-label">Cari Anak</label>
                            <input type="text" class="form-control mb-2" id="search_anak" placeholder="Masukkan ID, nama lengkap, atau nama panggilan...">
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">Pilih</th>
                                        <th width="10%">ID</th>
                                        <th width="35%">Nama Lengkap</th>
                                        <th width="25%">Nama Panggilan</th>
                                        <th width="25%">Sisa Pertemuan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($semua_anak)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center">Tidak ada data anak</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach($semua_anak as $a): ?>
                                            <tr class="anak-row">
                                                <td class="text-center">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="anak_id" value="<?= $a['id'] ?>" id="anak_<?= $a['id'] ?>" required>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-secondary">#<?= $a['id'] ?></span>
                                                </td>
                                                <td><?= $a['nama'] ?></td>
                                                <td><?= $a['nama_panggilan'] ?></td>
                                                <td class="text-center">
                                                    <span class="badge bg-info"><?= $a['sisa_pertemuan'] ?? 0 ?> pertemuan</span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mb-3 mt-3">
                            <button type="submit" class="btn btn-primary">Tambah Peserta</button>
                        </div>
                    </form>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Informasi Jadwal</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Tanggal</th>
                                    <td><?= date('d-m-Y', strtotime($jadwal['tanggal'])) ?></td>
                                </tr>
                                <tr>
                                    <th>Waktu</th>
                                    <td><?= date('H:i', strtotime($jadwal['jam_mulai'])) ?> - <?= date('H:i', strtotime($jadwal['jam_selesai'])) ?></td>
                                </tr>
                                <?php if(isset($jadwal['jenis_les'])): ?>
                                <tr>
                                    <th>Jenis Les</th>
                                    <td><?= $jadwal['jenis_les'] ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if(isset($jadwal['materi'])): ?>
                                <tr>
                                    <th>Materi</th>
                                    <td><?= $jadwal['materi'] ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('admin/templates/footer') ?>

<script>
$(document).ready(function() {
    // Fungsi pencarian untuk tabel anak
    $('#search_anak').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        
        // Filter baris tabel berdasarkan ID, nama lengkap, atau nama panggilan
        $('.anak-row').filter(function() {
            var rowText = $(this).text().toLowerCase();
            var namaLengkap = $(this).find('td:eq(2)').text().toLowerCase();
            var namaPanggilan = $(this).find('td:eq(3)').text().toLowerCase();
            
            // Cek apakah nilai pencarian ada di nama lengkap atau nama panggilan
            return rowText.indexOf(value) > -1 || 
                   namaLengkap.indexOf(value) > -1 || 
                   namaPanggilan.indexOf(value) > -1;
        }).show();
        
        // Sembunyikan baris yang tidak cocok
        $('.anak-row').filter(function() {
            var rowText = $(this).text().toLowerCase();
            var namaLengkap = $(this).find('td:eq(2)').text().toLowerCase();
            var namaPanggilan = $(this).find('td:eq(3)').text().toLowerCase();
            
            // Cek apakah nilai pencarian tidak ada di nama lengkap atau nama panggilan
            return rowText.indexOf(value) === -1 && 
                   namaLengkap.indexOf(value) === -1 && 
                   namaPanggilan.indexOf(value) === -1;
        }).hide();
    });
    
    // Klik pada baris tabel juga memilih radio button
    $('.anak-row').on('click', function() {
        var radioBtn = $(this).find('input[type="radio"]');
        radioBtn.prop('checked', true);
    });
});
</script>

<style>
/* Tambahkan CSS untuk memperbaiki tampilan */
.table th {
    font-weight: 600;
    vertical-align: middle;
}
.table td {
    vertical-align: middle;
}
.anak-row {
    cursor: pointer;
}
.anak-row:hover {
    background-color: #f8f9fa;
}
</style>