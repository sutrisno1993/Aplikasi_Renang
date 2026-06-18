<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Portal Orang Tua/Wali' ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('favicon.png?v=' . time()) ?>">
    <link rel="shortcut icon" type="image/png" href="<?= base_url('favicon.png?v=' . time()) ?>">
    <meta name="theme-color" content="#0B1220">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            color-scheme: light;
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
            --stroke: rgba(0, 0, 0, 0.06);
            --shadow: rgba(0, 0, 0, 0.04);
        }

        * {
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }
        html, body { height: 100%; }
        body {
            margin: 0;
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
        }

        a { color: inherit; text-decoration: none; }

        .app { min-height: 100dvh; display: flex; justify-content: center; }
        .frame {
            width: 100%;
            max-width: 430px;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            position: relative;
            background-color: var(--bg-color);
        }
        @media (min-width: 900px) {
            .frame {
                min-height: 92dvh;
                margin: 28px 0;
                border-radius: 28px;
                overflow: hidden;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
                border: 1px solid rgba(0, 0, 0, 0.06);
            }
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 20;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
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
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            box-shadow: 0 8px 16px rgba(79, 70, 229, 0.15);
            color: #ffffff;
            flex: 0 0 auto;
            font-size: 16px;
        }
        .brand { min-width: 0; }
        .brand-title { font-size: 13px; font-weight: 700; color: var(--text-main); letter-spacing: -0.2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 0; }
        .brand-sub { font-size: 11px; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 0; }
        .top-actions { display: flex; align-items: center; gap: 10px; }
        .top-link {
            font-size: 12px;
            font-weight: 600;
            color: var(--primary);
            padding: 6px 12px;
            border-radius: 8px;
            background: rgba(79, 70, 229, 0.05);
            transition: all 0.2s ease;
        }
        .top-link:hover {
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary-light);
            text-decoration: none;
        }

        .content {
            padding: 14px 12px calc(env(safe-area-inset-bottom) + 92px);
            background: transparent;
            color: var(--text-main);
            font-size: 13px;
            line-height: 1.45;
        }

        .content .btn {
            font-size: 12px !important;
            padding: 0.5rem 1rem !important;
            line-height: 1.2 !important;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .content .btn-sm {
            font-size: 11px !important;
            padding: 0.35rem 0.75rem !important;
            border-radius: 8px;
        }
        .content .badge {
            font-size: 11px !important;
            padding: 0.35em 0.65em;
            border-radius: 6px;
        }

        .content a { color: var(--primary); transition: color 0.2s; }
        .content a:hover { color: var(--primary-light); text-decoration: none; }

        .content h1 { font-size: 18px; font-weight: 700; }
        .content h2 { font-size: 16px; font-weight: 700; }
        .content h3 { font-size: 15px; font-weight: 600; }
        .content h4 { font-size: 14px; font-weight: 600; }
        .content h5 { font-size: 13px; font-weight: 600; }
        .content h6 { font-size: 12px; font-weight: 600; }

        .content .card,
        .content .modal-content,
        .content .dropdown-menu {
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
            background: #F9FAFB;
            border-bottom: 1px solid var(--stroke);
            color: var(--text-main);
            font-weight: 600;
            padding: 12px 16px;
        }
        .content .modal-footer {
            border-top: 1px solid var(--stroke);
            border-bottom: none;
        }

        .content .text-muted { color: var(--text-muted) !important; }
        .content .bg-light { background-color: #F9FAFB !important; }

        .content .nav-tabs {
            border-bottom: 2px solid rgba(0, 0, 0, 0.06);
            margin-bottom: 16px;
            gap: 8px;
        }
        .content .nav-tabs .nav-link {
            border: none;
            color: var(--text-muted);
            font-weight: 500;
            border-radius: 8px 8px 0 0;
            padding: 8px 16px;
            position: relative;
            background: transparent;
            transition: all 0.2s ease;
        }
        .content .nav-tabs .nav-link:hover {
            color: var(--primary);
            background: rgba(79, 70, 229, 0.05);
            border: none;
        }
        .content .nav-tabs .nav-link.active {
            color: var(--primary);
            background: transparent;
            font-weight: 600;
            border: none;
        }
        .content .nav-tabs .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--primary);
            border-radius: 3px 3px 0 0;
        }

        .content .table { color: var(--text-main); margin-bottom: 0; }
        .content .table thead th {
            background: #F9FAFB;
            border-bottom: 2px solid rgba(0, 0, 0, 0.06);
            color: var(--text-main);
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-top: none;
        }
        .content .table td,
        .content .table th { border-top: 1px solid rgba(0, 0, 0, 0.06); vertical-align: middle; font-size: 12px; }
        .content .table-striped tbody tr:nth-of-type(odd) { background-color: #F9FAFB; }

        .content .list-group-item {
            background: var(--card-bg);
            border-color: var(--stroke);
            color: var(--text-main);
        }

        .content .form-control,
        .content .custom-select {
            background: #FFFFFF;
            border: 1px solid rgba(0, 0, 0, 0.12);
            color: var(--text-main);
            border-radius: 10px;
            font-size: 13px;
            height: auto;
            padding: 0.5rem 0.75rem;
            transition: all 0.2s ease;
        }
        .content select.form-control option,
        .content select.custom-select option {
            background: #FFFFFF;
            color: var(--text-main);
        }
        .content .form-control:focus,
        .content .custom-select:focus {
            background: #FFFFFF;
            border-color: var(--primary-light);
            color: var(--text-main);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
            outline: none;
        }
        .content .input-group-text {
            background: #F9FAFB;
            border: 1px solid rgba(0, 0, 0, 0.12);
            color: var(--text-muted);
            font-size: 13px;
        }

        .content label {
            font-weight: 500;
            color: var(--text-main);
            font-size: 12px;
            margin-bottom: 6px;
        }

        .content .alert {
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, 0.06);
            font-size: 13px;
            padding: 12px 16px;
        }
        .content .alert-info {
            background: rgba(79, 70, 229, 0.05);
            border-color: rgba(79, 70, 229, 0.15);
            color: var(--primary);
        }
        .content .alert-success {
            background: rgba(16, 185, 129, 0.05);
            border-color: rgba(16, 185, 129, 0.15);
            color: var(--secondary);
        }
        .content .alert-danger {
            background: rgba(239, 68, 68, 0.05);
            border-color: rgba(239, 68, 68, 0.15);
            color: var(--danger);
        }
        .content .alert-warning {
            background: rgba(245, 158, 11, 0.05);
            border-color: rgba(245, 158, 11, 0.15);
            color: var(--warning);
        }

        .bottom-nav {
            position: fixed;
            left: 50%;
            transform: translateX(-50%);
            bottom: 0;
            width: 100%;
            max-width: 430px;
            z-index: 30;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(0, 0, 0, 0.06);
            padding: 8px 8px calc(env(safe-area-inset-bottom) + 6px);
        }
        .nav-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 4px; }
        .nav-item {
            border-radius: 12px;
            padding: 6px 4px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 500;
            color: var(--text-muted);
            transition: all 0.2s ease;
        }
        .nav-item:hover, .nav-item.active {
            color: var(--primary);
            text-decoration: none;
        }
        .nav-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            border: 1px solid rgba(0, 0, 0, 0.04);
            background: #F9FAFB;
            display: grid;
            place-items: center;
            font-size: 15px;
            transition: all 0.2s ease;
        }
        .nav-item:hover .nav-icon, .nav-item.active .nav-icon {
            background: rgba(79, 70, 229, 0.08);
            border-color: rgba(79, 70, 229, 0.2);
            transform: translateY(-2px);
        }

        /* Bukti Transfer Styling (matches /info/detail/:id) */
        .bukti-tf-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none !important;
            background: rgba(79, 70, 229, 0.04);
            padding: 0.35rem 0.65rem;
            border-radius: 8px;
            border: 1px solid rgba(79, 70, 229, 0.15);
            transition: all 0.2s ease-in-out;
            color: var(--primary) !important;
        }
        .bukti-tf-link:hover {
            background: rgba(79, 70, 229, 0.08);
            border-color: var(--primary);
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.08);
            color: var(--primary-light) !important;
        }
        .bukti-tf-thumb {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            object-fit: cover;
            border: 1px solid rgba(0, 0, 0, 0.08);
        }
        .bukti-tf-text {
            font-size: 11px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.2rem;
        }
    </style>
</head>
<body>
    <div class="app">
        <div class="frame">
            <header class="topbar">
                <div class="topbar-inner">
                    <div class="top-left">
                        <div class="logo" aria-hidden="true">
                            <img src="<?= app_logo('sportcenter_logo', 'logo.png') ?>" alt="Logo" style="width: 100%; height: 100%; object-fit: contain; border-radius: 8px;">
                        </div>
                        <div class="brand">
                            <p class="brand-title">Portal Orang Tua/Wali</p>
                            <p class="brand-sub">
                                <?php if (session()->get('parent_isLoggedIn')): ?>
                                    <?= esc((string) session()->get('parent_nama')) ?>
                                <?php else: ?>
                                    Aplikasi Renang
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="top-actions">
                        <a class="top-link" href="<?= base_url('parent/dashboard') ?>">Dashboard</a>
                        <?php if (session()->get('parent_isLoggedIn')): ?>
                            <a class="top-link" href="<?= base_url('parent/logout') ?>">Logout</a>
                        <?php endif; ?>
                    </div>
                </div>
            </header>

            <main class="content">
                <?= $this->renderSection('content') ?>
            </main>

            <nav class="bottom-nav" aria-label="Navigasi bawah">
                <div class="nav-grid">
                    <a class="nav-item" href="<?= base_url('parent/dashboard') ?>">
                        <span class="nav-icon" aria-hidden="true">🏠</span>
                        Home
                    </a>
                    <a class="nav-item" href="<?= base_url('parent/dashboard') ?>#anak">
                        <span class="nav-icon" aria-hidden="true">👧</span>
                        Anak
                    </a>
                    <a class="nav-item" href="<?= base_url('parent/curriculum') ?>">
                        <span class="nav-icon" aria-hidden="true">🏊</span>
                        Kurikulum
                    </a>
                    <a class="nav-item" href="<?= base_url('parent/dashboard') ?>#pembayaran">
                        <span class="nav-icon" aria-hidden="true">💳</span>
                        Bayar
                    </a>
                    <a class="nav-item" href="<?= base_url('parent/dashboard') ?>#jadwal">
                        <span class="nav-icon" aria-hidden="true">📅</span>
                        Jadwal
                    </a>
                </div>
            </nav>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <script>
        (function () {
            if (typeof window.jQuery === 'undefined') return;
            var $ = window.jQuery;

            function updateBottomNavActive() {
                var hash = window.location.hash || '';
                
                $('.bottom-nav .nav-item').removeClass('active');
                
                if (hash) {
                    var $activeItem = $('.bottom-nav a[href$="' + hash + '"]');
                    if ($activeItem.length) {
                        $activeItem.addClass('active');
                    }
                } else {
                    // Default to first item (Home / Dashboard)
                    $('.bottom-nav .nav-item').eq(0).addClass('active');
                }
            }

            function activateTabFromHash() {
                var hash = window.location.hash;
                if (!hash) return;
                var $link = $('a[data-toggle="tab"][href="' + hash + '"]');
                if ($link.length) $link.tab('show');
            }

            $(function () {
                activateTabFromHash();
                updateBottomNavActive();
                $(window).on('hashchange', function() {
                    activateTabFromHash();
                    updateBottomNavActive();
                });
                $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                    var href = e.target && e.target.getAttribute ? e.target.getAttribute('href') : '';
                    if (!href || href.charAt(0) !== '#') return;
                    if (history && history.replaceState) history.replaceState(null, '', href);
                    updateBottomNavActive();
                });
            });
        })();
    </script>
</body>
</html>
