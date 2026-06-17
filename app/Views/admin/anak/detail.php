<?= $this->include('admin/templates/header') ?>

<!-- Detail Anak Content -->
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Detail Anak</h5>
            <div>
                <a href="<?= base_url('admin/anak/edit/' . $anak['id']) ?>" class="btn btn-warning me-2">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
                <a href="<?= base_url('admin/anak') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-4 text-center mb-4">
                    <?php if(!empty($anak['foto'])): ?>
                        <img src="<?= r2_url($anak['foto'], 'anak') ?>" 
                             alt="Foto <?= $anak['nama'] ?>" 
                             class="img-thumbnail rounded-circle" 
                             style="width: 200px; height: 200px; object-fit: cover;"
                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<?= urlencode($anak['nama']) ?>&size=200&color=7F9CF5&background=EBF4FF';">
                    <?php else: ?>
                        <div class="bg-primary rounded-circle mx-auto d-flex align-items-center justify-content-center text-white fw-bold" 
                             style="width: 200px; height: 200px; font-size: 5rem;">
                            <?= get_initials($anak['nama']) ?>
                        </div>
                    <?php endif; ?>
                    
                    <h4 class="mt-3"><?= $anak['nama'] ?></h4>
                    <span class="badge badge-<?= $anak['status'] == 'aktif' ? 'success' : 'warning' ?> mb-2">
                        <?= $anak['status'] == 'aktif' ? 'Aktif' : 'Menunggu Pembayaran' ?>
                    </span>
                </div>
                
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Informasi Anak</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">ID</th>
                                    <td><?= $anak['id'] ?></td>
                                </tr>
                                <tr>
                                    <th>Tanggal Lahir</th>
                                    <td><?= date('d-m-Y', strtotime($anak['tanggal_lahir'])) ?></td>
                                </tr>
                                <tr>
                                    <th>Jenis Kelamin</th>
                                    <td><?= $anak['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                                </tr>
                                <tr>
                                    <th>Jenis Les</th>
                                    <td><?= $anak['nama_les'] ?></td>
                                </tr>
                                <tr>
                                    <th>Harga Les</th>
                                    <td>Rp <?= number_format($anak['harga_les'], 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <th>Sisa Pertemuan</th>
                                    <td>
                                        <?php $sisaVal = (int) ($detail_sisa['sisa'] ?? 0); ?>
                                        <span class="badge badge-<?= $sisaVal < 2 ? 'danger' : 'primary' ?>">
                                            <?= esc((string) ($detail_sisa['sisa_display'] ?? 0)) ?> kali
                                        </span>
                                        <br>
                                        <small class="text-muted">Paket ke-<?= $detail_sisa['paket_ke'] ?? 1 ?>, Pertemuan ke-<?= $detail_sisa['pertemuan_ke'] ?? 0 ?></small>
                                        <?php if (!empty($breakdown['hangus_total'])): ?>
                                            <br><small class="text-danger"><?= (int) $breakdown['hangus_total'] ?> pertemuan hangus (lewat masa berlaku)</small>
                                        <?php endif; ?>
                                        <?php if (!empty($breakdown['debt'])): ?>
                                            <br><small class="text-warning">Nunggak <?= (int) $breakdown['debt'] ?> pertemuan (belum tertutup paket)</small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Informasi Orang Tua</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Nama</th>
                                    <td><?= $anak['nama_parent'] ?></td>
                                </tr>
                                <!-- Ubah dari: -->
                            
                                
                                <!-- Menjadi: -->
                                <tr>
                                    <th>WhatsApp</th>
                                    <td><?= $anak['whatsapp_parent'] ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-12">
                    <h5>Pemetaan Pertemuan (Mapping)</h5>
                    <?php if(empty($history_groups)): ?>
                        <div class="alert alert-light border small">Belum ada pemetaan pertemuan untuk saat ini.</div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach($history_groups as $group): ?>
                                <div class="col-12 col-lg-6 mb-3">
                                    <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 15px; background: #f8f9ff;">
                                        <div class="row g-0">
                                            <!-- Sisi Kiri: Info Pembayaran -->
                                            <div class="col-4 p-3 text-center border-end d-flex flex-column justify-content-center align-items-center" style="background: rgba(79, 70, 229, 0.05);">
                                                <div class="small fw-bold text-primary mb-2"><?= $group['label'] ?></div>
                                                <?php if($group['payment']): ?>
                                                    <div class="small text-muted mb-1"><?= date('d/m/y', strtotime($group['payment']['tanggal'])) ?></div>
                                                    <?php if (!empty($group['berlaku_sampai'])): ?>
                                                        <div class="small text-muted mb-1">s/d <?= date('d/m/y', strtotime($group['berlaku_sampai'])) ?></div>
                                                    <?php endif; ?>
                                                    <?php if (!empty($group['is_expired'])): ?>
                                                        <span class="badge bg-danger mb-1" style="font-size:9px;">EXPIRED</span>
                                                        <?php if (!empty($group['hangus'])): ?>
                                                            <div class="small text-danger fw-bold"><?= (int) $group['hangus'] ?> hangus</div>
                                                        <?php endif; ?>
                                                    <?php elseif (($group['sisa_aktif'] ?? 0) > 0): ?>
                                                        <span class="badge bg-success mb-1" style="font-size:9px;">Aktif · sisa <?= (int) $group['sisa_aktif'] ?></span>
                                                    <?php endif; ?>
                                                    <div class="payment-proof mb-2">
                                                        <?php if(!empty($group['payment']['bukti_pembayaran'])): ?>
                                                            <img src="<?= r2_url($group['payment']['bukti_pembayaran'], 'pembayaran') ?>" 
                                                                 class="img-thumbnail cursor-pointer" 
                                                                 style="width: 70px; height: 70px; object-fit: cover; border-radius: 10px;"
                                                                 onclick="zoomImage(this.src)">
                                                        <?php else: ?>
                                                            <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 70px; height: 70px; border-radius: 10px; font-size: 10px;">No Photo</div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="badge bg-success" style="font-size: 10px;">Lunas</div>
                                                <?php else: ?>
                                                    <div class="text-warning small fw-bold">Belum Dibayar</div>
                                                    <i class="fas fa-exclamation-circle text-warning mt-2" style="font-size: 24px;"></i>
                                                <?php endif; ?>
                                            </div>
                                            <!-- Sisi Kanan: Daftar Pertemuan -->
                                            <div class="col-8 p-0">
                                                <div class="list-group list-group-flush h-100">
                                                    <?php 
                                                    $sessions = $group['sessions'];
                                                    foreach($sessions as $num => $session): 
                                                        $is_over_quota = !isset($group['payment']);
                                                        $is_hangus = is_array($session) && (($session['slot_status'] ?? '') === 'hangus');
                                                        $is_hadir = is_array($session) && !empty($session['tanggal']) && !$is_hangus;
                                                    ?>
                                                        <div class="list-group-item bg-transparent border-0 py-2 px-3 d-flex align-items-center justify-content-between" style="border-bottom: 1px solid rgba(0,0,0,0.05) !important; margin-bottom: 0 !important;">
                                                            <div class="d-flex align-items-center">
                                                                <span class="badge rounded-circle bg-<?= $is_hadir ? 'success' : ($is_hangus ? 'danger' : 'light') ?> text-<?= ($is_hadir || $is_hangus) ? 'white' : 'muted' ?> me-2 d-flex align-items-center justify-content-center" style="width: 20px; height: 20px; font-size: 10px;">
                                                                    <?= $is_over_quota ? ($num) : $num ?>
                                                                </span>
                                                                <span class="small <?= $is_hadir ? 'fw-bold' : ($is_hangus ? 'text-danger fw-bold' : 'text-muted') ?>">
                                                                    <?php if ($is_hangus): ?>
                                                                        Pert <?= $num ?> — Hangus
                                                                    <?php elseif ($is_hadir): ?>
                                                                        Pert <?= $num ?>
                                                                    <?php else: ?>
                                                                        Belum dijalankan
                                                                    <?php endif; ?>
                                                                </span>
                                                            </div>
                                                            <?php if($is_hadir): ?>
                                                                <div class="fw-bold text-dark" style="font-size: 13px;">
                                                                    <?= date('d/m/y', strtotime($session['tanggal'])) ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <h5>Riwayat Latihan</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Hari, Tanggal</th>
                                    <th>Waktu</th>
                                    <th>Pkt/Ke</th>
                                    <th>Materi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($kehadiran)): ?>
                                    <?php 
                                    $hari = [
                                        'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
                                        'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
                                    ];
                                    $no = 1; 
                                    foreach ($kehadiran as $k): 
                                        $dayName = date('l', strtotime($k['tanggal']));
                                    ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= $hari[$dayName] ?>, <?= date('d-m-Y', strtotime($k['tanggal'])) ?></td>
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
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Belum ada riwayat latihan</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <h5>Riwayat Pembayaran</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Bukti</th>
                                    <th>Tanggal</th>
                                    <th>Jumlah Pertemuan</th>
                                    <th>Total</th>
                                    <th>Metode</th>
                                    <th>Masa Berlaku</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($pembayaran)): ?>
                                    <?php $no = 1; foreach ($pembayaran as $p): ?>
                                        <?php 
                                            $isExpired = false;
                                            if ($p['status'] == 'success' && !empty($p['berlaku_sampai'])) {
                                                $isExpired = strtotime($p['berlaku_sampai']) < strtotime(date('Y-m-d'));
                                            }
                                        ?>
                                        <tr class="<?= $isExpired ? 'table-warning' : '' ?>">
                                            <td><?= $no++ ?></td>
                                            <td>
                                                <?php if (!empty($p['bukti_pembayaran'])) : ?>
                                                    <img src="<?= r2_url($p['bukti_pembayaran'], 'pembayaran') ?>" 
                                                         alt="Bukti" 
                                                         class="img-thumbnail cursor-pointer" 
                                                         style="width: 40px; height: 40px; object-fit: cover;"
                                                         onclick="zoomImage(this.src)">
                                                <?php else : ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('d-m-Y', strtotime($p['tanggal'])) ?></td>
                                            <td><?= $p['jumlah_pertemuan'] ?> kali</td>
                                            <td>Rp <?= number_format($p['total'], 0, ',', '.') ?></td>
                                            <td><?= esc($p['metode_pembayaran']) ?></td>
                                            <td>
                                                <span class="fw-bold <?= $isExpired ? 'text-danger' : 'text-success' ?>">
                                                    <?= !empty($p['berlaku_sampai']) ? date('d-m-Y', strtotime($p['berlaku_sampai'])) : '-' ?>
                                                </span>
                                                <?php if ($isExpired): ?>
                                                    <br><small class="badge bg-danger">Expired</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($p['status'] == 'pending'): ?>
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                <?php elseif($p['status'] == 'success'): ?>
                                                    <span class="badge bg-success text-white">Sukses</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger text-white">Ditolak</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if($p['status'] == 'success'): ?>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger" 
                                                            onclick="openExtendModal(<?= $p['id'] ?>, '<?= $p['berlaku_sampai'] ?>', '<?= date('d-m-Y', strtotime($p['tanggal'])) ?>')"
                                                            title="Perpanjang Masa Berlaku">
                                                        <i class="fas fa-calendar-plus"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Belum ada riwayat pembayaran</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
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

<!-- Modal Extend Masa Berlaku -->
<div class="modal fade" id="extendModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Perpanjang Masa Berlaku Paket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('admin/anak/extend-paket') ?>" method="post">
                <div class="modal-body">
                    <input type="hidden" name="pembayaran_id" id="extend_pembayaran_id">
                    <div class="mb-3">
                        <label class="form-label">Tanggal Pembayaran</label>
                        <input type="text" id="extend_tgl_bayar" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Masa Berlaku Baru</label>
                        <input type="date" name="berlaku_sampai" id="extend_new_date" class="form-control" required>
                        <small class="text-muted">Pilih tanggal baru untuk memperpanjang masa berlaku paket ini.</small>
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

<?= $this->include('admin/templates/footer') ?>

<script>
function zoomImage(src) {
    document.getElementById('zoomedImg').src = src;
    var myModal = new bootstrap.Modal(document.getElementById('zoomModal'));
    myModal.show();
}

function openExtendModal(id, currentDate, tglBayar) {
    document.getElementById('extend_pembayaran_id').value = id;
    document.getElementById('extend_tgl_bayar').value = tglBayar;
    document.getElementById('extend_new_date').value = currentDate;
    var myModal = new bootstrap.Modal(document.getElementById('extendModal'));
    myModal.show();
}
</script>
