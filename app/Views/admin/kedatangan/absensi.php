<?= $this->include('admin/templates/header') ?>

<!-- Remove the duplicate DataTables includes and add proper order of scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css">
<script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.id.min.js"></script>

<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                Absensi Kedatangan - <?= date('d-m-Y', strtotime($jadwal['tanggal'])) ?>
                (<?= date('H:i', strtotime($jadwal['jam_mulai'])) ?> - <?= date('H:i', strtotime($jadwal['jam_selesai'])) ?>)
            </h5>
            <div>
                <a href="<?= base_url('admin/kedatangan/export-excel/' . $jadwal['id']) ?>" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                <a href="<?= base_url('admin/kedatangan/cetak-laporan/' . $jadwal['id']) ?>" class="btn btn-danger" target="_blank">
                    <i class="fas fa-file-pdf"></i> Download PDF
                </a>
                <button type="button" class="btn btn-info" id="sortBtn" onclick="sortTableByName()">
                    <i class="fas fa-sort-alpha-down"></i> Urutkan Nama
                </button>
                <button type="button" class="btn btn-warning" id="finishBtn" onclick="handleSaveClick('selesai')">
                    <i class="fas fa-flag-checkered"></i> Selesaikan Jadwal
                </button>
                <?php if (($jadwal['status'] ?? '') === 'selesai'): ?>
                    <a href="<?= base_url('admin/kedatangan/buka/' . $jadwal['id']) ?>" class="btn btn-outline-primary">
                        <i class="fas fa-undo"></i> Buka Lagi
                    </a>
                <?php endif; ?>
                <a href="<?= base_url('admin/kedatangan/tambah-peserta-manual-form/' . $jadwal['id']) ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Peserta Manual
                </a>
                <a href="<?= base_url('admin/kedatangan') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
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

            <div class="card mb-3">
                <div class="card-body">
                    <form id="checkinForm" action="<?= base_url('admin/kedatangan/checkin') ?>" method="post" class="row g-2 align-items-end" onsubmit="disableCheckinButton()">
                        <?= csrf_field() ?>
                        <input type="hidden" name="jadwal_id" value="<?= $jadwal['id'] ?>">
                        <div class="col-md-6">
                            <label for="anak_id_scan" class="form-label">Check-in Dadakan (Scan Barcode / ID Anak)</label>
                            <input type="text" class="form-control" id="anak_id_scan" name="anak_id_scan" placeholder="Scan barcode (ID anak) lalu Enter" autocomplete="off">
                            <div class="form-text">Sistem otomatis menambahkan peserta ke jadwal dan menandai hadir.</div>
                        </div>
                        <div class="col-md-4">
                            <label for="anak_nama_search" class="form-label">Cari Nama Anak</label>
                            <input type="text" class="form-control" id="anak_nama_search" placeholder="Ketik nama / nama panggilan..." autocomplete="off">
                            <div class="position-relative">
                                <div class="list-group position-absolute w-100" id="anakSearchResults" style="z-index: 5; display: none;"></div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Check-in</button>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">
                                Jika sisa pertemuan habis, lakukan pembayaran dulu (bisa via Pembayaran Manual).
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body py-2 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Total Keseluruhan</h6>
                                <small>Semua Peserta</small>
                            </div>
                            <h3 class="mb-0" id="summaryTotal"><?= $summary['total'] ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-danger text-white">
                        <div class="card-body py-2 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Jumlah Private</h6>
                                <small>Khusus Private</small>
                            </div>
                            <h3 class="mb-0" id="summaryPrivate"><?= $summary['private'] ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body py-2 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Jumlah Reguler</h6>
                                <small>Reguler / Group</small>
                            </div>
                            <h3 class="mb-0" id="summaryReguler"><?= $summary['reguler'] ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form untuk menyimpan semua data -->
            <form id="absensiForm" action="<?= base_url('admin/kedatangan/save-all-absensi') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="jadwal_id" value="<?= $jadwal['id'] ?>">
                <input type="hidden" name="aksi" id="aksiInput" value="simpan">
                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="absensiTable">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 5%">No</th>
                                <th class="text-center" style="width: 8%">ID</th>
                                <th style="width: 30%">Nama Anak</th>
                                <th style="width: 15%">Jenis Les</th>
                                <th style="width: 30%">Status Kehadiran</th>
                                <th class="text-center" style="width: 12%">Hapus</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($peserta)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada peserta terdaftar</td>
                                </tr>
                            <?php else: ?>
                                <?php $i = 1; foreach($peserta as $p): ?>
                                <?php 
                                    $kehadiran_siswa = array_filter($kehadiran, function($k) use ($p) {
                                        return $k['anak_id'] == $p['id'];
                                    });
                                    $kehadiran_siswa = !empty($kehadiran_siswa) ? reset($kehadiran_siswa) : null;
                                    
                                    // Deteksi Private
                                    $isPrivate = str_contains(strtolower($p['jenis_les_nama'] ?? ''), 'private');
                                ?>
                                <tr class="<?= $isPrivate ? 'table-danger' : '' ?>">
                                    <td class="text-center"><?= $i++ ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">#<?= $p['id'] ?></span>
                                    </td>
                                    <td><?= $p['nama'] ?></td>
                                    <td>
                                        <?php if (!empty($p['jenis_les_nama'])): ?>
                                            <span class="badge <?= $isPrivate ? 'bg-danger' : 'bg-info text-dark' ?>">
                                                <?= $p['jenis_les_nama'] ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Belum terdaftar</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <select class="form-select form-select-sm" name="status_kehadiran[<?= $p['id'] ?>]" required style="width: auto">
                                                <option value="hadir" <?= ($kehadiran_siswa['status_kehadiran'] ?? '') == 'hadir' ? 'selected' : '' ?>>Hadir</option>
                                                <option value="tidak_hadir" <?= ($kehadiran_siswa['status_kehadiran'] ?? '') == 'tidak_hadir' ? 'selected' : '' ?>>Tidak Hadir</option>
                                                <option value="izin" <?= ($kehadiran_siswa['status_kehadiran'] ?? '') == 'izin' ? 'selected' : '' ?>>Izin</option>
                                            </select>
                                            <span class="badge bg-info">
                                                Pkt:<?= $p['paket_ke'] ?? 1 ?> Ke-<?= $p['pertemuan_ke'] ?? 0 ?> (Sisa: <?= $p['sisa_pertemuan_display'] ?? 0 ?>)
                                            </span>
                                        </div>
                                        <input type="hidden" name="anak_id[]" value="<?= $p['id'] ?>">
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('admin/kedatangan/delete-peserta/' . $jadwal['id'] . '/' . $p['id']) ?>" 
                                            class="text-danger">
                                            <i class="fas fa-times-circle fa-lg"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->include('admin/templates/footer') ?>

<!-- Modal Tambah Peserta Manual -->
<div class="modal fade" id="tambahPesertaManualModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Peserta Manual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/kedatangan/tambah-peserta-manual') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <input type="hidden" name="jadwal_id" value="<?= $jadwal['id'] ?>">
                    <div class="mb-3">
                        <label for="search_id" class="form-label">Cari ID Anak</label>
                        <input type="text" class="form-control mb-2" id="search_id" placeholder="Masukkan ID anak...">
                    </div>
                    <div class="mb-3">
                        <label for="anak_id" class="form-label">Pilih Anak</label>
                        <select class="form-select" id="anak_id" name="anak_id" required>
                            <option value="">Pilih Anak</option>
                            <?php foreach($semua_anak as $a): ?>
                            <option value="<?= $a['id'] ?>" data-id="<?= $a['id'] ?>">
                                <?= $a['nama'] ?> (<?= $a['nama_panggilan'] ?>) - ID: <?= $a['id'] ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Pindahkan semua script ke sini -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.id.min.js"></script>

<script>
function disableCheckinButton() {
    const btn = document.querySelector('#checkinForm button[type="submit"]');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
    }
}

function sortTableByName() {
    var table, rows, switching, i, x, y, shouldSwitch;
    table = document.getElementById("absensiTable");
    switching = true;
    while (switching) {
        switching = false;
        rows = table.rows;
        // Start from index 1 (skip header)
        for (i = 1; i < (rows.length - 1); i++) {
            shouldSwitch = false;
            // Get cells for Nama Anak (Index 2)
            x = rows[i].getElementsByTagName("TD")[2];
            y = rows[i + 1].getElementsByTagName("TD")[2];
            if (x && y) {
                if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                    shouldSwitch = true;
                    break;
                }
            }
        }
        if (shouldSwitch) {
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
        }
    }
    
    // Perbarui kolom nomor setelah sorting selesai
    rows = table.rows;
    for (i = 1; i < rows.length; i++) {
        // Cek apakah baris tersebut bukan baris "Tidak ada peserta"
        if (rows[i].getElementsByTagName("TD").length > 1) {
            rows[i].getElementsByTagName("TD")[0].innerHTML = i;
        }
    }
}

function handleSaveClick(aksi) {
    if (aksi === 'selesai') {
        if (!confirm('Selesaikan jadwal sekarang? Status jadwal akan berubah menjadi selesai.')) {
            return;
        }
        var aksiInput = document.getElementById('aksiInput');
        if (aksiInput) aksiInput.value = 'selesai';
        document.getElementById('absensiForm').submit();
        return;
    }

    // Untuk aksi simpan biasa (jika masih ada tombolnya nanti)
    var selectedStudents = document.querySelectorAll('input[name="selected_students[]"]:checked');
    if(selectedStudents.length === 0 && aksi !== 'selesai') {
        alert('Pilih minimal satu siswa untuk disimpan!');
        return;
    }

    var aksiInput = document.getElementById('aksiInput');
    if (aksiInput) aksiInput.value = aksi || 'simpan';
    
    // Submit form
    document.getElementById('absensiForm').submit();
}

// Fitur Real-time Auto Refresh
let lastDataHash = "";

function refreshAbsensiData() {
    const jadwalId = "<?= $jadwal['id'] ?>";
    fetch(`<?= base_url('admin/kedatangan/get-absensi-data') ?>/${jadwalId}`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        const currentHash = JSON.stringify(data);
        if (currentHash === lastDataHash) return; // Tidak ada perubahan
        
        lastDataHash = currentHash;
        updateAbsensiTable(data);
    })
    .catch(error => console.error('Error refreshing data:', error));
}

function updateAbsensiTable(data) {
    const tbody = document.querySelector('#absensiTable tbody');
    const summaryTotal = document.getElementById('summaryTotal');
    const summaryPrivate = document.getElementById('summaryPrivate');
    const summaryReguler = document.getElementById('summaryReguler');

    // Update Summary Cards
    if (summaryTotal) summaryTotal.innerText = data.summary.total;
    if (summaryPrivate) summaryPrivate.innerText = data.summary.private;
    if (summaryReguler) summaryReguler.innerText = data.summary.reguler;

    if (data.peserta.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">Tidak ada peserta terdaftar</td></tr>';
        return;
    }

    let html = '';
    data.peserta.forEach((p, index) => {
        const kehadiranSiswa = data.kehadiran.find(k => k.anak_id == p.id);
        const status = kehadiranSiswa ? kehadiranSiswa.status_kehadiran : '';
        const isPrivate = (p.jenis_les_nama || '').toLowerCase().includes('private');
        
        html += `
        <tr class="${isPrivate ? 'table-danger' : ''}">
            <td class="text-center">${index + 1}</td>
            <td class="text-center">
                <span class="badge bg-secondary">#${p.id}</span>
            </td>
            <td>${p.nama}</td>
            <td>
                ${p.jenis_les_nama ? 
                    `<span class="badge ${isPrivate ? 'bg-danger' : 'bg-info text-dark'}">${p.jenis_les_nama}</span>` : 
                    '<span class="badge bg-secondary">Belum terdaftar</span>'
                }
            </td>
            <td>
                <div class="d-flex align-items-center gap-2">
                    <select class="form-select form-select-sm" name="status_kehadiran[${p.id}]" required style="width: auto">
                        <option value="hadir" ${status === 'hadir' ? 'selected' : ''}>Hadir</option>
                        <option value="tidak_hadir" ${status === 'tidak_hadir' ? 'selected' : ''}>Tidak Hadir</option>
                        <option value="izin" ${status === 'izin' ? 'selected' : ''}>Izin</option>
                    </select>
                    <span class="badge bg-info">
                        Pkt:${p.paket_ke || 1} Ke-${p.pertemuan_ke || 0} (Sisa: ${p.sisa_pertemuan_display || 0})
                    </span>
                </div>
                <input type="hidden" name="anak_id[]" value="${p.id}">
            </td>
            <td class="text-center">
                <a href="<?= base_url('admin/kedatangan/delete-peserta/' . $jadwal['id']) ?>/${p.id}" class="text-danger">
                    <i class="fas fa-times-circle fa-lg"></i>
                </a>
            </td>
        </tr>`;
    });
    tbody.innerHTML = html;
}

// Jalankan refresh setiap 30 detik (sebagai backup saja)
setInterval(refreshAbsensiData, 30000);

// INTEGRASI PUSHER REAL-TIME
</script>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    // Inisialisasi Pusher
    const pusher = new Pusher('28be70c660847570524b', {
        cluster: 'ap1'
    });

    // Berlangganan ke channel
    const channel = pusher.subscribe('absensi-channel');

    // Dengar event update
    channel.bind('update-event', function(data) {
        const currentJadwalId = "<?= $jadwal['id'] ?>";
        console.log('Pusher Event Received:', data);
        
        // Hanya refresh jika jadwal yang di-update sama dengan yang sedang dibuka
        if (String(data.jadwal_id) === String(currentJadwalId)) {
            refreshAbsensiData();
        }
    });

    // Simpan hash awal
    lastDataHash = JSON.stringify({
        peserta: <?= json_encode($peserta) ?>,
        kehadiran: <?= json_encode($kehadiran) ?>,
        summary: <?= json_encode($summary) ?>
    });
</script>

<script>
(function() {
    var input = document.getElementById('anak_nama_search');
    var results = document.getElementById('anakSearchResults');
    var scanInput = document.getElementById('anak_id_scan');

    if (!input || !results || !scanInput) return;

    var timer = null;

    function hide() {
        results.style.display = 'none';
        results.innerHTML = '';
    }

    function render(rows) {
        if (!rows || !rows.length) {
            hide();
            return;
        }

        var html = '';
        rows.forEach(function(r) {
            var label = (r.nama || '-') + (r.nama_panggilan ? (' (' + r.nama_panggilan + ')') : '');
            var sisa = (r.sisa_pertemuan === null || r.sisa_pertemuan === undefined) ? '-' : String(r.sisa_pertemuan);
            html += '<button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" data-id="' + r.id + '">' +
                '<span><strong>#' + r.id + '</strong> ' + label + '</span>' +
                '<span class="badge bg-info text-dark">Sisa: ' + sisa + '</span>' +
            '</button>';
        });
        results.innerHTML = html;
        results.style.display = 'block';
    }

    function search(q) {
        fetch('<?= base_url('admin/kedatangan/search-anak') ?>?q=' + encodeURIComponent(q), { headers: { 'Accept': 'application/json' } })
            .then(function(r) { return r.json(); })
            .then(function(json) {
                render((json && json.data) ? json.data : []);
            })
            .catch(function() { hide(); });
    }

    input.addEventListener('input', function() {
        var q = input.value.trim();
        if (timer) window.clearTimeout(timer);
        if (q.length < 2) {
            hide();
            return;
        }
        timer = window.setTimeout(function() { search(q); }, 200);
    });

    results.addEventListener('click', function(e) {
        var btn = e.target.closest ? e.target.closest('button[data-id]') : null;
        if (!btn) return;
        var id = btn.getAttribute('data-id');
        if (!id) return;
        scanInput.value = String(id);
        hide();
        document.querySelector('form[action*="kedatangan/checkin"]').submit();
    });

    document.addEventListener('click', function(e) {
        if (e.target === input || results.contains(e.target)) return;
        hide();
    });
})();
</script>
</body>
</html>

<style>
/* Tambahkan CSS untuk memperbaiki tampilan */
.table th {
    font-weight: 600;
    vertical-align: middle;
}
.table td {
    vertical-align: middle;
}
.form-select-sm {
    min-width: 120px;
}
.badge {
    font-weight: 500;
}
textarea.form-control-sm {
    font-size: 0.875rem;
    padding: 0.25rem 0.5rem;
}
</style>
