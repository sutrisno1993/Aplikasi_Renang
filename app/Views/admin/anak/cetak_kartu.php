<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Cetak Kartu Peserta' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <!-- Include JsBarcode for linear barcodes -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <!-- Include html2pdf.js for PDF generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    
    <style>
        :root {
            --card-width: 95mm;
            --card-height: 65mm;
            --page-width: 210mm;
            --page-height: 297mm;
        }
        
        body {
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
            font-family: 'Inter', 'Segoe UI', sans-serif;
            color: black;
        }

        .no-print {
            text-align: center;
            margin-bottom: 20px;
        }

        .page-container {
            width: var(--page-width);
            height: var(--page-height);
            background: white;
            margin: 0 auto 20px auto;
            padding: 8mm;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-template-rows: repeat(4, 1fr);
            gap: 4mm;
            box-sizing: border-box;
            page-break-after: always;
            align-content: start;
        }

        .id-card {
            width: 100%;
            height: var(--card-height);
            border: 2.5px solid black;
            border-radius: 8px;
            padding: 0;
            box-sizing: border-box;
            background: white;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            page-break-inside: avoid;
            position: relative;
        }

        .header-club {
            font-size: 8.5pt;
            font-style: italic;
            text-align: center;
            padding: 3px 0;
            border-bottom: 1.5px solid black;
            font-weight: 600;
            letter-spacing: 0.5px;
            background-color: #f8f9fa;
        }

        .card-main-content {
            display: flex;
            flex-direction: row;
            align-items: center;
            flex-grow: 1;
            padding: 2mm 4mm;
            gap: 4mm;
        }

        .qr-section {
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            border-right: 1.5px solid #eee;
            padding-right: 3mm;
        }

        .qr-code-box {
            width: 24mm;
            height: 24mm;
            background: white;
            padding: 0;
        }

        .qr-instruction {
            font-size: 4.5pt;
            margin-top: 2px;
            font-weight: 700;
            text-transform: uppercase;
            color: #444;
            text-align: center;
            line-height: 1;
        }

        .student-info {
            flex: 1;
            text-align: left;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .nickname {
            font-size: 20pt;
            font-weight: 900;
            margin: 0;
            text-transform: uppercase;
            line-height: 1;
            letter-spacing: -0.5px;
        }

        .fullname {
            font-size: 7.5pt;
            font-style: italic;
            margin-bottom: 4px;
            color: #333;
            line-height: 1.1;
            max-width: 45mm;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .info-row {
            display: flex;
            align-items: center;
            gap: 2mm;
            margin-bottom: 2px;
        }

        .student-id {
            font-size: 11pt;
            font-weight: 800;
            color: black;
        }

        .jenis-les {
            font-size: 9pt;
            font-weight: 600;
            border: 1px solid black;
            padding: 0px 5px;
            border-radius: 2px;
            text-transform: uppercase;
        }

        .barcode-section {
            text-align: center;
            padding: 0 4mm;
            margin-top: -2mm;
        }

        .barcode-img {
            width: 100%;
            height: 8mm;
        }

        .parent-info {
            background-color: black;
            color: white;
            font-size: 7.5pt;
            padding: 3px 0;
            width: 100%;
            text-align: center;
            font-weight: 500;
            letter-spacing: 0.3px;
            -webkit-print-color-adjust: exact;
        }

        @media print {
            body {
                background-color: white;
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none;
            }
            .page-container {
                box-shadow: none;
                margin: 0;
                padding: 5mm;
                border: none;
            }
            .parent-info {
                background-color: black !important;
                color: white !important;
            }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <div class="card shadow mx-auto border-0 rounded-4" style="max-width: 600px; margin-top: 30px;">
            <div class="card-body p-4 text-center">
                <h4 class="fw-bold mb-3">Cetak Kartu Peserta</h4>
                <p class="text-muted mb-4">Desain Profesional B&W • Layout 2x4 A4</p>
                <div class="d-flex justify-content-center gap-3">
                    <button onclick="window.print()" class="btn btn-dark btn-lg px-4 rounded-pill shadow-sm">
                        <i class="fas fa-print me-2"></i> Print Kartu
                    </button>
                    <button id="downloadPdf" class="btn btn-outline-dark btn-lg px-4 rounded-pill shadow-sm">
                        <i class="fas fa-file-pdf me-2"></i> Download PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="print-area">
        <?php 
        $chunks = array_chunk($anakList, 8);
        foreach ($chunks as $pageIndex => $pageData): 
        ?>
        <div class="page-container">
            <?php foreach ($pageData as $anak): ?>
                <?php 
                    $checkUrl = base_url('info/detail/' . $anak['id']);
                ?>
                <div class="id-card">
                    <div class="header-club">11 MARET SPORT CENTER</div>
                    <div class="card-main-content">
                        <div class="qr-section">
                            <div class="qr-code-box" data-url="<?= $checkUrl ?>"></div>
                            <div class="qr-instruction">SCAN UNTUK SISA PERTEMUAN</div>
                        </div>
                        <div class="student-info">
                        <div class="nickname"><?= esc($anak['nama_panggilan'] ?: $anak['nama']) ?></div>
                        <div class="fullname"><?= esc($anak['nama']) ?></div>
                        
                        <div class="info-row">
                            <div class="student-id">#<?= str_pad($anak['id'], 5, '0', STR_PAD_LEFT) ?></div>
                            <div class="jenis-les"><?= esc($anak['nama_les'] ?? 'Reguler') ?></div>
                        </div>
                    </div>
                </div>
                <div class="barcode-section">
                    <svg class="barcode-img" 
                        jsbarcode-format="CODE128"
                        jsbarcode-value="<?= str_pad($anak['id'], 5, '0', STR_PAD_LEFT) ?>"
                        jsbarcode-height="25"
                        jsbarcode-width="1.5"
                        jsbarcode-displayValue="false"
                        jsbarcode-margin="0">
                    </svg>
                </div>
                <div class="parent-info">
                        <i class="fab fa-whatsapp me-1"></i> <?= esc($anak['whatsapp'] ?? '-') ?> &nbsp;•&nbsp; <?= esc($anak['nama_parent'] ?? '-') ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Generate QR Codes
            document.querySelectorAll('.qr-code-box').forEach(function(box) {
                var url = box.getAttribute('data-url');
                new QRCode(box, {
                    text: url,
                    width: 128,
                    height: 128,
                    colorDark : "#000000",
                    colorLight : "#ffffff",
                    correctLevel : QRCode.CorrectLevel.H
                });
                
                var img = box.querySelector('img');
                if (img) {
                    img.style.width = '100%';
                    img.style.height = '100%';
                }
            });

            // Generate Linear Barcodes
            JsBarcode(".barcode-img").init();

            // PDF Download Logic
            const downloadBtn = document.getElementById('downloadPdf');
            if (downloadBtn) {
                downloadBtn.addEventListener('click', function() {
                    const element = document.getElementById('print-area');
                    const options = {
                        margin: [0, 0],
                        filename: 'Kartu_Peserta_Renang.pdf',
                        image: { type: 'jpeg', quality: 0.98 },
                        html2canvas: { 
                            scale: 2, 
                            useCORS: true,
                            letterRendering: true
                        },
                        jsPDF: { 
                            unit: 'mm', 
                            format: 'a4', 
                            orientation: 'portrait' 
                        },
                        pagebreak: { mode: 'css', after: '.page-container' }
                    };

                    // Show loading state
                    const originalContent = downloadBtn.innerHTML;
                    downloadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Generating...';
                    downloadBtn.disabled = true;

                    html2pdf().set(options).from(element).save().then(() => {
                        // Reset button state
                        downloadBtn.innerHTML = originalContent;
                        downloadBtn.disabled = false;
                    }).catch(err => {
                        console.error('PDF Error:', err);
                        alert('Gagal mengunduh PDF. Silakan gunakan fitur Print (Save as PDF).');
                        downloadBtn.innerHTML = originalContent;
                        downloadBtn.disabled = false;
                    });
                });
            }
        });
    </script>
</body>
</html>