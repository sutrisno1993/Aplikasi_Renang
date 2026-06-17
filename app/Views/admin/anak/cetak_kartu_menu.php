<?= $this->include('admin/templates/header') ?>

<style>
    .picked-item {
        border: 1px solid #e9ecef;
        padding: 10px 15px;
        border-radius: 8px;
        margin-bottom: 10px;
        background-color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s;
    }
    .picked-item:hover {
        border-color: #0d6efd;
    }
    .btn-remove-pick {
        color: #dc3545;
        background: none;
        border: none;
        font-size: 1.2rem;
        cursor: pointer;
        padding: 0 5px;
    }
    .btn-remove-pick:hover {
        color: #a71d2a;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-5 mb-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Cari Anak untuk Dicetak Kartunya</h6>
                </div>
                <div class="card-body">
                    <label for="anakSearch" class="form-label">Cari (ID / Nama) atau Scan Barcode</label>
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" id="anakSearch" placeholder="Contoh: 4 atau Nadihfa" autocomplete="off">
                        <button class="btn btn-primary" type="button" id="btnCari">Cari</button>
                    </div>
                    <div class="text-muted small mb-3">Tekan Enter untuk mencari cepat.</div>

                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">ID</th>
                                    <th>Nama</th>
                                    <th>Jenis Les</th>
                                    <th style="width: 90px;"></th>
                                </tr>
                            </thead>
                            <tbody id="anakResults">
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Belum ada hasil pencarian</td>
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
                    <h6 class="mb-0">Daftar Anak Terpilih</h6>
                    <span class="badge bg-primary rounded-pill" id="totalPicked">0 anak</span>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('admin/cetak-kartu/print') ?>" method="post" target="_blank" id="formCetak">
                        <?= csrf_field() ?>
                        
                        <div id="pickedItemsContainer">
                            <div class="text-center text-muted py-4" id="emptyPickedState">
                                Belum ada anak yang dipilih.<br>Silakan cari dan pilih anak dari panel sebelah kiri.
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4 border-top pt-3">
                            <button type="submit" class="btn btn-success" id="btnProsesCetak" disabled>
                                <i class="fas fa-print me-1"></i> Cetak Kartu Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<template id="pickedItemTemplate">
    <div class="picked-item" data-id="{ID}">
        <div>
            <div class="fw-bold">{NAMA}</div>
            <div class="small text-muted">ID: {ID} &nbsp;|&nbsp; Les: {LES}</div>
            <input type="hidden" name="anak_id[]" value="{ID}">
        </div>
        <button type="button" class="btn-remove-pick" title="Hapus dari daftar cetak">&times;</button>
    </div>
</template>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var searchInput = document.getElementById('anakSearch');
        var btnCari = document.getElementById('btnCari');
        var resultsBody = document.getElementById('anakResults');
        var pickedContainer = document.getElementById('pickedItemsContainer');
        var emptyState = document.getElementById('emptyPickedState');
        var btnProsesCetak = document.getElementById('btnProsesCetak');
        var totalPickedEl = document.getElementById('totalPicked');
        var templateHtml = document.getElementById('pickedItemTemplate').innerHTML;
        
        var pickedIds = new Set(); // To prevent duplicates

        function updateUI() {
            var count = pickedIds.size;
            totalPickedEl.textContent = count + ' anak';
            
            if (count > 0) {
                emptyState.style.display = 'none';
                btnProsesCetak.disabled = false;
            } else {
                emptyState.style.display = 'block';
                btnProsesCetak.disabled = true;
            }
        }

        function addPickedItem(id, nama, les) {
            id = String(id);
            if (pickedIds.has(id)) {
                // Flash animation for existing
                var existing = pickedContainer.querySelector('.picked-item[data-id="' + id + '"]');
                if (existing) {
                    existing.style.borderColor = '#28a745';
                    existing.style.backgroundColor = '#d4edda';
                    setTimeout(function() {
                        existing.style.borderColor = '#e9ecef';
                        existing.style.backgroundColor = '#fff';
                    }, 500);
                }
                return;
            }

            pickedIds.add(id);
            var html = templateHtml
                        .replace(/{ID}/g, id)
                        .replace(/{NAMA}/g, nama)
                        .replace(/{LES}/g, les || 'Reguler');
            
            pickedContainer.insertAdjacentHTML('beforeend', html);
            updateUI();
        }

        // Handle remove pick
        pickedContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-remove-pick')) {
                var item = e.target.closest('.picked-item');
                var id = item.getAttribute('data-id');
                pickedIds.delete(String(id));
                item.remove();
                updateUI();
            }
        });

        // Search logic
        function renderEmpty(msg) {
            resultsBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">' + msg + '</td></tr>';
        }

        function doSearch() {
            var q = searchInput.value.trim();
            if (!q) {
                renderEmpty('Masukkan kata kunci pencarian');
                return;
            }

            resultsBody.innerHTML = '<tr><td colspan="4" class="text-center">Mencari...</td></tr>';
            
            // We use the existing manualSearch endpoint from Pembayaran
            fetch('<?= base_url('admin/pembayaran/manual-search') ?>?q=' + encodeURIComponent(q))
                .then(res => res.json())
                .then(data => {
                    var rows = data.data || [];
                    if (rows.length === 0) {
                        renderEmpty('Tidak ada data anak ditemukan');
                        return;
                    }

                    var html = '';
                    rows.forEach(function(r) {
                        html += `<tr>
                            <td>${r.id}</td>
                            <td>${r.nama || '-'}</td>
                            <td>${r.nama_les || '-'}</td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary btn-pick" 
                                    data-id="${r.id}" 
                                    data-nama="${r.nama}" 
                                    data-les="${r.nama_les}">Pilih</button>
                            </td>
                        </tr>`;
                    });
                    resultsBody.innerHTML = html;
                })
                .catch(err => {
                    renderEmpty('Terjadi kesalahan pencarian');
                });
        }

        btnCari.addEventListener('click', doSearch);
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                doSearch();
            }
        });

        // Handle pick button
        resultsBody.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-pick')) {
                var btn = e.target;
                addPickedItem(
                    btn.getAttribute('data-id'),
                    btn.getAttribute('data-nama'),
                    btn.getAttribute('data-les')
                );
            }
        });
    });
</script>

<?= $this->include('admin/templates/footer') ?>
