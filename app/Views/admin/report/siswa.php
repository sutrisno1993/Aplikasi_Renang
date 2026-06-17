<?= $this->include('admin/templates/header') ?>

<div class="container-fluid">
    <div class="row">
        <!-- Statistik Per Paket -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold">Siswa Per Paket Les</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <?php foreach($per_paket as $p): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <?= $p['nama_les'] ?: 'Belum Pilih Paket' ?>
                            <span class="badge bg-primary rounded-pill"><?= $p['jumlah'] ?> Siswa</span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Tabel Siswa Aktif -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">Daftar Siswa Aktif & Sisa Pertemuan</h6>
                    <button onclick="window.print()" class="btn btn-sm btn-outline-dark"><i class="fas fa-print"></i></button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">Siswa</th>
                                    <th>Orang Tua</th>
                                    <th>Paket</th>
                                    <th class="text-center">Sisa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($siswa as $s): ?>
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-bold"><?= $s['nama'] ?></div>
                                        <small class="text-muted">#<?= $s['id'] ?></small>
                                    </td>
                                    <td>
                                        <div><?= $s['nama_parent'] ?></div>
                                        <small class="text-muted"><?= $s['whatsapp'] ?></small>
                                    </td>
                                    <td><span class="small"><?= $s['nama_les'] ?></span></td>
                                    <td class="text-center">
                                        <?php 
                                            $color = 'success';
                                            if($s['sisa_pertemuan'] <= 1) $color = 'danger';
                                            elseif($s['sisa_pertemuan'] <= 2) $color = 'warning';
                                        ?>
                                        <span class="badge bg-<?= $color ?>"><?= $s['sisa_pertemuan'] ?> kali</span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('admin/templates/footer') ?>
