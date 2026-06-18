<?= $this->extend('templates/parent') ?>

<?= $this->section('content') ?>
<style>
.progress-container {
    background: #E5E7EB;
    border-radius: 9999px;
    height: 10px;
    overflow: hidden;
}
.progress-bar-custom {
    background: linear-gradient(135deg, var(--primary), var(--primary-light));
    height: 100%;
    border-radius: 9999px;
    transition: width 0.4s ease;
}
.grade-badge {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 13px;
}
.grade-A { background: #D1FAE5; color: #065F46; }
.grade-B { background: #DBEAFE; color: #1E40AF; }
.grade-C { background: #FEE2E2; color: #991B1B; }

.section-divider {
    border-bottom: 2px dashed rgba(0, 0, 0, 0.05);
    margin: 20px 0;
}
</style>

<div class="mb-4">
    <h4 class="font-weight-bold text-dark mb-1">Kurikulum & Perkembangan</h4>
    <p class="text-muted small">Pantau capaian teknik renang terstruktur putra-putri Anda.</p>
</div>

<?php if (empty($children)): ?>
    <div class="card border-0 p-4 text-center my-4">
        <div class="py-4">
            <span class="fs-1 d-block mb-3">🏊</span>
            <h6 class="font-weight-bold text-dark">Belum Ada Data Siswa</h6>
            <p class="text-muted small">Siswa aktif belum didaftarkan di bawah akun Anda.</p>
        </div>
    </div>
<?php else: ?>
    <!-- Loop Children -->
    <?php foreach ($children as $cData): ?>
        <?php 
            $c = $cData['child'];
            $evals = $cData['evaluations'];
            $exams = $cData['exams'];
            $certs = $cData['certificates'];
        ?>
        <div class="card border-0 mb-4 shadow-sm p-3">
            <!-- Header Anak -->
            <div class="d-flex align-items-center mb-3">
                <div class="logo mr-3" aria-hidden="true" style="width: 48px; height: 48px; border-radius: 50%; background: rgba(79, 70, 229, 0.1); color: var(--primary);">
                    <i class="fas fa-swimmer fs-4"></i>
                </div>
                <div>
                    <h5 class="font-weight-bold text-dark mb-0"><?= esc($c['nama']) ?></h5>
                    <span class="text-muted small">Coach: <?= esc($c['nama_coach'] ?? 'Belum Ditugaskan') ?></span>
                </div>
            </div>

            <!-- Tracker Level -->
            <div class="bg-light p-3 rounded-lg mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small font-weight-bold text-dark">Level Kompetensi:</span>
                    <span class="badge badge-primary px-2 py-1 font-weight-bold"><?= esc($c['nama_level'] ?? 'Level 1 : Water Introduction') ?></span>
                </div>
                <?php
                    // Calculate level sequence (e.g. extracts number from "Level X : ...")
                    $levelNum = 1;
                    if (!empty($c['nama_level'])) {
                        preg_match('/Level (\d+)/i', $c['nama_level'], $matches);
                        $levelNum = isset($matches[1]) ? (int) $matches[1] : 1;
                    }
                    $percent = ($levelNum / 7) * 100;
                ?>
                <div class="progress-container mb-1">
                    <div class="progress-bar-custom" style="width: <?= $percent ?>%"></div>
                </div>
                <div class="d-flex justify-content-between small text-muted">
                    <span>Level 1</span>
                    <span>Progres: <?= round($percent) ?>%</span>
                    <span>Level 7</span>
                </div>
            </div>

            <!-- Tabbed Panel for Child -->
            <ul class="nav nav-tabs" id="childTab-<?= $c['id'] ?>" role="tablist">
                <li class="nav-item m-0">
                    <a class="nav-link active small p-2" id="eval-tab-<?= $c['id'] ?>" data-toggle="tab" href="#eval-panel-<?= $c['id'] ?>" role="tab">Raport</a>
                </li>
                <li class="nav-item m-0">
                    <a class="nav-link small p-2" id="exam-tab-<?= $c['id'] ?>" data-toggle="tab" href="#exam-panel-<?= $c['id'] ?>" role="tab">Ujian</a>
                </li>
                <li class="nav-item m-0">
                    <a class="nav-link small p-2" id="cert-tab-<?= $c['id'] ?>" data-toggle="tab" href="#cert-panel-<?= $c['id'] ?>" role="tab">Sertifikat</a>
                </li>
            </ul>

            <div class="tab-content pt-2">
                <!-- Raport Panel -->
                <div class="tab-pane fade show active" id="eval-panel-<?= $c['id'] ?>" role="tabpanel">
                    <h6 class="font-weight-bold text-dark mb-2">Evaluasi Mingguan Pelatih</h6>
                    <?php if (empty($evals)): ?>
                        <p class="text-muted small py-3 text-center">Belum ada raport evaluasi mingguan.</p>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($evals as $ev): ?>
                                <div class="border rounded p-3 mb-2 bg-white">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="small font-weight-bold text-secondary"><?= date('d M Y', strtotime($ev['tanggal'])) ?></span>
                                        <span class="badge badge-light border text-muted small"><?= esc($ev['nama_level']) ?></span>
                                    </div>
                                    <div class="row text-center mb-2">
                                        <div class="col-4 border-right">
                                            <span class="d-block small text-muted mb-1">Kaki</span>
                                            <span class="grade-badge grade-<?= $ev['teknik_kaki'] ?>"><?= $ev['teknik_kaki'] ?></span>
                                        </div>
                                        <div class="col-4 border-right">
                                            <span class="d-block small text-muted mb-1">Tangan</span>
                                            <span class="grade-badge grade-<?= $ev['teknik_tangan'] ?>"><?= $ev['teknik_tangan'] ?></span>
                                        </div>
                                        <div class="col-4">
                                            <span class="d-block small text-muted mb-1">Nafas</span>
                                            <span class="grade-badge grade-<?= $ev['teknik_pernapasan'] ?>"><?= $ev['teknik_pernapasan'] ?></span>
                                        </div>
                                    </div>
                                    <div class="row text-center mb-2">
                                        <div class="col-4 border-right">
                                            <span class="d-block small text-muted mb-1">Berani</span>
                                            <span class="grade-badge grade-<?= $ev['keberanian'] ?>"><?= $ev['keberanian'] ?></span>
                                        </div>
                                        <div class="col-4 border-right">
                                            <span class="d-block small text-muted mb-1">Disiplin</span>
                                            <span class="grade-badge grade-<?= $ev['disiplin'] ?>"><?= $ev['disiplin'] ?></span>
                                        </div>
                                        <div class="col-4">
                                            <span class="d-block small text-muted mb-1">Fokus</span>
                                            <span class="grade-badge grade-<?= $ev['sikap_fokus'] ?>"><?= $ev['sikap_fokus'] ?></span>
                                        </div>
                                    </div>
                                    <?php if (!empty($ev['catatan_coach'])): ?>
                                        <div class="bg-light p-2 rounded small text-muted mt-2">
                                            <strong>Catatan Coach:</strong> <?= esc($ev['catatan_coach']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Ujian Panel -->
                <div class="tab-pane fade" id="exam-panel-<?= $c['id'] ?>" role="tabpanel">
                    <h6 class="font-weight-bold text-dark mb-2">Riwayat Ujian Kenaikan</h6>
                    <?php if (empty($exams)): ?>
                        <p class="text-muted small py-3 text-center">Belum ada riwayat ujian kenaikan tingkat.</p>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($exams as $ex): ?>
                                <div class="border rounded p-3 mb-2 bg-white">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="small font-weight-bold text-secondary"><?= date('d M Y', strtotime($ex['tanggal'])) ?></span>
                                        <?php if ($ex['status_kelulusan'] === 'lulus'): ?>
                                            <span class="badge badge-success rounded-pill font-weight-bold">LULUS</span>
                                        <?php elseif ($ex['status_kelulusan'] === 'pending'): ?>
                                            <span class="badge badge-warning rounded-pill font-weight-bold">MENUNGGU</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger rounded-pill font-weight-bold">TIDAK LULUS</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="small mb-1">
                                        <span class="text-muted">Target Naik:</span> 
                                        <strong><?= esc($ex['level_tujuan_nama']) ?></strong>
                                    </div>
                                    <div class="small mb-1">
                                        <span class="text-muted">Penguji:</span> 
                                        <strong><?= esc($ex['nama_examiner']) ?></strong>
                                    </div>
                                    <?php if (!empty($ex['tournament_name'])): ?>
                                        <div class="alert alert-info py-1 px-2 mb-1 mt-1 small">
                                            <i class="fas fa-trophy text-warning mr-1"></i> <?= esc($ex['tournament_name']) ?> - <strong><?= esc($ex['prestasi']) ?></strong>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($ex['catatan_evaluasi'])): ?>
                                        <div class="bg-light p-2 rounded small text-muted mt-2">
                                            <strong>Saran Evaluator:</strong> <?= esc($ex['catatan_evaluasi']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Sertifikat Panel -->
                <div class="tab-pane fade" id="cert-panel-<?= $c['id'] ?>" role="tabpanel">
                    <h6 class="font-weight-bold text-dark mb-2">Sertifikat Kelulusan Level</h6>
                    <?php if (empty($certs)): ?>
                        <p class="text-muted small py-3 text-center">Belum ada sertifikat digital kelulusan terbit.</p>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($certs as $crt): ?>
                                <div class="border rounded p-3 mb-2 bg-white d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="font-weight-bold text-dark small d-block"><?= esc($crt['nama_level']) ?></span>
                                        <small class="text-muted"><?= esc($crt['nomor_sertifikat']) ?></small>
                                    </div>
                                    <div>
                                        <a href="<?= base_url('parent/certificate/download/' . $c['id'] . '/' . $crt['level_id']) ?>" target="_blank" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm mr-2">
                                            <i class="fas fa-certificate mr-1"></i> Sertifikat
                                        </a>
                                        <a href="<?= base_url('parent/raport/download/' . $c['id'] . '/' . $crt['level_id']) ?>" target="_blank" class="btn btn-sm btn-outline-info rounded-pill px-3 shadow-sm">
                                            <i class="fas fa-file-invoice mr-1"></i> Raport
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
<?= $this->endSection() ?>
