<?= $this->include('admin/templates/header') ?>

<style>
.bukti-img {
    cursor: pointer;
    transition: transform 0.2s, opacity 0.2s;
}
.bukti-img:hover {
    opacity: 0.8;
    transform: scale(1.05);
}
.boss-accordion-btn {
    text-align: left;
    font-weight: 600;
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0;
    border: none;
    background: transparent;
}
.boss-accordion-btn:focus {
    box-shadow: none;
    text-decoration: none;
}
.boss-accordion-btn:hover {
    text-decoration: none;
}
.month-accordion-card {
    transition: all 0.3s ease;
}
.month-accordion-card:hover {
    transform: translateY(-2px);
}
.final-seal-container {
    border: 2px dashed #93c5fd !important;
    background: linear-gradient(180deg, #f8fafc 0%, #eff6ff 100%);
    border-radius: 16px;
}
</style>

<!-- Pembayaran Content -->
<div class="container-fluid">
    <?php if (session()->get('role') === 'boss'): ?>
        <!-- ================================================================= -->
        <!--                   DEDICATED BOSS GROUPED ACCORDION VIEW           -->
        <!-- ================================================================= -->

        <!-- Rekapan Keuangan (Double Approval Only) -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-primary text-white rounded-4">
                    <div class="card-body p-4 text-center">
                        <h6 class="text-uppercase small fw-bold opacity-75">Total Diapprove (Sesi Ini)</h6>
                        <h3 class="mb-0 fw-bold" id="top-bar-total">Rp <?= number_format($rekap['total'] ?? 0, 0, ',', '.') ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-info text-white rounded-4">
                    <div class="card-body p-4 text-center">
                        <h6 class="text-uppercase small fw-bold opacity-75">Bagian Coach (Sesi Ini)</h6>
                        <h3 class="mb-0 fw-bold" id="top-bar-coach">Rp <?= number_format($rekap['coach'] ?? 0, 0, ',', '.') ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-success text-white rounded-4">
                    <div class="card-body p-4 text-center">
                        <h6 class="text-uppercase small fw-bold opacity-75">Bagian Owner (Sesi Ini)</h6>
                        <h3 class="mb-0 fw-bold" id="top-bar-owner">Rp <?= number_format($rekap['owner'] ?? 0, 0, ',', '.') ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="mb-0 fw-bold text-primary"><i class="fas fa-wallet me-2"></i>Validasi & Approval Transfer Bulanan</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($groupedPending)): ?>
                    <div class="accordion" id="bossMonthAccordion">
                        <?php foreach ($groupedPending as $mKey => $group): ?>
                            <div class="card border shadow-sm rounded-4 mb-3 month-accordion-card" id="card-month-<?= $mKey ?>">
                                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center" id="heading-<?= $mKey ?>">
                                    <button class="boss-accordion-btn" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $mKey ?>" aria-expanded="false" aria-controls="collapse-<?= $mKey ?>">
                                        <div>
                                            <h6 class="mb-1 fw-bold text-dark fs-5"><i class="fas fa-calendar-alt text-primary me-2"></i><?= esc($group['label']) ?></h6>
                                            <span class="text-muted small">
                                                <i class="fas fa-info-circle me-1"></i> Di bulan ini, ada <strong class="text-danger pending-count-text" data-month="<?= $mKey ?>"><?= $group['count'] ?></strong> pembayaran yang belum Anda approve keabsahannya.
                                            </span>
                                        </div>
                                        <span class="badge bg-primary rounded-pill px-3 py-2 text-white"><i class="fas fa-eye me-1"></i> Lihat & Proses</span>
                                    </button>
                                </div>
                                
                                <div id="collapse-<?= $mKey ?>" class="collapse" aria-labelledby="heading-<?= $mKey ?>" data-bs-parent="#bossMonthAccordion">
                                    <div class="card-body border-top p-4">
                                        
                                        <!-- Table of Pending Payments for this Month -->
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-4" id="table-pending-<?= $mKey ?>">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 80px;">Bukti</th>
                                                        <th>Nama Anak</th>
                                                        <th>Nama Orang Tua</th>
                                                        <th>Jenis Les</th>
                                                        <th>Tanggal</th>
                                                        <th>Total</th>
                                                        <th>Metode</th>
                                                        <th class="text-center" style="width: 180px;">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($group['payments'] as $pb): ?>
                                                        <tr id="row-payment-<?= $pb['id'] ?>" class="payment-row" 
                                                            data-id="<?= $pb['id'] ?>" 
                                                            data-month="<?= $mKey ?>" 
                                                            data-total="<?= $pb['total'] ?>" 
                                                            data-coach="<?= $pb['earn_coach'] ?>" 
                                                            data-owner="<?= $pb['earn_owner'] ?>" 
                                                            data-les="<?= esc($pb['nama_les']) ?>">
                                                            <td>
                                                                <?php if (!empty($pb['bukti_pembayaran'])): ?>
                                                                    <img src="<?= r2_url($pb['bukti_pembayaran'], 'pembayaran') ?>" 
                                                                         alt="Bukti" 
                                                                         class="img-thumbnail bukti-img shadow-sm" 
                                                                         style="width: 55px; height: 55px; object-fit: cover;"
                                                                         onclick="zoomImage(this.src)">
                                                                <?php else: ?>
                                                                    <span class="text-muted small">-</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td class="fw-semibold text-dark"><?= esc($pb['nama_anak']) ?></td>
                                                            <td><?= esc($pb['nama_parent']) ?></td>
                                                            <td><span class="badge bg-light text-primary"><?= esc($pb['nama_les'] ?? 'Tidak ada') ?></span></td>
                                                            <td><?= date('d-m-Y H:i', strtotime($pb['tanggal'])) ?></td>
                                                            <td class="fw-bold text-dark">Rp <?= number_format($pb['total'], 0, ',', '.') ?></td>
                                                            <td><span class="text-muted small"><?= esc($pb['metode_pembayaran']) ?></span></td>
                                                            <td>
                                                                <div class="d-flex gap-2 justify-content-center">
                                                                    <button class="btn btn-success boss-action-btn btn-sm px-3 rounded-pill btn-approve-child" data-id="<?= $pb['id'] ?>" data-month="<?= $mKey ?>">
                                                                        <i class="fas fa-check me-1"></i> Valid
                                                                    </button>
                                                                    <button class="btn btn-danger boss-action-btn btn-sm px-3 rounded-pill btn-reject-child" data-id="<?= $pb['id'] ?>" data-month="<?= $mKey ?>">
                                                                        <i class="fas fa-times me-1"></i> Tolak
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                        <!-- Dynamic final seal / digital signature section for this Month (Hidden until pending count is 0) -->
                                        <div class="final-seal-container border p-4 shadow-sm d-none" id="final-seal-<?= $mKey ?>">
                                            <h5 class="fw-bold text-primary mb-3"><i class="fas fa-file-signature me-2"></i>Pernyataan Konfirmasi & Approval Bulanan</h5>
                                            
                                            <div class="card border-0 bg-white shadow-sm rounded-4 p-4 text-dark mb-4">
                                                <p class="mb-3 text-dark fs-6 lh-lg" id="summary-text-<?= $mKey ?>">
                                                    <!-- Filled dynamically by JavaScript -->
                                                </p>
                                                <hr class="my-3">
                                                <div class="form-check p-3 bg-light rounded-4 border d-flex align-items-start gap-2">
                                                    <input class="form-check-input ms-0 mt-1 shadow-sm" type="checkbox" id="disclaimer-agree-<?= $mKey ?>" style="width: 1.35rem; height: 1.35rem;">
                                                    <label class="form-check-label fw-medium text-dark ms-2 small" for="disclaimer-agree-<?= $mKey ?>">
                                                        Dengan menandatangani konfirmasi ini, saya menyatakan telah menerima dana pembayaran tersebut secara lengkap di rekening resmi dan berkomitmen untuk mengalokasikan serta menyalurkan bagian pendanaan yang menjadi hak para Coach sesuai ketentuan bagi hasil yang berlaku.
                                                    </label>
                                                </div>
                                            </div>
                                            
                                            <!-- Canvas TTD Digital -->
                                            <div class="mb-4">
                                                <label class="form-label fw-bold text-secondary mb-2"><i class="fas fa-signature me-1"></i>Bubuhkan Tanda Tangan Digital Anda Pada Area Putih:</label>
                                                <div class="border rounded-4 bg-white p-2 position-relative" style="height: 220px; border-color: #93c5fd !important;">
                                                    <canvas id="canvas-<?= $mKey ?>" class="w-100 h-100" style="touch-action: none; cursor: crosshair;"></canvas>
                                                    <button type="button" class="btn btn-outline-danger boss-action-btn btn-xs position-absolute bottom-0 end-0 m-3 rounded-pill px-3 shadow-sm clear-canvas-btn" data-month="<?= $mKey ?>">
                                                        <i class="fas fa-eraser me-1"></i> Bersihkan
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <div class="text-end">
                                                <button type="button" class="btn btn-primary boss-action-btn rounded-pill px-4 py-2 submit-final-btn" data-month="<?= $mKey ?>">
                                                    <i class="fas fa-check-double me-1"></i> Konfirmasi & Kirim Tanda Tangan
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-check-circle text-success mb-3 d-block" style="font-size: 50px;"></i>
                        <h5 class="fw-bold text-dark">Luar Biasa!</h5>
                        <p class="mb-0">Seluruh antrean verifikasi pembayaran bulanan Anda telah lengkap diselesaikan.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    <?php else: ?>
        <!-- ================================================================= -->
        <!--                   ORIGINAL ADMIN OPERASIONAL VIEW                 -->
        <!-- ================================================================= -->
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0 fw-bold text-primary"><i class="fas fa-tasks me-2"></i><?= $title ?></h5>
            </div>
            <div class="card-body">
                <?php if (!empty($bossRejected)): ?>
                    <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4">
                        <h6 class="fw-bold mb-2 text-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i> 
                            Terdapat <?= count($bossRejected) ?> Pembayaran Ditolak oleh Boss
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.85rem;">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Orang Tua</th>
                                        <th>Anak</th>
                                        <th>Total</th>
                                        <th>Alasan Penolakan Boss</th>
                                        <th>Tanggal Tolak</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bossRejected as $br): ?>
                                        <tr>
                                            <td><?= $br['id'] ?></td>
                                            <td><?= esc($br['nama_parent']) ?></td>
                                            <td><?= esc($br['nama_anak']) ?></td>
                                            <td>Rp <?= number_format($br['total'], 0, ',', '.') ?></td>
                                            <td class="text-danger fw-semibold"><?= esc($br['catatan_tolak_bos'] ?? '-') ?></td>
                                            <td><?= date('d-m-Y H:i', strtotime($br['waktu_approval_bos'])) ?></td>
                                            <td>
                                                <a href="<?= base_url('admin/pembayaran/detail/' . $br['id']) ?>" class="btn btn-xs btn-info text-white">
                                                    <i class="fas fa-eye"></i> Detail
                                                </a>
                                                <a href="<?= base_url('admin/pembayaran/delete/' . $br['id']) ?>" 
                                                   class="btn btn-xs btn-danger" 
                                                   onclick="return confirm('Hapus riwayat penolakan ini?')">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>

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
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Bukti</th>
                                <th>Nama Anak</th>
                                <th>Nama Orang Tua</th>
                                <th>Jenis Les</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Metode</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pembayaran)) : ?>
                                <?php foreach ($pembayaran as $bayar) : ?>
                                    <tr>
                                        <td><?= $bayar['id'] ?></td>
                                        <td>
                                            <?php if (!empty($bayar['bukti_pembayaran'])) : ?>
                                                <img src="<?= r2_url($bayar['bukti_pembayaran'], 'pembayaran') ?>" 
                                                     alt="Bukti" 
                                                     class="img-thumbnail bukti-img" 
                                                     style="width: 50px; height: 50px; object-fit: cover;"
                                                     onclick="zoomImage(this.src)">
                                            <?php else : ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($bayar['nama_anak']) ?></td>
                                        <td><?= esc($bayar['nama_parent']) ?></td>
                                        <td>
                                            <?php if (!empty($bayar['nama_les_snapshot'])): ?>
                                                <span class="badge bg-light text-primary border"><?= esc($bayar['nama_les_snapshot']) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted small"><i>Mengikuti data anak</i></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d-m-Y', strtotime($bayar['tanggal'])) ?></td>
                                        <td>Rp <?= number_format($bayar['total'], 0, ',', '.') ?></td>
                                        <td><?= esc($bayar['metode_pembayaran']) ?></td>
                                        <td>
                                            <?php if ($bayar['status'] == 'pending'): ?>
                                                <span class="badge bg-warning">Pending</span>
                                            <?php elseif ($bayar['status'] == 'success'): ?>
                                                <span class="badge bg-success">Success</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Rejected</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('admin/pembayaran/detail/' . $bayar['id']) ?>" class="btn btn-sm btn-info text-white mb-1">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                            <?php if ($bayar['status'] == 'pending'): ?>
                                                <div class="btn-group mb-1">
                                                    <a href="<?= base_url('admin/pembayaran/approve/' . $bayar['id']) ?>" class="btn btn-sm btn-success" onclick="return confirm('Setujui pembayaran ini secara operasional?')">
                                                        <i class="fas fa-check"></i> Setuju
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="openRejectModalAdmin(<?= $bayar['id'] ?>)">
                                                        <i class="fas fa-times"></i> Tolak
                                                    </button>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-5">
                                        <i class="fas fa-inbox mb-3 d-block" style="font-size: 40px;"></i>
                                        Tidak ada data pembayaran yang menunggu approval Admin.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Reject Admin (Operasional) -->
<div class="modal fade" id="rejectModalAdmin" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Tolak Pembayaran (Operasional)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectFormAdmin" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Alasan Penolakan</label>
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Contoh: Bukti transfer tidak terbaca / salah upload" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Pembayaran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Zoom Image (Bisa Dipakai Boss & Admin) -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body text-center p-0">
                <img id="modalImage" src="" alt="Bukti Pembayaran" class="img-fluid rounded shadow-lg" style="max-height: 90vh;">
                <div class="mt-3">
                    <button type="button" class="btn btn-light btn-sm rounded-pill" data-bs-dismiss="modal">Tutup</button>
                    <a id="downloadImage" href="" target="_blank" class="btn btn-primary btn-sm rounded-pill ms-2">Buka di Tab Baru / Download</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function zoomImage(imageUrl) {
    document.getElementById('modalImage').src = imageUrl;
    document.getElementById('downloadImage').href = imageUrl;
    var imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
    imageModal.show();
}

function openRejectModalAdmin(id) {
    document.getElementById('rejectFormAdmin').action = "<?= base_url('admin/pembayaran/reject/') ?>" + id;
    var myModal = new bootstrap.Modal(document.getElementById('rejectModalAdmin'));
    myModal.show();
}

$(document).ready(function() {
    $('#rejectFormAdmin').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: 'Konfirmasi Penolakan',
            text: 'Apakah Anda yakin ingin menolak pembayaran ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Tolak!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>

<?php if (session()->get('role') === 'boss'): ?>
<script>
let approvedListByMonth = {};
let signatureCanvases = {};

// Nilai awal untuk update real-time bilah atas (Mulai dari 0 sesuai instruksi)
let totalValid = 0;
let totalCoach = 0;
let totalOwner = 0;

function formatIDR(amount) {
    return 'Rp ' + new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
}

function updateTopBar() {
    $('#top-bar-total').text(formatIDR(totalValid));
    $('#top-bar-coach').text(formatIDR(totalCoach));
    $('#top-bar-owner').text(formatIDR(totalOwner));
}

// Fungsi untuk mencatat data anak yang disetujui Valid
function addToApprovedList(month, row) {
    if (!approvedListByMonth[month]) {
        approvedListByMonth[month] = [];
    }
    
    const total = parseFloat(row.data('total')) || 0;
    const les = row.data('les') || '';
    
    approvedListByMonth[month].push({
        total: total,
        les: les
    });
}

// Cek kuota sisa yang belum terproses
function checkPendingCount(month) {
    const table = $('#table-pending-' + month);
    const pendingRowsCount = table.find('tbody tr.payment-row').length;
    
    // Update badge teks count
    $(`.pending-count-text[data-month="${month}"]`).text(pendingRowsCount);
    
    if (pendingRowsCount === 0) {
        const list = approvedListByMonth[month] || [];
        
        if (list.length === 0) {
            // Seluruhnya ditolak, tidak perlu tanda tangan
            $(`#final-seal-${month}`).html(`
                <div class="alert alert-info border-0 rounded-4 text-center py-4 mb-0 shadow-sm">
                    <i class="fas fa-info-circle mb-2 d-block fs-3 text-info"></i>
                    <h6 class="fw-bold mb-1">Semua Pembayaran Ditolak</h6>
                    <p class="small text-muted mb-0">Seluruh pembayaran pada bulan ini telah ditolak oleh Anda. Tidak ada dana masuk yang perlu disahkan.</p>
                    <button class="btn btn-sm btn-secondary rounded-pill px-4 mt-3" onclick="location.reload()"><i class="fas fa-sync me-1"></i> Muat Ulang Halaman</button>
                </div>
            `).removeClass('d-none');
            table.closest('.table-responsive').addClass('d-none');
            return;
        }
        
        let totalDana = 0;
        let totalAnak = list.length;
        let totalReguler = 0;
        let totalPrivate = 0;
        
        list.forEach(item => {
            totalDana += item.total;
            const lesName = item.les.toLowerCase();
            // Asumsi 1 paket berisi 4 pertemuan kuota
            if (lesName.includes('private')) {
                totalPrivate += 4;
            } else {
                totalReguler += 4;
            }
        });
        
        const formattedDana = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(totalDana);
        
        const summaryText = `Terima kasih banyak telah menyempatkan waktu berharga Anda untuk melakukan approval dan verifikasi keabsahan data keuangan pembayaran pada bulan ini. <br><br>Dengan ini, Anda menyatakan secara sah telah menerima dana transfer sebesar <strong>${formattedDana}</strong> dari total <strong>${totalAnak}</strong> pembayaran anak, dengan rincian total sesi berupa <strong>${totalReguler} pertemuan reguler paket</strong> dan <strong>${totalPrivate} pertemuan private paket</strong>.`;
        
        $(`#summary-text-${month}`).html(summaryText);
        
        // Munculkan formulir TTD akhir
        table.closest('.table-responsive').fadeOut(300, function() {
            $(`#final-seal-${month}`).removeClass('d-none').hide().fadeIn(300);
            initSignatureCanvas(month);
        });
    }
}

// Inisialisasi Canvas Tanda Tangan
function initSignatureCanvas(month) {
    const canvas = document.getElementById(`canvas-${month}`);
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    let drawing = false;
    
    function resizeCanvas() {
        const rect = canvas.getBoundingClientRect();
        canvas.width = rect.width;
        canvas.height = rect.height;
        
        ctx.strokeStyle = '#0f172a';
        ctx.lineWidth = 3.5;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
    }
    
    resizeCanvas();
    
    canvas.addEventListener('mousedown', (e) => {
        drawing = true;
        ctx.beginPath();
        const pos = getMousePos(canvas, e);
        ctx.moveTo(pos.x, pos.y);
    });
    
    canvas.addEventListener('mousemove', (e) => {
        if (!drawing) return;
        const pos = getMousePos(canvas, e);
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();
    });
    
    window.addEventListener('mouseup', () => {
        drawing = false;
    });
    
    canvas.addEventListener('touchstart', (e) => {
        e.preventDefault();
        drawing = true;
        ctx.beginPath();
        const pos = getTouchPos(canvas, e);
        ctx.moveTo(pos.x, pos.y);
    }, { passive: false });
    
    canvas.addEventListener('touchmove', (e) => {
        e.preventDefault();
        if (!drawing) return;
        const pos = getTouchPos(canvas, e);
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();
    }, { passive: false });
    
    canvas.addEventListener('touchend', () => {
        drawing = false;
    });
    
    function getMousePos(c, evt) {
        const rect = c.getBoundingClientRect();
        return {
            x: evt.clientX - rect.left,
            y: evt.clientY - rect.top
        };
    }
    
    function getTouchPos(c, evt) {
        const rect = c.getBoundingClientRect();
        return {
            x: evt.touches[0].clientX - rect.left,
            y: evt.touches[0].clientY - rect.top
        };
    }
    
    signatureCanvases[month] = {
        canvas: canvas,
        ctx: ctx,
        isBlank: function() {
            const blank = document.createElement('canvas');
            blank.width = canvas.width;
            blank.height = canvas.height;
            return canvas.toDataURL() === blank.toDataURL();
        }
    };
}

$(document).ready(function() {
    // Tombol Approve per anak (AJAX)
    $('.btn-approve-child').on('click', function() {
        const id = $(this).data('id');
        const month = $(this).data('month');
        const row = $('#row-payment-' + id);
        
        Swal.fire({
            title: 'Konfirmasi Dana Masuk',
            text: 'Apakah Anda sudah memverifikasi bahwa dana pembayaran ini valid telah masuk ke rekening resmi?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10B981',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Valid!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url("admin/pembayaran/confirm-boss/") ?>' + id,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            // Ambil data dari atribut row
                            const totalRow = parseFloat(row.data('total')) || 0;
                            const lesName = (row.data('les') || '').toLowerCase();
                            
                            let currentCoach = 0;
                            let currentOwner = 0;

                            // Logika Pembagian sesuai instruksi User
                            if (lesName.includes('private')) {
                                currentCoach = 360000;
                                currentOwner = totalRow - currentCoach; // Sisa (240.000 jika total 600k)
                            } else {
                                // Default Reguler
                                currentCoach = 200000;
                                currentOwner = 100000;
                            }

                            // Update bilah atas secara real-time
                            totalValid += totalRow;
                            totalCoach += currentCoach;
                            totalOwner += currentOwner;
                            updateTopBar();

                            // Masukkan ke log approve sesi ini
                            addToApprovedList(month, row);
                            // Sembunyikan baris
                            row.fadeOut(350, function() {
                                $(this).remove();
                                checkPendingCount(month);
                            });
                        } else {
                            Swal.fire('Gagal', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal menghubungi server.', 'error');
                    }
                });
            }
        });
    });

    // Tombol Tolak per anak (AJAX)
    $('.btn-reject-child').on('click', function() {
        const id = $(this).data('id');
        const month = $(this).data('month');
        const row = $('#row-payment-' + id);
        
        Swal.fire({
            title: 'Tolak Verifikasi Transfer',
            text: 'Silakan masukkan alasan dana tidak valid (Alasan WAJIB diisi agar Admin bisa mem-follow up):',
            input: 'textarea',
            inputPlaceholder: 'Contoh: Dana belum masuk di mutasi rekening / Nominal tidak sesuai...',
            inputAttributes: {
                'required': 'true'
            },
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            confirmButtonText: 'Tolak & Kirim',
            cancelButtonText: 'Batal',
            inputValidator: (value) => {
                if (!value || value.trim() === '') {
                    return 'Alasan penolakan wajib diisi untuk notifikasi Admin!'
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const catatan = result.value;
                $.ajax({
                    url: '<?= base_url("admin/pembayaran/reject-boss/") ?>' + id,
                    type: 'POST',
                    data: { catatan: catatan },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            // Hapus baris row
                            row.fadeOut(350, function() {
                                $(this).remove();
                                checkPendingCount(month);
                            });
                        } else {
                            Swal.fire('Gagal', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal menghubungi server.', 'error');
                    }
                });
            }
        });
    });

    // Bersihkan canvas
    $(document).on('click', '.clear-canvas-btn', function() {
        const month = $(this).data('month');
        const sig = signatureCanvases[month];
        if (sig) {
            sig.ctx.clearRect(0, 0, sig.canvas.width, sig.canvas.height);
        }
    });

    // Submit TTD & Penutupan Final Bulanan
    $(document).on('click', '.submit-final-btn', function() {
        const month = $(this).data('month');
        const checkbox = $(`#disclaimer-agree-${month}`);
        const sig = signatureCanvases[month];
        
        if (!checkbox.is(':checked')) {
            Swal.fire('Peringatan', 'Mohon centang pernyataan persetujuan terlebih dahulu.', 'warning');
            return;
        }
        
        if (!sig || sig.isBlank()) {
            Swal.fire('Peringatan', 'Mohon gambarkan tanda tangan digital Anda terlebih dahulu pada area canvas.', 'warning');
            return;
        }
        
        const sigData = sig.canvas.toDataURL();
        
        Swal.fire({
            title: 'Konfirmasi Persetujuan Final',
            text: 'Apakah Anda yakin ingin menyelesaikan proses approval bulan ini dan mengunci dengan tanda tangan Anda?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3B82F6',
            confirmButtonText: 'Ya, Kirim Konfirmasi!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menyimpan...',
                    text: 'Sedang mengamankan tanda tangan digital Anda dan mengunci penutupan bulan.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                $.ajax({
                    url: '<?= base_url("admin/pembayaran/complete-month-boss") ?>',
                    type: 'POST',
                    data: {
                        month: month,
                        signature_data: sigData
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message,
                                icon: 'success'
                            }).then(() => {
                                // Hilangkan kartu bulan tersebut dengan animasi fadeOut
                                $(`#card-month-${month}`).fadeOut(400, function() {
                                    $(this).remove();
                                    // Jika tidak ada lagi bulan yang tersisa, reload untuk menampilkan halaman kosong sukses
                                    if ($('.month-accordion-card').length === 0) {
                                        location.reload();
                                    }
                                });
                            });
                        } else {
                            Swal.fire('Gagal', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        let msg = 'Gagal mengirimkan data ke server.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', msg, 'error');
                    }
                });
            }
        });
    });
});
</script>
<?php endif; ?>

<?= $this->include('admin/templates/footer') ?>