<?= $this->extend('templates/parent') ?>

<?= $this->section('content') ?>
<!-- Pastikan hanya ada satu set script di bagian atas -->
<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Bootstrap 4 Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<style>
.modal-body {
    max-height: 70vh;
    overflow-y: auto;
}

.list-group-item {
    border-radius: 0.25rem;
    margin-bottom: 0.25rem;
}

.list-group-item i {
    width: 20px;
    text-align: center;
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
                <p>Ini adalah halaman dashboard untuk orang tua/wali.</p>
                <div class="alert alert-info">
                    <h5>Informasi Akun:</h5>
                    <p>Nama: <?= session()->get('parent_nama') ?></p>
                </div>
            </div>
        </div>
        
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs mb-4" id="parentTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="anak-tab" data-toggle="tab" href="#anak" role="tab" aria-controls="anak" aria-selected="true">Daftar Anak</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="pembayaran-tab" data-toggle="tab" href="#pembayaran" role="tab" aria-controls="pembayaran" aria-selected="false">Pembayaran</a>
            </li>
            <li class="nav-item">
               <a class="nav-link" id="jadwal-tab" data-toggle="tab" href="#jadwal" role="tab" aria-controls="jadwal" aria-selected="false">Jadwal Les</a>
           </li>
           <li class="nav-item">
               <a class="nav-link" id="riwayat-latihan-tab" data-toggle="tab" href="#riwayat-latihan" role="tab" aria-controls="riwayat-latihan" aria-selected="false">Riwayat Latihan</a>
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
                                            $no = 1;
                                            foreach($riwayat_latihan as $rl): 
                                            ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
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
                            <div class="col-12 col-md-6 col-lg-4 mb-4">
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
                                                <button type="button" 
                                                        class="btn btn-primary btn-daftar-jadwal" 
                                                        data-jadwal-id="<?= $j['id'] ?>"
                                                        data-tanggal="<?= date('l, d-m-Y', strtotime($j['tanggal'])) ?>"
                                                        data-waktu="<?= $j['jam_mulai'] ?> - <?= $j['jam_selesai'] ?>"
                                                        data-materi="<?= $j['materi'] ?>">
                                                    <i class="fas fa-user-plus"></i> Daftar
                                                </button>
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
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tambahAnakModal">
                            Tambah Anak
                        </button>
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
                            <div class="row">
                                <?php foreach($anak as $a): ?>
                                <div class="col-12 col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100">
                                        <!-- Header Card dengan Foto -->
                                        <div class="card-header bg-light p-3 text-center">
                                            <?php if(!empty($a['foto'])): ?>
                                                <img src="<?= r2_url($a['foto'], 'anak') ?>" 
                                                     alt="Foto <?= $a['nama'] ?>" 
                                                     class="rounded-circle mb-2" 
                                                     style="width: 120px; height: 120px; object-fit: cover;"
                                                     onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<?= urlencode($a['nama'] ?? 'User') ?>&size=120&color=7F9CF5&background=EBF4FF';">
                                            <?php else: ?>
                                                <div class="bg-primary rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center text-white fw-bold" 
                                                     style="width: 120px; height: 120px; font-size: 3rem;">
                                                    <?= get_initials($a['nama']) ?>
                                                </div>
                                            <?php endif; ?>
                                            <h4 class="text-primary mb-1">#<?= $a['id'] ?></h4>
                                            <h5 class="card-title mb-0"><?= $a['nama'] ?></h5>
                                        </div>
                                        
                                        <!-- Body Card -->
                                        <div class="card-body">
                                            <!-- Info Les -->
                                            <div class="mb-3 p-2 bg-light rounded">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-swimming-pool text-primary mr-2"></i>
                                                    <strong>Jenis Les:</strong>
                                                </div>
                                                <span class="badge badge-info"><?= $a['nama_les'] ?? 'Belum ditentukan' ?></span>
                                            </div>
                                            <!-- Status -->
                                            <div class="mb-3 p-2 bg-light rounded">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-info-circle text-primary mr-2"></i>
                                                    <strong>Status:</strong>
                                                </div>
                                                <?php if($a['status'] == 'aktif'): ?>
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge badge-success">
                                                            <i class="fas fa-check-circle"></i> Aktif
                                                        </span>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="d-flex flex-column">
                                                        <?php if($a['sisa_pertemuan'] == 0): ?>
                                                            <span class="badge badge-warning mb-2">Perlu Pembayaran</span>
                                                            <button class="btn btn-sm btn-info" 
                                                                    onclick="$('#pembayaran-tab').tab('show')">
                                                                <i class="fas fa-wallet"></i> Bayar Sekarang
                                                            </button>
                                                        <?php else: ?>
                                                            <span class="badge badge-info">
                                                                Masih memiliki <?= $a['sisa_pertemuan'] ?> pertemuan
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <!-- Sisa Pertemuan -->
                                            <div class="mb-3 p-2 bg-light rounded">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-calendar-check text-primary mr-2"></i>
                                                    <strong>Sisa Pertemuan:</strong>
                                                </div>
                                                <h4 class="mb-0 text-center">
                                                    <span class="badge badge-<?= $a['sisa_pertemuan'] < 2 ? 'danger' : 'primary' ?>">
                                                        <?= $a['sisa_pertemuan'] ?> kali
                                                    </span>
                                                </h4>
                                                <?php if($a['sisa_pertemuan'] < 2): ?>
                                                    <div class="alert alert-warning mt-2 mb-0 py-1 px-2">
                                                        <small>
                                                            <i class="fas fa-exclamation-triangle"></i>
                                                            Perlu perpanjangan segera!
                                                        </small>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <!-- Berlaku Sampai -->
                                            <div class="mb-3 p-2 bg-light rounded">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-clock text-primary mr-2"></i>
                                                    <strong>Berlaku Sampai:</strong>
                                                </div>
                                                <?php 
                                                // Ambil pembayaran terakhir yang sukses untuk anak ini
                                                $lastPayment = null;
                                                if(!empty($pembayaran)) {
                                                    foreach($pembayaran as $p) {
                                                        if($p['anak_id'] == $a['id'] && $p['status'] == 'success' && !empty($p['berlaku_sampai'])) {
                                                            $lastPayment = $p;
                                                            break;
                                                        }
                                                    }
                                                }
                                                ?>
                                                
                                                <?php if($lastPayment && !empty($lastPayment['berlaku_sampai'])): ?>
                                                    <h4 class="mb-0 text-center">
                                                        <?php 
                                                        $today = new DateTime();
                                                        $expiry = new DateTime($lastPayment['berlaku_sampai']);
                                                        $interval = $today->diff($expiry);
                                                        $daysLeft = $interval->invert ? 0 : $interval->days;
                                                        $badgeClass = ($daysLeft <= 7) ? 'danger' : (($daysLeft <= 14) ? 'warning' : 'success');
                                                        ?>
                                                        <span class="badge badge-<?= $badgeClass ?>">
                                                            <?= date('d-m-Y', strtotime($lastPayment['berlaku_sampai'])) ?>
                                                        </span>
                                                    </h4>
                                                    <?php if($daysLeft <= 14 && $daysLeft > 0): ?>
                                                        <div class="alert alert-warning mt-2 mb-0 py-1 px-2">
                                                            <small>
                                                                <i class="fas fa-exclamation-triangle"></i>
                                                                <?= $daysLeft ?> hari lagi!
                                                            </small>
                                                        </div>
                                                    <?php elseif($daysLeft <= 0): ?>
                                                        <div class="alert alert-danger mt-2 mb-0 py-1 px-2">
                                                            <small>
                                                                <i class="fas fa-exclamation-circle"></i>
                                                                Sudah kedaluwarsa!
                                                            </small>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <p class="text-center text-muted mb-0">Belum tersedia</p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <!-- Footer Card dengan Tombol Aksi -->
                                        <div class="card-footer bg-white border-top p-3">
                                            <div class="d-flex justify-content-between">
                                                <button type="button" 
                                                        class="btn btn-warning btn-sm edit-anak" 
                                                        data-id="<?= $a['id'] ?>"
                                                        data-nama="<?= $a['nama'] ?>"
                                                        data-tanggal="<?= $a['tanggal_lahir'] ?>"
                                                        data-jk="<?= $a['jenis_kelamin'] ?>"
                                                        data-les="<?= $a['jenis_les_id'] ?>"
                                                        data-foto="<?= $a['foto'] ?>"
                                                        data-sisa="<?= $a['sisa_pertemuan'] ?>"
                                                        data-toggle="modal" 
                                                        data-target="#editAnakModal">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <a href="<?= base_url('parent/hapus-anak/' . $a['id']) ?>" 
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('Apakah Anda yakin ingin menghapus data anak ini?')">
                                                    <i class="fas fa-trash"></i> Hapus
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
            </div>
            
            <!-- Tab Pembayaran -->
            <div class="tab-pane fade" id="pembayaran" role="tabpanel" aria-labelledby="pembayaran-tab">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Pembayaran Deposit</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <p><strong>Informasi Pembayaran:</strong></p>
                            <p>1. Minimal deposit untuk 4 pertemuan</p>
                            <p>2. Pembayaran dapat dilakukan melalui transfer bank atau e-wallet</p>
                            <p>3. Status anak akan aktif setelah pembayaran dikonfirmasi</p>
                            <p>4. Bukti pembayaran akan diverifikasi dalam 1x24 jam</p>
                        </div>
                        
                        <!-- Daftar Anak yang Perlu Pembayaran -->
                       
                        <!-- Daftar Anak yang Perlu Pembayaran -->
<h5 class="mt-4">Anak yang Memerlukan Pembayaran</h5>
<?php 
$need_payment = false;
if(!empty($anak)):
    echo '<div class="row">'; // Tambahkan container row
    foreach($anak as $a):
        if($a['status'] != 'aktif' || $a['sisa_pertemuan'] < 2):
            $need_payment = true;
?>
    <!-- Card untuk setiap anak -->
    <div class="col-12 col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <!-- Header dengan nama dan status -->
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><?= $a['nama'] ?></h5>
                    <span class="badge badge-<?= $a['status'] == 'aktif' ? 'success' : 'warning' ?>">
                        <?= $a['status'] == 'aktif' ? 'Aktif' : 'Menunggu Pembayaran' ?>
                    </span>
                </div>
            </div>

            <div class="card-body">
                <!-- Foto Anak -->
              <!-- Foto Anak -->
<div class="text-center mb-3">
    <?php 
    // Debugging
    echo "<!-- Debug: Foto = " . ($a['foto'] ?? 'kosong') . " -->";
    echo "<!-- Debug: Path = " . r2_url($a['foto'] ?? '', 'anak') . " -->";
    if(!empty($a['foto'])): 
    ?>
        <img src="<?= r2_url($a['foto'], 'anak') ?>" 
             alt="Foto <?= $a['nama'] ?>" 
             class="rounded-circle" 
             style="width: 120px; height: 120px; object-fit: cover;">
    <?php else: ?>
        <div class="bg-light rounded-circle mx-auto d-flex align-items-center justify-content-center" 
             style="width: 120px; height: 120px;">
            <i class="fas fa-user fa-3x text-secondary"></i>
        </div>
    <?php endif; ?>
</div>

                <!-- Informasi Les -->
                        <div class="mb-3 p-2 bg-light rounded">
            <div class="d-flex align-items-center mb-2">
                <i class="fas fa-swimming-pool text-primary mr-2"></i>
                <strong>Jenis Les:</strong>
            </div>
            <span class="badge badge-info"><?= $a['nama_les'] ?? 'Belum ditentukan' ?></span>
        </div>

                <!-- Sisa Pertemuan -->
                <div class="mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-calendar-check text-success mr-2"></i>
                        <strong>Sisa Pertemuan:</strong>
                    </div>
                    <span class="badge badge-<?= $a['sisa_pertemuan'] < 2 ? 'danger' : 'primary' ?>">
                        <?= $a['sisa_pertemuan'] ?> kali
                    </span>
                    <?php if($a['sisa_pertemuan'] < 2): ?>
                        <div class="alert alert-warning mt-2 py-1 px-2">
                            <small><i class="fas fa-exclamation-triangle"></i> Perlu perpanjangan segera!</small>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
    <div class="d-flex align-items-center mb-2">
        <i class="fas fa-clock text-info mr-2"></i>
        <strong>Berlaku Sampai:</strong>
    </div>
    <?php 
    // Ambil pembayaran terakhir yang sukses untuk anak ini
    $lastPayment = null;
    if(!empty($pembayaran)) {
        foreach($pembayaran as $p) {
            if($p['anak_id'] == $a['id'] && $p['status'] == 'success' && !empty($p['berlaku_sampai'])) {
                $lastPayment = $p;
                break;
            }
        }
    }
    ?>
    
    <?php if($lastPayment && !empty($lastPayment['berlaku_sampai'])): ?>
        <span class="badge badge-<?= (strtotime($lastPayment['berlaku_sampai']) < strtotime('+7 days')) ? 'danger' : 'success' ?>">
            <?= date('d-m-Y', strtotime($lastPayment['berlaku_sampai'])) ?>
        </span>
        <?php if(strtotime($lastPayment['berlaku_sampai']) < strtotime('+14 days')): ?>
            <div class="alert alert-warning mt-2 py-1 px-2">
                <small><i class="fas fa-exclamation-triangle"></i> Segera berakhir!</small>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <span class="text-muted">Belum tersedia</span>
    <?php endif; ?>
</div>        
                <!-- Form Pembayaran -->
                <?php if($a['status'] != 'aktif'): ?>
                    <div class="bg-light p-3 rounded">
                        <h6 class="text-center mb-3">
                            <i class="fas fa-tag text-primary"></i>
                            <span class="d-block mt-2">
                                <?php if (isset($a['jenis_les']) && isset($a['jenis_les']['harga'])): ?>
                                    Rp <?= number_format($a['jenis_les']['harga'], 0, ',', '.') ?> / pertemuan
                                <?php else: ?>
                                    <span class="text-muted">Harga belum ditentukan</span>
                                <?php endif; ?>
                            </span>
                        </h6>
                        
                        <select class="form-control mb-3 jumlah-pertemuan" 
                                id="jumlah_pertemuan_<?= $a['id'] ?>" 
                                data-harga="<?= $a['jenis_les']['harga'] ?? 0 ?>" 
                                data-id="<?= $a['id'] ?>">
                            <option value="4">4 Pertemuan</option>
                            <option value="8">8 Pertemuan</option>
                            <option value="12">12 Pertemuan</option>
                            <option value="16">16 Pertemuan</option>
                        </select>
                        <div class="text-center mb-3">
                            <strong>Total:</strong>
                            <span class="d-block" id="total_<?= $a['id'] ?>">
                                <?php
                                $harga = isset($a['jenis_les']['harga']) ? $a['jenis_les']['harga'] : 0;
                                echo 'Rp ' . number_format($harga * 4, 0, ',', '.');
                                ?>
                            </span>
                        </div>

                        <button class="btn btn-primary btn-block"
                                onclick="bayarSekarang(<?= $a['id'] ?>)">
                            <i class="fas fa-wallet"></i> Bayar Sekarang
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php 
        endif;
    endforeach;
    echo '</div>'; // Tutup container row
    
    if(!$need_payment):
?>
    <div class="alert alert-success">
        Semua anak sudah aktif dan memiliki sisa pertemuan yang cukup.
    </div>
<?php 
    endif;
else:
?>
    <div class="alert alert-info">
        Anda belum mendaftarkan anak. Silakan tambahkan anak terlebih dahulu.
    </div>
<?php endif; ?>




                        <!-- Daftar Anak yang Perlu Pembayaran -->
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
                                                    <a href="<?= r2_url($p['bukti_pembayaran'], 'bukti_pembayaran') ?>" target="_blank" class="btn btn-sm btn-info">Lihat Bukti</a>
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
                        
                        <!-- Informasi Rekening -->
                        <div class="card mt-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Informasi Rekening Pembayaran</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Transfer Bank:</h6>
                                        <p>Bank BCA: 1234567890</p>
                                        <p>Bank BNI: 0987654321</p>
                                        <p>Bank Mandiri: 1122334455</p>
                                        <p>Atas Nama: PT Renang Ceria</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>E-Wallet:</h6>
                                        <p>DANA: 081234567890</p>
                                        <p>OVO: 081234567890</p>
                                        <p>GoPay: 081234567890</p>
                                        <p>Atas Nama: PT Renang Ceria</p>
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
                <h5 class="modal-title" id="editAnakModalLabel">Edit Data Anak</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('parent/update-anak') ?>" method="post" enctype="multipart/form-data">
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
                        <select class="form-control" id="edit_jenis_les_id" name="jenis_les_id" required>
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
            <form action="<?= base_url('parent/upload-bukti') ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="pembayaran_id" id="pembayaran_id">
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
                    <button type="submit" class="btn btn-primary btn-bayar" data-anak-id="<?= $a['id'] ?>" data-sisa-pertemuan="<?= $a['sisa_pertemuan'] ?>"><i class="fas fa-check"></i> Konfirmasi Pembayaran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function validatePayment(anakId, sisaPertemuan) {
    if (sisaPertemuan > 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Tidak dapat melakukan pembayaran',
            text: 'Pembayaran hanya bisa dilakukan jika sisa pertemuan = 0'
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
        var sisaPertemuan = $(this).data('sisa-pertemuan');
        
        if (validatePayment(anakId, sisaPertemuan)) {
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
            <form action="<?= base_url('parent/upload-bukti') ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="pembayaran_id" id="pembayaran_id">
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

// Format angka ke format Rupiah
function formatRupiah(angka) {
    var reverse = angka.toString().split('').reverse().join(''),
        ribuan = reverse.match(/\d{1,3}/g);
    if (ribuan) {
        ribuan = ribuan.join('.').split('').reverse().join('');
    } else {
        ribuan = '0';
    }
    return ribuan;
}

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
                    <button type="submit" class="btn btn-primary btn-bayar" data-anak-id="<?= $a['id'] ?>" data-sisa-pertemuan="<?= $a['sisa_pertemuan'] ?>"><i class="fas fa-check"></i> Konfirmasi Pembayaran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function validatePayment(anakId, sisaPertemuan) {
    if (sisaPertemuan > 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Tidak dapat melakukan pembayaran',
            text: 'Pembayaran hanya bisa dilakukan jika sisa pertemuan = 0'
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
        var sisaPertemuan = $(this).data('sisa-pertemuan');
        
        if (validatePayment(anakId, sisaPertemuan)) {
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
            <form action="<?= base_url('parent/upload-bukti') ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="pembayaran_id" id="pembayaran_id">
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
                    <button type="submit" class="btn btn-primary btn-bayar" data-anak-id="<?= $a['id'] ?>" data-sisa-pertemuan="<?= $a['sisa_pertemuan'] ?>"><i class="fas fa-check"></i> Konfirmasi Pembayaran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function validatePayment(anakId, sisaPertemuan) {
    if (sisaPertemuan > 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Tidak dapat melakukan pembayaran',
            text: 'Pembayaran hanya bisa dilakukan jika sisa pertemuan = 0'
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
        var sisaPertemuan = $(this).data('sisa-pertemuan');
        
        if (validatePayment(anakId, sisaPertemuan)) {
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





         
    });
});
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
          
<?= $this->endSection() ?>