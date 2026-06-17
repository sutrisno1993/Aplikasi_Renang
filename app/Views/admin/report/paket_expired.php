<?= $this->include('admin/templates/header') ?>

<div class="container-fluid">
    <div class="row mb-4 g-3">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h6 class="text-uppercase small fw-bold text-muted mb-1">Total Paket Expired</h6>
                    <h2 class="fw-bold text-danger mb-0"><?= (int) ($summary['total_paket'] ?? 0) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h6 class="text-uppercase small fw-bold text-muted mb-1">Pertemuan Hangus</h6>
                    <h2 class="fw-bold text-warning mb-0"><?= (int) ($summary['total_hangus'] ?? 0) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h6 class="text-uppercase small fw-bold text-muted mb-1">Siswa Terdampak</h6>
                    <h2 class="fw-bold text-primary mb-0"><?= (int) ($summary['total_siswa'] ?? 0) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <h6 class="text-uppercase small fw-bold text-muted mb-1">Total Nominal Paket</h6>
                    <h2 class="fw-bold text-dark mb-0" style="font-size: 1.35rem;">Rp <?= number_format((float) ($summary['total_nominal'] ?? 0), 0, ',', '.') ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body bg-light rounded-4">
            <form action="" method="get" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Masa berlaku habis dari</label>
                    <input type="date" name="tgl_mulai" class="form-control" value="<?= esc($filter['tgl_mulai']) ?>">
                    <small class="text-muted">Filter tanggal <strong>berlaku sampai</strong></small>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Sampai tanggal</label>
                    <input type="date" name="tgl_selesai" class="form-control" value="<?= esc($filter['tgl_selesai']) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Nama siswa</label>
                    <input type="text" name="nama_anak" class="form-control" value="<?= esc($filter['nama_anak']) ?>" placeholder="Cari nama anak...">
                </div>
                <div class="col-md-3">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="hanya_hangus" value="1" id="hanya_hangus" <?= ($filter['hanya_hangus'] ?? '0') === '1' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="hanya_hangus">
                            Hanya paket dengan pertemuan hangus
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-bold">Daftar Paket Expired</h5>
            <small class="text-muted">Masa berlaku = tanggal bayar + 90 hari</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Siswa / Orang Tua</th>
                            <th>Paket</th>
                            <th>Tanggal Bayar</th>
                            <th>Berlaku Sampai</th>
                            <th>Pemakaian</th>
                            <th>Keterangan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($rows)): ?>
                            <?php foreach ($rows as $row): ?>
                                <?php
                                    $badge = 'secondary';
                                    if (($row['hangus'] ?? 0) > 0) {
                                        $badge = 'danger';
                                    } elseif (($row['status_paket'] ?? '') === 'habis') {
                                        $badge = 'success';
                                    }
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold"><?= esc($row['nama_anak']) ?></div>
                                        <small class="text-muted d-block"><?= esc($row['nama_parent']) ?></small>
                                        <?php if (!empty($row['whatsapp']) && $row['whatsapp'] !== '-'): ?>
                                            <a href="https://wa.me/62<?= preg_replace('/^0/', '', preg_replace('/\D/', '', $row['whatsapp'])) ?>" target="_blank" class="small text-success">
                                                <i class="fab fa-whatsapp"></i> <?= esc($row['whatsapp']) ?>
                                            </a>
                                        <?php endif; ?>
                                        <div class="mt-1">
                                            <span class="badge bg-light text-dark border"><?= esc($row['jenis_les']) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-dark">Paket ke-<?= (int) $row['paket_ke'] ?></span>
                                        <?php if (!empty($row['invoice_number'])): ?>
                                            <div class="small text-muted mt-1"><?= esc($row['invoice_number']) ?></div>
                                        <?php endif; ?>
                                        <div class="small text-muted">Rp <?= number_format((float) $row['total_bayar'], 0, ',', '.') ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?= esc($row['tanggal_bayar_display']) ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-danger"><?= esc($row['berlaku_sampai_display']) ?></div>
                                        <small class="text-muted"><?= (int) $row['hari_sejak_expired'] ?> hari lalu</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $badge ?>">
                                            <?= (int) $row['terpakai'] ?>/<?= (int) $row['kuota'] ?> terpakai
                                        </span>
                                        <?php if ((int) $row['hangus'] > 0): ?>
                                            <div class="small text-danger fw-bold mt-1"><?= (int) $row['hangus'] ?> hangus</div>
                                        <?php endif; ?>
                                    </td>
                                    <td style="min-width: 280px;">
                                        <p class="mb-1 small"><?= esc($row['keterangan']) ?></p>
                                        <details class="small text-muted">
                                            <summary class="text-primary" style="cursor: pointer;">Detail per pertemuan</summary>
                                            <div class="mt-1"><?= esc($row['detail_pertemuan']) ?></div>
                                        </details>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('admin/anak/detail/' . $row['anak_id']) ?>" class="btn btn-sm btn-outline-primary" title="Detail siswa">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                                    <p class="mb-0">Tidak ada paket expired pada filter ini.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->include('admin/templates/footer') ?>
