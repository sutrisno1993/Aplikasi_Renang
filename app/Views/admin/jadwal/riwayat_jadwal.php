<?= $this->include('admin/templates/header') ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Riwayat Jadwal Les</h5>
            <div>
                <a href="<?= base_url('admin/jadwal') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="riwayatJadwalTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Jenis Les</th>
                            <th>Materi</th>
                            <th>Status</th>
                            <th>Pelatih</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($jadwal)): $no = 1; foreach($jadwal as $j): ?>
                        <?php if($j['status'] == 'selesai'): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
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
                            </td>
                            <td><?= date('H:i', strtotime($j['jam_mulai'])) ?> - <?= date('H:i', strtotime($j['jam_selesai'])) ?></td>
                            <td>
                                <?php 
                                if (!empty($j['jenis_les_nama'])): 
                                    $jenis_les_array = explode(',', $j['jenis_les_nama']);
                                    foreach($jenis_les_array as $les): 
                                ?>
                                    <span class="badge bg-info text-dark me-1"><?= trim($les) ?></span>
                                <?php 
                                    endforeach; 
                                else:
                                    echo '<span class="text-muted">-</span>';
                                endif;
                                ?>
                            </td>
                            <td><?= $j['materi'] ?></td>
                            <td>
                                <span class="badge bg-success">Selesai</span>
                            </td>
                            <td>
                                <?php
                                if (!empty($j['coach_names'])):
                                    $coach_names = explode(',', $j['coach_names']);
                                    foreach($coach_names as $coach):
                                ?>
                                    <span class="badge bg-secondary me-1"><?= trim($coach) ?></span>
                                <?php
                                    endforeach;
                                else:
                                    echo '<span class="text-muted">-</span>';
                                endif;
                                ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="window.location.href='<?= base_url('admin/jadwal/detail/'.$j['id']) ?>'">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#riwayatJadwalTable').DataTable({
        "pageLength": 25,
        "order": [[1, "desc"], [2, "desc"]], // Urutkan berdasarkan tanggal dan waktu terbaru
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
        },
        "columnDefs": [
            { "orderable": false, "targets": [0, 7] } // Kolom nomor dan aksi tidak bisa diurutkan
        ]
    });
});
</script>

<?= $this->include('admin/templates/footer') ?>