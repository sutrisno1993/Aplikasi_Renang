<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Montserrat:wght@300;400;600;700&family=Pinyon+Script&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #F3F4F6;
            margin: 0;
            padding: 40px 0;
            font-family: 'Montserrat', sans-serif;
            color: #1F2937;
        }

        /* Certificate Container */
        .cert-outer {
            width: 842px;
            height: 595px;
            margin: 0 auto;
            background-color: #FFFFFF;
            padding: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            border-radius: 8px;
            position: relative;
            box-sizing: border-box;
        }

        .cert-inner {
            width: 100%;
            height: 100%;
            border: 6px double #D4AF37; /* Gold border */
            border-radius: 4px;
            padding: 25px 50px 15px 50px;
            box-sizing: border-box;
            position: relative;
            text-align: center;
            background-color: #ffffff;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%234f46e5' fill-opacity='0.06' d='M0,224L60,213.3C120,203,240,181,360,186.7C480,192,600,224,720,234.7C840,245,960,235,1080,208C1200,181,1320,139,1380,117.3L1440,96L1440,320L1380,320C1320,320,1200,320,1080,320C960,320,840,320,720,320C600,320,480,320,360,320C240,320,120,320,60,320L0,320Z'%3E%3C/path%3E%3Cpath fill='%231a365d' fill-opacity='0.04' d='M0,128L60,149.3C120,171,240,213,360,208C480,203,600,149,720,144C840,139,960,181,1080,181.3C1200,181,1320,139,1380,117.3L1440,96L1440,320L1380,320C1320,320,1200,320,1080,320C960,320,840,320,720,320C600,320,480,320,360,320C240,320,120,320,60,320L0,320Z'%3E%3C/path%3E%3C/svg%3E");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: bottom;
            display: flex;
            flex-direction: column;
        }

        /* Watermark Background icon */
        .cert-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 260px;
            color: rgba(212, 175, 55, 0.03);
            z-index: 1;
            pointer-events: none;
        }

        /* Elements inside */
        .cert-header {
            font-family: 'Cinzel', serif;
            color: #1A365D;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 3px;
            margin-bottom: 5px;
            z-index: 2;
            position: relative;
        }

        .cert-subheader {
            font-family: 'Montserrat', sans-serif;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: #718096;
            margin-bottom: 20px;
            font-weight: 600;
            z-index: 2;
            position: relative;
        }

        .cert-title {
            font-family: 'Cinzel', serif;
            color: #D4AF37;
            font-size: 34px;
            font-weight: 700;
            letter-spacing: 2px;
            margin-bottom: 10px;
            z-index: 2;
            position: relative;
            text-shadow: 1px 1px 1px rgba(0,0,0,0.05);
        }

        .cert-presented {
            font-size: 13px;
            color: #4A5568;
            font-style: italic;
            margin-bottom: 15px;
            z-index: 2;
            position: relative;
        }

        .cert-name {
            font-family: 'Cinzel', serif;
            font-size: 36px;
            font-weight: 700;
            color: #1A365D;
            margin: 10px 0;
            line-height: 1;
            z-index: 2;
            position: relative;
        }

        .cert-line {
            width: 300px;
            height: 1px;
            background-color: rgba(212, 175, 55, 0.5);
            margin: 10px auto 20px auto;
            z-index: 2;
            position: relative;
        }

        .cert-text {
            font-size: 13px;
            color: #4A5568;
            max-width: 600px;
            margin: 0 auto 10px auto;
            line-height: 1.6;
            z-index: 2;
            position: relative;
        }

        .cert-text strong {
            color: #1A365D;
        }

        /* Signatures and Seals */
        .cert-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: auto;
            padding: 0 30px;
            z-index: 2;
            position: relative;
        }

        .cert-sig-block {
            width: 180px;
            text-align: center;
        }

        .cert-sig-line {
            border-top: 1px solid #A0AEC0;
            margin-top: 50px;
            padding-top: 5px;
            font-size: 12px;
            font-weight: 600;
            color: #2D3748;
        }

        .cert-sig-title {
            font-size: 10px;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .cert-seal {
            width: 75px;
            height: 75px;
            position: relative;
            margin-bottom: -10px;
        }

        .seal-gold {
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, #FFE082 0%, #FFB300 100%);
            border-radius: 50%;
            border: 2px dashed #D4AF37;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .seal-text {
            font-size: 8px;
            font-weight: 700;
            color: #7F5F00;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: center;
        }

        .cert-serial {
            font-size: 10px;
            letter-spacing: 2px;
            color: #718096;
            text-transform: uppercase;
            text-align: center;
            margin-top: -5px;
            margin-bottom: 20px;
            font-weight: 600;
            z-index: 2;
        }

        /* Printable Control Overlay */
        .control-panel {
            width: 842px;
            margin: 0 auto 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-print {
            background-color: #4F46E5;
            color: #ffffff;
            border: none;
            padding: 10px 24px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 30px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-print:hover {
            background-color: #4338CA;
            transform: translateY(-1px);
        }

        .btn-back {
            color: #4F46E5;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
        }

        /* Print Media Override */
        @media print {
            body {
                background-color: #FFFFFF;
                padding: 0;
                margin: 0;
            }
            .control-panel {
                display: none;
            }
            .cert-outer {
                box-shadow: none;
                padding: 0;
                margin: 0;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    <!-- Controls -->
    <div class="control-panel">
        <a href="<?= base_url('parent/curriculum') ?>" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali ke Kurikulum</a>
        <button onclick="window.print()" class="btn-print"><i class="fas fa-print"></i> Cetak Sertifikat (PDF)</button>
    </div>

    <!-- Certificate -->
    <div class="cert-outer">
        <div class="cert-inner">
            <div class="cert-watermark"><i class="fas fa-swimming-pool"></i></div>

            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0 40px; margin-bottom: 5px; margin-top: -10px; position: relative; z-index: 2;">
                <img src="<?= app_logo('sportcenter_logo') ?>" alt="Sport Center" style="height: 70px; width: auto; object-fit: contain;">
                <img src="<?= app_logo('bapopsi_logo') ?>" alt="Bapopsi" style="height: 55px; width: auto; object-fit: contain;">
                <img src="<?= app_logo('hrs_logo') ?>" alt="HRS" style="height: 55px; width: auto; object-fit: contain;">
            </div>

            <div class="cert-header">11 MARET SPORT CENTER & HRS SWIMMING FAMILY</div>
            <div class="cert-subheader">Sistem Evaluasi Pembelajaran Renang</div>

            <div class="cert-title">SERTIFIKAT KELULUSAN</div>
            
            <div class="cert-presented">Sertifikat ini dengan bangga diberikan kepada:</div>
            
            <div class="cert-name"><?= esc($child['nama']) ?></div>
            <div class="cert-line"></div>
            <div class="cert-serial">No. Sertifikat: <?= esc($cert['nomor_sertifikat']) ?> &nbsp;&middot;&nbsp; Tanggal: <?= date('d F Y', strtotime($cert['tanggal_lulus'])) ?></div>
            
            <div class="cert-text" style="margin-bottom: 5px;">
                Telah berhasil menyelesaikan seluruh kriteria penilaian teknik dan mental kelas renang terstruktur, serta dinyatakan <strong>LULUS & SELESAI</strong> pada tingkat kemampuan:
                <br>
                <strong style="font-size: 16px; display: block; margin: 6px 0; color: #D4AF37; font-family: 'Cinzel', serif;">
                    <?= esc($cert['nama_level']) ?>
                </strong>
            </div>

            <div class="cert-footer">
                <div class="cert-sig-block" style="width: 220px;">
                    <div style="height: 40px;"></div>
                    <div class="cert-sig-line" style="margin-top: 0;">Reza Pattriota Putra S,Kom</div>
                    <div class="cert-sig-title">Owner 11 Maret Sport Center</div>
                </div>

                <div class="cert-seal">
                    <div class="seal-gold">
                        <div class="seal-text" style="font-size: 8px; font-weight: 800; border-bottom: 1px solid #7F5F00; padding-bottom: 2px; margin-bottom: 2px;">MSC x HRS</div>
                        <div class="seal-text">OFFICIAL</div>
                        <div class="seal-text">SEAL</div>
                    </div>
                </div>

                <div class="cert-sig-block" style="width: 220px;">
                    <div style="height: 40px;"></div>
                    <div class="cert-sig-line" style="margin-top: 0;">Heri Setiawan S.Pd</div>
                    <div class="cert-sig-title">Owner HRS Swimming Family</div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
