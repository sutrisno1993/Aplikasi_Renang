<?= $this->include('admin/templates/header') ?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-primary"><i class="fas fa-edit me-2"></i>Edit Pembayaran</h5>
                        <a href="<?= base_url('admin/pembayaran/riwayat') ?>" class="btn btn-light btn-sm rounded-pill px-3">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Detail Anak -->
                    <div class="alert alert-info border-0 rounded-4 mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-2 text-center d-none d-md-block">
                                <i class="fas fa-user-circle fa-3x"></i>
                            </div>
                            <div class="col-md-10">
                                <h6 class="fw-bold mb-1"><?= esc($anak['nama']) ?> (<?= esc($anak['nama_panggilan']) ?>)</h6>
                                <p class="mb-0 small text-muted">ID Siswa: #<?= str_pad($anak['id'], 5, '0', STR_PAD_LEFT) ?></p>
                            </div>
                        </div>
                    </div>

                    <form action="<?= base_url('admin/pembayaran/update/' . $p['id']) ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tanggal Pembayaran</label>
                                <input type="datetime-local" name="tanggal" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($p['tanggal'])) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Berlaku Sampai</label>
                                <input type="date" name="berlaku_sampai" class="form-control" value="<?= $p['berlaku_sampai'] ?>" required>
                                <div class="form-text small">Perpanjang masa aktif paket (90 hari) tanpa pembayaran baru. Sisa/hangus dihitung ulang setelah simpan.</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Jumlah Pertemuan</label>
                                <input type="number" name="jumlah_pertemuan" id="jumlah_pertemuan" class="form-control" value="<?= $p['jumlah_pertemuan'] ?>" required>
                                <div class="form-text text-danger small">Hati-hati: Mengubah ini akan mengupdate sisa pertemuan anak secara otomatis.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Total Pembayaran (Rp)</label>
                                <input type="number" name="total" id="total_pembayaran" class="form-control" value="<?= (int)$p['total'] ?>" required>
                                <div class="form-text small text-primary" id="price-hint">Harga paket akan otomatis menyesuaikan saat Jenis Les diubah.</div>
                            </div>
                        </div>

                        <script>
                        $(document).ready(function() {
                            $('#jenis_les_id').on('change', function() {
                                const selectedOption = $(this).find('option:selected');
                                const hargaPerPertemuan = parseFloat(selectedOption.data('harga')) || 0;
                                const jumlahPertemuan = parseInt($('#jumlah_pertemuan').val()) || 4;
                                const inputTotal = $('#total_pembayaran');
                                
                                if (hargaPerPertemuan > 0) {
                                    // Hitung Total: Harga per Pertemuan x Jumlah Pertemuan (Paket 4x)
                                    const totalPaket = hargaPerPertemuan * jumlahPertemuan;
                                    inputTotal.val(totalPaket);
                                    
                                    // Efek Visual
                                    inputTotal.addClass('is-valid').css('background-color', '#d4edda');
                                    setTimeout(() => {
                                        inputTotal.removeClass('is-valid').css('background-color', '');
                                    }, 1500);
                                    
                                    console.log('Total Paket dihitung: ' + hargaPerPertemuan + ' x ' + jumlahPertemuan + ' = ' + totalPaket);
                                }
                            });

                            // Jika jumlah pertemuan diubah manual, total juga ikut update
                            $('#jumlah_pertemuan').on('input', function() {
                                const selectedOption = $('#jenis_les_id').find('option:selected');
                                const hargaPerPertemuan = parseFloat(selectedOption.data('harga')) || 0;
                                const jumlahPertemuan = parseInt($(this).val()) || 0;
                                $('#total_pembayaran').val(hargaPerPertemuan * jumlahPertemuan);
                            });
                        });
                        </script>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Jenis Les (Snapshot)</label>
                                <select name="jenis_les_id" id="jenis_les_id" class="form-select" required>
                                    <?php foreach ($jenisLes as $jl) : ?>
                                        <option value="<?= $jl['id'] ?>" 
                                                data-harga="<?= $jl['harga'] ?>" 
                                                <?= $p['jenis_les_id'] == $jl['id'] ? 'selected' : '' ?>>
                                            <?= esc($jl['nama_les']) ?> (Rp <?= number_format($jl['harga'], 0, ',', '.') ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text small">Mengubah ini akan mengunci riwayat paket pembayaran ini.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Metode Pembayaran</label>
                                <select name="metode_pembayaran" class="form-select" required>
                                    <option value="transfer_bca" <?= $p['metode_pembayaran'] == 'transfer_bca' ? 'selected' : '' ?>>Transfer BCA</option>
                                    <option value="transfer_bri" <?= $p['metode_pembayaran'] == 'transfer_bri' ? 'selected' : '' ?>>Transfer BRI</option>
                                    <option value="transfer_mandiri" <?= $p['metode_pembayaran'] == 'transfer_mandiri' ? 'selected' : '' ?>>Transfer Mandiri</option>
                                    <option value="cash" <?= $p['metode_pembayaran'] == 'cash' ? 'selected' : '' ?>>Tunai</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Catatan</label>
                            <textarea name="catatan" class="form-control" rows="2"><?= esc($p['catatan']) ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Bukti Pembayaran (Baru)</label>
                            <input type="file" name="bukti_pembayaran" class="form-control mb-2">
                            <?php if ($p['bukti_pembayaran']) : ?>
                                <div class="mt-2 p-2 border rounded text-center">
                                    <p class="small text-muted mb-2">Bukti Saat Ini:</p>
                                    <img src="<?= r2_url($p['bukti_pembayaran'], 'bukti_pembayaran') ?>" class="img-fluid rounded" style="max-height: 200px;">
                                </div>
                            <?php endif; ?>
                            <div class="form-text small text-muted italic">Kosongkan jika tidak ingin mengganti bukti pembayaran.</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm">
                                <i class="fas fa-save me-2"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('admin/templates/footer') ?>
