<?= $this->extend('templates/coach') ?>

<?= $this->section('content') ?>

<?php
$coachNama  = session()->get('coach_nama');
$coachEmail = session()->get('coach_email');
$coachRole  = session()->get('coach_role');
$isHead     = $coachRole === 'head_coach';
?>

<!-- Greeting Card -->
<div style="background: linear-gradient(135deg, #059669, #064E3B); border-radius: 20px; padding: 20px; margin-bottom: 16px; color: #fff; position: relative; overflow: hidden;">
    <div style="position:absolute;top:-20px;right:-20px;font-size:80px;opacity:0.08;">🏊</div>
    <div style="font-size:12px;opacity:0.75;margin-bottom:4px;">Selamat datang kembali,</div>
    <div style="font-size:20px;font-weight:700;margin-bottom:6px;"><?= esc($coachNama) ?></div>
    <div style="display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,0.15);border-radius:20px;padding:4px 12px;font-size:11px;font-weight:600;">
        <?php if ($isHead): ?>
            <i class="fas fa-star" style="font-size:9px;color:#FCD34D"></i> Head Coach
        <?php else: ?>
            <i class="fas fa-user-tie" style="font-size:9px"></i> Pelatih
        <?php endif; ?>
    </div>
</div>

<!-- Quick Actions -->
<div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:#059669;margin-bottom:10px;">Menu Utama</div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:20px;">

    <a href="<?= base_url('coach/evaluasi') ?>" style="background:#fff;border-radius:16px;padding:16px;border:1px solid rgba(5,150,105,0.1);box-shadow:0 2px 8px rgba(0,0,0,0.04);text-decoration:none;color:#1F2937;display:flex;flex-direction:column;gap:8px;transition:all 0.2s;">
        <div style="width:40px;height:40px;border-radius:12px;background:rgba(5,150,105,0.1);display:grid;place-items:center;font-size:18px;">📋</div>
        <div style="font-size:13px;font-weight:700;">Evaluasi Mingguan</div>
        <div style="font-size:11px;color:#6B7280;">Input nilai perkembangan siswa</div>
    </a>

    <a href="<?= base_url('coach/ujian') ?>" style="background:#fff;border-radius:16px;padding:16px;border:1px solid rgba(5,150,105,0.1);box-shadow:0 2px 8px rgba(0,0,0,0.04);text-decoration:none;color:#1F2937;display:flex;flex-direction:column;gap:8px;transition:all 0.2s;">
        <div style="width:40px;height:40px;border-radius:12px;background:rgba(14,165,233,0.1);display:grid;place-items:center;font-size:18px;">🎓</div>
        <div style="font-size:13px;font-weight:700;">Ujian Kenaikan</div>
        <div style="font-size:11px;color:#6B7280;">Rekomendasikan naik level</div>
    </a>

    <?php if ($isHead): ?>
    <a href="<?= base_url('coach/pelatih') ?>" style="background:#fff;border-radius:16px;padding:16px;border:1px solid rgba(5,150,105,0.1);box-shadow:0 2px 8px rgba(0,0,0,0.04);text-decoration:none;color:#1F2937;display:flex;flex-direction:column;gap:8px;transition:all 0.2s;">
        <div style="width:40px;height:40px;border-radius:12px;background:rgba(79,70,229,0.1);display:grid;place-items:center;font-size:18px;">👥</div>
        <div style="font-size:13px;font-weight:700;">Kelola Pelatih</div>
        <div style="font-size:11px;color:#6B7280;">Atur tanggung jawab level</div>
    </a>
    <?php endif; ?>

    <a href="<?= base_url('coach/logout') ?>" style="background:#fff;border-radius:16px;padding:16px;border:1px solid rgba(239,68,68,0.1);box-shadow:0 2px 8px rgba(0,0,0,0.04);text-decoration:none;color:#1F2937;display:flex;flex-direction:column;gap:8px;transition:all 0.2s;">
        <div style="width:40px;height:40px;border-radius:12px;background:rgba(239,68,68,0.08);display:grid;place-items:center;font-size:18px;">🚪</div>
        <div style="font-size:13px;font-weight:700;">Keluar</div>
        <div style="font-size:11px;color:#6B7280;">Logout dari portal</div>
    </a>

</div>

<!-- Info Akun -->
<div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:#059669;margin-bottom:10px;">Informasi Akun</div>
<div style="background:#fff;border-radius:16px;border:1px solid rgba(5,150,105,0.1);box-shadow:0 2px 8px rgba(0,0,0,0.04);overflow:hidden;">
    <div style="display:flex;align-items:center;gap:12px;padding:14px 16px;border-bottom:1px solid rgba(0,0,0,0.05);">
        <div style="width:32px;height:32px;border-radius:10px;background:rgba(5,150,105,0.08);display:grid;place-items:center;color:#059669;font-size:13px;flex:0 0 auto;">
            <i class="fas fa-user"></i>
        </div>
        <div>
            <div style="font-size:11px;color:#6B7280;">Nama</div>
            <div style="font-size:13px;font-weight:600;"><?= esc($coachNama) ?></div>
        </div>
    </div>
    <div style="display:flex;align-items:center;gap:12px;padding:14px 16px;border-bottom:1px solid rgba(0,0,0,0.05);">
        <div style="width:32px;height:32px;border-radius:10px;background:rgba(5,150,105,0.08);display:grid;place-items:center;color:#059669;font-size:13px;flex:0 0 auto;">
            <i class="fas fa-envelope"></i>
        </div>
        <div>
            <div style="font-size:11px;color:#6B7280;">Email</div>
            <div style="font-size:13px;font-weight:600;"><?= esc($coachEmail) ?></div>
        </div>
    </div>
    <div style="display:flex;align-items:center;gap:12px;padding:14px 16px;">
        <div style="width:32px;height:32px;border-radius:10px;background:rgba(5,150,105,0.08);display:grid;place-items:center;color:#059669;font-size:13px;flex:0 0 auto;">
            <i class="fas fa-id-badge"></i>
        </div>
        <div>
            <div style="font-size:11px;color:#6B7280;">Role</div>
            <div style="font-size:13px;font-weight:600;">
                <?php if ($isHead): ?>
                    <span style="background:rgba(14,165,233,0.1);color:#0369A1;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;">
                        <i class="fas fa-star" style="font-size:9px"></i> Head Coach
                    </span>
                <?php else: ?>
                    <span style="background:rgba(5,150,105,0.1);color:#065F46;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;">
                        Pelatih
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
