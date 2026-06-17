<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Portal Pelatih' ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('favicon.png') ?>">
    <meta name="theme-color" content="#064E3B">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary:        #059669;
            --primary-dark:   #064E3B;
            --primary-light:  #34D399;
            --accent:         #0EA5E9;
            --bg-color:       #F0FDF4;
            --card-bg:        #FFFFFF;
            --text-main:      #1F2937;
            --text-muted:     #6B7280;
            --danger:         #EF4444;
            --warning:        #F59E0B;
            --border-radius:  16px;
            --stroke:         rgba(0,0,0,0.06);
            --shadow:         rgba(0,0,0,0.04);
        }

        * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        html, body { height: 100%; margin: 0; }
        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
        }
        a { color: inherit; text-decoration: none; }

        /* ── App Shell ── */
        .app { min-height: 100dvh; display: flex; justify-content: center; background: #D1FAE5; }
        .frame {
            width: 100%;
            max-width: 430px;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            background-color: var(--bg-color);
        }
        @media (min-width: 900px) {
            .frame {
                min-height: 92dvh;
                margin: 28px 0;
                border-radius: 28px;
                overflow: hidden;
                box-shadow: 0 20px 60px rgba(5,150,105,0.12);
                border: 1px solid rgba(5,150,105,0.1);
            }
        }

        /* ── Topbar ── */
        .topbar {
            position: sticky;
            top: 0;
            z-index: 20;
            background: rgba(255,255,255,0.88);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(5,150,105,0.1);
        }
        .topbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 16px;
        }
        .top-left { display: flex; align-items: center; gap: 12px; min-width: 0; }
        .logo {
            width: 40px; height: 40px;
            border-radius: 12px;
            display: grid; place-items: center;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            box-shadow: 0 8px 16px rgba(5,150,105,0.2);
            color: #fff;
            flex: 0 0 auto;
            font-size: 18px;
            overflow: hidden;
        }
        .brand { min-width: 0; }
        .brand-title { font-size: 13px; font-weight: 700; color: var(--text-main); letter-spacing: -0.2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 0; }
        .brand-sub   { font-size: 11px; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 0; }
        .top-actions { display: flex; align-items: center; gap: 8px; }
        .top-link {
            font-size: 12px; font-weight: 600;
            color: var(--primary);
            padding: 6px 12px;
            border-radius: 8px;
            background: rgba(5,150,105,0.06);
            transition: all 0.2s;
            white-space: nowrap;
        }
        .top-link:hover { background: rgba(5,150,105,0.12); text-decoration: none; }
        .top-link.danger { color: var(--danger); background: rgba(239,68,68,0.06); }
        .top-link.danger:hover { background: rgba(239,68,68,0.12); }

        /* ── Role Badge ── */
        .role-badge {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 10px; font-weight: 700;
            padding: 3px 8px; border-radius: 20px;
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .role-badge.head { background: rgba(14,165,233,0.1); color: #0369A1; }
        .role-badge.coach { background: rgba(5,150,105,0.1); color: var(--primary); }

        /* ── Content ── */
        .content {
            flex: 1;
            padding: 14px 12px calc(env(safe-area-inset-bottom) + 92px);
            font-size: 13px;
            line-height: 1.45;
        }

        /* Bootstrap overrides inside content */
        .content .btn { font-size: 12px !important; padding: 0.5rem 1rem !important; border-radius: 10px; font-weight: 500; transition: all 0.2s; }
        .content .btn-sm { font-size: 11px !important; padding: 0.35rem 0.75rem !important; border-radius: 8px; }
        .content .btn-success { background: var(--primary); border-color: var(--primary); }
        .content .btn-success:hover { background: var(--primary-dark); border-color: var(--primary-dark); }
        .content .btn-outline-success { color: var(--primary); border-color: var(--primary); }
        .content .btn-outline-success:hover { background: var(--primary); border-color: var(--primary); }
        .content .badge { font-size: 11px !important; padding: 0.35em 0.65em; border-radius: 6px; }

        .content h1 { font-size: 18px; font-weight: 700; }
        .content h2 { font-size: 16px; font-weight: 700; }
        .content h3 { font-size: 15px; font-weight: 600; }
        .content h4 { font-size: 14px; font-weight: 600; }
        .content h5 { font-size: 13px; font-weight: 600; }
        .content h6 { font-size: 12px; font-weight: 600; }

        .content .card,
        .content .modal-content {
            background: var(--card-bg);
            border: 1px solid var(--stroke);
            border-radius: var(--border-radius);
            box-shadow: 0 4px 12px var(--shadow);
            color: var(--text-main);
            overflow: hidden;
            margin-bottom: 16px;
        }
        .content .card-header,
        .content .modal-header,
        .content .modal-footer {
            background: #F0FDF4;
            border-bottom: 1px solid rgba(5,150,105,0.1);
            color: var(--text-main);
            font-weight: 600;
            padding: 12px 16px;
        }
        .content .modal-footer { border-top: 1px solid var(--stroke); border-bottom: none; }
        .content .card-body { padding: 14px 16px; }

        .content .text-muted { color: var(--text-muted) !important; }
        .content .bg-light { background-color: #F0FDF4 !important; }

        .content .form-control,
        .content .custom-select {
            background: #fff;
            border: 1px solid rgba(0,0,0,0.12);
            color: var(--text-main);
            border-radius: 10px;
            font-size: 13px;
            padding: 0.5rem 0.75rem;
            transition: all 0.2s;
        }
        .content .form-control:focus,
        .content .custom-select:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(5,150,105,0.15);
            outline: none;
        }
        .content label { font-weight: 500; font-size: 12px; margin-bottom: 6px; color: var(--text-main); }

        .content .alert { border-radius: 12px; font-size: 13px; padding: 12px 16px; border: 1px solid transparent; }
        .content .alert-success { background: rgba(5,150,105,0.06); border-color: rgba(5,150,105,0.2); color: var(--primary-dark); }
        .content .alert-danger  { background: rgba(239,68,68,0.06); border-color: rgba(239,68,68,0.2); color: #B91C1C; }
        .content .alert-warning { background: rgba(245,158,11,0.06); border-color: rgba(245,158,11,0.2); color: #92400E; }
        .content .alert-info    { background: rgba(14,165,233,0.06); border-color: rgba(14,165,233,0.2); color: #0369A1; }
        .content .alert-light   { background: #F9FAFB; border-color: var(--stroke); color: var(--text-main); }

        .content .table { color: var(--text-main); margin-bottom: 0; }
        .content .table thead th {
            background: #F0FDF4;
            border-bottom: 2px solid rgba(5,150,105,0.1);
            color: var(--text-muted);
            font-weight: 600; font-size: 11px;
            text-transform: uppercase; letter-spacing: 0.5px;
            border-top: none;
        }
        .content .table td, .content .table th { border-top: 1px solid rgba(0,0,0,0.05); vertical-align: middle; font-size: 12px; }

        .content .list-group-item { background: var(--card-bg); border-color: var(--stroke); color: var(--text-main); }
        .content .list-group-item-action:hover { background: #F0FDF4; }

        /* ── Bottom Nav ── */
        .bottom-nav {
            position: fixed;
            left: 50%; transform: translateX(-50%);
            bottom: 0;
            width: 100%; max-width: 430px;
            z-index: 30;
            background: rgba(255,255,255,0.88);
            backdrop-filter: blur(12px);
            border-top: 1px solid rgba(5,150,105,0.1);
            padding: 8px 8px calc(env(safe-area-inset-bottom) + 6px);
        }
        .nav-grid { display: grid; gap: 4px; }
        .nav-grid-4 { grid-template-columns: repeat(4, 1fr); }
        .nav-grid-5 { grid-template-columns: repeat(5, 1fr); }
        .nav-item {
            border-radius: 12px; padding: 6px 4px;
            display: flex; flex-direction: column; align-items: center; gap: 4px;
            font-size: 10px; font-weight: 500; color: var(--text-muted);
            transition: all 0.2s;
        }
        .nav-item:hover, .nav-item.active { color: var(--primary); text-decoration: none; }
        .nav-icon {
            width: 36px; height: 36px; border-radius: 10px;
            border: 1px solid rgba(0,0,0,0.04);
            background: #F9FAFB;
            display: grid; place-items: center;
            font-size: 15px; transition: all 0.2s;
        }
        .nav-item:hover .nav-icon, .nav-item.active .nav-icon {
            background: rgba(5,150,105,0.08);
            border-color: rgba(5,150,105,0.2);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
<div class="app">
    <div class="frame">

        <!-- Topbar -->
        <header class="topbar">
            <div class="topbar-inner">
                <div class="top-left">
                    <div class="logo">
                        <img src="<?= base_url('logo.png') ?>" alt="Logo" style="width:100%;height:100%;object-fit:contain;border-radius:8px;" onerror="this.style.display='none';this.parentElement.innerHTML='🏊'">
                    </div>
                    <div class="brand">
                        <p class="brand-title">Portal Pelatih</p>
                        <p class="brand-sub">
                            <?php if (session()->get('coach_isLoggedIn')): ?>
                                <?= esc((string) session()->get('coach_nama')) ?>
                                &nbsp;
                                <?php if (session()->get('coach_role') === 'head_coach'): ?>
                                    <span class="role-badge head"><i class="fas fa-star" style="font-size:8px"></i> Head Coach</span>
                                <?php else: ?>
                                    <span class="role-badge coach">Pelatih</span>
                                <?php endif; ?>
                            <?php else: ?>
                                Aplikasi Renang
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                <div class="top-actions">
                    <?php if (session()->get('coach_isLoggedIn')): ?>
                        <a class="top-link danger" href="<?= base_url('coach/logout') ?>">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="content">
            <?= $this->renderSection('content') ?>
        </main>

        <!-- Bottom Nav -->
        <?php if (session()->get('coach_isLoggedIn')): ?>
        <nav class="bottom-nav" aria-label="Navigasi bawah">
            <?php $isHeadCoach = session()->get('coach_role') === 'head_coach'; ?>
            <div class="nav-grid <?= $isHeadCoach ? 'nav-grid-5' : 'nav-grid-4' ?>">
                <a class="nav-item" href="<?= base_url('coach/dashboard') ?>">
                    <span class="nav-icon">🏠</span>
                    Home
                </a>
                <a class="nav-item" href="<?= base_url('coach/evaluasi') ?>">
                    <span class="nav-icon">📋</span>
                    Evaluasi
                </a>
                <a class="nav-item" href="<?= base_url('coach/ujian') ?>">
                    <span class="nav-icon">🎓</span>
                    Ujian
                </a>
                <?php if ($isHeadCoach): ?>
                <a class="nav-item" href="<?= base_url('coach/pelatih') ?>">
                    <span class="nav-icon">👥</span>
                    Pelatih
                </a>
                <?php endif; ?>
                <a class="nav-item" href="<?= base_url('coach/logout') ?>">
                    <span class="nav-icon">🚪</span>
                    Keluar
                </a>
            </div>
        </nav>
        <?php endif; ?>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
<script>
(function () {
    var path = window.location.pathname;
    document.querySelectorAll('.bottom-nav .nav-item').forEach(function(el) {
        var href = el.getAttribute('href') || '';
        if (href && path.indexOf(href.replace(window.location.origin, '')) !== -1 && href !== '<?= base_url('/') ?>') {
            el.classList.add('active');
        }
    });
})();
</script>
</body>
</html>
