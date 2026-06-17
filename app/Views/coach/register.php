<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pelatih</title>
    <meta name="theme-color" content="#064E3B">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; margin: 0; padding: 0; }
        html, body { font-family: 'Outfit', sans-serif; background: #F0FDF4; color: #1F2937; }

        /* ── Shell ── */
        .app { min-height: 100dvh; display: flex; justify-content: center; background: #D1FAE5; }
        .frame {
            width: 100%; max-width: 430px;
            min-height: 100dvh;
            background: #F0FDF4;
            display: flex; flex-direction: column;
        }
        @media (min-width: 900px) {
            .frame {
                min-height: 92dvh; margin: 28px 0;
                border-radius: 28px; overflow: hidden;
                box-shadow: 0 20px 60px rgba(5,150,105,0.12);
                border: 1px solid rgba(5,150,105,0.1);
            }
        }

        /* ── Topbar ── */
        .topbar {
            position: sticky; top: 0; z-index: 20;
            background: rgba(255,255,255,0.88); backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(5,150,105,0.1);
            padding: 12px 16px;
            display: flex; align-items: center; gap: 12px;
        }
        .back-btn {
            width: 36px; height: 36px; border-radius: 10px;
            background: rgba(5,150,105,0.08); border: none;
            display: grid; place-items: center;
            color: #059669; font-size: 14px; cursor: pointer;
            flex: 0 0 auto; text-decoration: none;
        }
        .back-btn:hover { background: rgba(5,150,105,0.15); }
        .topbar-title { font-size: 15px; font-weight: 700; color: #1F2937; }
        .topbar-sub   { font-size: 11px; color: #6B7280; }

        /* ── Content ── */
        .content { padding: 16px 14px 40px; flex: 1; }

        /* Validation errors */
        .error-box {
            background: rgba(239,68,68,0.06); border: 1px solid rgba(239,68,68,0.2);
            border-radius: 12px; padding: 12px 14px; margin-bottom: 20px;
            font-size: 12px; color: #B91C1C;
        }
        .error-box ul { margin: 0; padding-left: 16px; }
        .error-box li { margin-bottom: 4px; }

        /* Section label */
        .section-label {
            font-size: 11px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.8px; color: #059669;
            margin: 20px 0 12px; padding-bottom: 6px;
            border-bottom: 1px solid rgba(5,150,105,0.15);
            display: flex; align-items: center; gap: 6px;
        }

        /* Form group */
        .form-group { margin-bottom: 14px; }
        label { display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .input-wrap { position: relative; }
        .input-icon {
            position: absolute; left: 13px; top: 50%; transform: translateY(-50%);
            color: #9CA3AF; font-size: 13px; pointer-events: none;
        }
        .input-icon.top { top: 16px; transform: none; }
        input[type="text"],
        input[type="email"],
        input[type="number"],
        input[type="password"],
        textarea {
            width: 100%;
            padding: 12px 13px 12px 38px;
            border: 1.5px solid #E5E7EB; border-radius: 12px;
            font-family: 'Outfit', sans-serif; font-size: 13px; color: #1F2937;
            background: #fff; outline: none; transition: all 0.2s;
        }
        textarea { padding-top: 12px; resize: none; }
        input:focus, textarea:focus {
            border-color: #059669; background: #fff;
            box-shadow: 0 0 0 3px rgba(5,150,105,0.12);
        }
        .toggle-pw {
            position: absolute; right: 13px; top: 50%; transform: translateY(-50%);
            color: #9CA3AF; cursor: pointer; font-size: 13px; background: none; border: none; padding: 0;
        }
        .toggle-pw:hover { color: #059669; }

        /* Submit */
        .btn-submit {
            width: 100%; padding: 14px;
            background: linear-gradient(135deg, #059669, #064E3B);
            color: #fff; border: none; border-radius: 14px;
            font-family: 'Outfit', sans-serif; font-size: 15px; font-weight: 600;
            cursor: pointer; transition: all 0.2s;
            box-shadow: 0 4px 16px rgba(5,150,105,0.3);
            display: flex; align-items: center; justify-content: center; gap: 8px;
            margin-top: 24px;
        }
        .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 8px 24px rgba(5,150,105,0.35); }

        .footer-link { text-align: center; font-size: 13px; color: #6B7280; margin-top: 16px; }
        .footer-link a { color: #059669; font-weight: 600; }
    </style>
</head>
<body>
<div class="app">
    <div class="frame">

        <header class="topbar">
            <a href="<?= base_url('coach/login') ?>" class="back-btn"><i class="fas fa-arrow-left"></i></a>
            <div>
                <div class="topbar-title">Daftar Akun Pelatih</div>
                <div class="topbar-sub">Isi data diri Anda dengan lengkap</div>
            </div>
        </header>

        <div class="content">
            <?php if (isset($validation)): ?>
                <div class="error-box">
                    <ul>
                        <?php foreach ($validation->getErrors() as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('coach/save') ?>" method="post">
                <?= csrf_field() ?>

                <!-- Data Pribadi -->
                <div class="section-label"><i class="fas fa-user"></i> Data Pribadi</div>

                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <div class="input-wrap">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" name="nama" id="nama" value="<?= old('nama') ?>" placeholder="Nama lengkap Anda" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <div class="input-wrap">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" name="email" id="email" value="<?= old('email') ?>" placeholder="email@contoh.com" required autocomplete="email">
                    </div>
                </div>

                <div class="form-group">
                    <label for="telepon">Nomor WhatsApp / Telepon</label>
                    <div class="input-wrap">
                        <i class="fab fa-whatsapp input-icon"></i>
                        <input type="text" name="telepon" id="telepon" value="<?= old('telepon') ?>" placeholder="08xxxxxxxxxx" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="alamat">Alamat Domisili</label>
                    <div class="input-wrap">
                        <i class="fas fa-map-marker-alt input-icon top"></i>
                        <textarea name="alamat" id="alamat" rows="3" placeholder="Jl. Contoh No. 1, Kota..."><?= old('alamat') ?></textarea>
                    </div>
                </div>

                <!-- Keahlian -->
                <div class="section-label"><i class="fas fa-swimming-pool"></i> Keahlian & Pengalaman</div>

                <div class="form-group">
                    <label for="keahlian">Spesialisasi / Keahlian Renang</label>
                    <div class="input-wrap">
                        <i class="fas fa-medal input-icon"></i>
                        <input type="text" name="keahlian" id="keahlian" value="<?= old('keahlian') ?>" placeholder="Contoh: Gaya Bebas, Gaya Dada" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="pengalaman">Pengalaman Melatih (tahun)</label>
                    <div class="input-wrap">
                        <i class="fas fa-clock input-icon"></i>
                        <input type="number" name="pengalaman" id="pengalaman" value="<?= old('pengalaman') ?>" placeholder="Contoh: 3" min="0" required>
                    </div>
                </div>

                <!-- Keamanan -->
                <div class="section-label"><i class="fas fa-lock"></i> Keamanan Akun</div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="password" id="password" placeholder="Minimal 6 karakter" required autocomplete="new-password">
                        <button type="button" class="toggle-pw" onclick="togglePw('password','pw-icon')">
                            <i class="fas fa-eye" id="pw-icon"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Konfirmasi Password</label>
                    <div class="input-wrap">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="Ulangi password" required autocomplete="new-password">
                        <button type="button" class="toggle-pw" onclick="togglePw('confirm_password','cpw-icon')">
                            <i class="fas fa-eye" id="cpw-icon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-user-plus"></i> Daftar Sekarang
                </button>
            </form>

            <div class="footer-link">
                Sudah punya akun? <a href="<?= base_url('coach/login') ?>">Login di sini</a>
            </div>
        </div>

    </div>
</div>

<script>
    function togglePw(inputId, iconId) {
        var inp = document.getElementById(inputId);
        var ico = document.getElementById(iconId);
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
