<?php 
    $db = \Config\Database::connect();
    $reg_fee = $db->table('settings')->where('key', 'registration_fee')->get()->getRowArray()['value'] ?? '25.000';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>11 Maret Sport Center</title>
    <link rel="icon" type="image/png" href="<?= base_url('favicon.png?v=' . time()) ?>">
    <link rel="shortcut icon" type="image/png" href="<?= base_url('favicon.png?v=' . time()) ?>">
    <meta name="theme-color" content="#4F46E5">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
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

        a {
            color: inherit;
            text-decoration: none;
        }

        .app {
            min-height: 100vh;
            display: flex;
            justify-content: center;
        }

        .frame {
            width: 100%;
            max-width: 480px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
            background-color: var(--bg-color);
            padding-bottom: 5.5rem; /* Space for bottom nav */
        }

        @media (min-width: 900px) {
            .frame {
                min-height: 92vh;
                margin: 28px 0;
                border-radius: 24px;
                overflow-x: hidden;
                overflow-y: auto;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
                border: 1px solid #E5E7EB;
            }
        }

        .header {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            padding: 2rem 1.5rem 4rem;
            color: white;
            border-bottom-left-radius: 24px;
            border-bottom-right-radius: 24px;
            position: relative;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .brand-info {
            display: flex;
            flex-direction: column;
        }

        .brand-title {
            font-size: 1.3rem;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .brand-sub {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.85);
            margin-top: 0.1rem;
            font-weight: 500;
        }

        .content {
            padding: 0 1.5rem;
        }

        .hero-card {
            background: var(--card-bg);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-top: -30px;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border-left: 4px solid var(--primary);
            position: relative;
        }

        .hero-head {
            display: flex;
            gap: 1rem;
            align-items: flex-start;
            margin-bottom: 1.2rem;
        }

        .hero-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(79, 70, 229, 0.08);
            color: var(--primary);
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .hero-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1.2;
            letter-spacing: -0.3px;
        }

        .hero-desc {
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.4;
            margin-top: 0.3rem;
        }

        .chips {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }

        .chip {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            background: rgba(79, 70, 229, 0.06);
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .actions {
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
        }

        .btn {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.9rem 1.2rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.9rem;
            transition: all 0.2s ease-in-out;
            cursor: pointer;
            border: 1px solid rgba(79, 70, 229, 0.15);
            background: var(--card-bg);
            color: var(--text-main);
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            background: rgba(79, 70, 229, 0.02);
            border-color: var(--primary);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            border: none;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
            transform: translateY(-1px);
            color: white;
        }

        .btn-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.8rem;
        }

        .btn-icon-wrapper {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .btn-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: rgba(79, 70, 229, 0.08);
            color: var(--primary);
            font-size: 1rem;
        }

        .btn-primary .btn-icon {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .btn-chev {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .btn-primary .btn-chev {
            color: rgba(255, 255, 255, 0.8);
        }

        .section-title {
            margin: 1.5rem 0 1rem;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-main);
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .section-title h2 {
            font-size: 1.1rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-title .note {
            font-size: 0.8rem;
            color: var(--text-muted);
            font-weight: 400;
        }

        .tile-list {
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
        }

        .tile-card {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 1.2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            border-left: 4px solid var(--primary);
        }

        .tile-card.secondary {
            border-left-color: var(--secondary);
        }

        .tile-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.5rem;
        }

        .tile-name {
            font-weight: 700;
            font-size: 0.8rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .tile-price {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--text-main);
            margin-top: 0.2rem;
        }

        .pill {
            padding: 0.25rem 0.6rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .pill-premium {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }

        .pill-favorit {
            background: rgba(16, 185, 129, 0.1);
            color: var(--secondary);
        }

        .tile-desc {
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.5;
            margin-top: 0.5rem;
        }

        .info-card {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 1.2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        }

        .info-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .info-row {
            display: flex;
            gap: 0.8rem;
            align-items: flex-start;
        }

        .info-number {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.8rem;
            flex-shrink: 0;
            margin-top: 0.1rem;
        }

        .num-primary { background: rgba(79, 70, 229, 0.1); color: var(--primary); }
        .num-secondary { background: rgba(16, 185, 129, 0.1); color: var(--secondary); }
        .num-warning { background: rgba(245, 158, 11, 0.1); color: var(--warning); }

        .info-text {
            font-size: 0.85rem;
            color: var(--text-main);
            line-height: 1.4;
        }

        details {
            background: var(--card-bg);
            border-radius: 12px;
            margin-bottom: 0.8rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            overflow: hidden;
            border: 1px solid #E5E7EB;
        }

        summary {
            padding: 1rem 1.2rem;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-main);
            display: flex;
            justify-content: space-between;
            align-items: center;
            user-select: none;
            outline: none;
        }

        summary::-webkit-details-marker {
            display: none;
        }

        .chev {
            font-size: 0.8rem;
            color: var(--text-muted);
            transition: transform 0.2s ease;
        }

        details[open] summary .chev {
            transform: rotate(180deg);
        }

        .accordion-body {
            padding: 0 1.2rem 1.2rem;
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.5;
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
            border-top: 1px solid #F3F4F6;
            padding-top: 1rem;
        }

        .schedule-box {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.6rem;
            margin-bottom: 0.5rem;
        }

        .schedule-item {
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
            padding: 0.6rem;
            background: rgba(79, 70, 229, 0.05);
            border: 1px solid rgba(79, 70, 229, 0.1);
            border-radius: 8px;
            align-items: center;
        }

        .schedule-day {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--primary);
        }

        .schedule-time {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .contact-list {
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
        }

        .contact-btn {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.9rem 1.2rem;
            border-radius: 12px;
            background: var(--card-bg);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            border: 1px solid #E5E7EB;
            transition: all 0.2s ease;
        }

        .contact-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border-color: var(--primary);
        }

        .contact-icon-wrapper {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .contact-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .icon-wa { background: rgba(16, 185, 129, 0.1); color: var(--secondary); }
        .icon-ig { background: rgba(232, 121, 249, 0.1); color: #E879F9; }

        .contact-text {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-main);
        }

        .footer {
            text-align: center;
            padding: 2.5rem 0;
            font-size: 0.8rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .bottom-nav {
            position: fixed;
            left: 50%;
            transform: translateX(-50%);
            bottom: 0;
            width: 100%;
            max-width: 480px;
            z-index: 30;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-top: 1px solid #E5E7EB;
            padding: 8px 8px calc(env(safe-area-inset-bottom) + 6px);
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.03);
        }

        @media (min-width: 900px) {
            .bottom-nav {
                border-bottom-left-radius: 24px;
                border-bottom-right-radius: 24px;
            }
        }

        .nav-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 4px;
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.2rem;
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--text-muted);
            transition: color 0.2s;
        }

        .nav-item:hover, .nav-item.active {
            color: var(--primary);
        }

        .nav-icon {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            transition: all 0.2s;
            color: var(--text-muted);
        }

        .nav-item:hover .nav-icon, .nav-item.active .nav-icon {
            background: rgba(79, 70, 229, 0.08);
            color: var(--primary);
        }
    </style>
</head>
<body>
    <div class="app">
        <div class="frame">
            <header class="header">
                <div class="logo-container">
                    <div class="logo">
                        <img src="<?= base_url('logo.png?v=' . time()) ?>" alt="Logo" style="width: 100%; height: 100%; object-fit: contain; border-radius: 12px;">
                    </div>
                    <div class="brand-info">
                        <div class="brand-title">11 Maret Sport Center</div>
                        <div class="brand-sub">HR Swimming Family • Les Renang</div>
                    </div>
                </div>
            </header>

            <main id="home" class="content">
                <!-- Hero Info & Akses Akun -->
                <section class="hero-card">
                    <div class="text-center mb-4">
                        <img src="<?= base_url('logo.png?v=' . time()) ?>" alt="Logo 11 Maret" style="max-width: 180px; height: auto;">
                    </div>
                    <div class="hero-head">
                        <div class="hero-icon">
                            <i class="fas fa-medal"></i>
                        </div>
                        <div>
                            <h1 class="hero-title">Kids & Teen Swimming Course</h1>
                            <p class="hero-desc">Tampilan premium dan mobile-first agar nyaman dibuka langsung dari smartphone Anda.</p>
                        </div>
                    </div>

                    <div class="chips">
                        <span class="chip"><i class="fas fa-star text-warning"></i> Pemula</span>
                        <span class="chip"><i class="fas fa-shield-alt text-primary"></i> Standar</span>
                        <span class="chip"><i class="fas fa-trophy text-danger"></i> Mahir</span>
                    </div>

                    <div class="actions">
                        <a class="btn btn-primary" href="<?= base_url('parent/login') ?>">
                            <div class="btn-icon-wrapper">
                                <div class="btn-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <span>Masuk Orang Tua / Wali</span>
                            </div>
                            <span class="btn-chev"><i class="fas fa-chevron-right"></i></span>
                        </a>
                        <div class="btn-group">
                            <a class="btn" href="<?= base_url('coach/login') ?>">
                                <div class="btn-icon-wrapper">
                                    <div class="btn-icon">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                    <span>Coach</span>
                                </div>
                                <span class="btn-chev"><i class="fas fa-chevron-right"></i></span>
                            </a>
                            <a class="btn" href="<?= base_url('admin/login') ?>">
                                <div class="btn-icon-wrapper">
                                    <div class="btn-icon">
                                        <i class="fas fa-user-shield"></i>
                                    </div>
                                    <span>Admin</span>
                                </div>
                                <span class="btn-chev"><i class="fas fa-chevron-right"></i></span>
                            </a>
                        </div>
                    </div>
                </section>

                <!-- Informasi Paket -->
                <section id="paket">
                    <div class="section-title">
                        <h2><i class="fas fa-tags text-primary"></i> Pilihan Paket</h2>
                        <span class="note">Termasuk tiket kolam</span>
                    </div>
                    <div class="tile-list">
                        <div class="tile-card">
                            <div class="tile-header">
                                <div>
                                    <div class="tile-name">Private</div>
                                    <div class="tile-price">Rp 600.000</div>
                                </div>
                                <span class="pill pill-premium">Premium</span>
                            </div>
                            <div class="tile-desc">
                                <i class="far fa-check-circle text-primary"></i> 4x pertemuan les<br>
                                <i class="far fa-check-circle text-primary"></i> Maksimal 2 siswa / 1 coach<br>
                                <i class="far fa-check-circle text-primary"></i> Jadwal fleksibel (sesuai kesepakatan pelatih)
                            </div>
                        </div>
                        <div class="tile-card secondary">
                            <div class="tile-header">
                                <div>
                                    <div class="tile-name">Reguler</div>
                                    <div class="tile-price">Rp 300.000</div>
                                </div>
                                <span class="pill pill-favorit">Favorit</span>
                            </div>
                            <div class="tile-desc">
                                <i class="far fa-check-circle text-success"></i> 4x pertemuan les<br>
                                <i class="far fa-check-circle text-success"></i> Minimal 5 siswa / 1 coach
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Alur Info Singkat -->
                <section id="info">
                    <div class="section-title">
                        <h2><i class="fas fa-info-circle text-primary"></i> Info Singkat</h2>
                        <span class="note">Alur pendaftaran</span>
                    </div>
                    <div class="info-card">
                        <div class="info-list">
                            <div class="info-row">
                                <div class="info-number num-primary">1</div>
                                <div class="info-text">Daftar akun baru → Login → Tambahkan data anak → Pilih jadwal yang tersedia.</div>
                            </div>
                            <div class="info-row">
                                <div class="info-number num-secondary">2</div>
                                <div class="info-text">Pembayaran dilakukan di muka sesuai dengan paket pilihan Anda.</div>
                            </div>
                            <div class="info-row">
                                <div class="info-number num-warning">3</div>
                                <div class="info-text">Paket 4 pertemuan berlaku maksimal 90 hari sejak tanggal konfirmasi pembayaran.</div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Syarat & Ketentuan -->
                <section id="syarat">
                    <div class="section-title">
                        <h2><i class="fas fa-balance-scale text-primary"></i> Syarat & Ketentuan</h2>
                        <span class="note">Ringkasan</span>
                    </div>
                    <div class="tile-card" style="margin-bottom: 0.8rem; border-left-color: var(--warning);">
                        <div class="tile-desc" style="margin-top: 0; font-size: 0.85rem; color: var(--text-main);">
                            <i class="fas fa-exclamation-circle text-warning"></i> Dengan mendaftar, orang tua/wali dan peserta dianggap telah membaca dan menyetujui seluruh ketentuan di bawah ini.
                        </div>
                    </div>
                    
                    <div class="list">
                        <details>
                            <summary>
                                <span>Pendaftaran & Persetujuan</span>
                                <span class="chev"><i class="fas fa-chevron-down"></i></span>
                            </summary>
                            <div class="accordion-body">
                                <div>1) Sebelum memulai sesi les renang, orang tua/wali <strong>diwajibkan</strong> mendaftarkan anaknya secara mandiri melalui aplikasi ini.</div>
                                <div>2) Setiap pendaftaran baru dikenakan biaya pendaftaran sebesar <strong>Rp <?= number_format((int)$reg_fee, 0, ',', '.') ?></strong> untuk keperluan administrasi dan cetak kartu peserta.</div>
                                <div>3) Dengan melakukan pendaftaran (klik daftar/simpan), Anda dianggap telah membaca, memahami, dan menyetujui seluruh Syarat & Ketentuan yang berlaku di HR Swimming Family.</div>
                            </div>
                        </details>

                        <details>
                            <summary>
                                <span>Ketentuan Pendamping</span>
                                <span class="chev"><i class="fas fa-chevron-down"></i></span>
                            </summary>
                            <div class="accordion-body">
                                <div>1) Setiap 1 (satu) orang peserta hanya diperkenankan membawa maksimal <strong>1 (satu) orang pendamping</strong>.</div>
                                <div>2) Apabila jumlah pendamping lebih dari 1 (satu) orang, maka pendamping tambahan wajib membayar tiket masuk kolam renang secara mandiri.</div>
                                <div>3) Pendamping <strong>tidak diperkenankan turun/masuk</strong> ke dalam kolam renang. Apabila melanggar ketentuan ini, akan dikenakan biaya tiket kolam tambahan sebesar <strong>Rp 20.000</strong>.</div>
                            </div>
                        </details>

                        <details>
                            <summary>
                                <span>Masa Berlaku & Sisa Kuota</span>
                                <span class="chev"><i class="fas fa-chevron-down"></i></span>
                            </summary>
                            <div class="accordion-body">
                                <div>1) Paket les (4 pertemuan) memiliki masa aktif <strong>maksimal 90 hari</strong> terhitung sejak tanggal konfirmasi pembayaran. Sisa pertemuan yang tidak digunakan dalam kurun waktu tersebut otomatis hangus.</div>
                                <div>2) Sisa kuota pertemuan yang hangus <strong>tidak dapat diuangkan kembali (non-refundable)</strong> dalam bentuk apa pun.</div>
                                <div>3) Sisa kuota pertemuan bersifat mutlak milik peserta terdaftar dan <strong>tidak dapat dipindahtangankan</strong> kepada orang lain, sekalipun kepada saudara kandung (kakak/adik).</div>
                            </div>
                        </details>

                        <details>
                            <summary>
                                <span>Program & Jadwal Les</span>
                                <span class="chev"><i class="fas fa-chevron-down"></i></span>
                            </summary>
                            <div class="accordion-body">
                                <div class="schedule-box">
                                    <div class="schedule-item">
                                        <span class="schedule-day">Minggu Pagi</span>
                                        <span class="schedule-time"><i class="far fa-clock"></i> 07.30 WIB</span>
                                    </div>
                                    <div class="schedule-item">
                                        <span class="schedule-day">Jumat Sore</span>
                                        <span class="schedule-time"><i class="far fa-clock"></i> 15.40 WIB</span>
                                    </div>
                                </div>
                                <div style="margin-bottom: 0.4rem; font-size: 0.85rem;"><strong style="color: var(--text-main)"><i class="fas fa-circle text-primary" style="font-size: 0.5rem; vertical-align: middle;"></i> Les Privat:</strong> Maks 2 peserta per pelatih • Rp 600.000 / 4x<br><span style="font-size: 11px; color: var(--text-muted); margin-left: 0.8rem;">*Jadwal fleksibel mengikuti ketersediaan pelatih</span></div>
                                <div style="font-size: 0.85rem;"><strong style="color: var(--text-main)"><i class="fas fa-circle text-secondary" style="font-size: 0.5rem; vertical-align: middle;"></i> Les Reguler:</strong> Min 5 peserta per pelatih • Rp 300.000 / 4x</div>
                            </div>
                        </details>
                    </div>
                </section>

                <!-- Informasi Kontak -->
                <section id="kontak">
                    <div class="section-title">
                        <h2><i class="fas fa-comments text-primary"></i> Kontak Kami</h2>
                        <span class="note">Tap untuk chat</span>
                    </div>
                    <div class="contact-list">
                        <a class="contact-btn" href="https://wa.me/628981274514" target="_blank" rel="noreferrer">
                            <div class="contact-icon-wrapper">
                                <div class="contact-icon icon-wa">
                                    <i class="fab fa-whatsapp"></i>
                                </div>
                                <span class="contact-text">Sutrisno • 0898-1274-514</span>
                            </div>
                            <span class="btn-chev"><i class="fas fa-chevron-right"></i></span>
                        </a>
                        <a class="contact-btn" href="https://wa.me/6287885522844" target="_blank" rel="noreferrer">
                            <div class="contact-icon-wrapper">
                                <div class="contact-icon icon-wa">
                                    <i class="fab fa-whatsapp"></i>
                                </div>
                                <span class="contact-text">Ridwan • 0878-8552-2844</span>
                            </div>
                            <span class="btn-chev"><i class="fas fa-chevron-right"></i></span>
                        </a>
                        <a class="contact-btn" href="https://instagram.com/hrsswimmingfamily" target="_blank" rel="noreferrer">
                            <div class="contact-icon-wrapper">
                                <div class="contact-icon icon-ig">
                                    <i class="fab fa-instagram"></i>
                                </div>
                                <span class="contact-text">@hrsswimmingfamily</span>
                            </div>
                            <span class="btn-chev"><i class="fas fa-chevron-right"></i></span>
                        </a>
                    </div>
                </section>

                <div class="footer">© <?= date('Y') ?> 11 Maret Sport Center</div>
            </main>

            <!-- Bottom Nav -->
            <nav class="bottom-nav" aria-label="Navigasi bawah">
                <div class="nav-grid">
                    <a class="nav-item active" href="#home">
                        <span class="nav-icon"><i class="fas fa-home"></i></span>
                        Home
                    </a>
                    <a class="nav-item" href="#paket">
                        <span class="nav-icon"><i class="fas fa-wallet"></i></span>
                        Paket
                    </a>
                    <a class="nav-item" href="<?= base_url('parent/login') ?>">
                        <span class="nav-icon"><i class="fas fa-key"></i></span>
                        Login
                    </a>
                    <a class="nav-item" href="#kontak">
                        <span class="nav-icon"><i class="fas fa-phone-alt"></i></span>
                        Kontak
                    </a>
                </div>
            </nav>
        </div>
    </div>

    <script>
        // Simple scroll to section and set active bottom nav item
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href.startsWith('#')) {
                    e.preventDefault();
                    document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
                    this.classList.add('active');
                    
                    const targetElement = document.querySelector(href);
                    if (targetElement) {
                        targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            });
        });
    </script>
</body>
</html>
