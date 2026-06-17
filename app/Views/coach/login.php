<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pelatih</title>
    <meta name="theme-color" content="#064E3B">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; margin: 0; padding: 0; }
        html, body { height: 100%; font-family: 'Outfit', sans-serif; }
        body {
            background: linear-gradient(160deg, #064E3B 0%, #065F46 40%, #059669 100%);
            display: flex; align-items: center; justify-content: center;
            min-height: 100dvh;
            padding: 20px;
        }

        .login-shell {
            width: 100%; max-width: 390px;
            display: flex; flex-direction: column; align-items: center; gap: 28px;
        }

        /* Hero */
        .hero { text-align: center; color: #fff; }
        .hero-icon {
            width: 72px; height: 72px; border-radius: 22px;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.2);
            display: grid; place-items: center;
            font-size: 32px; margin: 0 auto 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        }
        .hero h1 { font-size: 22px; font-weight: 700; letter-spacing: -0.3px; margin-bottom: 6px; }
        .hero p  { font-size: 13px; opacity: 0.75; }

        /* Card */
        .card {
            width: 100%;
            background: #fff;
            border-radius: 24px;
            padding: 28px 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }

        /* Flash */
        .flash {
            border-radius: 12px; padding: 12px 14px;
            font-size: 13px; font-weight: 500;
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 20px;
        }
        .flash.error   { background: rgba(239,68,68,0.08); color: #B91C1C; border: 1px solid rgba(239,68,68,0.2); }
        .flash.success { background: rgba(5,150,105,0.08); color: #065F46; border: 1px solid rgba(5,150,105,0.2); }

        /* Form */
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 7px; }
        .input-wrap { position: relative; }
        .input-icon {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            color: #9CA3AF; font-size: 14px; pointer-events: none;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%; padding: 13px 14px 13px 40px;
            border: 1.5px solid #E5E7EB; border-radius: 12px;
            font-family: 'Outfit', sans-serif; font-size: 14px; color: #1F2937;
            background: #F9FAFB; outline: none; transition: all 0.2s;
        }
        input:focus { border-color: #059669; background: #fff; box-shadow: 0 0 0 3px rgba(5,150,105,0.12); }

        .toggle-pw {
            position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
            color: #9CA3AF; cursor: pointer; font-size: 14px; background: none; border: none; padding: 0;
        }
        .toggle-pw:hover { color: #059669; }

        .btn-login {
            width: 100%; padding: 14px;
            background: linear-gradient(135deg, #059669, #064E3B);
            color: #fff; border: none; border-radius: 14px;
            font-family: 'Outfit', sans-serif; font-size: 15px; font-weight: 600;
            cursor: pointer; transition: all 0.2s;
            box-shadow: 0 4px 16px rgba(5,150,105,0.3);
            display: flex; align-items: center; justify-content: center; gap: 8px;
            margin-top: 8px;
        }
        .btn-login:hover { transform: translateY(-1px); box-shadow: 0 8px 24px rgba(5,150,105,0.35); }
        .btn-login:active { transform: translateY(0); }

        .footer-link {
            text-align: center; font-size: 13px; color: #6B7280; margin-top: 20px;
        }
        .footer-link a { color: #059669; font-weight: 600; }
        .footer-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="login-shell">

        <div class="hero">
            <div class="hero-icon">🏊</div>
            <h1>Portal Pelatih</h1>
            <p>Masuk untuk mengelola evaluasi & perkembangan siswa</p>
        </div>

        <div class="card">
            <?php if (session()->getFlashdata('msg')): ?>
                <div class="flash error"><i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('msg') ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('success')): ?>
                <div class="flash success"><i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>

            <form action="<?= base_url('coach/login') ?>" method="post">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <div class="input-wrap">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" name="email" id="email" placeholder="pelatih@email.com" required autocomplete="email">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="password" id="password" placeholder="••••••••" required autocomplete="current-password">
                        <button type="button" class="toggle-pw" onclick="togglePw()">
                            <i class="fas fa-eye" id="pw-icon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Masuk
                </button>
            </form>

            <div class="footer-link">
                Belum punya akun? <a href="<?= base_url('coach/register') ?>">Daftar di sini</a>
            </div>
        </div>

    </div>

    <script>
        function togglePw() {
            var inp = document.getElementById('password');
            var ico = document.getElementById('pw-icon');
            if (inp.type === 'password') {
                inp.type = 'text';
                ico.className = 'fas fa-eye-slash';
            } else {
                inp.type = 'password';
                ico.className = 'fas fa-eye';
            }
        }
    </script>
</body>
</html>
