<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin Dashboard' ?> - Renang</title>
    
    <link rel="icon" type="image/png" href="<?= base_url('favicon.png?v=' . time()) ?>">
    <link rel="shortcut icon" type="image/png" href="<?= base_url('favicon.png?v=' . time()) ?>">
    
    <!-- jQuery HARUS dimuat pertama -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- DataTables CSS dan JS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Bootstrap JS dan plugin lainnya dimuat SETELAH jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.id.min.js"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Sisanya dari header.php tetap sama -->
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
            --stroke: rgba(0, 0, 0, 0.06);
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            --sidebar-width: 280px;
        }
        
        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: #0B1220;
            color: white;
            padding: 24px 16px;
            overflow-y: auto;
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }

        .sidebar.collapsed {
            left: calc(-1 * var(--sidebar-width));
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.6);
            padding: 12px 16px;
            margin: 4px 0;
            border-radius: 12px;
            transition: all 0.2s;
            font-weight: 500;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
        }
        
        .sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.05);
            color: white;
        }

        /* Hanya halaman yang sedang dibuka yang dapat highlight ungu */
        .sidebar .nav-link.active {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            box-shadow: 0 8px 16px rgba(79, 70, 229, 0.2);
        }

        /* Fokus keyboard jangan meniru tampilan menu aktif */
        .sidebar .nav-link:focus,
        .sidebar .nav-link:focus-visible {
            outline: none;
            box-shadow: none;
        }

        .sidebar .nav-link:not(.active):focus,
        .sidebar .nav-link:not(.active):focus-visible {
            background-color: rgba(255, 255, 255, 0.08);
            color: white;
        }

        .sidebar .nav-link.active:focus,
        .sidebar .nav-link.active:focus-visible {
            outline: 2px solid rgba(255, 255, 255, 0.35);
            outline-offset: 2px;
        }
        
        .sidebar .nav-link i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }

        .sidebar > .nav.flex-column {
            min-height: calc(100vh - 140px);
            display: flex;
            flex-direction: column;
        }
        
        .sidebar-header {
            padding: 0 16px 24px;
            margin-bottom: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .sidebar-header h4 {
            font-weight: 700;
            font-size: 1.25rem;
            letter-spacing: -0.5px;
        }
        
        .content {
            margin-left: var(--sidebar-width);
            padding: 24px;
            min-height: 100vh;
            background: var(--bg-color);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .content.expanded {
            margin-left: 0;
        }
        
        .navbar {
            background-color: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            box-shadow: 0 1px 0 var(--stroke);
            padding: 12px 24px;
            border-radius: 0;
            position: sticky;
            top: 0;
            z-index: 900;
        }

        .sidebar-toggle {
            background: var(--bg-color);
            border: 1px solid var(--stroke);
            width: 40px;
            height: 40px;
            display: grid;
            place-items: center;
            border-radius: 12px;
            color: var(--text-main);
            cursor: pointer;
            transition: all 0.2s;
            margin-right: 16px;
        }

        .sidebar-toggle:hover {
            background-color: white;
            border-color: var(--primary);
            color: var(--primary);
        }
        
        .card {
            border: 1px solid var(--stroke);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 24px;
            transition: transform 0.2s, box-shadow 0.2s;
            background: white;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }
        
        .card-stats {
            padding: 24px;
        }
        
        .card-stats .icon {
            font-size: 32px;
            background: rgba(79, 70, 229, 0.08);
            color: var(--primary);
            width: 56px;
            height: 56px;
            display: grid;
            place-items: center;
            border-radius: 14px;
            margin-bottom: 16px;
        }
        
        .card-stats .number {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-main);
            letter-spacing: -0.5px;
        }
        
        .card-stats .card-title {
            font-size: 0.85rem;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            margin-bottom: 4px;
        }
        
        .bg-gradient-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-light)) !important;
            color: white;
        }
        
        .bg-gradient-success {
            background: linear-gradient(135deg, var(--secondary), #34D399) !important;
            color: white;
        }
        
        .bg-gradient-info {
            background: linear-gradient(135deg, #3B82F6, #60A5FA) !important;
            color: white;
        }
        
        .bg-gradient-warning {
            background: linear-gradient(135deg, var(--warning), #FBBF24) !important;
            color: white;
        }
        
        .table-container {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 24px;
            border: 1px solid var(--stroke);
            box-shadow: var(--shadow);
        }

        .btn {
            border-radius: 12px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border: none;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.3);
        }
        
        /* Tambahkan CSS untuk sub-menu */
        .nav-treeview {
            padding-left: 12px;
            display: none;
            margin: 4px 0;
            list-style: none;
        }
        
        .menu-open > .nav-link i.right {
            transform: rotate(90deg);
        }

        .menu-open > .nav-treeview {
            display: block;
        }
        
        .nav-treeview .nav-link {
            padding-left: 16px;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.5);
        }

        .nav-treeview .nav-link:hover, .nav-treeview .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.05);
        }

        .nav-treeview .nav-link i {
            font-size: 0.8rem;
            opacity: 0.5;
        }
        
        .right {
            margin-left: auto;
            transition: transform 0.2s;
            font-size: 0.8rem;
            opacity: 0.5;
        }

        .dropdown-menu {
            border: 1px solid var(--stroke);
            border-radius: 14px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            padding: 8px;
        }

        .dropdown-item {
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 500;
            color: var(--text-main);
        }

        .dropdown-item:hover {
            background-color: var(--bg-color);
            color: var(--primary);
        }

        .dropdown-item i {
            width: 20px;
            margin-right: 8px;
        }
    </style>
    <?php if (session()->get('role') === 'boss'): ?>
    <style>
        /* Sembunyikan semua tombol aksi yang memanipulasi data untuk role Boss */
        /* Kecuali tombol yang memiliki class .boss-action-btn */
        .btn-primary:not([type="submit"]):not(.boss-action-btn), 
        .btn-success:not([type="submit"]):not(.boss-action-btn), 
        .btn-danger:not(.boss-action-btn), 
        .btn-warning:not(.boss-action-btn),
        .fa-edit, .fa-trash, .fa-plus-circle, .fa-user-plus {
            display: none !important;
        }
        /* Pastikan tombol aksi boss tetap tampil */
        .boss-action-btn {
            display: inline-block !important;
        }
        .btn-outline-primary, .btn-info {
            display: inline-block !important;
        }
    </style>
    <?php endif; ?>
</head>
<body>
    <?php 
    // Set a default value for $active if it's not set
    $active = $active ?? '';
    ?>
    
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="d-flex align-items-center mb-2">
                <img src="<?= base_url('logo.png?v=' . time()) ?>" alt="Logo" style="width: 32px; height: 32px; object-fit: contain; margin-right: 10px; border-radius: 6px;">
                <h4 class="mb-0">Renang Admin</h4>
            </div>
            <p class="mb-0 small text-light opacity-75">Selamat datang, <?= $nama ?? 'Admin' ?></p>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= uri_string() == 'admin/dashboard' ? 'active' : '' ?>" href="<?= base_url('admin/dashboard') ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= uri_string() == 'admin/jenis-les' ? 'active' : '' ?>" href="<?= base_url('admin/jenis-les') ?>">
                    <i class="fas fa-swimming-pool"></i> Jenis Les
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= uri_string() == 'admin/anak' ? 'active' : '' ?>" href="<?= base_url('admin/anak') ?>">
                    <i class="fas fa-users"></i> Anak
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= uri_string() == 'admin/parents' ? 'active' : '' ?>" href="<?= base_url('admin/parents') ?>">
                    <i class="fas fa-user-friends"></i> Orang Tua
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= uri_string() == 'admin/cetak-kartu' ? 'active' : '' ?>" href="<?= base_url('admin/cetak-kartu') ?>">
                    <i class="fas fa-id-card"></i> Cetak Kartu
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($active == 'coach') ? 'active' : '' ?>" href="<?= base_url('admin/coach') ?>">
                    <i class="fas fa-user-tie"></i> Pelatih
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($active == 'curriculum') ? 'active' : '' ?>" href="<?= base_url('admin/curriculum') ?>">
                    <i class="fas fa-graduation-cap"></i> Kurikulum & Ujian
                </a>
            </li>
            
            <!-- Menu Jadwal -->
            <li class="nav-item <?= ($active == 'jadwal' || $active == 'riwayat-jadwal') ? 'menu-open' : '' ?>">
                <a class="nav-link <?= ($active == 'jadwal' || $active == 'riwayat-jadwal') ? 'active' : '' ?>" href="#">
                    <i class="fas fa-calendar-alt"></i> Jadwal
                    <i class="right fas fa-angle-left"></i>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a class="nav-link <?= ($active == 'jadwal') ? 'active' : '' ?>" href="<?= base_url('admin/jadwal') ?>">
                            <i class="far fa-circle"></i> Kelola Jadwal
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($active == 'riwayat-jadwal') ? 'active' : '' ?>" href="<?= base_url('admin/jadwal/riwayat') ?>">
                            <i class="far fa-circle"></i> Riwayat Jadwal
                        </a>
                    </li>
                </ul>
            </li>
            
            <!-- Menu Pembayaran dengan Sub-Menu -->
            <li class="nav-item <?= ($active == 'pembayaran' || $active == 'riwayat-pembayaran' || $active == 'pembayaran-manual') ? 'menu-open' : '' ?>">
                <a class="nav-link <?= ($active == 'pembayaran' || $active == 'riwayat-pembayaran' || $active == 'pembayaran-manual') ? 'active' : '' ?>" href="#">
                    <i class="fas fa-money-bill-wave"></i> Pembayaran
                    <i class="right fas fa-angle-left"></i>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a class="nav-link <?= ($active == 'pembayaran') ? 'active' : '' ?>" href="<?= base_url('admin/pembayaran') ?>">
                            <i class="far fa-circle"></i> Approval Pembayaran
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($active == 'pembayaran-manual') ? 'active' : '' ?>" href="<?= base_url('admin/pembayaran/manual') ?>">
                            <i class="far fa-circle"></i> Pembayaran Manual
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($active == 'riwayat-pembayaran') ? 'active' : '' ?>" href="<?= base_url('admin/pembayaran/riwayat') ?>">
                            <i class="far fa-circle"></i> Riwayat Pembayaran
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Menu Laporan -->
            <li class="nav-item <?= ($active == 'report-keuangan' || $active == 'report-kehadiran' || $active == 'report-siswa' || $active == 'report-pembayaran' || $active == 'report-paket-expired') ? 'menu-open' : '' ?>">
                <a class="nav-link <?= ($active == 'report-keuangan' || $active == 'report-kehadiran' || $active == 'report-siswa' || $active == 'report-pembayaran' || $active == 'report-paket-expired') ? 'active' : '' ?>" href="#">
                    <i class="fas fa-chart-line"></i> Laporan
                    <i class="right fas fa-angle-left"></i>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a class="nav-link <?= ($active == 'report-pembayaran') ? 'active' : '' ?>" href="<?= base_url('admin/report/pembayaran') ?>">
                            <i class="far fa-circle"></i> Pembayaran (Bulanan)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($active == 'report-keuangan') ? 'active' : '' ?>" href="<?= base_url('admin/report/keuangan') ?>">
                            <i class="far fa-circle"></i> Keuangan & Bagi Hasil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($active == 'report-kehadiran') ? 'active' : '' ?>" href="<?= base_url('admin/report/kehadiran') ?>">
                            <i class="far fa-circle"></i> Kehadiran Siswa
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($active == 'report-siswa') ? 'active' : '' ?>" href="<?= base_url('admin/report/siswa') ?>">
                            <i class="far fa-circle"></i> Analisis Data Siswa
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($active == 'report-paket-expired') ? 'active' : '' ?>" href="<?= base_url('admin/report/paket-expired') ?>">
                            <i class="far fa-circle"></i> Monitoring Paket Expired
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Menu Kedatangan -->
            <li class="nav-item <?= ($active == 'kedatangan' || $active == 'riwayat-kedatangan' || $active == 'edit-kedatangan') ? 'menu-open' : '' ?>">
                <a class="nav-link <?= ($active == 'kedatangan' || $active == 'riwayat-kedatangan' || $active == 'edit-kedatangan') ? 'active' : '' ?>" href="#">
                    <i class="fas fa-clipboard-check"></i> Kedatangan
                    <i class="right fas fa-angle-left"></i>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a class="nav-link <?= ($active == 'kedatangan') ? 'active' : '' ?>" href="<?= base_url('admin/kedatangan') ?>">
                            <i class="far fa-circle"></i> Kelola Kedatangan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($active == 'edit-kedatangan') ? 'active' : '' ?>" href="<?= base_url('admin/kedatangan/edit') ?>">
                            <i class="far fa-circle"></i> Edit Kedatangan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($active == 'riwayat-kedatangan') ? 'active' : '' ?>" href="<?= base_url('admin/kedatangan/riwayat') ?>">
                            <i class="far fa-circle"></i> Riwayat Kedatangan
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item mt-auto pt-3 border-top border-secondary border-opacity-25">
                <a class="nav-link <?= (str_starts_with(uri_string(), 'admin/settings')) ? 'active' : '' ?>" href="<?= base_url('admin/settings') ?>">
                    <i class="fas fa-cogs"></i> Pengaturan Sistem
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-danger" href="<?= base_url('auth/logout') ?>">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>

    <!-- Content -->
    <div class="content" id="mainContent">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light mb-4">
            <div class="container-fluid">
                <div class="d-flex align-items-center">
                    <button id="toggleSidebar" class="sidebar-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h4 class="mb-0"><?= $title ?? 'Dashboard' ?></h4>
                </div>
                <div class="d-flex">
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i> <?= $nama ?? 'Admin' ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Profil</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Pengaturan</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= base_url('auth/logout') ?>"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

<script>
$(document).ready(function() {
    // Sidebar Toggle
    $('#toggleSidebar').on('click', function() {
        $('#sidebar').toggleClass('collapsed');
        $('#mainContent').toggleClass('expanded');
        
        // Simpan status ke localStorage agar tidak reset saat refresh
        const isCollapsed = $('#sidebar').hasClass('collapsed');
        localStorage.setItem('sidebarCollapsed', isCollapsed);
    });

    // Cek status sidebar saat page load
    if (localStorage.getItem('sidebarCollapsed') === 'true') {
        $('#sidebar').addClass('collapsed');
        $('#mainContent').addClass('expanded');
    }

    // Toggle sub-menu saat menu parent diklik
    $('.nav-item > .nav-link').on('click', function(e) {
        if ($(this).next('.nav-treeview').length > 0) {
            e.preventDefault();
            $(this).parent().toggleClass('menu-open');
            return false; // Mencegah default behavior
        }
    });
    
    // Hapus hash dari URL jika ada
    if (window.location.hash) {
        history.replaceState('', document.title, window.location.pathname);
    }

    // Hilangkan fokus sidebar saat klik area konten (cegah menu terlihat "aktif" padahal cuma fokus)
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#sidebar').length) {
            $('#sidebar .nav-link').blur();
        }
    });
});
</script>

       
