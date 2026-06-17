<?= $this->extend('templates/coach') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-lg">
                <div class="card-header bg-success text-white py-3">
                    <div class="d-flex align-items-center">
                        <a href="<?= base_url('coach/evaluasi') ?>" class="text-white mr-3"><i class="fas fa-arrow-left fs-4"></i></a>
                        <div>
                            <h5 class="mb-0 font-weight-bold">Raport Evaluasi Perkembangan</h5>
                            <p class="mb-0 small text-white-50">Siswa: <?= esc($student['nama']) ?> · Level: <?= esc($level['nama_level']) ?></p>
                        </div>
                    </div>
                </div>
                <form action="<?= base_url('coach/evaluasi/store') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="anak_id" value="<?= $student['id'] ?>">
                    
                    <div class="card-body p-4">
                        <div class="alert alert-light border rounded p-3 mb-4">
                            <span class="small font-weight-bold text-muted d-block mb-1">Panduan Pengisian Parameter Nilai:</span>
                            <span class="badge badge-success px-2 py-1 font-weight-bold">A</span> Sangat Baik · 
                            <span class="badge badge-primary px-2 py-1 font-weight-bold">B</span> Baik/Cukup · 
                            <span class="badge badge-danger px-2 py-1 font-weight-bold">C</span> Kurang/Perlu Latihan Ekstra
                        </div>

                        <!-- Parameter Teknik Fisik -->
                        <h6 class="font-weight-bold text-success border-bottom pb-2 mb-3">1. Parameter Teknik Fisik</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="teknik_kaki" class="form-label font-weight-bold small text-muted">Teknik Gerakan Kaki</label>
                                <select class="form-control" name="teknik_kaki" id="teknik_kaki" required>
                                    <option value="B">B (Baik)</option>
                                    <option value="A">A (Sangat Baik)</option>
                                    <option value="C">C (Kurang)</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="teknik_tangan" class="form-label font-weight-bold small text-muted">Teknik Gerakan Tangan</label>
                                <select class="form-control" name="teknik_tangan" id="teknik_tangan" required>
                                    <option value="B">B (Baik)</option>
                                    <option value="A">A (Sangat Baik)</option>
                                    <option value="C">C (Kurang)</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="teknik_pernapasan" class="form-label font-weight-bold small text-muted">Teknik Pernapasan</label>
                                <select class="form-control" name="teknik_pernapasan" id="teknik_pernapasan" required>
                                    <option value="B">B (Baik)</option>
                                    <option value="A">A (Sangat Baik)</option>
                                    <option value="C">C (Kurang)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Parameter Mental & Sikap -->
                        <h6 class="font-weight-bold text-success border-bottom pb-2 mt-4 mb-3">2. Parameter Mental & Karakter</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="keberanian" class="form-label font-weight-bold small text-muted">Keberanian & Kemandirian</label>
                                <select class="form-control" name="keberanian" id="keberanian" required>
                                    <option value="B">B (Baik)</option>
                                    <option value="A">A (Sangat Baik)</option>
                                    <option value="C">C (Kurang)</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="disiplin" class="form-label font-weight-bold small text-muted">Kedisiplinan & Kehadiran</label>
                                <select class="form-control" name="disiplin" id="disiplin" required>
                                    <option value="B">B (Baik)</option>
                                    <option value="A">A (Sangat Baik)</option>
                                    <option value="C">C (Kurang)</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="sikap_fokus" class="form-label font-weight-bold small text-muted">Sikap Fokus & Kepatuhan</label>
                                <select class="form-control" name="sikap_fokus" id="sikap_fokus" required>
                                    <option value="B">B (Baik)</option>
                                    <option value="A">A (Sangat Baik)</option>
                                    <option value="C">C (Kurang)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Catatan Catatan Pelatih -->
                        <h6 class="font-weight-bold text-success border-bottom pb-2 mt-4 mb-3">3. Catatan Tambahan Coach</h6>
                        <div class="mb-3">
                            <label for="catatan_coach" class="form-label font-weight-bold small text-muted">Catatan Evaluasi / Rekomendasi Latihan Mingguan</label>
                            <textarea class="form-control" name="catatan_coach" id="catatan_coach" rows="4" placeholder="Tuliskan catatan khusus perkembangan teknik, motivasi, atau pekerjaan rumah (PR) teknik renang anak didik Anda..."></textarea>
                        </div>
                    </div>
                    <div class="card-footer bg-light p-3 d-flex justify-content-between">
                        <a href="<?= base_url('coach/evaluasi') ?>" class="btn btn-secondary rounded-pill px-4">Batal</a>
                        <button type="submit" class="btn btn-success rounded-pill px-5 shadow-sm">Simpan Evaluasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
