<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= esc($title) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4F46E5;
            --primary-light: #818CF8;
            --secondary: #10B981;
            --bg-color: #F3F4F6;
            --card-bg: #FFFFFF;
            --text-main: #1F2937;
            --text-muted: #6B7280;
            --danger: #EF4444;
            --warning: #F59E0B;
            --border-radius: 16px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Outfit', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            padding-bottom: 2rem;
        }

        .header {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            padding: 1.5rem 1.5rem 4rem;
            color: white;
            border-bottom-left-radius: 24px;
            border-bottom-right-radius: 24px;
            position: relative;
        }

        .back-btn {
            color: white;
            text-decoration: none;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .profile-card {
            background: var(--card-bg);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin: -40px 1.5rem 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            position: relative;
            display: flex;
            align-items: center;
            gap: 1.2rem;
        }

        .avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-light), var(--primary));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 600;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3);
        }

        .profile-info {
            flex-grow: 1;
        }

        .profile-name {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-main);
            line-height: 1.2;
            margin-bottom: 0.3rem;
        }

        .profile-nickname {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .status-badges {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .badge {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-sisa { background: rgba(79, 70, 229, 0.1); color: var(--primary); }
        .badge-sisa.low { background: rgba(239, 68, 68, 0.1); color: var(--danger); }
        .badge-date { background: rgba(16, 185, 129, 0.1); color: var(--secondary); }
        .badge-date.expired { background: rgba(239, 68, 68, 0.12); color: var(--danger); }
        .badge-expired { background: var(--danger); color: white; }

        .alert-expired {
            margin: 0 1.5rem 1rem;
            padding: 0.9rem 1rem;
            border-radius: 12px;
            background: #FEF2F2;
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #991B1B;
            font-size: 0.88rem;
            line-height: 1.45;
        }

        .alert-expired strong {
            display: block;
            margin-bottom: 0.25rem;
            font-size: 0.95rem;
        }

        .profile-card.is-expired {
            border: 1px solid rgba(239, 68, 68, 0.35);
        }

        .section-title {
            padding: 0 1.5rem;
            margin: 1.5rem 0 1rem;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .list-container {
            padding: 0 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .item-card {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 1rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            border-left: 4px solid var(--primary);
        }

        .item-card.payment {
            border-left-color: var(--secondary);
        }

        .item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .item-date {
            font-size: 0.85rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .item-title {
            font-weight: 600;
            font-size: 1rem;
            color: var(--text-main);
        }

        .item-subtitle {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: 0.3rem;
        }

        .status-label {
            font-size: 0.75rem;
            padding: 0.2rem 0.6rem;
            border-radius: 12px;
            font-weight: 600;
        }

        .status-success { background: rgba(16, 185, 129, 0.1); color: var(--secondary); }
        .status-pending { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
        .status-rejected { background: rgba(239, 68, 68, 0.1); color: var(--danger); }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: var(--text-muted);
            background: var(--card-bg);
            border-radius: 12px;
        }

        .empty-state i {
            font-size: 2rem;
            color: #D1D5DB;
            margin-bottom: 0.5rem;
        }
        
        .tabs {
            display: flex;
            padding: 0 1.5rem;
            margin-bottom: 1rem;
            gap: 1rem;
        }
        
        .tab {
            flex: 1;
            text-align: center;
            padding: 0.8rem;
            background: transparent;
            border: none;
            border-bottom: 2px solid transparent;
            font-weight: 600;
            color: var(--text-muted);
            transition: all 0.3s;
        }
        
        .tab.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .bukti-tf-container {
            margin-top: 0.8rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding-top: 0.8rem;
            border-top: 1px dashed #E5E7EB;
        }
        
        .bukti-tf-label {
            font-size: 0.8rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .bukti-tf-link {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            text-decoration: none;
            background: rgba(79, 70, 229, 0.04);
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            border: 1px solid rgba(79, 70, 229, 0.15);
            transition: all 0.2s ease-in-out;
        }

        .bukti-tf-link:hover {
            background: rgba(79, 70, 229, 0.08);
            border-color: var(--primary);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.08);
        }

        .bukti-tf-thumb {
            width: 36px;
            height: 36px;
            border-radius: 6px;
            object-fit: cover;
            border: 1px solid #E5E7EB;
            transition: all 0.2s ease;
        }

        .bukti-tf-text {
            font-size: 0.8rem;
            color: var(--primary);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
    </style>
</head>
<body>

    <div class="header">
        <a href="<?= base_url('info') ?>" class="back-btn">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <?php 
        $sisa = (int)$anak['sisa_pertemuan'];
        $isExpired = !empty($anak['is_expired']);
        $isLow = $sisa <= 2 && !$isExpired;
        $initial = strtoupper(substr($anak['nama_panggilan'] ?: $anak['nama'], 0, 1));
        $berlaku = $anak['berlaku_sampai'] ? date('d M Y', strtotime($anak['berlaku_sampai'])) : '-';
    ?>

    <?php if ($isExpired && !empty($anak['hangus_total'])): ?>
        <div class="alert-expired">
            <strong><i class="fas fa-clock"></i> Ada pertemuan hangus</strong>
            Total <strong><?= (int) $anak['hangus_total'] ?> pertemuan</strong> sudah hangus karena melewati tanggal <em>berlaku sampai</em> paket pembayaran.
            Sisa yang masih bisa dipakai: <strong><?= $sisa >= 0 ? $sisa : ('nunggak ' . abs($sisa)) ?></strong> pertemuan.
        </div>
    <?php endif; ?>

    <div class="profile-card <?= $isExpired ? 'is-expired' : '' ?>">
        <div class="avatar"><?= $initial ?></div>
        <div class="profile-info">
            <div class="profile-name"><?= esc($anak['nama']) ?></div>
            <?php if($anak['nama_panggilan']): ?>
                <div class="profile-nickname">"<?= esc($anak['nama_panggilan']) ?>"</div>
            <?php endif; ?>
            <div class="status-badges">
                <div class="badge badge-sisa <?= ($isExpired || $isLow) ? 'low' : '' ?>">
                    <i class="fas fa-swimmer me-1"></i> 
                    <?php if ($sisa >= 0): ?>
                        Sisa <?= $sisa ?> Pertemuan
                    <?php else: ?>
                        Nunggak <?= abs($sisa) ?> Pertemuan
                    <?php endif; ?>
                </div>
                <?php if ($isExpired): ?>
                    <div class="badge badge-expired">
                        <i class="fas fa-exclamation-triangle me-1"></i> Expired
                    </div>
                <?php endif; ?>
                <div class="badge badge-date <?= $isExpired ? 'expired' : '' ?>">
                    <i class="fas fa-calendar-check me-1"></i>
                    <?= $isExpired ? 'Berlaku s/d' : 'Aktif s/d' ?> <?= $berlaku ?>
                </div>
            </div>
        </div>
    </div>

    <div class="tabs">
        <button class="tab active" onclick="switchTab('paket')">
            <i class="fas fa-box me-1"></i> Paket
        </button>
        <button class="tab" onclick="switchTab('latihan')">
            <i class="fas fa-history me-1"></i> Riwayat Latihan
        </button>
        <button class="tab" onclick="switchTab('pembayaran')">
            <i class="fas fa-receipt me-1"></i> Pembayaran
        </button>
    </div>

    <div id="paket" class="tab-content active">
        <div class="list-container">
            <?php if (empty($history_groups)): ?>
                <div class="empty-state"><p>Belum ada paket pembayaran sukses.</p></div>
            <?php else: ?>
                <?php foreach ($history_groups as $group): ?>
                    <div class="item-card" style="border-left-color: <?= !empty($group['is_expired']) ? 'var(--danger)' : 'var(--primary)' ?>;">
                        <div class="item-header">
                            <div class="item-title"><?= esc($group['label']) ?></div>
                            <?php if (($group['status_label'] ?? '') === 'expired'): ?>
                                <span class="status-label" style="background: rgba(239,68,68,.15); color: var(--danger);">Expired</span>
                            <?php elseif (($group['status_label'] ?? '') === 'nunggak'): ?>
                                <span class="status-label" style="background: rgba(245,158,11,.15); color: var(--warning);">Nunggak</span>
                            <?php else: ?>
                                <span class="status-label status-success"><?= ucfirst($group['status_label'] ?? 'aktif') ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($group['payment'])): ?>
                            <div class="item-date"><i class="fas fa-calendar-check"></i> Bayar: <?= date('d M Y', strtotime($group['payment']['tanggal'])) ?></div>
                            <?php if (!empty($group['berlaku_sampai'])): ?>
                                <div class="item-subtitle">Berlaku s/d <?= date('d M Y', strtotime($group['berlaku_sampai'])) ?></div>
                            <?php endif; ?>
                        <?php endif; ?>
                        <div class="item-subtitle" style="margin-top: 0.5rem;">
                            Terpakai: <?= (int) ($group['terpakai'] ?? 0) ?>/4
                            <?php if (!empty($group['hangus'])): ?>
                                · <span style="color: var(--danger); font-weight: 600;"><?= (int) $group['hangus'] ?> hangus</span>
                            <?php endif; ?>
                            <?php if (($group['sisa_aktif'] ?? 0) > 0): ?>
                                · Sisa aktif: <?= (int) $group['sisa_aktif'] ?>
                            <?php endif; ?>
                        </div>
                        <div style="margin-top: 0.5rem; font-size: 0.8rem;">
                            <?php foreach ($group['sessions'] as $num => $session): ?>
                                <?php
                                    $isHangus = is_array($session) && (($session['slot_status'] ?? '') === 'hangus');
                                    $isHadir = is_array($session) && !empty($session['tanggal']) && !$isHangus;
                                ?>
                                <span style="display:inline-block;margin:2px 4px 2px 0;padding:2px 8px;border-radius:8px;background:<?= $isHangus ? '#FEE2E2' : ($isHadir ? '#D1FAE5' : '#F3F4F6') ?>;color:<?= $isHangus ? '#991B1B' : ($isHadir ? '#065F46' : '#6B7280') ?>;">
                                    P<?= $num ?>: <?= $isHangus ? 'Hangus' : ($isHadir ? date('d/m/y', strtotime($session['tanggal'])) : '—') ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Riwayat Latihan -->
    <div id="latihan" class="tab-content">
        <div class="list-container">
            <?php if(empty($latihan)): ?>
                <div class="empty-state">
                    <i class="fas fa-clipboard-list"></i>
                    <p>Belum ada riwayat latihan</p>
                </div>
            <?php else: ?>
                <?php 
                    $total_latihan = count($latihan);
                    foreach($latihan as $index => $l): 
                        $nomor_latihan = $total_latihan - $index;
                ?>
                    <div class="item-card">
                        <div class="item-header">
                            <div class="item-title">Latihan Renang <?= $nomor_latihan ?></div>
                            <div class="item-date">
                                <i class="fas fa-calendar-day"></i> 
                                <?php
                                    $dayEng = date('D', strtotime($l['tanggal']));
                                    $daysIndo = [
                                        'Sun' => 'Minggu',
                                        'Mon' => 'Senin',
                                        'Tue' => 'Selasa',
                                        'Wed' => 'Rabu',
                                        'Thu' => 'Kamis',
                                        'Fri' => 'Jumat',
                                        'Sat' => 'Sabtu'
                                    ];
                                    echo ($daysIndo[$dayEng] ?? '') . ', ' . date('d M Y', strtotime($l['tanggal']));
                                ?>
                            </div>
                        </div>
                        <div class="item-subtitle">
                            <i class="far fa-clock"></i> <?= date('H:i', strtotime($l['jam_mulai'])) ?> - <?= date('H:i', strtotime($l['jam_selesai'])) ?>
                            <?php if($l['status_kehadiran'] == 'hadir'): ?>
                                <span style="color: var(--secondary); margin-left: 0.5rem; font-weight: 500;">(Hadir)</span>
                            <?php else: ?>
                                <span style="color: var(--danger); margin-left: 0.5rem; font-weight: 500;">(<?= ucfirst($l['status_kehadiran']) ?>)</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Riwayat Pembayaran -->
    <div id="pembayaran" class="tab-content">
        <div class="list-container">
            <?php if(empty($pembayaran)): ?>
                <div class="empty-state">
                    <i class="fas fa-money-bill-wave"></i>
                    <p>Belum ada riwayat pembayaran</p>
                </div>
            <?php else: ?>
                <?php foreach($pembayaran as $p): ?>
                    <div class="item-card payment">
                        <div class="item-header">
                            <div class="item-title">Paket <?= $p['jumlah_pertemuan'] ?> Pertemuan</div>
                            <span class="status-label status-<?= $p['status'] ?>">
                                <?= ucfirst($p['status'] == 'success' ? 'Berhasil' : $p['status']) ?>
                            </span>
                        </div>
                        <div class="item-date" style="margin-bottom: 0.3rem;">
                            <i class="fas fa-calendar-check"></i> <?= date('d M Y', strtotime($p['tanggal'])) ?>
                            <?php if (($p['status'] ?? '') === 'success' && !empty($p['berlaku_sampai'])): ?>
                                <span style="margin-left: 0.5rem; color: var(--text-muted); font-size: 0.8rem;">
                                    (Berlaku s/d <?= date('d M Y', strtotime($p['berlaku_sampai'])) ?>)
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="item-subtitle" style="font-weight: 600; color: var(--text-main);">
                            Rp <?= number_format($p['total'], 0, ',', '.') ?> 
                            <span style="font-weight: 400; color: var(--text-muted); font-size: 0.75rem; margin-left: 0.5rem;">(Via <?= strtoupper($p['metode_pembayaran']) ?>)</span>
                        </div>
                        <?php if (!empty($p['bukti_pembayaran'])) : ?>
                            <div class="bukti-tf-container">
                                <span class="bukti-tf-label">Bukti Transfer:</span>
                                <a href="<?= r2_url($p['bukti_pembayaran'], 'bukti_pembayaran') ?>" target="_blank" class="bukti-tf-link">
                                    <img src="<?= r2_url($p['bukti_pembayaran'], 'bukti_pembayaran') ?>" alt="Bukti Transfer" class="bukti-tf-thumb">
                                    <span class="bukti-tf-text">Lihat Detail <i class="fas fa-external-link-alt"></i></span>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function switchTab(tabId) {
            // Remove active class from all tabs
            document.querySelectorAll('.tab').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            
            // Add active class to clicked tab
            event.currentTarget.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        }
    </script>
</body>
</html>
