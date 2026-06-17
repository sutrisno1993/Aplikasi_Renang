<?= $this->include('admin/templates/header') ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Pertemuan Kehadiran</h5>
            <a href="<?= base_url('admin/kedatangan/riwayat') ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <div class="alert alert-info">
                Ubah tanggal dan jenis les untuk pertemuan ini. Perubahan akan diterapkan ke tabel `latihan_attendance`.
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="35%">ID Kehadiran</th>
                            <td>#<?= (int) $kehadiran['id'] ?></td>
                        </tr>
                        <tr>
                            <th>Nama Anak</th>
                            <td><?= esc($kehadiran['nama_anak']) ?></td>
                        </tr>
                        <tr>
                            <th>Nama Panggilan</th>
                            <td><?= esc($kehadiran['nama_panggilan'] ?? '-') ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="35%">Tanggal Lama</th>
                            <td><?= date('d-m-Y', strtotime($kehadiran['tanggal'])) ?></td>
                        </tr>
                        <tr>
                            <th>Waktu</th>
                            <td><?= date('H:i', strtotime($kehadiran['jam_mulai'])) ?> - <?= date('H:i', strtotime($kehadiran['jam_selesai'])) ?></td>
                        </tr>
                        <tr>
                            <th>Materi</th>
                            <td><?= esc($kehadiran['materi'] ?? '-') ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <form action="<?= base_url('admin/kedatangan/update-kehadiran/' . (int) $kehadiran['id']) ?>" method="post">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tanggal Pertemuan</label>
                        <input type="date" name="tanggal" class="form-control" required value="<?= old('tanggal', $kehadiran['tanggal']) ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Jenis Les (Snapshot)</label>
                        <select name="jenis_les_id" class="form-select" required>
                            <option value="">-- Pilih Jenis Les --</option>
                            <?php foreach ($jenis_les as $jl): ?>
                                <option value="<?= (int) $jl['id'] ?>" <?= (string) old('jenis_les_id', $kehadiran['jenis_les_id'] ?? '') === (string) $jl['id'] ? 'selected' : '' ?>>
                                    <?= esc($jl['nama_les']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status Kehadiran</label>
                        <select name="status_kehadiran" class="form-select" required>
                            <?php $statusNow = old('status_kehadiran', $kehadiran['status_kehadiran'] ?? 'hadir'); ?>
                            <option value="hadir" <?= $statusNow === 'hadir' ? 'selected' : '' ?>>Hadir</option>
                            <option value="izin" <?= $statusNow === 'izin' ? 'selected' : '' ?>>Izin</option>
                            <option value="tidak_hadir" <?= $statusNow === 'tidak_hadir' ? 'selected' : '' ?>>Tidak Hadir</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" rows="3" class="form-control" placeholder="Catatan perubahan..."><?= esc(old('catatan', $kehadiran['catatan'] ?? '')) ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
</div>

<?= $this->include('admin/templates/footer') ?>
