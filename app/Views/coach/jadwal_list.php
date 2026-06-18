<?= $this->extend('templates/coach') ?>

<?= $this->section('content') ?>

<?php
$hariIndo = [
    'Sunday'    => 'Min',
    'Monday'    => 'Sen',
    'Tuesday'   => 'Sel',
    'Wednesday' => 'Rab',
    'Thursday'  => 'Kam',
    'Friday'    => 'Jum',
    'Saturday'  => 'Sab',
];
?>

<!-- Flash messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:12px;font-size:13px;margin-bottom:12px;">
        <i class="fas fa-check-circle me-1"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="font-size:18px;line-height:1;">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius:12px;font-size:13px;margin-bottom:12px;">
        <i class="fas fa-exclamation-circle me-1"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="font-size:18px;line-height:1;">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- Header -->
<div style="margin-bottom:16px;">
    <div style="font-size:18px;font-weight:700;color:#1F2937;margin-bottom:2px;">📅 Jadwal Les</div>
    <div style="font-size:12px;color:#6B7280;">Lihat jadwal aktif dan bergabung sebagai pelatih</div>
</div>

<?php if (empty($jadwal)): ?>
    <div style="background:#fff;border-radius:16px;border:1px solid rgba(5,150,105,0.1);box-shadow:0 2px 8px rgba(0,0,0,0.04);padding:40px 20px;text-align:center;">
        <div style="font-size:48px;margin-bottom:12px;">📭</div>
        <div style="font-size:14px;font-weight:600;color:#1F2937;margin-bottom:6px;">Belum ada jadwal aktif</div>
        <div style="font-size:12px;color:#6B7280;">Admin belum membuat jadwal les. Cek kembali nanti.</div>
    </div>
<?php else: ?>
    <?php foreach ($jadwal as $j): ?>
        <?php
        $tgl      = strtotime($j['tanggal']);
        $hariEn   = date('l', $tgl);
        $hariSing = $hariIndo[$hariEn] ?? substr($hariEn, 0, 3);
        $tglNum   = date('d', $tgl);
        $bulanThn = date('M Y', $tgl);
        $jamMulai = date('H:i', strtotime($j['jam_mulai']));
        $jamSeles = date('H:i', strtotime($j['jam_selesai']));
        $jumlahSiswa  = count($j['students']);
        $jumlahCoach  = count($j['coaches']);
        $sudahJoin    = $j['sudah_join'];
        ?>
        <div style="background:#fff;border-radius:16px;border:1px solid rgba(5,150,105,0.1);box-shadow:0 2px 8px rgba(0,0,0,0.04);margin-bottom:14px;overflow:hidden;">

            <!-- Top strip: tanggal + jam + jenis les -->
            <div style="background:linear-gradient(135deg,#059669,#064E3B);padding:14px 16px;display:flex;align-items:center;gap:14px;">
                <!-- Date badge -->
                <div style="background:rgba(255,255,255,0.15);border-radius:12px;padding:8px 12px;text-align:center;min-width:52px;flex:0 0 auto;">
                    <div style="font-size:10px;color:rgba(255,255,255,0.75);font-weight:600;text-transform:uppercase;"><?= $hariSing ?></div>
                    <div style="font-size:22px;font-weight:700;color:#fff;line-height:1.1;"><?= $tglNum ?></div>
                    <div style="font-size:9px;color:rgba(255,255,255,0.7);"><?= $bulanThn ?></div>
                </div>
                <!-- Schedule info -->
                <div style="flex:1;min-width:0;">
                    <div style="font-size:14px;font-weight:700;color:#fff;margin-bottom:4px;">
                        🕐 <?= $jamMulai ?> – <?= $jamSeles ?> WIB
                    </div>
                    <?php if (!empty($j['jenis_les_nama'])): ?>
                        <div style="display:flex;flex-wrap:wrap;gap:4px;">
                            <?php foreach (explode(', ', $j['jenis_les_nama']) as $les): ?>
                                <span style="background:rgba(255,255,255,0.2);color:#fff;font-size:10px;font-weight:600;padding:2px 8px;border-radius:20px;"><?= esc(trim($les)) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <!-- Join status badge -->
                <?php if ($sudahJoin): ?>
                    <span style="background:rgba(255,255,255,0.9);color:#059669;font-size:10px;font-weight:700;padding:4px 10px;border-radius:20px;white-space:nowrap;flex:0 0 auto;">
                        ✓ Joined
                    </span>
                <?php endif; ?>
            </div>

            <!-- Body -->
            <div style="padding:14px 16px;">

                <!-- Materi & Kapasitas -->
                <div style="display:flex;gap:12px;margin-bottom:12px;">
                    <div style="flex:1;">
                        <div style="font-size:10px;color:#6B7280;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:3px;">Materi</div>
                        <div style="font-size:13px;font-weight:600;color:#1F2937;"><?= esc($j['materi']) ?></div>
                    </div>
                    <div>
                        <div style="font-size:10px;color:#6B7280;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:3px;">Kapasitas</div>
                        <div style="font-size:13px;font-weight:600;color:#1F2937;">
                            <span style="color:<?= $jumlahSiswa >= $j['kapasitas'] ? '#EF4444' : '#059669' ?>;"><?= $jumlahSiswa ?></span>/<?= $j['kapasitas'] ?>
                        </div>
                    </div>
                    <div>
                        <div style="font-size:10px;color:#6B7280;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:3px;">Kategori</div>
                        <div style="font-size:11px;font-weight:600;">
                            <?php if ($j['jenis_latihan'] === 'private'): ?>
                                <span style="background:rgba(79,70,229,0.1);color:#4338CA;padding:2px 8px;border-radius:20px;">Private</span>
                            <?php else: ?>
                                <span style="background:rgba(14,165,233,0.1);color:#0369A1;padding:2px 8px;border-radius:20px;">Group</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Pelatih yang sudah join -->
                <?php if (!empty($j['coaches'])): ?>
                    <div style="margin-bottom:10px;">
                        <div style="font-size:10px;color:#6B7280;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;">
                            👨‍🏫 Pelatih (<?= $jumlahCoach ?>)
                        </div>
                        <div style="display:flex;flex-wrap:wrap;gap:5px;">
                            <?php foreach ($j['coaches'] as $c): ?>
                                <span style="background:#F0FDF4;border:1px solid rgba(5,150,105,0.15);color:#065F46;font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;">
                                    <?= esc($c['nama']) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div style="margin-bottom:10px;">
                        <div style="font-size:10px;color:#6B7280;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">👨‍🏫 Pelatih</div>
                        <div style="font-size:12px;color:#9CA3AF;font-style:italic;">Belum ada pelatih yang bergabung</div>
                    </div>
                <?php endif; ?>

                <!-- Siswa terdaftar -->
                <?php if (!empty($j['students'])): ?>
                    <div style="margin-bottom:12px;">
                        <div style="font-size:10px;color:#6B7280;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;">
                            🏊 Siswa Terdaftar (<?= $jumlahSiswa ?>)
                        </div>
                        <div style="display:flex;flex-wrap:wrap;gap:5px;">
                            <?php foreach ($j['students'] as $s): ?>
                                <span style="background:#F0F9FF;border:1px solid rgba(14,165,233,0.15);color:#0369A1;font-size:11px;font-weight:500;padding:3px 10px;border-radius:20px;">
                                    <?= esc($s['nama_anak']) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div style="margin-bottom:12px;">
                        <div style="font-size:10px;color:#6B7280;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">🏊 Siswa Terdaftar</div>
                        <div style="font-size:12px;color:#9CA3AF;font-style:italic;">Belum ada siswa terdaftar</div>
                    </div>
                <?php endif; ?>

                <!-- CTA Button -->
                <div style="border-top:1px solid rgba(0,0,0,0.05);padding-top:12px;">
                    <?php if (!$sudahJoin): ?>
                        <a href="<?= base_url('coach/jadwal/join/' . $j['id']) ?>"
                           onclick="return confirm('Bergabung sebagai pelatih di jadwal ini?')"
                           style="display:flex;align-items:center;justify-content:center;gap:8px;background:linear-gradient(135deg,#059669,#047857);color:#fff;font-size:13px;font-weight:700;padding:11px 20px;border-radius:12px;text-decoration:none;transition:all 0.2s;box-shadow:0 4px 12px rgba(5,150,105,0.3);">
                            <i class="fas fa-plus-circle"></i> Join Jadwal Ini
                        </a>
                    <?php else: ?>
                        <div style="display:flex;gap:8px;">
                            <div style="flex:1;background:#F0FDF4;border:1px solid rgba(5,150,105,0.15);border-radius:12px;padding:10px 14px;display:flex;align-items:center;gap:8px;">
                                <i class="fas fa-check-circle" style="color:#059669;"></i>
                                <span style="font-size:13px;font-weight:600;color:#065F46;">Anda sudah bergabung</span>
                            </div>
                            <a href="<?= base_url('coach/jadwal/cancel/' . $j['id']) ?>"
                               onclick="return confirm('Batalkan keikutsertaan di jadwal ini?')"
                               style="display:flex;align-items:center;gap:6px;background:#fff;border:1px solid rgba(239,68,68,0.3);color:#EF4444;font-size:12px;font-weight:600;padding:10px 14px;border-radius:12px;text-decoration:none;white-space:nowrap;">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= $this->endSection() ?>
