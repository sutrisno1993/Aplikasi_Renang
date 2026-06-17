<?= $this->extend('templates/parent') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Edit Data Anak</h4>
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
                    
                    <form action="<?= base_url('parent/update-anak/' . $anak['id']) ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="jenis_les_id" value="<?= $anak['jenis_les_id'] ?>">
                        <input type="hidden" name="status" value="<?= $anak['status'] ?>">
                        
                        <div class="form-group mb-3">
                            <label for="nama">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama" name="nama" value="<?= old('nama', $anak['nama']) ?>" required>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="nama_panggilan">Nama Panggilan</label>
                            <input type="text" class="form-control" id="nama_panggilan" name="nama_panggilan" value="<?= old('nama_panggilan', $anak['nama_panggilan']) ?>">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="tanggal_lahir">Tanggal Lahir</label>
                            <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" value="<?= old('tanggal_lahir', $anak['tanggal_lahir']) ?>" required>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="asal_sekolah">Asal Sekolah</label>
                            <input type="text" class="form-control" id="asal_sekolah" name="asal_sekolah" value="<?= old('asal_sekolah', $anak['asal_sekolah']) ?>">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="jenis_kelamin">Jenis Kelamin</label>
                            <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>
                                <option value="">-- Pilih Jenis Kelamin --</option>
                                <option value="Laki-laki" <?= (old('jenis_kelamin', $anak['jenis_kelamin']) == 'Laki-laki') ? 'selected' : '' ?>>Laki-laki</option>
                                <option value="Perempuan" <?= (old('jenis_kelamin', $anak['jenis_kelamin']) == 'Perempuan') ? 'selected' : '' ?>>Perempuan</option>
                            </select>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label>Jenis Les</label>
                            <input type="text" class="form-control" value="<?= isset($jenis_les_nama) ? $jenis_les_nama : 'Jenis Les tidak tersedia' ?>" readonly>
                            <small class="text-muted">Perubahan jenis les hanya dapat dilakukan oleh admin</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="riwayat_penyakit">Riwayat Penyakit (Opsional)</label>
                            <textarea class="form-control" id="riwayat_penyakit" name="riwayat_penyakit" rows="3"><?= old('riwayat_penyakit', $anak['riwayat_penyakit']) ?></textarea>
                            <small class="text-muted">Masukkan informasi kesehatan yang perlu diperhatikan (alergi, penyakit, dll)</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="foto">Foto (Opsional)</label>
                            <?php if(!empty($anak['foto'])): ?>
                                <div class="mb-2">
                                    <img src="<?= r2_url($anak['foto'], 'anak') ?>" alt="Foto <?= $anak['nama'] ?>" class="img-thumbnail" style="max-height: 200px;">
                                    <div class="mt-1">
                                        <small class="text-muted">Foto saat ini</small>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" id="foto" name="foto">
                            <small class="text-muted">Format: JPG, PNG, JPEG. Maks: 2MB. Kosongkan jika tidak ingin mengubah foto.</small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block mt-4">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Fungsi untuk preview gambar yang diupload
document.addEventListener('DOMContentLoaded', function() {
    const fotoInput = document.getElementById('foto');
    const previewContainer = document.createElement('div');
    previewContainer.className = 'mt-2';
    previewContainer.id = 'preview-container';
    fotoInput.parentNode.insertBefore(previewContainer, fotoInput.nextSibling);
    
    fotoInput.addEventListener('change', function() {
        previewContainer.innerHTML = '';
        
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'img-thumbnail';
                img.style.maxHeight = '200px';
                previewContainer.appendChild(img);
                
                const caption = document.createElement('div');
                caption.className = 'mt-1';
                caption.innerHTML = '<small class="text-muted">Foto baru yang akan diupload</small>';
                previewContainer.appendChild(caption);
            }
            
            reader.readAsDataURL(this.files[0]);
        }
    });
});
</script>
<?= $this->endSection() ?>