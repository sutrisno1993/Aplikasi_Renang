<?= $this->include('admin/templates/header') ?>

<!-- Edit Anak Content -->
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Data Anak</h5>
            <a href="<?= base_url('admin/anak') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= esc(session()->getFlashdata('error')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->has('errors')) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        <?php foreach (session('errors') as $error) : ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <form action="<?= base_url('admin/anak/update/' . $anak['id']) ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama" name="nama" value="<?= old('nama', $anak['nama']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="nama_panggilan" class="form-label">Nama Panggilan</label>
                            <input type="text" class="form-control" id="nama_panggilan" name="nama_panggilan" value="<?= old('nama_panggilan', $anak['nama_panggilan']) ?>">
                        </div>

                        <div class="mb-3">
                            <label for="asal_sekolah" class="form-label">Asal Sekolah</label>
                            <input type="text" class="form-control" id="asal_sekolah" name="asal_sekolah" value="<?= old('asal_sekolah', $anak['asal_sekolah']) ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                            <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" value="<?= old('tanggal_lahir', $anak['tanggal_lahir']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                            <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                                <option value="Laki-laki" <?= (old('jenis_kelamin', $anak['jenis_kelamin']) == 'Laki-laki') ? 'selected' : '' ?>>Laki-laki</option>
                                <option value="Perempuan" <?= (old('jenis_kelamin', $anak['jenis_kelamin']) == 'Perempuan') ? 'selected' : '' ?>>Perempuan</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="jenis_les_id" class="form-label">Jenis Les <span class="text-muted fw-normal">(pindah paket)</span></label>
                            <?php if (!empty($bolehPindahPaket)): ?>
                                <select class="form-select" id="jenis_les_id" name="jenis_les_id" required>
                                    <?php foreach ($jenisLes as $les): ?>
                                        <option value="<?= $les['id'] ?>" <?= (old('jenis_les_id', $anak['jenis_les_id']) == $les['id']) ? 'selected' : '' ?>>
                                            <?= $les['nama_les'] ?> - Rp <?= number_format($les['harga'], 0, ',', '.') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-success d-block mt-1">
                                    <i class="fas fa-check-circle me-1"></i>
                                    Paket saat ini sudah selesai. Anda dapat memindahkan ke jenis les lain.
                                </small>
                            <?php else: ?>
                                <input type="hidden" name="jenis_les_id" value="<?= esc($anak['jenis_les_id']) ?>">
                                <select class="form-select" id="jenis_les_id" disabled>
                                    <?php foreach ($jenisLes as $les): ?>
                                        <option value="<?= $les['id'] ?>" <?= ($anak['jenis_les_id'] == $les['id']) ? 'selected' : '' ?>>
                                            <?= $les['nama_les'] ?> - Rp <?= number_format($les['harga'], 0, ',', '.') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="alert alert-warning py-2 px-3 mt-2 mb-0 small">
                                    <i class="fas fa-lock me-1"></i>
                                    <strong>Pindah paket dikunci.</strong>
                                    <?= esc($paketStatus['message'] ?? 'Selesaikan paket saat ini terlebih dahulu (sisa pertemuan harus 0).') ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="riwayat_penyakit" class="form-label">Riwayat Penyakit</label>
                            <textarea class="form-control" id="riwayat_penyakit" name="riwayat_penyakit" rows="2"><?= old('riwayat_penyakit', $anak['riwayat_penyakit']) ?></textarea>
                            <small class="text-muted">Isi jika ada, atau biarkan kosong jika tidak ada.</small>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="aktif" <?= (old('status', $anak['status']) == 'aktif') ? 'selected' : '' ?>>Aktif</option>
                                <option value="non-aktif" <?= (old('status', $anak['status']) == 'non-aktif') ? 'selected' : '' ?>>Non-Aktif</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="foto" class="form-label">Foto Anak</label>
                            <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                            <small class="form-text text-muted">Upload foto baru (opsional). Biarkan kosong jika tidak ingin mengubah foto.</small>
                            
                            <?php if(!empty($anak['foto'])): ?>
                                <div class="mt-2">
                                    <p>Foto saat ini:</p>
                                    <img src="<?= r2_url($anak['foto'], 'anak') ?>" alt="Foto anak" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                                <input type="hidden" name="old_foto" value="<?= $anak['foto'] ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="alert alert-info py-2 small mt-4 mb-0">
        <i class="fas fa-info-circle me-1"></i>
        Sisa kuota <strong>tidak bisa diedit manual</strong>. Kelola lewat riwayat pembayaran &amp; latihan di bawah (atau menu Kedatangan); sistem menghitung ulang otomatis (FIFO).
    </div>

    <!-- Riwayat Pembayaran -->
    <div class="card mt-3">
        <div class="card-header bg-white">
            <h5 class="mb-0">Riwayat Pembayaran</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Tanggal</th>
                            <th>Bukti</th>
                            <th>Paket</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pembayaran as $p): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold"><?= date('d/m/Y', strtotime($p['tanggal'])) ?></div>
                                <small class="text-muted"><?= date('H:i', strtotime($p['tanggal'])) ?></small>
                            </td>
                            <td>
                                <?php if(!empty($p['bukti_pembayaran'])): ?>
                                    <img src="<?= r2_url($p['bukti_pembayaran'], 'pembayaran') ?>" 
                                         class="img-thumbnail cursor-pointer" 
                                         style="width: 50px; height: 50px; object-fit: cover;"
                                         onclick="zoomImage(this.src)">
                                <?php else: ?>
                                    <span class="text-muted small">No File</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $p['jumlah_pertemuan'] ?> Sesi</td>
                            <td class="fw-bold">Rp <?= number_format($p['total'], 0, ',', '.') ?></td>
                            <td>
                                <?php 
                                    $badge = 'secondary';
                                    if($p['status'] == 'success') $badge = 'success';
                                    if($p['status'] == 'pending') $badge = 'warning';
                                    if($p['status'] == 'rejected') $badge = 'danger';
                                ?>
                                <span class="badge bg-<?= $badge ?>"><?= ucfirst($p['status']) ?></span>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmDeletePembayaran(<?= $p['id'] ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($pembayaran)): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat pembayaran</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Riwayat Latihan -->
    <div class="card mt-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Riwayat Latihan</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Hari, Tanggal</th>
                            <th>Waktu</th>
                            <th>Pkt/Ke</th>
                            <th>Materi</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($kehadiran as $k): ?>
                        <tr>
                            <td class="ps-4">
                                <?php 
                                    $hari = [
                                        'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
                                        'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
                                    ];
                                    $dayName = date('l', strtotime($k['tanggal']));
                                ?>
                                <div class="fw-bold"><?= $hari[$dayName] ?>, <?= date('d/m/Y', strtotime($k['tanggal'])) ?></div>
                            </td>
                            <td><?= date('H:i', strtotime($k['jam_mulai'])) ?></td>
                            <td>
                                <?php if($k['pertemuan_ke'] !== '-'): ?>
                                    <span class="badge bg-info text-dark">Pkt:<?= $k['paket_ke'] ?> Ke-<?= $k['pertemuan_ke'] ?></span>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?= $k['materi'] ?: '-' ?></td>
                            <td>
                                <?php 
                                    $badge = 'secondary';
                                    if($k['status_kehadiran'] == 'hadir') $badge = 'success';
                                    if($k['status_kehadiran'] == 'izin') $badge = 'warning';
                                    if($k['status_kehadiran'] == 'tidak_hadir') $badge = 'danger';
                                ?>
                                <span class="badge bg-<?= $badge ?>"><?= ucfirst(str_replace('_', ' ', $k['status_kehadiran'])) ?></span>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmDeleteLatihan(<?= $k['id'] ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($kehadiran)): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat latihan</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Zoom Image -->
<div class="modal fade" id="zoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body p-0 text-center">
                <img src="" id="zoomedImg" class="img-fluid rounded shadow-lg" style="max-height: 90vh;">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        </div>
    </div>
</div>

<?= $this->include('admin/templates/footer') ?>

<script>
function zoomImage(src) {
    document.getElementById('zoomedImg').src = src;
    var myModal = new bootstrap.Modal(document.getElementById('zoomModal'));
    myModal.show();
}

function confirmDeletePembayaran(id) {
    if (confirm('Hapus riwayat pembayaran ini?\n\nSistem akan menghitung ulang sisa kuota dari semua pembayaran & absensi (FIFO). Jika paket sudah expired, pertemuan hangus tidak kembali menjadi sisa.')) {
        window.location.href = '<?= base_url('admin/anak/delete-pembayaran/') ?>' + id;
    }
}

function confirmDeleteLatihan(id) {
    if (confirm('Hapus absensi ini?\n\nSistem menghitung ulang sisa kuota (FIFO). Bisa menambah sisa jika pertemuan ini sebelumnya memotong paket aktif; tidak mengubah pertemuan yang sudah hangus.')) {
        window.location.href = '<?= base_url('admin/anak/delete-kehadiran/') ?>' + id;
    }
}
</script>