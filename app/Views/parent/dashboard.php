<?= $this->extend('templates/parent') ?>

<?= $this->section('content') ?>
<?php
    $anak = $anak ?? [];
    $pembayaran = $pembayaran ?? [];
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Bootstrap 4 Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<style>
.modal-body {
    max-height: 70vh;
    overflow-y: auto;
    font-size: 13px;
    color: var(--text-main);
}

.list-group-item {
    border-radius: 12px !important;
    margin-bottom: 10px;
    border: 1px solid rgba(0, 0, 0, 0.06) !important;
    background: var(--card-bg) !important;
    color: var(--text-main) !important;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
    transition: all 0.2s ease;
}

.list-group-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04);
}

.list-group-item i {
    width: 20px;
    text-align: center;
    color: var(--primary);
}

/* Style untuk modal */
.modal-dialog {
    max-width: 90% !important;
    width: 90% !important;
    margin: 1.75rem auto;
}

.modal-content {
    border-radius: var(--border-radius);
    border: 1px solid rgba(0, 0, 0, 0.06);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.06);
    background-color: #F9FAFB;
}

.modal-header .modal-title {
    font-size: 15px !important;
    font-weight: 700;
    color: var(--text-main);
}

.modal-body label {
    font-size: 12px !important;
    font-weight: 500;
    color: var(--text-main);
    margin-bottom: 6px;
    display: block;
}

.modal-body input,
.modal-body select,
.modal-body textarea {
    font-size: 13px !important;
    padding: 0.6rem 0.75rem !important;
    border-radius: 10px;
    border: 1px solid rgba(0, 0, 0, 0.12);
    width: 100% !important;
    max-width: 100% !important;
    min-width: 100% !important;
    margin-bottom: 0.9rem;
    height: auto !important;
    background-color: #FFFFFF;
    color: var(--text-main);
    transition: all 0.2s ease;
}

.modal-body input:focus,
.modal-body select:focus,
.modal-body textarea:focus {
    border-color: var(--primary-light);
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
    outline: none;
}

.modal-body select option {
    padding: 0.6rem !important;
    white-space: normal !important;
    word-wrap: break-word !important;
}

.modal-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid rgba(0, 0, 0, 0.06);
    background-color: #F9FAFB;
}

.modal-footer .btn {
    font-size: 12px !important;
    padding: 0.5rem 1.25rem !important;
    border-radius: 10px;
    min-width: 100px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    h3, h4, h5 {
        font-size: 15px !important;
    }
    
    p, .card-text, td, th {
        font-size: 12px !important;
    }
    
    .badge, .form-label {
        font-size: 11px !important;
    }
    
    .btn {
        font-size: 11px !important;
        padding: 0.45rem 0.85rem !important;
    }
    
    .alert {
        font-size: 12px !important;
    }
    
    .nav-link {
        font-size: 12px !important;
        padding: 0.55rem 0.75rem !important;
    }
    
    .table {
        font-size: 12px !important;
    }
    
    .card-header {
        font-size: 13px !important;
    }
    
    .badge-success, .badge-danger, .badge-info, .badge-warning {
        font-size: 10px !important;
        padding: 0.35em 0.55em !important;
    }
    
        .modal-dialog {
            max-width: 95% !important;
            width: 95% !important;
            margin: 0.5rem auto;
        }
    }
    /* Chevron animation for collapsible card */
    .transition-transform {
        transition: transform 0.2s ease-in-out;
        display: inline-block;
    }
    [data-toggle="collapse"].collapsed .transition-transform {
        transform: rotate(-90deg);
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header">
                <h3>Dashboard Orang Tua/Wali</h3>
            </div>
            <div class="card-body">
                <h5>Selamat datang, <?= session()->get('parent_nama') ?>!</h5>
                <details class="mt-2">
                    <summary class="small">Informasi Akun</summary>
                    <div class="alert alert-info mt-2">
                        <div class="small">Nama: <?= session()->get('parent_nama') ?></div>
                    </div>
                </details>
            </div>
        </div>
        
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs mb-4" id="parentTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="anak-tab" data-toggle="tab" href="#anak" role="tab" aria-controls="anak" aria-selected="true">
                    <i class="fas fa-child"></i> Daftar Anak
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="pembayaran-tab" data-toggle="tab" href="#pembayaran" role="tab" aria-controls="pembayaran" aria-selected="false">
                    <i class="fas fa-money-bill-wave"></i> Pembayaran
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="jadwal-tab" data-toggle="tab" href="#jadwal" role="tab" aria-controls="jadwal" aria-selected="false">
                    <i class="fas fa-calendar-alt"></i> Jadwal Les
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="riwayat-latihan-tab" data-toggle="tab" href="#riwayat-latihan" role="tab" aria-controls="riwayat-latihan" aria-selected="false">
                    <i class="fas fa-history"></i> Riwayat Latihan
                </a>
            </li>
        </ul>
        
        <!-- Tab Content -->
        <div class="tab-content" id="parentTabContent">
            <!-- Tab Riwayat Latihan -->
<div class="tab-pane fade" id="riwayat-latihan" role="tabpanel" aria-labelledby="riwayat-latihan-tab">
    <div class="card">
        <div class="card-header">
            <h4>Riwayat Latihan Anak</h4>
        </div>
        <div class="card-body">
            <?php if(empty($anak)): ?>
                <div class="alert alert-info">
                    Belum ada data anak yang terdaftar.
                </div>
            <?php else: ?>
                <?php foreach($anak as $a): ?>
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><?= $a['nama'] ?></h5>
                        </div>
                        <div class="card-body">
                            <?php 
                            $riwayat_latihan = [];
                            $unique_schedules = [];
                            
                            if(!empty($latihan_attendance)) {
                                foreach($latihan_attendance as $la) {
                                    if($la['anak_id'] == $a['id']) {
                                        // Buat kunci unik berdasarkan tanggal, waktu, dan materi
                                        $schedule_key = $la['tanggal'] . $la['jam_mulai'] . $la['jam_selesai'] . $la['materi'];
                                        if (!isset($unique_schedules[$schedule_key])) {
                                            $unique_schedules[$schedule_key] = $la;
                                            $riwayat_latihan[] = $la;
                                        }
                                    }
                                }
                            }
                            ?>

                            <?php if(empty($riwayat_latihan)): ?>
                                <div class="alert alert-info">
                                    Belum ada riwayat latihan untuk <?= $a['nama'] ?>.
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tanggal</th>
                                                <th>Waktu</th>
                                                <th>Materi</th>
                                                <th>Status Kehadiran</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $total_latihan = count($riwayat_latihan);
                                            foreach($riwayat_latihan as $index => $rl): 
                                                $nomor_latihan = $total_latihan - $index;
                                            ?>
                                                <tr>
                                                    <td><strong>Latihan Renang <?= $nomor_latihan ?></strong></td>
                                                    <td>
                                                        <?php 
                                                        $hari = date('l', strtotime($rl['tanggal']));
                                                        $hari_indo = [
                                                            'Sunday' => 'Minggu',
                                                            'Monday' => 'Senin',
                                                            'Tuesday' => 'Selasa',
                                                            'Wednesday' => 'Rabu',
                                                            'Thursday' => 'Kamis',
                                                            'Friday' => 'Jumat',
                                                            'Saturday' => 'Sabtu'
                                                        ];
                                                        echo $hari_indo[$hari] . ', ' . date('d-m-Y', strtotime($rl['tanggal']));
                                                        ?>
                                                    </td>
                                                    <td><?= $rl['jam_mulai'] ?> - <?= $rl['jam_selesai'] ?></td>
                                                    <td><?= $rl['materi'] ?></td>
                                                    <td>
                                                        <?php
                                                        $badge_class = 'badge-primary';
                                                        if($rl['status_kehadiran'] == 'hadir') $badge_class = 'badge-success';
                                                        elseif($rl['status_kehadiran'] == 'tidak_hadir') $badge_class = 'badge-danger';
                                                        elseif($rl['status_kehadiran'] == 'izin') $badge_class = 'badge-warning';
                                                        ?>
                                                        <span class="badge <?= $badge_class ?>">
                                                            <?= ucfirst(str_replace('_', ' ', $rl['status_kehadiran'])) ?>
                                                        </span>
                                                    </td>

                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
               <!-- Tab Jadwal -->
    <div class="tab-pane fade" id="jadwal" role="tabpanel" aria-labelledby="jadwal-tab">
        <div class="card mb-4">
            <div class="card-header">
                <h4>Jadwal Les</h4>
            </div>
            <div class="card-body">
                <?php if(empty($jadwal)): ?>
                    <div class="alert alert-info">
                        Belum ada jadwal les yang tersedia.
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach($jadwal as $j): ?>
                            <div class="col-12 col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">
                                            <?php 
                                            $hari = date('l', strtotime($j['tanggal']));
                                            $hari_indo = [
                                                'Sunday' => 'Minggu',
                                                'Monday' => 'Senin', 
                                                'Tuesday' => 'Selasa',
                                                'Wednesday' => 'Rabu',
                                                'Thursday' => 'Kamis',
                                                'Friday' => 'Jumat',
                                                'Saturday' => 'Sabtu'
                                            ];
                                            echo $hari_indo[$hari] . ', ' . date('d-m-Y', strtotime($j['tanggal']));
                                            ?>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <i class="fas fa-clock text-primary"></i>
                                            <strong>Waktu:</strong>
                                            <p class="mb-0"><?= $j['jam_mulai'] ?> - <?= $j['jam_selesai'] ?></p>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <i class="fas fa-swimming-pool text-primary"></i>
                                            <strong>Jenis Les:</strong><br>
                                            <?php 
                                            if(!empty($j['jenis_les_names'])) {
                                                $jenis_les_array = array_unique(explode(',', $j['jenis_les_names']));
                                                foreach($jenis_les_array as $les): ?>
                                                    <span class="badge badge-info me-1"><?= trim($les) ?></span>
                                                <?php endforeach;
                                            } else {
                                                echo '<span class="badge badge-secondary">Tidak ada</span>';
                                            } ?>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <i class="fas fa-book text-primary"></i>
                                            <strong>Materi:</strong>
                                            <p class="mb-0"><?= $j['materi'] ?></p>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <i class="fas fa-users text-primary"></i>
                                            <strong>Kapasitas:</strong>
                                            <p class="mb-0"><?= $j['kapasitas'] ?> orang</p>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <i class="fas fa-info-circle text-primary"></i>
                                            <strong>Status:</strong><br>
                                            <?php if($j['status'] == 'aktif'): ?>
                                                <span class="badge badge-success">Tersedia</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Penuh</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="card-footer bg-white">
                                    <div class="d-flex justify-content-between">
                                        <?php if($j['status'] == 'aktif'): ?>
                                            <a href="<?= base_url('parent/jadwal/daftar/'.$j['id']) ?>" 
                                               class="btn btn-primary">
                                                <i class="fas fa-user-plus"></i> Daftar
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-secondary" disabled>
                                                <i class="fas fa-ban"></i> Penuh
                                            </button>
                                        <?php endif; ?>
                                        
                                        <!-- Tombol Detail Jadwal -->
                                        <a href="<?= base_url('parent/jadwal/detail/'.$j['id']) ?>" class="btn btn-info btn-sm">
                                            <i class="fas fa-info-circle"></i> Detail
                                        </a>
                                    </div>
                                </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Jadwal Terdaftar -->
        <div class="card">
            <div class="card-header">
                <h4>Jadwal Les Terdaftar</h4>
            </div>
            <div class="card-body">
                <?php if(empty($jadwal_terdaftar)): ?>
                    <div class="alert alert-info">
                        Belum ada jadwal les yang terdaftar.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                    <th>Nama Anak</th>
                                    <th>Jenis Les</th>
                                    <th>Materi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($jadwal_terdaftar as $jt): ?>
                                    <tr>
                                        <td><?php 
                $hari = date('l', strtotime($jt['tanggal']));
                $hari_indo = [
                    'Sunday' => 'Minggu',
                    'Monday' => 'Senin',
                    'Tuesday' => 'Selasa', 
                    'Wednesday' => 'Rabu',
                    'Thursday' => 'Kamis',
                    'Friday' => 'Jumat',
                    'Saturday' => 'Sabtu'
                ];
                echo $hari_indo[$hari] . ', ' . date('d-m-Y', strtotime($jt['tanggal']));
            ?></td>
                                        <td><?= $jt['jam_mulai'] ?> - <?= $jt['jam_selesai'] ?></td>
                                        <td><?= $jt['nama_anak'] ?? '-' ?></td>
                                        <td>
    <?php 
    if (isset($jt['jenis_les_names'])): 
        $jenis_les_array = explode(',', $jt['jenis_les_names']);
        foreach($jenis_les_array as $les): ?>
            <span class="badge badge-info me-1"><?= $les ?></span>
    <?php 
        endforeach;
    else:
        echo '-';
    endif;
    ?>
</td>
                                        <td><?= $jt['materi'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
            <!-- Tab Daftar Anak -->
            <div class="tab-pane fade show active" id="anak" role="tabpanel" aria-labelledby="anak-tab">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Daftar Anak</h4>
                        <a href="<?= base_url('parent/tambah-anak-form') ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Tambah Anak
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if(session()->getFlashdata('success')): ?>
                            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                        <?php endif; ?>
                        
                        <?php if(session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                        <?php endif; ?>
                        
                        <?php if(empty($anak)): ?>
                            <div class="alert alert-info">Anda belum mendaftarkan anak. Silakan tambahkan anak untuk mengikuti les renang.</div>
                        <?php else: ?>
                            <div class="list-group">
                                <?php foreach($anak as $a): ?>
                                    <?php
                                        $sisa = (int) ($a['sisa_pertemuan'] ?? 0);
                                        $isActive = $sisa > 0;
                                        $progressMax = 4;
                                        $progress = $progressMax > 0 ? (int) round(min(1, $sisa / $progressMax) * 100) : 0;

                                        $lastPayment = null;
                                        if (!empty($pembayaran)) {
                                            foreach ($pembayaran as $p) {
                                                if (($p['anak_id'] ?? null) == ($a['id'] ?? null) && ($p['status'] ?? null) === 'success' && !empty($p['berlaku_sampai'])) {
                                                    $lastPayment = $p;
                                                    break;
                                                }
                                            }
                                        }

                                        $detailId = 'anak-detail-' . ($a['id'] ?? '0');
                                    ?>
                                    <div class="list-group-item mb-2">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <?php if(!empty($a['foto'])): ?>
                                                    <img src="<?= r2_url($a['foto'], 'anak') ?>" 
                                                        alt="Foto <?= esc((string) ($a['nama'] ?? '')) ?>" 
                                                        class="rounded-circle mr-2" 
                                                        style="width: 44px; height: 44px; object-fit: cover;"
                                                        onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<?= urlencode($a['nama'] ?? 'User') ?>&color=7F9CF5&background=EBF4FF';">
                                                <?php else: ?>
                                                    <div class="bg-primary rounded-circle mr-2 d-flex align-items-center justify-content-center text-white fw-bold" style="width: 44px; height: 44px; font-size: 0.9rem;">
                                                        <?= get_initials($a['nama'] ?? 'U') ?>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="min-w-0">
                                                    <div class="font-weight-bold mb-0"><?= esc((string) ($a['nama'] ?? '-')) ?></div>
                                                    <div class="small text-muted">#<?= esc((string) ($a['id'] ?? '-')) ?> • <?= esc((string) ($a['nama_les'] ?? 'Belum ditentukan')) ?></div>
                                                </div>
                                            </div>
                                            <button class="btn btn-sm btn-outline-info" type="button" data-toggle="collapse" data-target="#<?= esc((string) $detailId) ?>" aria-expanded="false" aria-controls="<?= esc((string) $detailId) ?>">
                                                Detail
                                            </button>
                                        </div>

                                        <div class="mt-2 d-flex align-items-center justify-content-between">
                                            <span class="badge badge-<?= $isActive ? 'success' : 'danger' ?>">
                                                <?= $isActive ? 'Aktif' : 'Non Aktif' ?>
                                            </span>
                                            <span class="badge badge-<?= $sisa < 2 ? 'warning' : 'primary' ?>">Sisa <?= $sisa ?>x</span>
                                        </div>

                                        <div class="progress mt-2" style="height: 6px;">
                                            <div class="progress-bar bg-<?= $isActive ? 'info' : 'secondary' ?>" role="progressbar" style="width: <?= $progress ?>%;" aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>

                                        <div class="collapse mt-3" id="<?= esc((string) $detailId) ?>">
                                            <div class="small text-muted mb-2">Detail Anak</div>
                                            <div class="table-responsive">
                                                <table class="table table-sm mb-2">
                                                    <tbody>
                                                        <tr>
                                                            <th style="width: 40%;">Nama Panggilan</th>
                                                            <td><?= esc((string) ($a['nama_panggilan'] ?? '-')) ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Jenis Kelamin</th>
                                                            <td><?= esc((string) ($a['jenis_kelamin'] ?? '-')) ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Tanggal Lahir</th>
                                                            <td><?= !empty($a['tanggal_lahir']) ? esc(date('d-m-Y', strtotime((string) $a['tanggal_lahir']))) : '-' ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Asal Sekolah</th>
                                                            <td><?= esc((string) ($a['asal_sekolah'] ?? '-')) ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Riwayat Penyakit</th>
                                                            <td><?= esc((string) ($a['riwayat_penyakit'] ?? '-')) ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Berlaku Sampai</th>
                                                            <td>
                                                                <?php if (!empty($a['berlaku_sampai'])): ?>
                                                                    <?= esc(date('d-m-Y', strtotime($a['berlaku_sampai']))) ?>
                                                                <?php else: ?>
                                                                    -
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                        <?php if (!empty($a['hangus_total'])): ?>
                                                        <tr>
                                                            <th>Pertemuan Hangus</th>
                                                            <td class="text-danger fw-bold"><?= (int) $a['hangus_total'] ?> (lewat masa berlaku)</td>
                                                        </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <?php if (!empty($a['history_groups'])): ?>
                                            <div class="small text-muted mb-1">Pemetaan paket (FIFO)</div>
                                            <div class="mb-3" style="max-height: 220px; overflow-y: auto;">
                                                <?php foreach ($a['history_groups'] as $grp): ?>
                                                    <div class="border rounded p-2 mb-2 small" style="background: #f8f9fa;">
                                                        <strong><?= esc($grp['label']) ?></strong>
                                                        <?php if (!empty($grp['is_expired'])): ?>
                                                            <span class="badge badge-danger ml-1">Expired</span>
                                                            <?php if (!empty($grp['hangus'])): ?>
                                                                <span class="text-danger">· <?= (int) $grp['hangus'] ?> hangus</span>
                                                            <?php endif; ?>
                                                        <?php elseif (($grp['sisa_aktif'] ?? 0) > 0): ?>
                                                            <span class="text-success">· sisa <?= (int) $grp['sisa_aktif'] ?></span>
                                                        <?php endif; ?>
                                                        <?php if (!empty($grp['berlaku_sampai'])): ?>
                                                            <div class="text-muted">s/d <?= date('d-m-Y', strtotime($grp['berlaku_sampai'])) ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <?php endif; ?>

                                            <div class="d-flex justify-content-end">
                                                <a href="<?= base_url('parent/edit-anak/' . (string) ($a['id'] ?? '')) ?>" class="btn btn-sm btn-warning mr-2">
                                                    Edit
                                                </a>
                                                <?php if(!$isActive): ?>
                                                    <button class="btn btn-sm btn-primary" type="button" onclick="$('#pembayaran-tab').tab('show')">
                                                        Bayar
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
<!-- Tab Pembayaran -->

<div class="tab-pane fade" id="pembayaran" role="tabpanel" aria-labelledby="pembayaran-tab">
    <div class="card">
        <div class="card-header">
            <h4>Pembayaran Les</h4>
        </div>
        <div class="card-body">
            <?php if(empty($anak)): ?>
                <div class="alert alert-info">Belum ada data anak yang terdaftar.</div>
            <?php else: ?>
                <?php foreach($anak as $a): ?>
                    <?php if($a['status'] != 'aktif' || $a['sisa_pertemuan'] <= 0): ?>
                        <?php
                            $namaLesPembayaran = strtolower(trim((string) ($a['nama_les'] ?? '')));
                            $hargaPerPertemuan = 0;
                            if ($namaLesPembayaran === 'private') {
                                $hargaPerPertemuan = 150000;
                            } elseif ($namaLesPembayaran === 'reguler' || $namaLesPembayaran === 'regular') {
                                $hargaPerPertemuan = 75000;
                            }
                            $totalPaket = $hargaPerPertemuan * 4;
                            $hasPendingPembayaran = !empty($a['has_pending_pembayaran']);
                            $canPay = !empty($a['can_pay']) && $hargaPerPertemuan > 0;
                        ?>
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <?= $a['nama'] ?>
                                    <span class="badge badge-<?= $a['status'] == 'aktif' ? 'success' : 'warning' ?> ml-2">
                                        <?= ucfirst($a['status']) ?>
                                    </span>
                                </h5>
                            </div>
                            <div class="card-body">
                                <!-- Informasi Jenis Les -->
                                <div class="mb-3">
                                    <label class="form-label"><strong>Jenis Les:</strong></label>
                                    <?php if(isset($a['nama_les']) && !empty($a['nama_les'])): ?>
                                        <span class="badge badge-info"><?= $a['nama_les'] ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Belum ditentukan</span>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><strong>Harga per Pertemuan:</strong></label>
                                    <?php if($hargaPerPertemuan > 0): ?>
                                        <h4 class="text-primary">Rp <?= number_format($hargaPerPertemuan, 0, ',', '.') ?></h4>
                                    <?php else: ?>
                                        <div class="alert alert-warning mb-0">
                                            Jenis les belum valid untuk perhitungan tarif. Silakan hubungi admin untuk set jenis les <strong>private</strong> / <strong>reguler</strong>.
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Informasi Sisa Pertemuan -->
                                <div class="mb-3">
                                    <label class="form-label"><strong>Sisa Pertemuan:</strong></label>
                                    <span class="badge badge-warning"><?= $a['sisa_pertemuan'] ?? 0 ?> kali</span>
                                </div>

                                <!-- Form Pembayaran -->
                                <form action="<?= base_url('parent/konfirmasi-pembayaran') ?>" method="POST" enctype="multipart/form-data">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="anak_id" value="<?= $a['id'] ?>">
                                    
                                    <!-- Harga per Pertemuan -->
                                    <div class="mb-3">
                                    <label class="form-label"><strong>Harga per Pertemuan:</strong></label>
                                    <?php if($hargaPerPertemuan > 0): ?>
                                        <h4 class="text-primary">Rp <?= number_format($hargaPerPertemuan, 0, ',', '.') ?></h4>
                                    <?php else: ?>
                                        <div class="alert alert-warning mb-0">
                                            Jenis les belum valid untuk perhitungan tarif. Silakan hubungi admin untuk set jenis les <strong>private</strong> / <strong>reguler</strong>.
                                        </div>
                                    <?php endif; ?>
                                    </div>

                                    <!-- Pilihan Jumlah Pertemuan -->
                                    <div class="mb-3">
                                    <label class="form-label"><strong>Jumlah Pertemuan:</strong></label>
                                    <input type="text" class="form-control" value="4 Pertemuan (tetap)" readonly>
                                    <input type="hidden" name="jumlah_pertemuan" value="4">
                                    <input type="hidden" class="jumlah-pertemuan"
                                           id="jumlah_pertemuan_<?= $a['id'] ?>"
                                           data-harga="<?= $hargaPerPertemuan ?>"
                                           data-id="<?= $a['id'] ?>"
                                           value="4">
                                    <small class="form-text text-muted mt-2">
                                        Pembayaran selalu untuk <strong>4 pertemuan ke depan</strong>: private Rp 150.000/pertemuan (total Rp 600.000), reguler Rp 75.000/pertemuan (total Rp 300.000).
                                    </small>
                                </div>

                        

                                    <!-- Total Pembayaran -->
                                <div class="mb-3">
                                    <label class="form-label"><strong>Total Pembayaran:</strong></label>
                                    <h3 class="text-success" id="total_<?= $a['id'] ?>">
                                        <?php if($totalPaket > 0): ?>
                                            Rp <?= number_format($totalPaket, 0, ',', '.') ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </h3>
                                    <input type="hidden" name="total" id="total_input_<?= $a['id'] ?>" 
                                           value="<?= $totalPaket ?>">
                                </div>
                                    <!-- Metode Pembayaran -->
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Metode Pembayaran:</strong></label>
                                        <select class="form-control" name="metode_pembayaran" required>
                                            <option value="transfer">Transfer Bank</option>
                                            <option value="tunai">Tunai</option>
                                        </select>
                                    </div>

                                    <!-- Upload Bukti Pembayaran -->
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Upload Bukti Pembayaran:</strong></label>
                                        <input type="file" class="form-control" name="bukti_pembayaran" accept="image/*" required>
                                    </div>

                                         <!-- Tombol Bayar -->
                                <button type="submit" class="btn btn-primary btn-block" <?= $canPay ? '' : 'disabled' ?>>
                                        <i class="fas fa-money-bill-wave"></i> Bayar Sekarang
                                    </button>

                                    <?php if($hargaPerPertemuan <= 0): ?>
                                        <div class="alert alert-warning mt-3">
                                            <i class="fas fa-exclamation-triangle"></i> Pembayaran belum bisa dilakukan karena tarif belum dapat dihitung (jenis les belum valid).
                                        </div>
                                    <?php endif; ?>

                                    <?php if($a['sisa_pertemuan'] > 0): ?>
                                        <div class="alert alert-info mt-3">
                                            <i class="fas fa-info-circle"></i> Pembayaran tidak dapat dilakukan karena masih memiliki sisa <?= $a['sisa_pertemuan'] ?> pertemuan.
                                        </div>
                                    <?php endif; ?>

                                    <?php if($hasPendingPembayaran): ?>
                                        <div class="alert alert-warning mt-3">
                                            <i class="fas fa-clock"></i> Pembayaran tidak dapat dilakukan karena masih ada pembayaran yang menunggu verifikasi admin untuk anak ini.
                                        </div>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Peringatan Expired -->
<?php if (!empty($near_expired)): ?>
<div class="modal fade" id="expiredWarningModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-warning border-0 py-3">
                <h5 class="modal-title font-weight-bold text-dark">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Peringatan Masa Aktif
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted mb-4">Masa aktif paket untuk anak-anak berikut akan segera berakhir. Segera lakukan perpanjangan agar sesi latihan tidak hangus.</p>
                
                <?php foreach ($near_expired as $ne): ?>
                    <div class="d-flex align-items-center p-3 mb-3 bg-light rounded-lg border">
                        <div class="flex-grow-1">
                            <h6 class="font-weight-bold mb-1 text-primary"><?= esc($ne['nama_anak']) ?></h6>
                            <div class="small text-dark mb-1">
                                <i class="fas fa-calendar-times mr-1 text-danger"></i> 
                                <strong><?= $ne['days_left'] ?> Hari Lagi</strong> (Sampai <?= date('d M Y', strtotime($ne['berlaku_sampai'])) ?>)
                            </div>
                            <div class="small text-muted">
                                <i class="fas fa-swimmer mr-1"></i> Sisa <?= $ne['sisa_sesi'] ?> Pertemuan
                            </div>
                        </div>
                        <div class="ml-3">
                            <span class="badge badge-pill badge-warning px-3 py-2"><?= $ne['days_left'] ?> Hari</span>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="mt-4">
                    <button type="button" class="btn btn-primary btn-block py-3 font-weight-bold shadow-sm" style="border-radius: 12px;" data-dismiss="modal">
                        Saya Mengerti
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Script untuk menghitung total pembayaran -->
<script>
$(document).ready(function() {
    <?php if (!empty($near_expired)): ?>
        $('#expiredWarningModal').modal('show');
    <?php endif; ?>

    $('.jumlah-pertemuan').change(function() {
        var id = $(this).data('id');
        var harga = parseFloat($(this).data('harga'));
        var jumlah = parseInt($(this).val());
        var total = harga * jumlah;
        
        $('#total_' + id).text('Rp ' + total.toLocaleString('id-ID'));
        $('#total_input_' + id).val(total);
    });
});
</script>



                    
                        <!-- Riwayat Pembayaran -->
                        <h5 class="mt-4">Riwayat Pembayaran</h5>
                        <?php if(empty($pembayaran)): ?>
                            <div class="alert alert-info">Belum ada riwayat pembayaran.</div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Nama Anak</th>
                                            <th>Jumlah Pertemuan</th>
                                            <th>Total</th>
                                            <th>Berlaku Sampai</th>
                                            <th>Status</th>
                                            <th>Bukti</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; foreach($pembayaran as $p): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= date('d-m-Y H:i', strtotime($p['tanggal'])) ?></td>  
                                            <td><?= $p['nama_anak'] ?></td>
                                            <td><?= $p['jumlah_pertemuan'] ?> kali</td>
                                            <td>Rp <?= number_format($p['total'], 0, ',', '.') ?></td>
                                            <td>
    <?php if ($p['status'] == 'success' && !empty($p['berlaku_sampai'])) : ?>
        <?= date('d-m-Y', strtotime($p['berlaku_sampai'])) ?>
    <?php else : ?>
        -
    <?php endif; ?>
</td>
                                            <td>
                                                <?php if($p['status'] == 'pending'): ?>
                                                    <span class="badge badge-warning">Menunggu Approval Admin</span>
                                                <?php elseif($p['status'] == 'success'): ?>
                                                    <span class="badge badge-success">Sukses</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">Ditolak</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if(!empty($p['bukti_pembayaran'])): ?>
                                                    <a href="<?= r2_url($p['bukti_pembayaran'], 'bukti_pembayaran') ?>" target="_blank" class="bukti-tf-link">
                                                        <img src="<?= r2_url($p['bukti_pembayaran'], 'bukti_pembayaran') ?>" alt="Bukti Transfer" class="bukti-tf-thumb">
                                                        <span class="bukti-tf-text">Lihat <i class="fas fa-external-link-alt"></i></span>
                                                    </a>
                                                <?php else: ?>
                                                    <?php if($p['status'] == 'pending'): ?>
                                                        <button class="btn btn-sm btn-primary upload-bukti" data-id="<?= $p['id'] ?>">Upload Bukti</button>
                                                    <?php else: ?>
                                                        <span class="text-muted">Tidak ada</span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card mt-4 overflow-hidden" style="border: 1px solid var(--stroke); border-radius: var(--border-radius); box-shadow: 0 4px 12px var(--shadow);">
                            <div class="card-header d-flex justify-content-between align-items-center" style="cursor: pointer; background: #F9FAFB;" data-toggle="collapse" data-target="#rekeningInfo" aria-expanded="true" aria-controls="rekeningInfo">
                                <h5 class="mb-0 d-flex align-items-center font-weight-bold" style="color: var(--text-main); font-size: 14px;">
                                    <i class="fas fa-credit-card me-2 text-primary"></i> Informasi Rekening Pembayaran
                                </h5>
                                <span class="collapse-icon-wrapper text-muted">
                                    <i class="fas fa-chevron-down transition-transform"></i>
                                </span>
                            </div>
                            <div class="collapse show" id="rekeningInfo">
                                <div class="card-body" style="background: #FFFFFF; padding: 20px;">
                                    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                                        <div class="d-flex align-items-center" style="gap: 12px;">
                                            <img src="<?= base_url('uploads/mandiri_logo.png') ?>" alt="Bank Mandiri Logo" class="img-fluid" style="max-height: 28px; width: auto; object-fit: contain; border-radius: 4px;">
                                            <div>
                                                <div class="text-muted small" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Bank</div>
                                                <div class="font-weight-bold" style="font-size: 13px; color: var(--text-main);">Mandiri</div>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="badge bg-light text-primary px-3 py-2 border font-weight-bold" style="font-size: 11px; border-radius: 8px;">Transfer Bank</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="text-muted small mb-1" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Nama Rekening</div>
                                        <div class="font-weight-bold" style="font-size: 13px; color: var(--text-main);">Reza Patriota putra</div>
                                    </div>
                                    
                                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center bg-light p-3 rounded-lg border" style="border-radius: 12px; gap: 12px;">
                                        <div>
                                            <div class="text-muted small mb-1" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Nomor Rekening</div>
                                            <div class="font-weight-bold text-monospace" style="font-size: 15px; color: var(--text-main); letter-spacing: 0.5px;">1670007253452</div>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-primary btn-copy w-100 font-weight-bold d-flex align-items-center justify-content-center" style="border-radius: 8px; border-color: var(--primary); color: var(--primary); padding: 8px 16px; gap: 6px;" onclick="copyToClipboard('1670007253452', this)">
                                                <i class="far fa-copy"></i> Salin No. Rekening
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Anak -->

<!-- Modal Tambah Anak -->
<div class="modal fade" id="tambahAnakModal" tabindex="-1" aria-labelledby="tambahAnakModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahAnakModalLabel">Tambah Data Anak</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('parent/tambah-anak') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <!-- Data Diri -->
                    <div class="form-group mb-3">
                        <label for="nama">Nama Lengkap Anak</label>
                        <input type="text" class="form-control" id="nama" name="nama" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="nama_panggilan">Nama Panggilan</label>
                        <input type="text" class="form-control" id="nama_panggilan" name="nama_panggilan">
                    </div>

                    <div class="form-group mb-3">
                        <label for="tanggal_lahir">Tanggal Lahir</label>
                        <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="jenis_kelamin">Jenis Kelamin</label>
                        <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>

                    <!-- Data Sekolah -->
                    <div class="form-group mb-3">
                        <label for="asal_sekolah">Asal Sekolah</label>
                        <input type="text" class="form-control" id="asal_sekolah" name="asal_sekolah" placeholder="Masukkan nama sekolah...">
                    </div>

                    <!-- Data Les -->
                    <div class="form-group mb-3">
                        <label for="jenis_les_id">Jenis Les</label>
                        <select class="form-control" id="jenis_les_id" name="jenis_les_id" required>
                            <option value="">Pilih Jenis Les</option>
                            <?php if(isset($jenis_les) && is_array($jenis_les)): ?>
                                <?php foreach($jenis_les as $les): ?>
                                    <option value="<?= $les['id'] ?>"><?= $les['nama_les'] ?> - Rp <?= number_format($les['harga'], 0, ',', '.') ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Data Kesehatan -->
                    <div class="form-group mb-3">
                        <label for="riwayat_penyakit">Riwayat Penyakit</label>
                        <textarea class="form-control" id="riwayat_penyakit" name="riwayat_penyakit" rows="3" 
                                 placeholder="Masukkan riwayat penyakit jika ada..."></textarea>
                        <small class="text-muted">Kosongkan jika tidak ada riwayat penyakit</small>
                    </div>

                    <!-- Upload Foto -->
                    <div class="form-group mb-3">
                        <label for="foto">Foto Anak</label>
                        <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                        <small class="text-muted">Format: JPG, PNG, JPEG. Maks: 2MB</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Anak -->
<div class="modal fade" id="editAnakModal" tabindex="-1" role="dialog" aria-labelledby="editAnakModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Data Anak</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('parent/update-anak') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="form-group">
                        <label for="edit_nama">Nama Lengkap</label>
                        <input type="text" class="form-control" id="edit_nama" name="nama" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_tanggal_lahir">Tanggal Lahir</label>
                        <input type="date" class="form-control" id="edit_tanggal_lahir" name="tanggal_lahir" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_jenis_kelamin">Jenis Kelamin</label>
                        <select class="form-control" id="edit_jenis_kelamin" name="jenis_kelamin" required>
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_jenis_les_id">Jenis Les</label>
                        <select class="form-control" id="edit_jenis_les_id" name="jenis_les_id" required disabled>
                            <option value="">Pilih Jenis Les</option>
                            <?php if(isset($jenis_les) && is_array($jenis_les)): ?>
                                <?php foreach($jenis_les as $les): ?>
                                    <option value="<?= $les['id'] ?>"><?= $les['nama_les'] ?> - Rp <?= number_format($les['harga'], 0, ',', '.') ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_foto">Foto Anak</label>
                        <input type="file" class="form-control-file" id="edit_foto" name="foto" accept="image/*">
                        <small class="form-text text-muted">Upload foto baru (opsional). Biarkan kosong jika tidak ingin mengubah foto.</small>
                        <div id="current_foto_container" class="mt-2" style="display: none;">
                            <p>Foto saat ini:</p>
                            <img id="current_foto" src="" alt="Foto anak" class="img-thumbnail" style="max-height: 150px;">
                        </div>
                        <input type="hidden" name="old_foto" id="edit_old_foto">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Upload Bukti Pembayaran -->
<div class="modal fade" id="uploadBuktiModal" tabindex="-1" role="dialog" aria-labelledby="uploadBuktiModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadBuktiModalLabel">Upload Bukti Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('parent/konfirmasi-pembayaran') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <input type="hidden" name="pembayaran_id" id="pembayaran_id">
                    <input type="hidden" name="total" value="<?= $pembayaran['total'] ?? 0 ?>">
                    <div class="form-group">
                        <label for="bukti_pembayaran">Bukti Pembayaran</label>
                        <input type="file" class="form-control-file" id="bukti_pembayaran" name="bukti_pembayaran" accept="image/*" required>
                        <small class="form-text text-muted">Upload bukti transfer/pembayaran. Format: JPG, PNG, maksimal 2MB.</small>
                    </div>
                    <div class="form-group">
                        <label for="catatan">Catatan (Opsional)</label>
                        <textarea class="form-control" id="catatan" name="catatan" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Pembayaran -->
<div class="modal fade" id="konfirmasiPembayaranModal" tabindex="-1" role="dialog" aria-labelledby="konfirmasiPembayaranModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="konfirmasiPembayaranModalLabel">Konfirmasi Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('parent/konfirmasi-pembayaran') ?>" method="post" enctype="multipart/form-data" id="formPembayaran">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <input type="hidden" name="anak_id" id="konfirmasi_anak_id">
                    <input type="hidden" name="jumlah_pertemuan" id="konfirmasi_jumlah_pertemuan">
                    <input type="hidden" name="total" id="konfirmasi_total">
                    
                    <div id="konfirmasi_detail"></div>
                    
                    <div class="form-group mt-3">
                        <label for="metode_pembayaran">Metode Pembayaran</label>
                        <select class="form-control" id="metode_pembayaran" name="metode_pembayaran" required>
                            <option value="">Pilih Metode Pembayaran</option>
                            <option value="transfer_bca">Transfer BCA</option>
                            <option value="transfer_bni">Transfer BNI</option>
                            <option value="transfer_mandiri">Transfer Mandiri</option>
                            <option value="dana">DANA</option>
                            <option value="ovo">OVO</option>
                            <option value="gopay">GoPay</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="bukti_pembayaran_konfirmasi">Bukti Pembayaran</label>
                        <input type="file" class="form-control-file" id="bukti_pembayaran_konfirmasi" name="bukti_pembayaran" accept="image/*">
                        <small class="form-text text-muted">Upload bukti transfer/pembayaran. Format: JPG, PNG, maksimal 2MB.</small>
                        <small class="form-text text-muted">Anda juga dapat mengupload bukti pembayaran nanti.</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="catatan_konfirmasi">Catatan (Opsional)</label>
                        <textarea class="form-control" id="catatan_konfirmasi" name="catatan" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-bayar" data-anak-id="<?= isset($anak['id']) ? $anak['id'] : '' ?>" data-sisa-pertemuan="<?= isset($anak['sisa_pertemuan']) ? $anak['sisa_pertemuan'] : '' ?>" data-has-pending="<?= !empty($anak['has_pending_pembayaran']) ? '1' : '0' ?>"><i class="fas fa-check"></i> Konfirmasi Pembayaran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function validatePayment(anakId, sisaPertemuan, hasPending) {
    if (hasPending) {
        Swal.fire({
            icon: 'warning',
            title: 'Tidak dapat melakukan pembayaran',
            text: 'Masih ada pembayaran yang menunggu verifikasi admin untuk anak ini.'
        });
        return false;
    }
    if (sisaPertemuan > 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Tidak dapat melakukan pembayaran',
            text: 'Pembayaran hanya bisa dilakukan jika sisa pertemuan 0 atau kurang.'
        });
        return false;
    }
    return true;
}

// Modifikasi event handler tombol pembayaran
$(document).ready(function() {
    $('.btn-bayar').click(function(e) {
        e.preventDefault();
        var anakId = $(this).data('anak-id');
        var sisaPertemuan = parseInt($(this).data('sisa-pertemuan'), 10) || 0;
        var hasPending = $(this).data('has-pending') == 1;
        
        if (validatePayment(anakId, sisaPertemuan, hasPending)) {
            $('#formPembayaran').submit();
        }
    });
});
</script>

<!-- Modal Daftar Jadwal -->


<!-- Modal Upload Bukti Pembayaran -->
<div class="modal fade" id="uploadBuktiModal" tabindex="-1" role="dialog" aria-labelledby="uploadBuktiModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadBuktiModalLabel">Upload Bukti Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('parent/konfirmasi-pembayaran') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <input type="hidden" name="pembayaran_id" id="pembayaran_id">
                    <input type="hidden" name="total" value="<?= $pembayaran['total'] ?? 0 ?>">
                    <div class="form-group">
                        <label for="bukti_pembayaran">Bukti Pembayaran</label>
                        <input type="file" class="form-control-file" id="bukti_pembayaran" name="bukti_pembayaran" accept="image/*" required>
                        <small class="form-text text-muted">Upload bukti transfer/pembayaran. Format: JPG, PNG, maksimal 2MB.</small>
                    </div>
                    <div class="form-group">
                        <label for="catatan">Catatan (Opsional)</label>
                        <textarea class="form-control" id="catatan" name="catatan" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Hitung total pembayaran saat jumlah pertemuan berubah
    $('.jumlah-pertemuan').change(function() {
        var id = $(this).data('id');
        var harga = $(this).data('harga');
        var jumlah = $(this).val();
        var total = harga * jumlah;
        
        $('#total_' + id).text('Rp ' + formatRupiah(total));
    });
    
    // Upload bukti pembayaran
    $('.upload-bukti').click(function() {
        var id = $(this).data('id');
        $('#pembayaran_id').val(id);
        $('#uploadBuktiModal').modal('show');
    });
});

$(document).ready(function() {
    $('.jumlah-pertemuan').change(function() {
        var id = $(this).data('id');
        var harga = parseFloat($(this).data('harga'));
        var jumlah = parseInt($(this).val());
        var total = harga * jumlah;
        
        $('#total_' + id).text('Rp ' + total.toLocaleString('id-ID'));
        $('#total_input_' + id).val(total);
    });
});

// Fungsi untuk menampilkan modal konfirmasi pembayaran
function bayarSekarang(id) {
    console.log("Fungsi bayarSekarang dipanggil dengan ID: " + id);
    var jumlah = $('#jumlah_pertemuan_' + id).val();
    var harga = $('#jumlah_pertemuan_' + id).data('harga');
    var total = harga * jumlah;
    var nama = '';
    var jenis_les = '';
    
    // Cari nama dan jenis les dari data yang ada
    $('.card-body').find('[data-id="' + id + '"]').each(function() {
        var card = $(this).closest('.card-body');
        nama = card.find('h5').first().text();
        jenis_les = card.find('p:contains("Jenis Les:")').text().replace('Jenis Les:', '').trim();
    });
    
    $('#konfirmasi_anak_id').val(id);
    $('#konfirmasi_jumlah_pertemuan').val(jumlah);
    $('#konfirmasi_total').val(total);
    
    var detailHTML = `
        <div class="alert alert-info">
            <h5>Detail Pembayaran:</h5>
            <p><strong>Nama Anak:</strong> ${nama}</p>
            <p><strong>Jenis Les:</strong> ${jenis_les}</p>
            <p><strong>Jumlah Pertemuan:</strong> ${jumlah} kali</p>
            <p><strong>Harga per Pertemuan:</strong> Rp ${formatRupiah(harga)}</p>
            <p><strong>Total Pembayaran:</strong> Rp ${formatRupiah(total)}</p>
        </div>
    `;
    
    $('#konfirmasi_detail').html(detailHTML);
    $('#konfirmasiPembayaranModal').modal('show');
}
</script>



<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Modal Konfirmasi Pembayaran -->
<div class="modal fade" id="konfirmasiPembayaranModal" tabindex="-1" role="dialog" aria-labelledby="konfirmasiPembayaranModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="konfirmasiPembayaranModalLabel">Konfirmasi Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('parent/konfirmasi-pembayaran') ?>" method="post" enctype="multipart/form-data" id="formPembayaran">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <input type="hidden" name="anak_id" id="konfirmasi_anak_id">
                    <input type="hidden" name="jumlah_pertemuan" id="konfirmasi_jumlah_pertemuan">
                    <input type="hidden" name="total" id="konfirmasi_total">
                    
                    <div id="konfirmasi_detail"></div>
                    
                    <div class="form-group mt-3">
                        <label for="metode_pembayaran">Metode Pembayaran</label>
                        <select class="form-control" id="metode_pembayaran" name="metode_pembayaran" required>
                            <option value="">Pilih Metode Pembayaran</option>
                            <option value="transfer_bca">Transfer BCA</option>
                            <option value="transfer_bni">Transfer BNI</option>
                            <option value="transfer_mandiri">Transfer Mandiri</option>
                            <option value="dana">DANA</option>
                            <option value="ovo">OVO</option>
                            <option value="gopay">GoPay</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="bukti_pembayaran_konfirmasi">Bukti Pembayaran</label>
                        <input type="file" class="form-control-file" id="bukti_pembayaran_konfirmasi" name="bukti_pembayaran" accept="image/*">
                        <small class="form-text text-muted">Upload bukti transfer/pembayaran. Format: JPG, PNG, maksimal 2MB.</small>
                        <small class="form-text text-muted">Anda juga dapat mengupload bukti pembayaran nanti.</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="catatan_konfirmasi">Catatan (Opsional)</label>
                        <textarea class="form-control" id="catatan_konfirmasi" name="catatan" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-bayar" data-anak-id="<?= isset($anak['id']) ? $anak['id'] : '' ?>" data-sisa-pertemuan="<?= isset($anak['sisa_pertemuan']) ? $anak['sisa_pertemuan'] : '' ?>" data-has-pending="<?= !empty($anak['has_pending_pembayaran']) ? '1' : '0' ?>"><i class="fas fa-check"></i> Konfirmasi Pembayaran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function validatePayment(anakId, sisaPertemuan, hasPending) {
    if (hasPending) {
        Swal.fire({
            icon: 'warning',
            title: 'Tidak dapat melakukan pembayaran',
            text: 'Masih ada pembayaran yang menunggu verifikasi admin untuk anak ini.'
        });
        return false;
    }
    if (sisaPertemuan > 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Tidak dapat melakukan pembayaran',
            text: 'Pembayaran hanya bisa dilakukan jika sisa pertemuan 0 atau kurang.'
        });
        return false;
    }
    return true;
}

// Modifikasi event handler tombol pembayaran
$(document).ready(function() {
    $('.btn-bayar').click(function(e) {
        e.preventDefault();
        var anakId = $(this).data('anak-id');
        var sisaPertemuan = parseInt($(this).data('sisa-pertemuan'), 10) || 0;
        var hasPending = $(this).data('has-pending') == 1;
        
        if (validatePayment(anakId, sisaPertemuan, hasPending)) {
            $('#formPembayaran').submit();
        }
    });
});
</script>




<!-- Modal Upload Bukti Pembayaran -->
<div class="modal fade" id="uploadBuktiModal" tabindex="-1" role="dialog" aria-labelledby="uploadBuktiModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadBuktiModalLabel">Upload Bukti Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('parent/konfirmasi-pembayaran') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <input type="hidden" name="pembayaran_id" id="pembayaran_id">
                    <input type="hidden" name="total" value="<?= $pembayaran['total'] ?? 0 ?>">
                    <div class="form-group">
                        <label for="bukti_pembayaran">Bukti Pembayaran</label>
                        <input type="file" class="form-control-file" id="bukti_pembayaran" name="bukti_pembayaran" accept="image/*" required>
                        <small class="form-text text-muted">Upload bukti transfer/pembayaran. Format: JPG, PNG, maksimal 2MB.</small>
                    </div>
                    <div class="form-group">
                        <label for="catatan">Catatan (Opsional)</label>
                        <textarea class="form-control" id="catatan" name="catatan" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Hitung total pembayaran saat jumlah pertemuan berubah
    $('.jumlah-pertemuan').change(function() {
        var id = $(this).data('id');
        var harga = $(this).data('harga');
        var jumlah = $(this).val();
        var total = harga * jumlah;
        
        $('#total_' + id).text('Rp ' + formatRupiah(total));
    });
    
    // Upload bukti pembayaran
    $('.upload-bukti').click(function() {
        var id = $(this).data('id');
        $('#pembayaran_id').val(id);
        $('#uploadBuktiModal').modal('show');
    });
    
    // Edit anak
    $('.edit-anak').click(function() {
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        var tanggal = $(this).data('tanggal');
        var jk = $(this).data('jk');
        var les = $(this).data('les');
        var foto = $(this).data('foto');
        var sisa_pertemuan = $(this).data('sisa');
        
        // Debug - cek nilai yang diterima
        console.log('ID:', id);
        console.log('Nama:', nama);
        console.log('Tanggal:', tanggal);
        console.log('JK:', jk);
        console.log('Les:', les);
        console.log('Foto:', foto);
        console.log('Sisa:', sisa_pertemuan);
        
        $('#edit_id').val(id);
        $('#edit_nama').val(nama);
        
        // Format tanggal untuk input type="date" (YYYY-MM-DD)
        if(tanggal) {
            // Jika format tanggal adalah dd-mm-yyyy, konversi ke yyyy-mm-dd
            if(tanggal.includes('-')) {
                var parts = tanggal.split('-');
                if(parts.length === 3 && parts[0].length === 2) {
                    // Asumsi format dd-mm-yyyy
                    tanggal = parts[2] + '-' + parts[1] + '-' + parts[0];
                }
            }
            $('#edit_tanggal_lahir').val(tanggal);
        }
        
        $('#edit_jenis_kelamin').val(jk);
        $('#edit_jenis_les_id').val(les);
        $('#edit_old_foto').val(foto);
        
        // Tampilkan foto jika ada
        if (foto) {
            $('#current_foto').attr('src', '<?= r2_url('anak/') ?>' + foto);
            $('#current_foto_container').show();
        } else {
            $('#current_foto_container').hide();
        }
    });
    
    // Tambahkan event listener untuk modal
    $('#editAnakModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var nama = button.data('nama');
        var tanggal = button.data('tanggal');
        var jk = button.data('jk');
        var les = button.data('les');
        var foto = button.data('foto');
        var sisa_pertemuan = button.data('sisa');
        
        console.log('Modal Event - ID:', id);
        console.log('Modal Event - Nama:', nama);
        
        var modal = $(this);
        modal.find('#edit_id').val(id);
        modal.find('#edit_nama').val(nama);
        modal.find('#edit_tanggal_lahir').val(tanggal);
        modal.find('#edit_jenis_kelamin').val(jk);
        modal.find('#edit_jenis_les_id').val(les);
        modal.find('#edit_old_foto').val(foto);
        
        // Tampilkan foto jika ada
        if (foto) {
            modal.find('#current_foto').attr('src', '<?= r2_url('anak/') ?>' + foto);
            modal.find('#current_foto_container').show();
        } else {
            modal.find('#current_foto_container').hide();
        }
    });
});
</script>


<!-- Modal Konfirmasi Pembayaran -->
<!-- Modal Konfirmasi Pembayaran -->
<div class="modal fade" id="konfirmasiPembayaranModal" tabindex="-1" role="dialog" aria-labelledby="konfirmasiPembayaranModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="konfirmasiPembayaranModalLabel">Konfirmasi Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('parent/konfirmasi-pembayaran') ?>" method="post" enctype="multipart/form-data" id="formPembayaran">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <input type="hidden" name="anak_id" id="konfirmasi_anak_id">
                    <input type="hidden" name="jumlah_pertemuan" id="konfirmasi_jumlah_pertemuan">
                    <input type="hidden" name="total" id="konfirmasi_total">
                    
                    <div id="konfirmasi_detail"></div>
                    
                    <div class="form-group mt-3">
                        <label for="metode_pembayaran">Metode Pembayaran</label>
                        <select class="form-control" id="metode_pembayaran" name="metode_pembayaran" required>
                            <option value="">Pilih Metode Pembayaran</option>
                            <option value="transfer_bca">Transfer BCA</option>
                            <option value="transfer_bni">Transfer BNI</option>
                            <option value="transfer_mandiri">Transfer Mandiri</option>
                            <option value="dana">DANA</option>
                            <option value="ovo">OVO</option>
                            <option value="gopay">GoPay</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="bukti_pembayaran_konfirmasi">Bukti Pembayaran</label>
                        <input type="file" class="form-control-file" id="bukti_pembayaran_konfirmasi" name="bukti_pembayaran" accept="image/*">
                        <small class="form-text text-muted">Upload bukti transfer/pembayaran. Format: JPG, PNG, maksimal 2MB.</small>
                        <small class="form-text text-muted">Anda juga dapat mengupload bukti pembayaran nanti.</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="catatan_konfirmasi">Catatan (Opsional)</label>
                        <textarea class="form-control" id="catatan_konfirmasi" name="catatan" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-bayar" data-anak-id="<?= isset($anak['id']) ? $anak['id'] : '' ?>" data-sisa-pertemuan="<?= isset($anak['sisa_pertemuan']) ? $anak['sisa_pertemuan'] : '' ?>" data-has-pending="<?= !empty($anak['has_pending_pembayaran']) ? '1' : '0' ?>"><i class="fas fa-check"></i> Konfirmasi Pembayaran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function validatePayment(anakId, sisaPertemuan, hasPending) {
    if (hasPending) {
        Swal.fire({
            icon: 'warning',
            title: 'Tidak dapat melakukan pembayaran',
            text: 'Masih ada pembayaran yang menunggu verifikasi admin untuk anak ini.'
        });
        return false;
    }
    if (sisaPertemuan > 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Tidak dapat melakukan pembayaran',
            text: 'Pembayaran hanya bisa dilakukan jika sisa pertemuan 0 atau kurang.'
        });
        return false;
    }
    return true;
}

// Modifikasi event handler tombol pembayaran
$(document).ready(function() {
    $('.btn-bayar').click(function(e) {
        e.preventDefault();
        var anakId = $(this).data('anak-id');
        var sisaPertemuan = parseInt($(this).data('sisa-pertemuan'), 10) || 0;
        var hasPending = $(this).data('has-pending') == 1;
        
        if (validatePayment(anakId, sisaPertemuan, hasPending)) {
            $('#formPembayaran').submit();
        }
    });
});
</script>

<!-- Modal Daftar Jadwal -->



<script>
$(document).ready(function() {
    // Hitung total pembayaran saat jumlah pertemuan berubah
    $('.jumlah-pertemuan').change(function() {
        var id = $(this).data('id');
        var harga = $(this).data('harga');
        var jumlah = $(this).val();
        var total = harga * jumlah;
        
        $('#total_' + id).text('Rp ' + formatRupiah(total));
    });
    
    // Upload bukti pembayaran
    $('.upload-bukti').click(function() {
        var id = $(this).data('id');
        $('#pembayaran_id').val(id);
        $('#uploadBuktiModal').modal('show');
    });
    
    // Edit anak
    $('.edit-anak').click(function() {
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        var tanggal = $(this).data('tanggal');
        var jk = $(this).data('jk');
        var les = $(this).data('les');
        var foto = $(this).data('foto');
        var sisa = $(this).data('sisa');
        
        $('#edit_id').val(id);
        $('#edit_nama').val(nama);
        $('#edit_tanggal_lahir').val(tanggal);
        $('#edit_jenis_kelamin').val(jk);
        $('#edit_jenis_les_id').val(les);
        
        if(foto) {
            $('#current_foto').show();
        } else {
            $('#current_foto').hide();
        }
    });
});

// Format angka ke format Rupiah
function formatRupiah(angka) {
    var reverse = angka.toString().split('').reverse().join(''),
        ribuan = reverse.match(/\d{1,3}/g);
    ribuan = ribuan.join('.').split('').reverse().join('');
    return ribuan;
}

// Fungsi untuk menampilkan modal konfirmasi pembayaran
function bayarSekarang(id) {
    var jumlah = $('#jumlah_pertemuan_' + id).val();
    var harga = $('#jumlah_pertemuan_' + id).data('harga');
    var total = harga * jumlah;
    var nama = '';
    var jenis_les = '';
    
    // Cari nama dan jenis les dari data yang ada
    $('.card-body').find('[data-id="' + id + '"]').each(function() {
        var card = $(this).closest('.card-body');
        nama = card.find('h5').first().text();
        jenis_les = card.find('p:contains("Jenis Les:")').text().replace('Jenis Les:', '').trim();
    });
    
    $('#konfirmasi_anak_id').val(id);
    $('#konfirmasi_jumlah_pertemuan').val(jumlah);
    $('#konfirmasi_total').val(total);
    
    var detailHTML = `
        <div class="alert alert-info">
            <h5>Detail Pembayaran:</h5>
            <p><strong>Nama Anak:</strong> ${nama}</p>
            <p><strong>Jenis Les:</strong> ${jenis_les}</p>
            <p><strong>Jumlah Pertemuan:</strong> ${jumlah} kali</p>
            <p><strong>Harga per Pertemuan:</strong> Rp ${formatRupiah(harga)}</p>
            <p><strong>Total Pembayaran:</strong> Rp ${formatRupiah(total)}</p>
        </div>
    `;
    
    $('#konfirmasi_detail').html(detailHTML);
    $('#konfirmasiPembayaranModal').modal('show');
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fungsi untuk mengupdate status dan tombol berdasarkan sisa pertemuan
    function updateStatusDanTombol() {
        // Cari semua card anak
        const cards = document.querySelectorAll('.card');
        
        cards.forEach(card => {
            // Cari elemen sisa pertemuan
            const sisaPertemuanText = card.querySelector('.badge-primary, .badge-danger');
            if (sisaPertemuanText) {
                // Ambil angka dari teks (misal: "4 kali" -> 4)
                const sisaPertemuan = parseInt(sisaPertemuanText.textContent);
                
                // Cari elemen status dan tombol bayar
                const statusBadge = card.querySelector('.badge-warning');
                const bayarButton = card.querySelector('.btn-info[onclick*="pembayaran-tab"]');
                
                if (sisaPertemuan > 0) {
                    // Jika masih ada sisa pertemuan
                    if (statusBadge) {
                        // Ubah status menjadi aktif
                        statusBadge.classList.remove('badge-warning');
                        statusBadge.classList.add('badge-success');
                        statusBadge.innerHTML = '<i class="fas fa-check-circle"></i> Aktif';
                    }
                    
                    // Nonaktifkan tombol bayar jika ada
                    if (bayarButton) {
                        bayarButton.classList.add('disabled');
                        bayarButton.setAttribute('disabled', 'disabled');
                        bayarButton.style.cursor = 'not-allowed';
                        bayarButton.style.pointerEvents = 'none';
                        bayarButton.style.opacity = '0.6';
                    }
                }
                
                // Tambahkan peringatan jika sisa pertemuan kurang dari 2
                if (sisaPertemuan < 2) {
                    const warningContainer = card.querySelector('.card-body');
                    if (warningContainer && !warningContainer.querySelector('.alert-warning')) {
                        const warningDiv = document.createElement('div');
                        warningDiv.className = 'alert alert-warning mt-2 py-1 px-2';
                        warningDiv.innerHTML = `
                            <small>
                                <i class="fas fa-exclamation-triangle"></i>
                                Perlu perpanjangan segera!
                            </small>
                        `;
                        warningContainer.appendChild(warningDiv);
                    }
                }
            }
        });
    }

    // Jalankan fungsi saat halaman dimuat
    updateStatusDanTombol();
});
document.addEventListener('DOMContentLoaded', function() {
    // Fungsi untuk mengecek status pembayaran
    function cekStatusPembayaran() {
        // Ambil tabel pembayaran
        const tabelPembayaran = document.querySelector('.table-bordered');
        if (!tabelPembayaran) return;

        // Ambil semua baris pembayaran
        const barisPayment = tabelPembayaran.querySelectorAll('tbody tr');
        
        // Buat object untuk menyimpan status pembayaran terakhir per anak
        const statusPembayaran = {};
        
        // Loop setiap baris pembayaran untuk mendapatkan status terakhir
        barisPayment.forEach(baris => {
            const anakId = baris.getAttribute('data-anak-id');
            const status = baris.querySelector('td:nth-child(5)').textContent.trim().toLowerCase();
            
            if (anakId) {
                statusPembayaran[anakId] = status;
            }
        });

        // Update tampilan status di card anak
        document.querySelectorAll('.card').forEach(card => {
            const anakId = card.getAttribute('data-id');
            if (!anakId) return;

            const statusBadge = card.querySelector('.badge-warning');
            const bayarButton = card.querySelector('.btn-info[onclick*="pembayaran-tab"]');
            
            if (statusPembayaran[anakId] === 'pending') {
                if (statusBadge) {
                    // Ubah tampilan badge
                    statusBadge.classList.remove('badge-warning');
                    statusBadge.classList.add('badge-info');
                    statusBadge.innerHTML = '<i class="fas fa-clock"></i> Menunggu Approval Admin';
                }
                
                // Sembunyikan tombol bayar
                if (bayarButton) {
                    bayarButton.style.display = 'none';
                }
            }
        });
    }

    // Jalankan fungsi saat halaman dimuat
    cekStatusPembayaran();
});
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>





         

<script>
$(document).ready(function() {
    // Event handler untuk tombol daftar jadwal
    $('.btn-daftar-jadwal').on('click', function(e) {
        e.preventDefault();
        console.log('Button clicked');
        
        // Ambil data dari tombol
        var jadwalId = $(this).data('jadwal-id');
        var tanggal = $(this).data('tanggal');
        var waktu = $(this).data('waktu');
        var materi = $(this).data('materi');
        
        console.log('Data jadwal:', {jadwalId, tanggal, waktu, materi});
        
        // Buat modal HTML
        var modalHtml = `
            <div class="modal" id="modalDaftarJadwal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Daftar Jadwal Les</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="formDaftarJadwal" method="POST" action="<?= base_url('parent/jadwal/daftar') ?>">
                                <?= csrf_field() ?>
                                <input type="hidden" name="jadwal_id" value="${jadwalId}">
                                
                                <div class="mb-3">
                                    <label class="form-label">Detail Jadwal:</label>
                                    <div class="card">
                                        <div class="card-body">
                                            <p><i class="fas fa-calendar-alt"></i> Tanggal: ${tanggal}</p>
                                            <p><i class="fas fa-clock"></i> Waktu: ${waktu}</p>
                                            <p><i class="fas fa-book"></i> Materi: ${materi}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Pilih Anak:</label>
                                    <div class="list-group">
                                        <?php foreach($anak as $a): ?>
                                            <?php if($a['status'] == 'aktif' && $a['sisa_pertemuan'] > 0): ?>
                                                <label class="list-group-item">
                                                    <input type="radio" name="anak_id" value="<?= $a['id'] ?>" class="form-check-input me-2" required>
                                                    <?= $a['nama'] ?> 
                                                    <span class="badge bg-info float-end">Sisa: <?= $a['sisa_pertemuan'] ?> pertemuan</span>
                                                </label>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-primary" onclick="submitForm()">Daftar</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Hapus modal lama jika ada
        $('#modalDaftarJadwal').remove();
        
        // Tambahkan modal baru ke body
        $('body').append(modalHtml);
        
        // Tampilkan modal
        $('#modalDaftarJadwal').modal('show');
    });
});

function submitForm() {
    var formJadwalId = $('input[name="jadwal_id"]').val();
    var formAnakId = $('input[name="anak_id"]:checked').val();
    
    // Tampilkan alert untuk konfirmasi data
    alert('Data yang akan dikirim:\nJadwal ID: ' + formJadwalId + '\nAnak ID: ' + formAnakId);
    
    if (!formJadwalId) {
        alert('Error: ID Jadwal tidak valid!');
        return false;
    }
    
    if (!formAnakId) {
        alert('Silakan pilih anak terlebih dahulu!');
        return false;
    }
    
    // Submit form
    $('#formDaftarJadwal').submit();
}
</script>
          
<!-- Script untuk menangani modal -->
<script>
$(document).ready(function() {
    // Fungsi untuk menutup modal edit
    $('.btn-close, .close').click(function() {
        $('#editAnakModal').modal('hide');
        $('.modal-backdrop').remove();
    });
    
    // Fungsi untuk membersihkan modal saat ditutup
    $('#editAnakModal').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
        $('.modal-backdrop').remove();
    });
});

function copyToClipboard(text, buttonEl) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(() => {
            showSuccess(buttonEl);
        }).catch(err => {
            fallbackCopy(text, buttonEl);
        });
    } else {
        fallbackCopy(text, buttonEl);
    }
}

function fallbackCopy(text, buttonEl) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.position = "fixed";
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    try {
        document.execCommand('copy');
        showSuccess(buttonEl);
    } catch (err) {
        console.error('Fallback copy failed', err);
    }
    document.body.removeChild(textArea);
}

function showSuccess(buttonEl) {
    const originalText = buttonEl.innerHTML;
    const originalColor = buttonEl.style.color;
    const originalBorderColor = buttonEl.style.borderColor;
    const originalBgColor = buttonEl.style.backgroundColor;
    
    buttonEl.innerHTML = '<i class="fas fa-check"></i> Tersalin!';
    buttonEl.style.color = '#ffffff';
    buttonEl.style.borderColor = '#28a745';
    buttonEl.style.backgroundColor = '#28a745';
    
    setTimeout(() => {
        buttonEl.innerHTML = originalText;
        buttonEl.style.color = originalColor;
        buttonEl.style.borderColor = originalBorderColor;
        buttonEl.style.backgroundColor = originalBgColor;
    }, 2000);
}
</script>

<?= $this->endSection() ?>
