<?= $this->include('admin/templates/header') ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Kelola Jadwal Les</h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahJadwalModal">
                <i class="fas fa-plus"></i> Tambah Jadwal
            </button>
        </div>
        <div class="card-body">
            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php 
                        $err = session()->getFlashdata('error');
                        if (is_array($err)) {
                            echo implode('<br>', $err);
                        } else {
                            echo esc($err);
                        }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-hover" id="jadwalTable">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Jenis Les</th>
                            <th>Materi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($jadwal)): foreach($jadwal as $j): ?>
                        <?php if($j['status'] == 'aktif'): ?> <!-- Tambahkan kondisi ini -->
                        <tr>
                            <td>
                                <?php 
                                    $hari = date('l', strtotime($j['tanggal']));
                                    // Konversi nama hari ke Bahasa Indonesia
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
                                <?php
                                $badge_class = 'bg-primary';
                                if($j['status'] == 'selesai') $badge_class = 'bg-success';
                                elseif($j['status'] == 'dibatalkan') $badge_class = 'bg-danger';
                                ?>
                                <span class="badge <?= $badge_class ?>"><?= ucfirst($j['status']) ?></span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="window.location.href='<?= base_url('admin/jadwal/detail/'.$j['id']) ?>'">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editJadwalModal<?= $j['id'] ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteJadwal(<?= $j['id'] ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endif; ?> <!-- Tutup kondisi status aktif -->
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Jadwal -->
<div class="modal fade" id="tambahJadwalModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Jadwal Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/jadwal/save') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                        </div>
                    </div>

                    <input type="hidden" id="jam_mulai" name="jam_mulai" value="06:00" required>
                    <input type="hidden" id="jam_selesai" name="jam_selesai" value="18:00" required>
                    <input type="hidden" id="kapasitas" name="kapasitas" value="50" required>
                    <input type="hidden" id="jenis_latihan" name="jenis_latihan" value="group" required>
                    <input type="hidden" id="materi" name="materi" value="Latihan Rutin" required>

                    <?php foreach ($jenis_les as $les): ?>
                        <input type="hidden" name="jenis_les[]" value="<?= $les['id'] ?>">
                    <?php endforeach; ?>

                    <?php foreach ($coaches as $coach): ?>
                        <input type="hidden" name="coach_id[]" value="<?= $coach['id'] ?>">
                    <?php endforeach; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Jadwal -->
<?php foreach($jadwal as $j): ?>
<div class="modal fade" id="editJadwalModal<?= $j['id'] ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Jadwal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/jadwal/update/'.$j['id']) ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="tanggal<?= $j['id'] ?>" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal<?= $j['id'] ?>" name="tanggal" 
                                   value="<?= $j['tanggal'] ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label for="jam_mulai<?= $j['id'] ?>" class="form-label">Jam Mulai</label>
                            <input type="time" class="form-control" id="jam_mulai<?= $j['id'] ?>" name="jam_mulai" 
                                   value="<?= $j['jam_mulai'] ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label for="jam_selesai<?= $j['id'] ?>" class="form-label">Jam Selesai</label>
                            <input type="time" class="form-control" id="jam_selesai<?= $j['id'] ?>" name="jam_selesai" 
                                   value="<?= $j['jam_selesai'] ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jenis Les</label>
                        <div class="row">
                            <?php 
                            $selected_les = !empty($j['jenis_les_nama']) ? explode(',', $j['jenis_les_nama']) : [];
                            foreach($jenis_les as $les): 
                            ?>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="jenis_les[]" 
                                           value="<?= $les['id'] ?>" id="les<?= $les['id'] ?>_<?= $j['id'] ?>"
                                           <?= in_array(trim($les['nama_les']), array_map('trim', $selected_les)) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="les<?= $les['id'] ?>_<?= $j['id'] ?>">
                                        <?= $les['nama_les'] ?>
                                    </label>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="materi<?= $j['id'] ?>" class="form-label">Materi</label>
                        <textarea class="form-control" id="materi<?= $j['id'] ?>" name="materi" rows="3" required><?= $j['materi'] ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="kapasitas<?= $j['id'] ?>" class="form-label">Kapasitas</label>
                        <input type="number" class="form-control" id="kapasitas<?= $j['id'] ?>" name="kapasitas" 
                               min="1" value="<?= $j['kapasitas'] ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="status<?= $j['id'] ?>" class="form-label">Status</label>
                        <select class="form-control" id="status<?= $j['id'] ?>" name="status" required>
                            <option value="aktif" <?= $j['status'] == 'aktif' ? 'selected' : '' ?>>Aktif</option>
                            <option value="selesai" <?= $j['status'] == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                            <option value="dibatalkan" <?= $j['status'] == 'dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                        </select>
                    </div>

                    <!-- Tambahkan field untuk memilih multiple coach -->
                    <div class="mb-3">
                        <label for="coach_id<?= $j['id'] ?>" class="form-label">Pelatih</label>
                        <select class="form-select" id="coach_id<?= $j['id'] ?>" name="coach_id[]" multiple>
                            <?php foreach($coaches as $coach): ?>
                                <option value="<?= $coach['id'] ?>" 
                                    <?= in_array($coach['id'], explode(',', $j['coach_ids'] ?? '')) ? 'selected' : '' ?>>
                                    <?= $coach['nama'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Tekan CTRL untuk memilih lebih dari satu pelatih</small>
                    </div>

                    <!-- Tambahkan field jenis latihan -->
                    <div class="mb-3">
                        <label for="jenis_latihan<?= $j['id'] ?>" class="form-label">Jenis Latihan</label>
                        <select class="form-select" id="jenis_latihan<?= $j['id'] ?>" name="jenis_latihan" required>
                            <option value="private" <?= $j['jenis_latihan'] == 'private' ? 'selected' : '' ?>>Private</option>
                            <option value="group" <?= $j['jenis_latihan'] == 'group' ? 'selected' : '' ?>>Group</option>
                        </select>
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
<?php endforeach; ?>

<!-- Tambahkan di bagian head atau sebelum closing body -->


<script>
function deleteJadwal(id) {
    if(confirm('Apakah Anda yakin ingin menghapus jadwal ini?')) {
        window.location.href = '<?= base_url('admin/jadwal/delete/') ?>' + id;
    }
}
</script>

<?= $this->include('admin/templates/footer') ?>
