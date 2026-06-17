<?= $this->extend('templates/parent') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Tambah Data Anak</h4>
                    <a href="<?= base_url('parent/dashboard') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <?php if(session()->getFlashdata('success')): ?>
                        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                    <?php endif; ?>
                    
                    <?php if(session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                    <?php endif; ?>
                    
                    <?php if(session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach(session()->getFlashdata('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?= base_url('parent/tambah-anak') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <div class="form-group mb-3">
                            <label for="nama">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama" name="nama" value="<?= old('nama') ?>" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="nama_panggilan">Nama Panggilan</label>
                            <input type="text" class="form-control" id="nama_panggilan" name="nama_panggilan" value="<?= old('nama_panggilan') ?>">
                        </div>
                        <div class="form-group mb-3">
                            <label for="asal_sekolah">Asal Sekolah</label>
                            <input type="text" class="form-control" id="asal_sekolah" name="asal_sekolah" value="<?= old('asal_sekolah') ?>">
                        </div>
                        <div class="form-group mb-3">
                            <label for="tanggal_lahir">Tanggal Lahir</label>
                            <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" value="<?= old('tanggal_lahir') ?>" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="jenis_kelamin">Jenis Kelamin</label>
                            <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="jenis_les_id">Jenis Les</label>
                            <select class="form-control" id="jenis_les_id" name="jenis_les_id" required>
                                <option value="">-- Pilih Jenis Les --</option>
                                <?php if(isset($jenis_les) && is_array($jenis_les)): ?>
                                    <?php foreach($jenis_les as $les): ?>
                                        <option value="<?= $les['id'] ?>" <?= old('jenis_les_id') == $les['id'] ? 'selected' : '' ?>><?= $les['nama_les'] ?> - Rp <?= number_format($les['harga'], 0, ',', '.') ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="riwayat_penyakit">Riwayat Penyakit (Opsional)</label>
                            <textarea class="form-control" id="riwayat_penyakit" name="riwayat_penyakit" rows="3"><?= old('riwayat_penyakit') ?></textarea>
                            <small class="text-muted">Masukkan informasi kesehatan yang perlu diperhatikan (alergi, penyakit, dll)</small>
                        </div>
                        <div class="form-group mb-3">
                            <label for="foto">Foto (Opsional)</label>
                            <input type="file" class="form-control" id="foto" name="foto">
                            <small class="text-muted">Format: JPG, PNG, JPEG. Maks: 2MB</small>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block mt-4">Simpan Data Anak</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Fungsi untuk mengubah format tanggal menjadi bahasa Indonesia
document.addEventListener('DOMContentLoaded', function() {
    // Dapatkan elemen input tanggal
    var tanggalInput = document.getElementById('tanggal_lahir');
    
    // Tambahkan event listener untuk mengubah format saat tanggal dipilih
    tanggalInput.addEventListener('change', function() {
        var tanggal = new Date(this.value);
        if(!isNaN(tanggal.getTime())) {
            // Array nama bulan dalam bahasa Indonesia
            var bulanIndo = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            
            // Format tanggal: DD Bulan YYYY
            var hari = tanggal.getDate();
            var bulan = bulanIndo[tanggal.getMonth()];
            var tahun = tanggal.getFullYear();
            
            // Tampilkan format Indonesia di samping input
            var formatIndo = document.getElementById('format_indo');
            if(!formatIndo) {
                formatIndo = document.createElement('small');
                formatIndo.id = 'format_indo';
                formatIndo.className = 'form-text text-muted';
                this.parentNode.appendChild(formatIndo);
            }
            formatIndo.textContent = 'Format Indonesia: ' + hari + ' ' + bulan + ' ' + tahun;
        }
    });
});
</script>

<?= $this->endSection() ?>