<?= $this->include('admin/templates/header') ?>

<style>
    .dropzone-container {
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        background-color: #f8f9fa;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }
    .dropzone-container:hover, .dropzone-container.dragover {
        border-color: #0d6efd;
        background-color: #e9ecef;
    }
    .dropzone-container input[type="file"] {
        display: none;
    }
    .dropzone-active {
        border-color: #0d6efd !important;
        background-color: #e9ecef !important;
    }
    .preview-container {
        display: none;
        margin-top: 15px;
    }
    .preview-container img {
        max-width: 100%;
        max-height: 200px;
        border-radius: 4px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .payment-item {
        position: relative;
        padding: 20px;
        border: 1px solid #dee2e6;
        border-radius: 12px;
        margin-bottom: 25px;
        background: #fff;
        transition: all 0.2s;
    }
    
    /* Date Picker Enlargement */
    input[type="date"].large-picker {
        height: 55px;
        font-size: 1.2rem;
        font-weight: 600;
        cursor: pointer;
        padding: 10px 15px;
    }
    
    input[type="date"].large-picker::-webkit-calendar-picker-indicator {
        width: 30px;
        height: 30px;
        cursor: pointer;
    }

    .payment-item:hover {
        border-color: #0d6efd;
        box-shadow: 0 5px 15px rgba(13,110,253,0.1);
    }
    .btn-remove-item {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 10;
    }
</style>

<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">Pembayaran Manual (Langsung Sukses)</h5>
                <div class="text-muted small">Scan barcode / ketik ID Anak / cari nama, lalu simpan pembayaran.</div>
            </div>
            <a href="<?= base_url('admin/pembayaran/riwayat') ?>" class="btn btn-outline-secondary btn-sm">Riwayat</a>
        </div>
        <div class="card-body">
            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-5 mb-4">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h6 class="mb-0">Cari Anak</h6>
                        </div>
                        <div class="card-body">
                            <label for="anakSearch" class="form-label">Cari (ID / Nama) atau Scan Barcode (ID Anak)</label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" id="anakSearch" placeholder="Contoh: 4 atau Nadihfa" autocomplete="off">
                                <button class="btn btn-primary" type="button" id="btnCari">Cari</button>
                            </div>
                            <div class="text-muted small mb-3">Tips: fokus ke input, scan barcode, lalu Enter.</div>

                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 80px;">ID</th>
                                            <th>Nama / Panggilan</th>
                                            <th>Orang Tua / HP</th>
                                            <th style="width: 80px;">Sisa</th>
                                            <th style="width: 90px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="anakResults">
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">Belum ada hasil</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7 mb-4">
                    <div class="card">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Input Pembayaran</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddPayment" disabled>+ Tambah Bukti Lain</button>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('admin/pembayaran/manual') ?>" method="post" id="manualForm" enctype="multipart/form-data">
                                <?= csrf_field() ?>
                                <input type="hidden" name="anak_id" id="anakId" value="">

                                <div class="mb-3 p-3 bg-light rounded border">
                                    <div class="row g-2 mb-2">
                                        <div class="col-sm-4">
                                            <div class="text-muted small">Anak</div>
                                            <div class="fw-semibold" id="selectedNama">-</div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="text-muted small">Jenis Les</div>
                                            <div class="fw-semibold" id="selectedLes">-</div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="text-muted small">Harga/Paket</div>
                                            <div class="fw-semibold" id="selectedHarga">-</div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="text-muted small">Sisa</div>
                                            <div class="fw-semibold" id="selectedSisa">-</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Riwayat Pembayaran List -->
                                    <div id="riwayatContainer" style="display: none;">
                                        <div class="text-muted small mb-1 border-top pt-2 mt-2">Riwayat Pembayaran Terakhir:</div>
                                        <div id="riwayatList" class="d-flex flex-wrap gap-1"></div>
                                    </div>
                                </div>

                                <div id="paymentItemsContainer">
                                    <!-- Dynamic Payment Items will be injected here -->
                                </div>

                                <div class="mb-3 mt-3 p-3 bg-light border rounded">
                                    <div class="text-muted small">Total Pembayaran (<span id="totalPaket">0</span> paket)</div>
                                    <div class="fs-4 fw-bold text-success" id="totalDisplay">Rp 0</div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-success px-4" id="btnSimpan" disabled>Simpan Semua (Sukses)</button>
                                    <button type="button" class="btn btn-outline-secondary" id="btnReset">Reset</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<template id="paymentItemTemplate">
    <div class="payment-item" data-index="{INDEX}">
        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-item" title="Hapus Item Ini">&times;</button>
        <h6 class="mb-3 text-primary border-bottom pb-2">Pembayaran Ke-{PEMBAYARAN_KE} (#{NUM})</h6>
        
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label fw-bold">Tanggal Pembayaran</label>
                <input type="date" name="tanggal[]" class="form-control large-picker shadow-sm" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Metode Pembayaran</label>
                <select class="form-select" name="metode_pembayaran[]" required>
                    <option value="transfer">Transfer</option>
                    <option value="tunai">Tunai</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Upload Bukti/Kwitansi (Opsional)</label>
            <div class="dropzone-container">
                <div class="dropzone-text">
                    <div style="font-size: 2rem; color: #6c757d; margin-bottom: 5px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" class="bi bi-cloud-arrow-up" viewBox="0 0 16 16">
                          <path fill-rule="evenodd" d="M7.646 5.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 6.707V10.5a.5.5 0 0 1-1 0V6.707L6.354 7.854a.5.5 0 1 1-.708-.708z"/>
                          <path d="M4.406 3.342A5.53 5.53 0 0 1 8 2c2.69 0 4.923 2 5.166 4.579C14.758 6.804 16 8.137 16 9.773 16 11.569 14.502 13 12.687 13H3.781C1.708 13 0 11.366 0 9.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383m.653.757c-.757.653-1.153 1.44-1.153 2.056v.448l-.445.049C2.064 6.805 1 7.952 1 9.318 1 10.785 2.23 12 3.781 12h8.906C13.98 12 15 10.988 15 9.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 4.825 10.328 3 8 3a4.53 4.53 0 0 0-2.941 1.1z"/>
                        </svg>
                    </div>
                    <p class="mb-0 small text-secondary">
                        Klik area ini lalu <strong>Paste (Ctrl+V)</strong><br>
                        atau <button type="button" class="btn btn-link p-0 m-0 text-decoration-none btn-browse">Klik di sini</button> untuk pilih file manual
                    </p>
                </div>
                <input type="file" name="bukti_pembayaran[]" accept="image/*" class="file-input">
                <div class="preview-container">
                    <img src="" alt="Preview">
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-image" style="position: relative; z-index: 2;">Hapus Gambar</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-2">
            <input type="text" class="form-control form-control-sm" name="catatan[]" placeholder="Catatan opsional...">
        </div>
    </div>
</template>

<script>
    (function () {
        var $search = document.getElementById('anakSearch');
        var $btnCari = document.getElementById('btnCari');
        var $results = document.getElementById('anakResults');
        var $anakId = document.getElementById('anakId');
        var $selectedNama = document.getElementById('selectedNama');
        var $selectedLes = document.getElementById('selectedLes');
        var $selectedHarga = document.getElementById('selectedHarga');
        var $selectedSisa = document.getElementById('selectedSisa');
        var $total = document.getElementById('totalDisplay');
        var $totalPaket = document.getElementById('totalPaket');
        var $btnSimpan = document.getElementById('btnSimpan');
        var $btnReset = document.getElementById('btnReset');
        var $btnAddPayment = document.getElementById('btnAddPayment');
        var $container = document.getElementById('paymentItemsContainer');
        var $riwayatContainer = document.getElementById('riwayatContainer');
        var $riwayatList = document.getElementById('riwayatList');
        var templateHtml = document.getElementById('paymentItemTemplate').innerHTML;

        var selected = null;
        var paymentCount = 0;
        var totalSblmnya = 0;
        var activeDropzone = null; // Track which dropzone was clicked/focused for paste

        function formatRupiah(n) {
            var num = Number(n || 0);
            return 'Rp ' + num.toLocaleString('id-ID');
        }

        function renderEmpty(msg) {
            $results.innerHTML = '<tr><td colspan="5" class="text-center text-muted">' + msg + '</td></tr>';
        }

        function renderRows(rows) {
            if (!rows || !rows.length) {
                renderEmpty('Tidak ada hasil');
                return;
            }

            var html = '';
            rows.forEach(function (r) {
                var sisa = (r.sisa_pertemuan === null || r.sisa_pertemuan === undefined) ? '-' : String(r.sisa_pertemuan);
                var namaDisplay = (r.nama || '-') + (r.nama_panggilan ? ' <span class="text-muted small">(' + r.nama_panggilan + ')</span>' : '');
                var parentDisplay = (r.nama_parent || '-') + (r.whatsapp ? ' <br><span class="text-muted small">' + r.whatsapp + '</span>' : '');
                
                html += '<tr>' +
                    '<td>' + r.id + '</td>' +
                    '<td>' + namaDisplay + '</td>' +
                    '<td>' + parentDisplay + '</td>' +
                    '<td>' + sisa + '</td>' +
                    '<td class="text-end"><button type="button" class="btn btn-sm btn-outline-primary" data-pick="' + r.id + '">Pilih</button></td>' +
                '</tr>';
            });
            $results.innerHTML = html;
        }

        function updateTotal() {
            var items = document.querySelectorAll('.payment-item').length;
            var harga = selected ? Number(selected.harga || 0) : 0;
            // 1 Paket = 4 pertemuan. Harga di DB adalah harga 1 pertemuan. Jadi harga 1 paket = harga * 4
            var hargaPaket = harga * 4; 
            
            $totalPaket.textContent = items;
            $total.textContent = formatRupiah(hargaPaket * items);
            
            if (items === 0) {
                $btnSimpan.disabled = true;
            } else if (selected) {
                $btnSimpan.disabled = false;
            }
        }

        function setupPaymentItem(itemEl) {
            var fileInput = itemEl.querySelector('.file-input');
            var dropzone = itemEl.querySelector('.dropzone-container');
            var dropzoneText = itemEl.querySelector('.dropzone-text');
            var previewContainer = itemEl.querySelector('.preview-container');
            var imgPreview = itemEl.querySelector('img');
            var btnRemoveImage = itemEl.querySelector('.btn-remove-image');
            var btnRemoveItem = itemEl.querySelector('.btn-remove-item');
            var btnBrowse = itemEl.querySelector('.btn-browse');

            function handleFiles(files) {
                if (files && files.length > 0) {
                    var file = files[0];
                    if (file.type.startsWith('image/')) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            imgPreview.src = e.target.result;
                            previewContainer.style.display = 'block';
                            dropzoneText.style.display = 'none';
                        }
                        reader.readAsDataURL(file);

                        var dt = new DataTransfer();
                        dt.items.add(file);
                        fileInput.files = dt.files;
                    } else {
                        alert('Hanya file gambar yang diperbolehkan.');
                    }
                }
            }

            fileInput.addEventListener('change', function() {
                handleFiles(this.files);
            });

            dropzone.addEventListener('dragover', function(e) {
                e.preventDefault();
                dropzone.classList.add('dragover');
            });

            dropzone.addEventListener('dragleave', function(e) {
                e.preventDefault();
                dropzone.classList.remove('dragover');
            });

            dropzone.addEventListener('drop', function(e) {
                e.preventDefault();
                dropzone.classList.remove('dragover');
                handleFiles(e.dataTransfer.files);
            });

            btnBrowse.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                fileInput.click();
            });

            // Set active dropzone on click so paste works on the right one
            dropzone.addEventListener('click', function() {
                document.querySelectorAll('.dropzone-container').forEach(function(dz) {
                    dz.classList.remove('dropzone-active');
                });
                dropzone.classList.add('dropzone-active');
                activeDropzone = handleFiles;
            });

            btnRemoveImage.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                fileInput.value = '';
                imgPreview.src = '';
                previewContainer.style.display = 'none';
                dropzoneText.style.display = 'block';
            });

            btnRemoveItem.addEventListener('click', function() {
                itemEl.remove();
                updateTotal();
                
                // Rename numbering and "Pembayaran Ke"
                var items = document.querySelectorAll('.payment-item h6');
                items.forEach(function(h6, idx) {
                    var pKe = totalSblmnya + idx + 1;
                    h6.textContent = 'Pembayaran Ke-' + pKe + ' (#' + (idx + 1) + ')';
                });
            });
            
            // Set as active dropzone when created
            document.querySelectorAll('.dropzone-container').forEach(function(dz) {
                dz.classList.remove('dropzone-active');
            });
            dropzone.classList.add('dropzone-active');
            activeDropzone = handleFiles;
        }

        function addPaymentItem() {
            paymentCount++;
            var currentItemsCount = document.querySelectorAll('.payment-item').length;
            var pembayaranKe = totalSblmnya + currentItemsCount + 1;
            
            var html = templateHtml
                .replace('{INDEX}', paymentCount)
                .replace('{NUM}', currentItemsCount + 1)
                .replace('{PEMBAYARAN_KE}', pembayaranKe);
            
            var div = document.createElement('div');
            div.innerHTML = html;
            var newEl = div.firstElementChild;
            $container.appendChild(newEl);
            
            setupPaymentItem(newEl);
            updateTotal();
            
            // Hide remove button if only 1 item
            var items = document.querySelectorAll('.payment-item');
            if(items.length === 1) {
                items[0].querySelector('.btn-remove-item').style.display = 'none';
            } else {
                items.forEach(function(it, idx) {
                    it.querySelector('.btn-remove-item').style.display = 'block';
                });
            }
        }

        document.addEventListener('paste', function(e) {
            if (e.clipboardData && e.clipboardData.files && e.clipboardData.files.length > 0) {
                var target = e.target;
                if (target.tagName === 'INPUT' && (target.type === 'text' || target.type === 'date')) {
                    return;
                }
                // Jika tidak ada activeDropzone tapi ada item, set ke item terakhir
                if (!activeDropzone) {
                    var items = document.querySelectorAll('.payment-item');
                    if(items.length > 0) {
                        var lastItem = items[items.length - 1];
                        var fileInput = lastItem.querySelector('.file-input');
                        var imgPreview = lastItem.querySelector('img');
                        var previewContainer = lastItem.querySelector('.preview-container');
                        var dropzoneText = lastItem.querySelector('.dropzone-text');
                        
                        activeDropzone = function(files) {
                            if (files && files.length > 0) {
                                var file = files[0];
                                if (file.type.startsWith('image/')) {
                                    var reader = new FileReader();
                                    reader.onload = function(e) {
                                        imgPreview.src = e.target.result;
                                        previewContainer.style.display = 'block';
                                        dropzoneText.style.display = 'none';
                                    }
                                    reader.readAsDataURL(file);
                                    var dt = new DataTransfer();
                                    dt.items.add(file);
                                    fileInput.files = dt.files;
                                }
                            }
                        };
                    }
                }

                if (activeDropzone) {
                    activeDropzone(e.clipboardData.files);
                }
            }
        });

        $btnAddPayment.addEventListener('click', function() {
            addPaymentItem();
        });

        function pickById(id, rows) {
            var picked = null;
            for (var i = 0; i < rows.length; i++) {
                if (String(rows[i].id) === String(id)) {
                    picked = rows[i];
                    break;
                }
            }
            if (!picked && rows && rows.length === 1) picked = rows[0];
            if (!picked) return;

            selected = picked;
            $anakId.value = picked.id;
            totalSblmnya = Number(picked.total_pembayaran_sebelumnya || 0);
            
            var displayNama = (picked.nama || '-') + (picked.nama_panggilan ? ' (' + picked.nama_panggilan + ')' : '') + ' [#' + picked.id + ']';
            var displayParent = (picked.nama_parent || '-') + (picked.whatsapp ? ' - ' + picked.whatsapp : '');
            
            $selectedNama.innerHTML = displayNama + '<br><span class="text-muted small">' + displayParent + '</span>';
            $selectedLes.textContent = picked.nama_les || '-';
            
            var hargaPaket = picked.harga ? (Number(picked.harga) * 4) : 0;
            $selectedHarga.textContent = formatRupiah(hargaPaket);
            
            $selectedSisa.textContent = (picked.sisa_pertemuan === null || picked.sisa_pertemuan === undefined) ? '-' : String(picked.sisa_pertemuan);
            
            // Tampilkan Riwayat Kecil
            if (picked.riwayat_pembayaran && picked.riwayat_pembayaran.length > 0) {
                $riwayatContainer.style.display = 'block';
                var rHtml = '';
                picked.riwayat_pembayaran.forEach(function(rh, idx) {
                    var tgl = new Date(rh.tanggal).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                    var pKe = totalSblmnya - idx;
                    rHtml += '<span class="badge bg-white border text-dark fw-normal" style="font-size: 0.75rem;">' + 
                             pKe + '. ' + tgl + ' (' + (rh.nama_les || picked.nama_les || 'Reguler') + ')</span>';
                });
                $riwayatList.innerHTML = rHtml;
            } else {
                $riwayatContainer.style.display = 'none';
                $riwayatList.innerHTML = '';
            }

            $btnAddPayment.disabled = false;
            
            // Reset items if different child
            $container.innerHTML = '';
            paymentCount = 0;
            
            addPaymentItem();
        }

        function search(q, autoPickId) {
            if (!q) {
                renderEmpty('Masukkan kata kunci');
                return;
            }

            renderEmpty('Mencari...');
            fetch('<?= base_url('admin/pembayaran/manual-search') ?>?q=' + encodeURIComponent(q), {
                headers: { 'Accept': 'application/json' }
            })
            .then(function (r) { return r.json(); })
            .then(function (json) {
                var rows = (json && json.data) ? json.data : [];
                renderRows(rows);
                if (autoPickId) pickById(autoPickId, rows);
            })
            .catch(function () {
                renderEmpty('Gagal mencari data');
            });
        }

        $btnCari.addEventListener('click', function () {
            var q = $search.value.trim();
            search(q, null);
        });

        $search.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                var q = $search.value.trim();
                var autoPickId = (/^\d+$/).test(q) ? q : null;
                search(q, autoPickId);
            }
        });

        $results.addEventListener('click', function (e) {
            var btn = e.target.closest ? e.target.closest('button[data-pick]') : null;
            if (!btn) return;
            var id = btn.getAttribute('data-pick');
            if (!id) return;
            search(String(id), String(id));
        });

        $btnReset.addEventListener('click', function () {
            selected = null;
            $anakId.value = '';
            $selectedNama.textContent = '-';
            $selectedLes.textContent = '-';
            $selectedHarga.textContent = '-';
            $selectedSisa.textContent = '-';
            $btnSimpan.disabled = true;
            $btnAddPayment.disabled = true;
            $total.textContent = 'Rp 0';
            $totalPaket.textContent = '0';
            $search.value = '';
            $container.innerHTML = ''; // Hapus semua payment items
            paymentCount = 0;
            renderEmpty('Belum ada hasil');
            $search.focus();
        });

        window.setTimeout(function () { $search.focus(); }, 100);
    })();
</script>

<?= $this->include('admin/templates/footer') ?>
