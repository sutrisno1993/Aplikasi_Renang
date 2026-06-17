<?= $this->extend('templates/coach') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-lg">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex align-items-center">
                        <a href="<?= base_url('coach/ujian') ?>" class="text-white mr-3"><i class="fas fa-arrow-left fs-4"></i></a>
                        <div>
                            <h5 class="mb-0 font-weight-bold">Penilaian Ujian Kenaikan Tingkat</h5>
                            <p class="mb-0 small text-white-50">Siswa: <?= esc($exam['nama_anak']) ?> · Naik Ke: <?= esc($exam['level_tujuan_nama']) ?></p>
                        </div>
                    </div>
                </div>
                <form action="<?= base_url('coach/ujian/evaluasi/store') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="exam_id" value="<?= $exam['id'] ?>">
                    
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label for="status_kelulusan" class="form-label font-weight-bold text-primary">Keputusan Ujian</label>
                            <select class="form-control form-control-lg font-weight-bold text-dark border-primary" name="status_kelulusan" id="status_kelulusan" required>
                                <option value="lulus">LULUS (Naik Tingkat & Terbitkan Sertifikat)</option>
                                <option value="tidak_lulus">TIDAK LULUS (Perlu Mengulang)</option>
                            </select>
                        </div>

                        <!-- Parameter Nilai Ujian -->
                        <h6 class="font-weight-bold text-primary border-bottom pb-2 mb-3">1. Nilai Kriteria Ujian</h6>
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
                                <label for="disiplin" class="form-label font-weight-bold small text-muted">Kedisiplinan</label>
                                <select class="form-control" name="disiplin" id="disiplin" required>
                                    <option value="B">B (Baik)</option>
                                    <option value="A">A (Sangat Baik)</option>
                                    <option value="C">C (Kurang)</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="sikap_fokus" class="form-label font-weight-bold small text-muted">Sikap Fokus</label>
                                <select class="form-control" name="sikap_fokus" id="sikap_fokus" required>
                                    <option value="B">B (Baik)</option>
                                    <option value="A">A (Sangat Baik)</option>
                                    <option value="C">C (Kurang)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Integrasi Turnamen (Optional) -->
                        <h6 class="font-weight-bold text-primary border-bottom pb-2 mt-4 mb-3">2. Integrasi Prestasi Kejuaraan / Turnamen (Opsional)</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tournament_name" class="form-label font-weight-bold small text-muted">Nama Kejuaraan / Kompetisi</label>
                                <input type="text" class="form-control" name="tournament_name" id="tournament_name" placeholder="Contoh: Kejuaraan Renang Walikota Cup 2026">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="prestasi" class="form-label font-weight-bold small text-muted">Prestasi / Juara Yang Diraih</label>
                                <input type="text" class="form-control" name="prestasi" id="prestasi" placeholder="Contoh: Juara 1 Gaya Bebas 50m Putra">
                            </div>
                        </div>

                        <!-- Catatan Catatan Evaluasi -->
                        <h6 class="font-weight-bold text-primary border-bottom pb-2 mt-4 mb-3">3. Catatan Evaluasi Head Coach</h6>
                        <div class="mb-3">
                            <label for="catatan_evaluasi" class="form-label font-weight-bold small text-muted">Umpan Balik & Catatan Penguji</label>
                            <textarea class="form-control" name="catatan_evaluasi" id="catatan_evaluasi" rows="4" placeholder="Berikan saran teknik, umpan balik detail untuk anak didik atau pelatih pendampingnya..."></textarea>
                        </div>
                    </div>
                    <div class="card-footer bg-light p-3 d-flex justify-content-between">
                        <a href="<?= base_url('coach/ujian') ?>" class="btn btn-secondary rounded-pill px-4">Batal</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">Simpan Hasil Ujian</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
