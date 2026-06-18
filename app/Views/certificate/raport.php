<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --navy: #1A365D;
            --gold: #D4AF37;
            --dark-gray: #2D3748;
            --light-gray: #F7FAFC;
            --border-color: rgba(212, 175, 55, 0.3);
        }

        body {
            background-color: #F3F4F6;
            margin: 0;
            padding: 40px 0;
            font-family: 'Montserrat', sans-serif;
            color: var(--dark-gray);
            line-height: 1.5;
        }

        /* Raport A4 Portrait Container */
        .raport-outer {
            width: 794px;
            min-height: 1123px;
            margin: 0 auto;
            background-color: #FFFFFF;
            padding: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            border-radius: 8px;
            position: relative;
            box-sizing: border-box;
        }

        .raport-inner {
            width: 100%;
            height: 100%;
            border: 4px double var(--gold);
            border-radius: 4px;
            padding: 30px 40px;
            box-sizing: border-box;
            position: relative;
            background-color: #ffffff;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%234f46e5' fill-opacity='0.03' d='M0,224L60,213.3C120,203,240,181,360,186.7C480,192,600,224,720,234.7C840,245,960,235,1080,208C1200,181,1320,139,1380,117.3L1440,96L1440,320L1380,320C1320,320,1200,320,1080,320C960,320,840,320,720,320C600,320,480,320,360,320C240,320,120,320,60,320L0,320Z'%3E%3C/path%3E%3C/svg%3E");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: bottom;
        }

        /* Header logos and title */
        .raport-header-logos {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 10px;
            margin-bottom: 15px;
        }

        .logo-sc { height: 65px; width: auto; object-fit: contain; }
        .logo-bapopsi { height: 50px; width: auto; object-fit: contain; }
        .logo-hrs { height: 50px; width: auto; object-fit: contain; }

        .school-title {
            font-family: 'Cinzel', serif;
            color: var(--navy);
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 2px;
            text-align: center;
            margin-bottom: 2px;
        }

        .school-subtitle {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #718096;
            text-align: center;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .divider-line {
            width: 100%;
            height: 2px;
            background-color: var(--gold);
            margin-bottom: 20px;
        }

        /* Document Title */
        .doc-title {
            font-family: 'Cinzel', serif;
            color: var(--navy);
            font-size: 22px;
            font-weight: 700;
            text-align: center;
            letter-spacing: 1px;
            margin-bottom: 25px;
        }

        /* Student profile grid */
        .student-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            background-color: var(--light-gray);
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 30px;
            font-size: 13px;
        }

        .info-item {
            display: flex;
        }

        .info-label {
            width: 130px;
            font-weight: 600;
            color: #4A5568;
        }

        .info-value {
            color: var(--navy);
            font-weight: 700;
        }

        /* Table of Grades */
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            font-size: 13px;
        }

        .grades-table th {
            background-color: var(--navy);
            color: #FFFFFF;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 12px;
            border: 1px solid var(--navy);
            text-align: left;
        }

        .grades-table td {
            padding: 10px 12px;
            border: 1px solid #E2E8F0;
        }

        .grades-table tr:nth-child(even) td {
            background-color: var(--light-gray);
        }

        .grade-badge {
            display: inline-block;
            width: 28px;
            height: 28px;
            line-height: 28px;
            border-radius: 50%;
            text-align: center;
            font-weight: 700;
            font-size: 13px;
        }

        .badge-A { background-color: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0; }
        .badge-B { background-color: #DBEAFE; color: #1E40AF; border: 1px solid #BFDBFE; }
        .badge-C { background-color: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; }

        /* Sections */
        .section-title {
            font-family: 'Cinzel', serif;
            color: var(--navy);
            font-size: 14px;
            font-weight: 700;
            border-bottom: 2px solid var(--gold);
            padding-bottom: 5px;
            margin-bottom: 12px;
            text-transform: uppercase;
        }

        .section-content {
            font-size: 13px;
            color: #4A5568;
            margin-bottom: 25px;
            background-color: var(--light-gray);
            border-left: 3px solid var(--gold);
            padding: 10px 15px;
            border-radius: 0 6px 6px 0;
        }

        /* Achievements banner */
        .achievement-box {
            background: linear-gradient(135deg, #FFFDF5 0%, #FFF9E6 100%);
            border: 1px dashed var(--gold);
            border-radius: 8px;
            padding: 12px 20px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .achievement-icon {
            font-size: 28px;
            color: #E2B93B;
        }

        .achievement-text {
            font-size: 13px;
            color: #744210;
        }

        /* Rubric / Grading Concept list */
        .rubric-text {
            font-size: 11px;
            color: #718096;
            margin-top: -15px;
            margin-bottom: 25px;
            display: flex;
            gap: 15px;
        }

        /* Signatures and Seals */
        .raport-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 40px;
            padding: 0 20px;
        }

        .sig-block {
            width: 220px;
            text-align: center;
        }

        .sig-line {
            border-top: 1px solid #A0AEC0;
            margin-top: 55px;
            padding-top: 5px;
            font-size: 12px;
            font-weight: 700;
            color: #2D3748;
        }

        .sig-title {
            font-size: 10px;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .seal-container {
            width: 70px;
            height: 70px;
            position: relative;
            margin-bottom: -5px;
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
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .seal-text {
            font-size: 7px;
            font-weight: 800;
            color: #7F5F00;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: center;
        }

        .examiner-info {
            text-align: center;
            font-size: 11px;
            color: #718096;
            margin-top: 20px;
        }

        /* Control Panel */
        .control-panel {
            width: 794px;
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

        /* Print Override */
        @media print {
            body {
                background-color: #FFFFFF;
                padding: 0;
                margin: 0;
            }
            .control-panel {
                display: none;
            }
            .raport-outer {
                box-shadow: none;
                padding: 0;
                margin: 0;
                width: 100%;
                min-height: auto;
                page-break-inside: avoid;
            }
            .raport-inner {
                border: 4px double var(--gold);
                padding: 30px 40px;
            }
        }
    </style>
</head>
<body>

    <!-- Controls -->
    <div class="control-panel">
        <a href="javascript:window.close();" class="btn-back"><i class="fas fa-times me-1"></i> Tutup Halaman</a>
        <button onclick="window.print()" class="btn-print"><i class="fas fa-print"></i> Cetak Raport (PDF)</button>
    </div>

    <!-- Raport Container -->
    <div class="raport-outer">
        <div class="raport-inner">
            
            <!-- Logos -->
            <div class="raport-header-logos">
                <img src="<?= app_logo('sportcenter_logo') ?>" alt="Sport Center" class="logo-sc">
                <img src="<?= app_logo('bapopsi_logo') ?>" alt="Bapopsi" class="logo-bapopsi">
                <img src="<?= app_logo('hrs_logo') ?>" alt="HRS" class="logo-hrs">
            </div>

            <!-- School Title -->
            <div class="school-title">11 MARET SPORT CENTER & HRS SWIMMING FAMILY</div>
            <div class="school-subtitle">Sistem Evaluasi Pembelajaran Renang</div>
            <div class="divider-line"></div>

            <!-- Document Title -->
            <div class="doc-title">RAPORT EVALUASI KENAIKAN TINGKAT</div>

            <!-- Profile Info -->
            <div class="student-info-grid">
                <div class="info-item">
                    <span class="info-label">Nama Siswa</span>
                    <span class="info-value">: <?= esc($child['nama']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">No. Sertifikat</span>
                    <span class="info-value">: <?= esc($cert['nomor_sertifikat']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tingkat Baru</span>
                    <span class="info-value">: <?= esc($cert['nama_level']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tanggal Lulus</span>
                    <span class="info-value">: <?= date('d F Y', strtotime($cert['tanggal_lulus'])) ?></span>
                </div>
            </div>

            <!-- Grades Table -->
            <div class="section-title">Hasil Penilaian Kriteria</div>
            <table class="grades-table">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Aspek / Kriteria Penilaian</th>
                        <th width="80" style="text-align: center;">Nilai</th>
                        <th>Deskripsi Kompetensi Aspek</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td class="fw-bold">Teknik Gerakan Kaki</td>
                        <td style="text-align: center;">
                            <span class="grade-badge badge-<?= $cert['teknik_kaki'] ?>"><?= $cert['teknik_kaki'] ?></span>
                        </td>
                        <td>Kemampuan dorongan, kestabilan kayuhan kaki, dan posisi tubuh (streamline) di permukaan air.</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td class="fw-bold">Teknik Gerakan Tangan</td>
                        <td style="text-align: center;">
                            <span class="grade-badge badge-<?= $cert['teknik_tangan'] ?>"><?= $cert['teknik_tangan'] ?></span>
                        </td>
                        <td>Efektivitas kayuhan lengan, teknik jangkauan (reach), tarikan (pull), dan pengembalian posisi lengan (recovery).</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td class="fw-bold">Teknik Pernapasan</td>
                        <td style="text-align: center;">
                            <span class="grade-badge badge-<?= $cert['teknik_pernapasan'] ?>"><?= $cert['teknik_pernapasan'] ?></span>
                        </td>
                        <td>Ketepatan waktu pengambilan napas (inhale), pengeluaran napas di air (bubbles/exhale), dan ritme pernapasan.</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td class="fw-bold">Keberanian & Kemandirian</td>
                        <td style="text-align: center;">
                            <span class="grade-badge badge-<?= $cert['keberanian'] ?>"><?= $cert['keberanian'] ?></span>
                        </td>
                        <td>Tingkat kepercayaan diri, kemandirian bergerak tanpa alat bantu, dan mentalitas menghadapi kedalaman air.</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td class="fw-bold">Kedisiplinan</td>
                        <td style="text-align: center;">
                            <span class="grade-badge badge-<?= $cert['disiplin'] ?>"><?= $cert['disiplin'] ?></span>
                        </td>
                        <td>Kepatuhan terhadap arahan instruktur, ketepatan waktu hadir, keseriusan berlatih, serta penggunaan perlengkapan lengkap.</td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td class="fw-bold">Sikap Fokus</td>
                        <td style="text-align: center;">
                            <span class="grade-badge badge-<?= $cert['sikap_fokus'] ?>"><?= $cert['sikap_fokus'] ?></span>
                        </td>
                        <td>Konsentrasi saat menerima instruksi teknik, ketekunan mengulang gerakan, dan ketahanan fokus selama sesi latihan.</td>
                    </tr>
                </tbody>
            </table>

            <!-- Rubric Notes -->
            <div class="rubric-text">
                <span><strong>Keterangan Skala Nilai:</strong></span>
                <span><span class="grade-badge badge-A" style="width: 18px; height: 18px; line-height: 18px; font-size: 10px;">A</span> Sangat Baik</span>
                <span><span class="grade-badge badge-B" style="width: 18px; height: 18px; line-height: 18px; font-size: 10px;">B</span> Baik</span>
                <span><span class="grade-badge badge-C" style="width: 18px; height: 18px; line-height: 18px; font-size: 10px;">C</span> Cukup</span>
            </div>

            <!-- Capaian Pembelajaran -->
            <div class="section-title">Capaian Pembelajaran Tingkat</div>
            <div class="section-content">
                <strong><?= esc($cert['nama_level']) ?>:</strong><br>
                <?= esc($cert['level_deskripsi'] ?: 'Siswa telah menyelesaikan semua kompetensi standar yang ditentukan pada tingkat ini.') ?>
            </div>

            <!-- Tournament/Competition Integration (Optional) -->
            <?php if (!empty($cert['tournament_name'])): ?>
                <div class="section-title">Prestasi Kejuaraan & Kompetisi</div>
                <div class="achievement-box">
                    <div class="achievement-icon"><i class="fas fa-trophy"></i></div>
                    <div class="achievement-text">
                        Siswa mencatatkan prestasi gemilang pada <strong><?= esc($cert['tournament_name']) ?></strong> dengan pencapaian: 
                        <strong class="text-primary"><?= esc($cert['prestasi'] ?: 'Peserta / Finisher') ?></strong>.
                    </div>
                </div>
            <?php endif; ?>

            <!-- Catatan Penguji -->
            <div class="section-title">Catatan & Umpan Balik Evaluator</div>
            <div class="section-content">
                <?= !empty($cert['catatan_evaluasi']) ? nl2br(esc($cert['catatan_evaluasi'])) : 'Siswa menunjukkan performa yang sangat baik dan siap untuk melanjutkan ke materi pembelajaran tingkat selanjutnya.' ?>
            </div>

            <!-- Signatures -->
            <div class="raport-footer">
                <div class="sig-block">
                    <div class="sig-title">Mengetahui,</div>
                    <div class="sig-line">Reza Pattriota Putra S,Kom</div>
                    <div class="sig-title">Owner 11 Maret Sport Center</div>
                </div>

                <div class="seal-container">
                    <div class="seal-gold">
                        <div class="seal-text" style="font-size: 8px; font-weight: 800; border-bottom: 1px solid #7F5F00; padding-bottom: 2px; margin-bottom: 2px;">MSC x HRS</div>
                        <div class="seal-text">OFFICIAL</div>
                        <div class="seal-text">SEAL</div>
                    </div>
                </div>

                <div class="sig-block">
                    <div class="sig-title">Menyetujui,</div>
                    <div class="sig-line">Heri Setiawan S.Pd</div>
                    <div class="sig-title">Owner HRS Swimming Family</div>
                </div>
            </div>

            <!-- Examiner info -->
            <?php if (!empty($cert['nama_penguji'])): ?>
                <div class="examiner-info">
                    <i class="fas fa-user-check me-1"></i> Dievaluasi oleh: <strong>Coach <?= esc($cert['nama_penguji']) ?></strong>
                </div>
            <?php endif; ?>

        </div>
    </div>

</body>
</html>
