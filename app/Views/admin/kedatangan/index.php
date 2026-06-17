<?= $this->include('admin/templates/header') ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <div>
                <h5 class="mb-0 fw-bold"><i class="fas fa-calendar-check me-2 text-primary"></i>Kelola Kedatangan</h5>
            </div>
            <div class="d-flex gap-2">
                <div class="btn-group p-1 bg-light rounded-3">
                    <a href="<?= base_url('admin/kedatangan?status=aktif') ?>" class="btn <?= $current_status == 'aktif' ? 'btn-white shadow-sm fw-bold' : 'btn-light border-0' ?> btn-sm px-3 rounded-2">
                        <i class="fas fa-clock me-1 text-warning"></i> Rencana
                    </a>
                    <a href="<?= base_url('admin/kedatangan?status=selesai') ?>" class="btn <?= $current_status == 'selesai' ? 'btn-white shadow-sm fw-bold' : 'btn-light border-0' ?> btn-sm px-3 rounded-2">
                        <i class="fas fa-check-circle me-1 text-success"></i> Selesai (Done)
                    </a>
                </div>
                <button type="button" class="btn btn-dark btn-sm shadow-sm rounded-3 px-3" data-bs-toggle="modal" data-bs-target="#modalBulkManual">
                    <i class="fas fa-users-cog me-1"></i> Kedatangan Manual
                </button>
            </div>
        </div>
        <div class="card-body">
            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-hover" id="jadwalTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Hari</th>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Materi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($jadwal)) : ?>
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada jadwal aktif</td>
                            </tr>
                        <?php else : ?>
                            <?php $i = 1; foreach($jadwal as $j): ?>
                            <?php 
                                // Konversi hari ke bahasa Indonesia
                                $hari = [
                                    'Sunday' => 'Minggu',
                                    'Monday' => 'Senin',
                                    'Tuesday' => 'Selasa',
                                    'Wednesday' => 'Rabu',
                                    'Thursday' => 'Kamis',
                                    'Friday' => 'Jumat',
                                    'Saturday' => 'Sabtu'
                                ];
                                $namaHari = $hari[date('l', strtotime($j['tanggal']))];
                            ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= $namaHari ?></td>
                                <td><?= date('d-m-Y', strtotime($j['tanggal'])) ?></td>
                                <td><?= date('H:i', strtotime($j['jam_mulai'])) ?> - <?= date('H:i', strtotime($j['jam_selesai'])) ?></td>
                                <td><?= $j['materi'] ?></td>
                                <td>
                                    <?php if ($j['status'] == 'aktif') : ?>
                                        <span class="badge bg-warning-light text-warning border border-warning">Rencana</span>
                                    <?php else : ?>
                                        <span class="badge bg-success-light text-success border border-success">Selesai</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?= base_url('admin/kedatangan/absensi/' . $j['id']) ?>" class="btn btn-primary btn-sm rounded-start-2">
                                            <i class="fas fa-clipboard-check"></i> Absensi
                                        </a>
                                        <?php if ($j['status'] == 'selesai') : ?>
                                            <a href="<?= base_url('admin/kedatangan/kirim-wa-group-jadwal/' . $j['id']) ?>" target="_blank" class="btn btn-success btn-sm rounded-end-2">
                                                <i class="fab fa-whatsapp"></i> Grup
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->include('admin/templates/footer') ?>

<!-- Modal Bulk Manual -->
<div class="modal fade" id="modalBulkManual" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-dark text-white rounded-top-4">
                <h5 class="modal-title"><i class="fas fa-users-cog me-2"></i>Input Kedatangan Manual (Bulk)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('admin/kedatangan/save-bulk-manual') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="alert alert-info small mb-4">
                        <i class="fas fa-info-circle me-1"></i> Gunakan fitur ini untuk menginput banyak absensi sekaligus. Sisa pertemuan anak akan otomatis berkurang.
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase">1. Pilih Tanggal</label>
                            <input type="date" name="tanggal" id="bulk_tanggal" class="form-control form-control-lg border-0 bg-light shadow-sm" required value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase">2. Pilih Jadwal</label>
                            <select name="schedule_id" id="bulk_schedule_id" class="form-select form-select-lg border-0 bg-light shadow-sm" required disabled>
                                <option value="">-- Pilih Tanggal Dahulu --</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold small text-uppercase">3. Tempelkan/Ketik ID Anak</label>
                        <textarea name="id_anak_list" class="form-control border-0 bg-light shadow-sm" rows="6" placeholder="Contoh:&#10;72&#10;45&#10;120&#10;&#10;(Gunakan baris baru, spasi, atau koma sebagai pemisah)" required></textarea>
                        <div class="form-text small mt-2">Sistem akan otomatis membersihkan teks dan mengambil angka ID saja.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 rounded-bottom-4 p-3">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dark px-4 shadow-sm">
                        <i class="fas fa-check-circle me-1"></i> Proses Absensi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#jadwalTable').DataTable({
        "order": [[2, "desc"]]
    });

    // Fungsi untuk mengambil jadwal berdasarkan tanggal
    function fetchSchedules(tanggal) {
        if (!tanggal) return;
        
        const $select = $('#bulk_schedule_id');
        $select.html('<option value="">Memuat jadwal...</option>').prop('disabled', true);

        $.ajax({
            url: '<?= base_url('admin/kedatangan/get-schedules-by-date') ?>',
            type: 'GET',
            data: { tanggal: tanggal },
            dataType: 'json',
            success: function(response) {
                let options = '<option value="">-- Pilih Jadwal --</option>';
                if (response.length > 0) {
                    response.forEach(function(item) {
                        options += `<option value="${item.id}">${item.jam_mulai} - ${item.jam_selesai} (${item.materi || 'Tanpa Materi'}) [${item.status}]</option>`;
                    });
                    $select.html(options).prop('disabled', false);
                } else {
                    $select.html('<option value="">Tidak ada jadwal pada tanggal ini</option>').prop('disabled', true);
                }
            },
            error: function() {
                $select.html('<option value="">Gagal memuat data</option>').prop('disabled', true);
            }
        });
    }

    // Trigger saat modal dibuka
    $('#modalBulkManual').on('shown.bs.modal', function () {
        fetchSchedules($('#bulk_tanggal').val());
    });

    // Trigger saat tanggal diubah
    $('#bulk_tanggal').on('change', function() {
        fetchSchedules($(this).val());
    });
});
</script>