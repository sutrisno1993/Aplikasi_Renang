<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= esc($title) ?></title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
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
        }

        .header {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            padding: 2rem 1.5rem 3rem;
            color: white;
            border-bottom-left-radius: 24px;
            border-bottom-right-radius: 24px;
            box-shadow: 0 4px 20px rgba(79, 70, 229, 0.2);
        }

        .header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .header p {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .search-container {
            margin: -24px 1.5rem 1.5rem;
            position: relative;
        }

        .search-box {
            width: 100%;
            padding: 1rem 1.5rem 1rem 3rem;
            border-radius: 50px;
            border: none;
            outline: none;
            font-size: 1rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            background: var(--card-bg);
            transition: all 0.3s ease;
        }

        .search-box:focus {
            box-shadow: 0 4px 20px rgba(79, 70, 229, 0.15);
        }

        .search-icon {
            position: absolute;
            left: 1.2rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }

        .student-list {
            padding: 0 1.5rem 2rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .student-card {
            background: var(--card-bg);
            border-radius: var(--border-radius);
            padding: 1.2rem;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            transition: transform 0.2s ease, opacity 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .student-card:active {
            transform: scale(0.98);
        }

        .avatar {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--primary-light), var(--primary));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            font-weight: 700;
            margin-right: 1.2rem;
            flex-shrink: 0;
            position: relative;
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.2);
        }
        .avatar img.avatar-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 16px;
        }
        .badge-id {
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--text-main);
            color: white;
            font-size: 0.65rem;
            font-weight: 700;
            border-radius: 6px;
            padding: 2px 6px;
            white-space: nowrap;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            border: 1px solid rgba(255,255,255,0.1);
        }

        .student-info {
            flex-grow: 1;
            min-width: 0;
            padding-bottom: 5px;
        }

        .student-name {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 0.2rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: var(--text-main);
        }

        .student-nickname {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }

        .stats-row {
            display: flex;
            gap: 1rem;
            font-size: 0.85rem;
        }

        .stat {
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .badge-sisa {
            background: var(--primary);
            color: white;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-sisa.low {
            background: var(--danger);
        }

        .badge-expired {
            background: var(--danger);
            color: white;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .stat-date.expired {
            color: var(--danger);
            font-weight: 600;
        }

        .expired-note {
            margin-top: 0.5rem;
            font-size: 0.78rem;
            color: var(--danger);
            line-height: 1.35;
            display: flex;
            align-items: flex-start;
            gap: 0.35rem;
        }

        .student-card.is-expired {
            border: 1px solid rgba(239, 68, 68, 0.35);
            background: #FEF2F2;
        }

        .stats-row-wrap {
            width: 100%;
        }

        .stat-icon {
            color: var(--text-muted);
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-muted);
            display: none;
        }

        .empty-state i {
            font-size: 3rem;
            color: #D1D5DB;
            margin-bottom: 1rem;
        }

        .detail-btn {
            color: var(--primary);
            background: rgba(79, 70, 229, 0.1);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.2s;
            margin-left: 0.5rem;
        }

        .detail-btn:hover, .detail-btn:active {
            background: var(--primary);
            color: white;
        }

        /* Styling Kategori Header */
        .category-header {
            background: rgba(79, 70, 229, 0.05);
            padding: 0.6rem 1.2rem;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin: 0.5rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border-left: 4px solid var(--primary);
        }

        .category-header.inactive {
            color: var(--text-muted);
            background: #E5E7EB;
            border-left-color: var(--text-muted);
        }

        .stat-active-date {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 0.3rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        /* Micro animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .student-card {
            animation: fadeIn 0.4s ease forwards;
            opacity: 0; /* Ensures cards are hidden before animation */
        }

        <?php if(!empty($siswa)): ?>
        <?php foreach($siswa as $index => $s): ?>
        .student-card:nth-child(<?= $index + 1 ?>) {
            animation-delay: <?= $index * 0.05 ?>s;
        }
        <?php endforeach; ?>
        <?php endif; ?>
    </style>
</head>
<body>

    <div class="header">
        <h1><i class="fas fa-swimming-pool"></i> Info Siswa</h1>
        <p>Cek sisa pertemuan & masa berlaku paket (90 hari sejak pembayaran terakhir).</p>
    </div>

    <div class="search-container">
        <i class="fas fa-search search-icon"></i>
        <input type="text" class="search-box" id="searchInput" placeholder="Cari nama atau ID siswa..." autocomplete="off">
    </div>

    <div class="student-list" id="studentList">
        <?php if(empty($siswa)): ?>
            <div class="empty-state" style="display: block;">
                <i class="fas fa-folder-open"></i>
                <h3>Belum ada data siswa</h3>
                <p>Data siswa aktif akan muncul di sini.</p>
            </div>
        <?php else: ?>
            <?php 
                $today = new \DateTime(date('Y-m-d'));
                $hasShownInactiveHeader = false;
                $hasShownActiveHeader = false;
            ?>
            <?php foreach($siswa as $index => $s): ?>
                <?php 
                    $lastActiveDate = $s['tanggal_aktif_terakhir'] ?? '1970-01-01';
                    $lastActive = new \DateTime($lastActiveDate);
                    $diff = $today->diff($lastActive)->days;
                    if ($lastActive > $today) $diff = 0;
                    $isInactive = ($diff > 30 || $lastActiveDate === '1970-01-01');

                    // Tampilkan header kategori aktif jika belum muncul
                    if (!$isInactive && !$hasShownActiveHeader) {
                        echo '<div class="category-header"><i class="fas fa-bolt"></i> Aktif (30 Hari Terakhir)</div>';
                        $hasShownActiveHeader = true;
                    }

                    // Tampilkan header kategori tidak aktif jika belum muncul
                    if ($isInactive && !$hasShownInactiveHeader) {
                        echo '<div class="category-header inactive"><i class="fas fa-moon"></i> Tidak Aktif (> 30 Hari)</div>';
                        $hasShownInactiveHeader = true;
                    }

                    $sisa = (int)$s['sisa_pertemuan'];
                    $isLow = $sisa <= 2 && empty($s['is_expired']);
                    $isExpired = !empty($s['is_expired']);
                    $initial = strtoupper(substr($s['nama_panggilan'] ?: $s['nama'], 0, 1));
                    $berlaku = $s['berlaku_sampai'] ? date('d M Y', strtotime($s['berlaku_sampai'])) : '-';
                    $lastActiveFmt = ($lastActiveDate === '1970-01-01') ? 'Belum pernah' : date('d M Y', strtotime($lastActiveDate));
                ?>
                <div class="student-card <?= $isExpired ? 'is-expired' : '' ?>" data-search="<?= strtolower($s['id'] . ' ' . $s['nama'] . ' ' . $s['nama_panggilan'] . ' expired') ?>">
                    <div class="avatar">
                        <?php if (!empty($s['foto'])): ?>
                            <img src="<?= r2_url($s['foto'], 'anak') ?>" alt="Foto <?= esc($s['nama']) ?>" class="avatar-img" />
                        <?php else: ?>
                            <?= $initial ?>
                        <?php endif; ?>
                        <div class="badge-id">#<?= esc($s['id']) ?></div>
                    </div>
                    <div class="student-info">
                        <div class="student-name"><?= esc($s['nama']) ?></div>
                        <?php if($s['nama_panggilan']): ?>
                            <div class="student-nickname">"<?= esc($s['nama_panggilan']) ?>"</div>
                        <?php endif; ?>
                        
                        <div class="stats-row-wrap">
                            <div class="stats-row">
                                <div class="stat">
                                    <span class="badge-sisa <?= $isExpired ? 'low' : ($isLow ? 'low' : '') ?>">
                                        <?php if ($sisa >= 0): ?>
                                            Sisa <?= $sisa ?> Pertemuan
                                        <?php else: ?>
                                            Nunggak <?= abs($sisa) ?> Pertemuan
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <?php if ($isExpired): ?>
                                    <div class="stat">
                                        <span class="badge-expired"><i class="fas fa-clock"></i> Expired</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="stat-active-date">
                                <i class="fas fa-history"></i> Terakhir Aktif: <?= $lastActiveFmt ?>
                            </div>
                            <div class="stat-active-date <?= $isExpired ? 'expired' : '' ?>" style="margin-top: 2px;">
                                <i class="fas fa-calendar-alt"></i> Berlaku s/d <?= $berlaku ?>
                            </div>
                            <?php if ($isExpired && !empty($s['hangus_total'])): ?>
                                <div class="expired-note">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <span><?= (int) $s['hangus_total'] ?> pertemuan hangus (lewat berlaku sampai). Sisa aktif: <?= $sisa >= 0 ? $sisa : ('nunggak ' . abs($sisa)) ?>.</span>
                                </div>
                            <?php elseif ($sisa <= 0 && $berlaku !== '-'): ?>
                                <div class="expired-note" style="color: var(--warning);">
                                    <i class="fas fa-info-circle"></i>
                                    <span>Sisa pertemuan habis. Perpanjang paket melalui pembayaran baru.</span>
                                </div>
                            <?php elseif ($sisa > 0 && $berlaku !== '-'): ?>
                                <div class="expired-note" style="color: var(--secondary);">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Paket masih aktif — gunakan sisa pertemuan sebelum <?= $berlaku ?>.</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <a href="<?= base_url('info/detail/' . $s['id']) ?>" class="detail-btn">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <div class="empty-state" id="noResults">
            <i class="fas fa-search-minus"></i>
            <h3>Tidak ditemukan</h3>
            <p>Coba gunakan kata kunci lain.</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('searchInput');
            const studentCards = document.querySelectorAll('.student-card');
            const noResults = document.getElementById('noResults');

            if(searchInput) {
                searchInput.addEventListener('input', (e) => {
                    const searchTerm = e.target.value.toLowerCase().trim();
                    let hasResults = false;

                    studentCards.forEach(card => {
                        const searchableText = card.getAttribute('data-search');
                        if (searchableText.includes(searchTerm)) {
                            card.style.display = 'flex';
                            hasResults = true;
                        } else {
                            card.style.display = 'none';
                        }
                    });

                    if (!hasResults && studentCards.length > 0) {
                        noResults.style.display = 'block';
                    } else {
                        noResults.style.display = 'none';
                    }
                });
            }
        });
    </script>
</body>
</html>
