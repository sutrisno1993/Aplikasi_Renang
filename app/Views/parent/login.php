<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Orang Tua/Wali - Aplikasi Renang</title>
    <meta name="theme-color" content="#4F46E5">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
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

        html, body {
            height: 100%;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .app {
            min-height: 100dvh;
            display: flex;
            justify-content: center;
        }

        .frame {
            width: 100%;
            max-width: 430px;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            position: relative;
            background-color: var(--card-bg);
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
            padding: 14px 16px;
        }

        .left {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .logo {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.15);
            flex: 0 0 auto;
        }

        .title {
            min-width: 0;
        }

        .title strong {
            display: block;
            font-size: 14px;
            font-weight: 700;
            color: var(--text-main);
            letter-spacing: -0.2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .title span {
            display: block;
            font-size: 12px;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .link {
            font-size: 12px;
            font-weight: 600;
            color: var(--primary);
        }

        .content {
            padding: 24px 20px calc(env(safe-area-inset-bottom) + 92px);
            background-color: var(--bg-color);
            flex-grow: 1;
        }

        .card {
            border: 1px solid rgba(0, 0, 0, 0.06);
            background: var(--card-bg);
            border-radius: 24px;
            padding: 24px 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        }

        h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            color: var(--text-main);
            letter-spacing: -0.4px;
        }

        p {
            margin: 8px 0 0;
            color: var(--text-muted);
            font-size: 13px;
            line-height: 1.4;
        }

        .alert {
            border-radius: 14px;
            padding: 12px 14px;
            margin-top: 16px;
            font-size: 13px;
        }

        .alert-danger {
            border: 1px solid rgba(239, 68, 68, 0.15);
            background: rgba(239, 68, 68, 0.06);
            color: var(--danger);
        }

        .alert-success {
            border: 1px solid rgba(16, 185, 129, 0.15);
            background: rgba(16, 185, 129, 0.06);
            color: var(--secondary);
        }

        form {
            margin-top: 20px;
            display: grid;
            gap: 16px;
        }

        label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 6px;
        }

        .field {
            border-radius: 14px;
            border: 1px solid #D1D5DB;
            background: #F9FAFB;
            padding: 10px 12px;
            transition: all 0.2s ease;
        }

        .field:focus-within {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.15);
            background: #FFFFFF;
        }

        .input {
            width: 100%;
            border: 0;
            outline: none;
            background: transparent;
            color: var(--text-main);
            font-size: 14px;
            padding: 2px 0;
        }

        .row {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .icon {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            background: rgba(79, 70, 229, 0.08);
            font-size: 12px;
            font-weight: 700;
            color: var(--primary);
            flex: 0 0 auto;
        }

        .toggle {
            border: none;
            background: transparent;
            color: var(--text-muted);
            padding: 6px;
            font-size: 14px;
            cursor: pointer;
            outline: none;
        }

        .btn {
            border: none;
            border-radius: 14px;
            padding: 14px;
            font-weight: 700;
            font-size: 14px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: #ffffff;
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.15);
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 24px rgba(79, 70, 229, 0.2);
        }

        .below {
            margin-top: 12px;
            font-size: 13px;
            color: var(--text-muted);
            text-align: center;
        }

        .below a {
            color: var(--primary);
            font-weight: 600;
        }

        .below a:hover {
            text-decoration: underline;
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

        .nav-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 4px;
        }

        .nav-item {
            border-radius: 14px;
            padding: 8px 4px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
        }

        .nav-item:hover {
            color: var(--primary);
        }

        .nav-icon {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            background: rgba(0, 0, 0, 0.03);
            display: grid;
            place-items: center;
            font-size: 16px;
            color: var(--text-muted);
            transition: all 0.2s ease;
        }

        .nav-item:hover .nav-icon {
            background: rgba(79, 70, 229, 0.08);
            color: var(--primary);
        }
    </style>
</head>
<body>
    <div class="app">
        <div class="frame">
            <header class="topbar">
                <div class="topbar-inner">
                    <div class="left">
                        <div class="logo" aria-hidden="true">
                            <img src="<?= base_url('logo.png?v=' . time()) ?>" alt="Logo" style="width: 100%; height: 100%; object-fit: contain; border-radius: 10px;">
                        </div>
                        <div class="title">
                            <strong>Login Orang Tua / Wali</strong>
                            <span>Masuk untuk akses dashboard</span>
                        </div>
                    </div>
                    <a class="link" href="<?= base_url('/') ?>">Home</a>
                </div>
            </header>

            <main class="content">
                <section class="card">
                    <h1>Selamat datang</h1>
                    <p>Masukkan nomor WhatsApp dan password untuk masuk. Kamu juga bisa isi password dengan nomor WhatsApp.</p>

                    <?php if (session()->getFlashdata('msg')): ?>
                        <div class="alert alert-danger"><?= session()->getFlashdata('msg') ?></div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                    <?php endif; ?>

                    <form action="<?= base_url('parent/login') ?>" method="post" autocomplete="on">
                        <?= csrf_field() ?>
                        <div>
                            <label for="whatsapp">Nomor WhatsApp</label>
                            <div class="field">
                                <div class="row">
                                    <div class="icon" aria-hidden="true"><i class="fa-brands fa-whatsapp"></i></div>
                                    <input class="input" type="tel" inputmode="numeric" name="whatsapp" id="whatsapp" placeholder="Contoh: 081234567890" required>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="password">Password (atau No. WhatsApp)</label>
                            <div class="field">
                                <div class="row">
                                    <div class="icon" aria-hidden="true"><i class="fa-solid fa-lock"></i></div>
                                    <input class="input" type="password" name="password" id="password" placeholder="Masukkan password" required>
                                    <button class="toggle" type="button" id="togglePassword" aria-label="Tampilkan password">👁️</button>
                                </div>
                            </div>
                        </div>

                        <button class="btn" type="submit">Masuk</button>
                        <div class="below">
                            Belum punya akun? <a href="<?= base_url('parent/register') ?>">Daftar di sini</a>
                        </div>
                    </form>
                </section>
            </main>

            <nav class="bottom-nav" aria-label="Navigasi bawah">
                <div class="nav-grid">
                    <a class="nav-item" href="<?= base_url('/') ?>">
                        <span class="nav-icon" aria-hidden="true">🏠</span>
                        Home
                    </a>
                    <a class="nav-item" href="<?= base_url('parent/register') ?>">
                        <span class="nav-icon" aria-hidden="true">📝</span>
                        Daftar
                    </a>
                    <a class="nav-item" href="https://wa.me/628981274514" target="_blank" rel="noreferrer">
                        <span class="nav-icon" aria-hidden="true">💬</span>
                        Bantuan
                    </a>
                </div>
            </nav>
        </div>
    </div>

    <script>
        (function () {
            var btn = document.getElementById('togglePassword');
            var input = document.getElementById('password');
            if (!btn || !input) return;

            btn.addEventListener('click', function () {
                var isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                btn.textContent = isPassword ? '🙈' : '👁️';
                btn.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Tampilkan password');
            });
        })();
    </script>
</body>
</html>
