<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PembayaranModel;
use App\Models\AnakModel;
use App\Models\ParentModel;
use App\Models\JenisLesModel;
use App\Libraries\R2Client;

class Pembayaran extends BaseController
{
    private const PERTEMUAN_PAKET = 4;
    private const TARIF_PRIVATE_PER_PERTEMUAN = 150000;
    private const TARIF_REGULER_PER_PERTEMUAN = 75000;

    protected $pembayaranModel;
    protected $anakModel;
    protected $parentModel;
    protected $jenisLesModel;
    protected $r2;

    private function getTarifPerPertemuanByNamaLes(string $namaLes): int
    {
        $normalized = strtolower(trim($namaLes));

        if ($normalized === 'private') {
            return self::TARIF_PRIVATE_PER_PERTEMUAN;
        }

        if ($normalized === 'reguler' || $normalized === 'regular') {
            return self::TARIF_REGULER_PER_PERTEMUAN;
        }

        return 0;
    }

    public function __construct()
    {
        $this->pembayaranModel = new PembayaranModel();
        $this->anakModel = new AnakModel();
        $this->parentModel = new ParentModel();
        $this->jenisLesModel = new JenisLesModel();
        $this->r2 = new R2Client();
    }

    private function denyIfBossForAdminAction()
    {
        if (session()->get('role') === 'boss') {
            return redirect()->to('admin/pembayaran')
                ->with('error', 'Aksi ini khusus admin operasional.');
        }

        return null;
    }
    
    public function index()
    {
        $data['active'] = 'pembayaran';
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        $pembayaran = [];
        $rekap = [
            'total' => 0,
            'coach' => 0,
            'owner' => 0
        ];
        $pending_rekap = [
            'total' => 0,
            'coach' => 0,
            'owner' => 0
        ];

        $filterBulanIni = false;

        if (session()->get('role') === 'boss') {
            $builder = $this->pembayaranModel->select('pembayaran.*, anak.nama as nama_anak, parents.nama as nama_parent, jenis_les.nama_les as nama_les')
                ->join('anak', 'anak.id = pembayaran.anak_id')
                ->join('parents', 'parents.id = pembayaran.parent_id')
                ->join('jenis_les', 'jenis_les.id = pembayaran.jenis_les_id', 'left')
                ->where('pembayaran.status_approval_bos', 0)
                ->where('pembayaran.status !=', 'rejected')
                ->orderBy('pembayaran.tanggal', 'DESC');

            $pembayaran = $builder->findAll();
            $title = 'Validasi Transfer (Boss)';

            // Pengelompokan bulanan di PHP
            $groupedPending = [];
            foreach ($pembayaran as $pb) {
                $monthKey = date('Y-m', strtotime($pb['tanggal']));
                $monthLabel = date('F Y', strtotime($pb['tanggal']));
                
                if (!isset($groupedPending[$monthKey])) {
                    $groupedPending[$monthKey] = [
                        'label' => $monthLabel,
                        'key' => $monthKey,
                        'payments' => [],
                        'count' => 0,
                        'total_amount' => 0,
                        'total_reguler' => 0,
                        'total_private' => 0,
                    ];
                }
                
                $groupedPending[$monthKey]['payments'][] = $pb;
                $groupedPending[$monthKey]['count']++;
                $groupedPending[$monthKey]['total_amount'] += $pb['total'];

                $namaLes = strtolower(trim((string) ($pb['nama_les'] ?? '')));
                if (str_contains($namaLes, 'private')) {
                    $groupedPending[$monthKey]['total_private']++;
                } else {
                    $groupedPending[$monthKey]['total_reguler']++;
                }
            }

            // Hitung rekap pending untuk modal verifikasi massal
            foreach ($pembayaran as $pb) {
                $pending_rekap['total'] += $pb['total'];
                $pending_rekap['coach'] += $pb['earn_coach'];
                $pending_rekap['owner'] += $pb['earn_owner'];
            }

            // Rekap keuangan (Mulai dari 0 untuk Boss, hanya menghitung yang di-approve di sesi ini)
            $rekap = [
                'total' => 0,
                'coach' => 0,
                'owner' => 0
            ];

            $data = [
                'title' => $title,
                'nama' => session()->get('nama'),
                'pembayaran' => $pembayaran,
                'groupedPending' => $groupedPending, // Kita kirim array kelompok ini
                'rekap' => $rekap,
                'pending_rekap' => $pending_rekap,
                'filter_bulan_ini' => false,
                'label_bulan_ini' => date('F Y'),
                'bossRejected' => [],
            ];
            
            return view('admin/pembayaran/index', $data);
        } else {
            // Jika Admin, tampilkan hanya yang pending untuk operasional
            $pembayaran = $this->pembayaranModel->getPendingPayments();
            
            // Ambil pembayaran yang ditolak oleh Boss agar Admin mendapat notice/peringatan
            $bossRejected = $this->pembayaranModel->select('pembayaran.*, anak.nama as nama_anak, parents.nama as nama_parent, jenis_les.nama_les as nama_les')
                ->join('anak', 'anak.id = pembayaran.anak_id')
                ->join('parents', 'parents.id = pembayaran.parent_id')
                ->join('jenis_les', 'jenis_les.id = anak.jenis_les_id', 'left')
                ->where('pembayaran.status_approval_bos', 2)
                ->where('pembayaran.status', 'rejected')
                ->orderBy('pembayaran.updated_at', 'DESC')
                ->findAll();

            $title = 'Approval Pembayaran';
        }
        
        $data = [
            'title' => $title,
            'nama' => session()->get('nama'),
            'pembayaran' => $pembayaran,
            'rekap' => $rekap,
            'pending_rekap' => $pending_rekap,
            'filter_bulan_ini' => $filterBulanIni,
            'label_bulan_ini' => date('F Y'),
            'bossRejected' => $bossRejected ?? [],
        ];
        
        return view('admin/pembayaran/index', $data);
    }
    
    public function detail($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }
        
        $pembayaran = $this->pembayaranModel->getPaymentDetail($id);
        
        if (!$pembayaran) {
            session()->setFlashdata('error', 'Data pembayaran tidak ditemukan');
            return redirect()->to('admin/pembayaran');
        }
        
        $data = [
            'title' => 'Detail Pembayaran',
            'nama' => session()->get('nama'),
            'pembayaran' => $pembayaran,
            'active' => 'pembayaran'  // Add this line to set the active menu
        ];
        
        return view('admin/pembayaran/detail', $data);
    }
    
    public function approve($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }
        if ($deny = $this->denyIfBossForAdminAction()) {
            return $deny;
        }

        $pembayaran = $this->pembayaranModel->find($id);
        if (!$pembayaran) {
            return redirect()->back()->with('error', 'Data pembayaran tidak ditemukan');
        }

        $anak = $this->anakModel->find($pembayaran['anak_id']);

        try {
            // Update status approval admin & simpan snapshot jenis les jika belum ada
            $updateData = [
                'status' => 'success',
                'status_approval_admin' => 1,
                'waktu_approval_admin' => date('Y-m-d H:i:s')
            ];

            if (empty($pembayaran['jenis_les_id']) && $anak) {
                $updateData['jenis_les_id'] = $anak['jenis_les_id'];
            }

            // --- PERBAIKAN LOGIKA BAGI HASIL ---
            // Ambil data jenis les (snapshot saat approval) untuk menghitung earn_coach & earn_owner
            $jLesId = $updateData['jenis_les_id'] ?? $pembayaran['jenis_les_id'];
            if ($jLesId) {
                $jLes = $this->jenisLesModel->find($jLesId);
                if ($jLes) {
                    $updateData['earn_coach'] = $jLes['earn_coach'];
                    $updateData['earn_owner'] = $jLes['earn_owner'];
                }
            }
            // ------------------------------------

            $this->pembayaranModel->update($id, $updateData);

            // Logika operasional: Tambah sesi & masa aktif agar anak bisa langsung latihan
            $this->anakModel->recalculateSisaPertemuan($pembayaran['anak_id']);
            
            // Jika status anak masih 'menunggu', aktifkan otomatis setelah pembayaran pertama disetujui
            if ($anak && $anak['status'] === 'menunggu') {
                $this->anakModel->update($pembayaran['anak_id'], ['status' => 'aktif']);
            }
            
            session()->setFlashdata('success', 'Pembayaran disetujui secara operasional. Anak sudah bisa mendaftar jadwal latihan.');
        } catch (\Throwable $e) {
            session()->setFlashdata('error', 'Gagal menyetujui pembayaran: ' . $e->getMessage());
        }

        return redirect()->back();
    }

    public function reject($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }
        if ($deny = $this->denyIfBossForAdminAction()) {
            return $deny;
        }

        $catatan = $this->request->getPost('catatan');
        
        $this->pembayaranModel->update($id, [
            'status' => 'rejected',
            'status_approval_admin' => 2,
            'catatan_tolak_admin' => $catatan,
            'waktu_approval_admin' => date('Y-m-d H:i:s')
        ]);

        session()->setFlashdata('success', 'Pembayaran telah ditolak.');
        return redirect()->to('admin/pembayaran');
    }

    public function manual()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }
        if ($deny = $this->denyIfBossForAdminAction()) {
            return $deny;
        }

        return view('admin/pembayaran/manual', [
            'title' => 'Pembayaran Manual',
            'nama' => session()->get('nama'),
            'active' => 'pembayaran-manual',
        ]);
    }

    public function manualSearch()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }
        if (session()->get('role') === 'boss') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Aksi ini khusus admin operasional']);
        }

        $q = trim((string) $this->request->getGet('q'));
        if ($q === '') {
            return $this->response->setJSON(['data' => []]);
        }

        $builder = $this->anakModel
            ->select('anak.id, anak.nama, anak.nama_panggilan, anak.sisa_pertemuan, anak.status, anak.jenis_les_id, parents.nama as nama_parent, parents.whatsapp, jenis_les.nama_les, jenis_les.harga')
            ->join('parents', 'parents.id = anak.parent_id', 'left')
            ->join('jenis_les', 'jenis_les.id = anak.jenis_les_id', 'left');

        if (ctype_digit($q)) {
            $builder->groupStart()
                ->where('anak.id', (int) $q)
                ->orLike('anak.nama', $q)
                ->orLike('anak.nama_panggilan', $q)
                ->groupEnd();
        } else {
            $builder->groupStart()
                ->like('anak.nama', $q)
                ->orLike('anak.nama_panggilan', $q)
                ->groupEnd();
        }

        $rows = $builder->orderBy('anak.id', 'DESC')->findAll(20);
        foreach ($rows as &$row) {
            $tarif = $this->getTarifPerPertemuanByNamaLes((string) ($row['nama_les'] ?? ''));
            $row['harga'] = $tarif;
            
            // Ambil riwayat pembayaran terakhir anak ini (hanya yang sukses)
            // Query ini lebih aman tanpa join yang berisiko jika kolom tidak ada
            $riwayat = $this->pembayaranModel
                ->select('tanggal, jumlah_pertemuan, total')
                ->where('anak_id', $row['id'])
                ->where('status', 'success')
                ->orderBy('tanggal', 'DESC')
                ->limit(5)
                ->findAll();

            $row['riwayat_pembayaran'] = $riwayat;
            $row['total_pembayaran_sebelumnya'] = $this->pembayaranModel->where('anak_id', $row['id'])->where('status', 'success')->countAllResults();
        }

        return $this->response->setJSON(['data' => $rows]);
    }

    public function manualStore()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }
        if ($deny = $this->denyIfBossForAdminAction()) {
            return $deny;
        }

        $anakId = (int) $this->request->getPost('anak_id');
        $tanggalArr = $this->request->getPost('tanggal');
        $metodeArr = $this->request->getPost('metode_pembayaran');
        $catatanArr = $this->request->getPost('catatan');

        if ($anakId <= 0) {
            return redirect()->back()->withInput()->with('error', 'Anak wajib dipilih.');
        }

        if (!is_array($tanggalArr) || count($tanggalArr) === 0) {
            return redirect()->back()->withInput()->with('error', 'Minimal satu pembayaran harus diisi.');
        }

        $anak = $this->anakModel
            ->select('anak.*, jenis_les.nama_les, jenis_les.harga')
            ->join('jenis_les', 'jenis_les.id = anak.jenis_les_id', 'left')
            ->where('anak.id', $anakId)
            ->first();

        if (!$anak) {
            return redirect()->back()->withInput()->with('error', 'Data anak tidak ditemukan.');
        }

        $harga = $this->getTarifPerPertemuanByNamaLes((string) ($anak['nama_les'] ?? ''));
        if ($harga <= 0) {
            return redirect()->back()->withInput()->with('error', 'Jenis les tidak valid. Pastikan anak memiliki jenis les private/reguler.');
        }

        // Selalu paksa paket pembayaran 4 pertemuan per item.
        $jumlahPertemuan = self::PERTEMUAN_PAKET;
        $totalPerItem = $harga * $jumlahPertemuan;
        
        $namaLes = strtolower(trim((string) ($anak['nama_les'] ?? '')));
        $earnCoachPerItem = 0;
        $earnOwnerPerItem = 0;
        
        if (!empty($anak['jenis_les_id'])) {
            $jLes = $this->jenisLesModel->find($anak['jenis_les_id']);
            if ($jLes) {
                $earnCoachPerItem = $jLes['earn_coach'];
                $earnOwnerPerItem = $jLes['earn_owner'];
            }
        }

        if ($earnCoachPerItem == 0 && $earnOwnerPerItem == 0) {
            // Fallback lama jika data di jenis_les kosong
            if ($namaLes === 'private') {
                $earnCoachPerItem = 90000 * $jumlahPertemuan;
                $earnOwnerPerItem = 60000 * $jumlahPertemuan;
            } else {
                $earnCoachPerItem = 50000 * $jumlahPertemuan;
                $earnOwnerPerItem = 25000 * $jumlahPertemuan;
            }
        }

        $buktiFiles = $this->request->getFileMultiple('bukti_pembayaran');
        
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $waktuFix = date('H:i:s');
            $totalJumlahPertemuan = 0;

            for ($i = 0; $i < count($tanggalArr); $i++) {
                $tgl = $tanggalArr[$i] ?? date('Y-m-d');
                $met = $metodeArr[$i] ?? 'transfer';
                $cat = trim((string) ($catatanArr[$i] ?? ''));
                $tanggalSimpan = $tgl ? date('Y-m-d H:i:s', strtotime($tgl . ' ' . $waktuFix)) : date('Y-m-d H:i:s');
                $berlakuSampai = $this->pembayaranModel->computeBerlakuSampaiFromPaymentDate($tanggalSimpan)
                    ?? date('Y-m-d', strtotime($tanggalSimpan . ' +90 days'));
                
                $buktiFileName = null;
                if (isset($buktiFiles[$i]) && $buktiFiles[$i]->isValid() && !$buktiFiles[$i]->hasMoved()) {
                    $file = $buktiFiles[$i];
                    $tempPath = $file->getTempName();
                    try {
                        \Config\Services::image()->withFile($tempPath)->resize(800, 800, true, 'auto')->save($tempPath, 70);
                    } catch (\Exception $e) { }

                    $key = 'bukti_pembayaran/' . $file->getRandomName();
                    $uploadedPath = $this->r2->upload($key, fopen($tempPath, 'r'), $file->getMimeType());
                    
                    if ($uploadedPath) {
                        $buktiFileName = $key;
                    }
                }

                $this->pembayaranModel->insert([
                    'anak_id' => $anakId,
                    'parent_id' => (int) ($anak['parent_id'] ?? 0),
                    'jenis_les_id' => (int) ($anak['jenis_les_id'] ?? 0), // Snapshot saat input manual
                    'tanggal' => $tanggalSimpan,
                    'jumlah_pertemuan' => $jumlahPertemuan,
                    'total' => $totalPerItem,
                    'metode_pembayaran' => $met,
                    'bukti_pembayaran' => $buktiFileName,
                    'catatan' => $cat !== '' ? $cat : 'manual_admin',
                    'status' => 'success',
                    'berlaku_sampai' => $berlakuSampai,
                    'earn_coach' => $earnCoachPerItem,
                    'earn_owner' => $earnOwnerPerItem,
                ]);

                $totalJumlahPertemuan += $jumlahPertemuan;
            }

            // Update sisa pertemuan menggunakan sinkronisasi total
            $this->anakModel->recalculateSisaPertemuan($anakId);

            $db->transComplete();
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan pembayaran manual: ' . $e->getMessage());
        }

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan pembayaran manual.');
        }

        $lastBerlaku = $this->pembayaranModel->getBerlakuSampaiForAnak($anakId);
        $berlakuMsg = $lastBerlaku ? date('d-m-Y', strtotime($lastBerlaku)) : '-';

        return redirect()->to('admin/pembayaran/manual')->with('success', count($tanggalArr) . ' Pembayaran manual berhasil disimpan. Berlaku sampai ' . $berlakuMsg);
    }

    public function edit($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }
        if ($deny = $this->denyIfBossForAdminAction()) {
            return $deny;
        }

        $pembayaran = $this->pembayaranModel->find($id);
        if (!$pembayaran) {
            return redirect()->to('admin/pembayaran/riwayat')->with('error', 'Data pembayaran tidak ditemukan');
        }

        $anak = $this->anakModel->find($pembayaran['anak_id']);
        $jenisLes = $this->jenisLesModel->findAll();
        
        $data = [
            'title' => 'Edit Pembayaran',
            'active' => 'riwayat-pembayaran',
            'p' => $pembayaran,
            'anak' => $anak,
            'jenisLes' => $jenisLes
        ];

        return view('admin/pembayaran/edit', $data);
    }

    public function update($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }
        if ($deny = $this->denyIfBossForAdminAction()) {
            return $deny;
        }

        $pembayaran = $this->pembayaranModel->find($id);
        if (!$pembayaran) {
            return redirect()->to('admin/pembayaran/riwayat')->with('error', 'Data pembayaran tidak ditemukan');
        }

        $rules = [
            'tanggal' => 'required',
            'jumlah_pertemuan' => 'required|numeric',
            'total' => 'required|numeric',
            'metode_pembayaran' => 'required',
            'berlaku_sampai' => 'required',
            'bukti_pembayaran' => 'permit_empty|is_image[bukti_pembayaran]|max_size[bukti_pembayaran,2048]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $tanggal = $this->request->getPost('tanggal');
        $berlakuSampai = $this->request->getPost('berlaku_sampai');
        
        // Hanya hitung otomatis jika tanggal berubah dan berlaku_sampai tidak diisi manual (opsional, tapi di sini kita prioritaskan input form)
        // Berdasarkan requirement: Admin berhak mengubah kapan pun.
        
        $data = [
            'tanggal' => $tanggal,
            'jumlah_pertemuan' => $this->request->getPost('jumlah_pertemuan'),
            'total' => $this->request->getPost('total'),
            'jenis_les_id' => $this->request->getPost('jenis_les_id'),
            'metode_pembayaran' => $this->request->getPost('metode_pembayaran'),
            'berlaku_sampai' => $berlakuSampai,
            'catatan' => $this->request->getPost('catatan'),
        ];

        $this->pembayaranModel->update($id, $data);

        // Update sisa pertemuan anak menggunakan sinkronisasi total
        $this->anakModel->recalculateSisaPertemuan($pembayaran['anak_id']);

        return redirect()->to('admin/pembayaran/riwayat')->with('success', 'Data pembayaran berhasil diperbarui');
    }

    public function delete($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }
        if ($deny = $this->denyIfBossForAdminAction()) {
            return $deny;
        }

        $pembayaran = $this->pembayaranModel->find($id);
        if (!$pembayaran) {
            return redirect()->back()->with('error', 'Data pembayaran tidak ditemukan');
        }

        // Ambil data anak untuk update sisa pertemuan
        $anak_id = $pembayaran['anak_id'];

        // Hapus data pembayaran
        if ($this->pembayaranModel->delete($id)) {
            // Update sisa pertemuan menggunakan sinkronisasi total
            $this->anakModel->recalculateSisaPertemuan($anak_id);
            return redirect()->back()->with('success', 'Riwayat pembayaran berhasil dihapus');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus riwayat pembayaran');
        }
    }
    
    public function confirmByBoss($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'boss') {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak']);
            }
            return redirect()->to('/auth')->with('error', 'Akses ditolak');
        }

        $pembayaran = $this->pembayaranModel->find($id);
        $updateData = [
            'status_approval_bos' => 1,
            'is_confirmed_boss' => 1,
            'waktu_approval_bos' => date('Y-m-d H:i:s')
        ];

        // Jika jenis_les_id masih kosong (data lama), ambil dari data anak saat ini sebagai fallback
        if (empty($pembayaran['jenis_les_id'])) {
            $anak = $this->anakModel->find($pembayaran['anak_id']);
            if ($anak) {
                $updateData['jenis_les_id'] = $anak['jenis_les_id'];
            }
        }

        $this->pembayaranModel->update($id, $updateData);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Dana pembayaran telah dikonfirmasi masuk.']);
        }

        return redirect()->back()->with('success', 'Dana pembayaran telah dikonfirmasi masuk.');
    }

    public function bulkConfirmByBoss()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'boss') {
            return redirect()->to('/auth')->with('error', 'Akses ditolak');
        }

        $signature = $this->request->getPost('signature_data');
        $disclaimer = $this->request->getPost('disclaimer_agree');

        if (empty($signature) || $disclaimer !== '1') {
            return redirect()->back()->with('error', 'Persetujuan dan tanda tangan wajib diisi.');
        }

        // Ambil filter bulan dari post data untuk memastikan data yang di-approve sama dengan yang tampil
        $filterBulanIni = ($this->request->getPost('filter_bulan_ini_val') === '1');

        $builder = $this->pembayaranModel->select('pembayaran.*')
            ->where('pembayaran.status_approval_bos', 0)
            ->where('pembayaran.status !=', 'rejected');

        if ($filterBulanIni) {
            $awalBulan = date('Y-m-01 00:00:00');
            $akhirBulan = date('Y-m-t 23:59:59');
            $builder->where('pembayaran.tanggal >=', $awalBulan)
                ->where('pembayaran.tanggal <=', $akhirBulan);
        }

        $pendingPayments = $builder->findAll();

        if (empty($pendingPayments)) {
            return redirect()->back()->with('error', 'Tidak ada pembayaran tertunda yang memenuhi filter.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            foreach ($pendingPayments as $p) {
                $this->pembayaranModel->update($p['id'], [
                    'status_approval_bos' => 1,
                    'is_confirmed_boss' => 1,
                    'waktu_approval_bos' => date('Y-m-d H:i:s'),
                    'signature_boss' => $signature
                ]);

                // Sinkronisasi kuota anak
                $this->anakModel->recalculateSisaPertemuan($p['anak_id']);
            }
            $db->transComplete();
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal memproses verifikasi: ' . $e->getMessage());
        }

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal memproses transaksi verifikasi massal.');
        }

        return redirect()->back()->with('success', count($pendingPayments) . ' Pembayaran berhasil diverifikasi secara massal.');
    }

    public function rejectByBoss($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'boss') {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak']);
            }
            return redirect()->to('/auth')->with('error', 'Akses ditolak');
        }

        $catatan = $this->request->getPost('catatan');

        $pembayaran = $this->pembayaranModel->select('pembayaran.*, anak.nama as nama_anak')
            ->join('anak', 'anak.id = pembayaran.anak_id')
            ->where('pembayaran.id', $id)
            ->first();

        if (!$pembayaran) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Data pembayaran tidak ditemukan']);
            }
            return redirect()->back()->with('error', 'Data pembayaran tidak ditemukan');
        }

        // Set status to rejected so recalculateSisaPertemuan knows it is invalid
        $this->pembayaranModel->update($id, [
            'status' => 'rejected',
            'status_approval_bos' => 2,
            'catatan_tolak_bos' => $catatan,
            'waktu_approval_bos' => date('Y-m-d H:i:s')
        ]);

        $this->anakModel->recalculateSisaPertemuan($pembayaran['anak_id']);

        // Insert notification to all admins in notification_queue
        $db = \Config\Database::connect();
        $admins = $db->table('admin')->where('role', 'admin')->get()->getResultArray();
        foreach ($admins as $admin) {
            $db->table('notification_queue')->insert([
                'user_type' => 'admin',
                'user_id' => $admin['id'],
                'channel' => 'push',
                'title' => 'Pembayaran Ditolak oleh Boss',
                'message' => 'Pembayaran ID #' . $id . ' untuk anak ' . ($pembayaran['nama_anak'] ?? '') . ' ditolak oleh Boss dengan alasan: ' . $catatan,
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Pembayaran ditolak oleh Boss. Notifikasi telah dikirim ke Admin.']);
        }

        return redirect()->back()->with('success', 'Pembayaran ditolak oleh Boss. Notifikasi telah dikirim ke Admin.');
    }

    public function completeMonthBoss()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'boss') {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Akses ditolak']);
        }

        $month = $this->request->getPost('month'); // e.g. "2026-05"
        $signature = $this->request->getPost('signature_data');

        if (empty($month) || empty($signature)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Bulan dan Tanda tangan wajib diisi']);
        }

        $awalBulan = $month . '-01 00:00:00';
        $akhirBulan = date('Y-m-t 23:59:59', strtotime($awalBulan));

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update all payments in this month that are approved by boss but have no signature yet
            $db->table('pembayaran')
                ->where('status_approval_bos', 1)
                ->where('tanggal >=', $awalBulan)
                ->where('tanggal <=', $akhirBulan)
                ->where('(signature_boss IS NULL OR signature_boss = "")')
                ->update(['signature_boss' => $signature]);

            $db->transComplete();
        } catch (\Throwable $e) {
            $db->transRollback();
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal memproses persetujuan akhir: ' . $e->getMessage()]);
        }

        if ($db->transStatus() === false) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal memproses transaksi persetujuan akhir.']);
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Persetujuan akhir bulan ' . date('F Y', strtotime($awalBulan)) . ' berhasil disimpan.']);
    }

    public function riwayat()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        // Ambil parameter filter dari URL
        $filter = [
            'tanggal_mulai' => $this->request->getGet('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getGet('tanggal_selesai'),
            'metode' => $this->request->getGet('metode'),
            'nama_anak' => $this->request->getGet('nama_anak'),
            'jenis_les' => $this->request->getGet('jenis_les')
        ];
    
        // Buat query dasar dengan JOIN tunggal yang efisien untuk mencegah N+1 Query
        // Hanya menampilkan data yang sudah di-confirm Admin (status success) 
        // DAN sudah di-confirm Boss (is_confirmed_boss 1)
        $builder = $this->pembayaranModel->select('
            pembayaran.*, 
            anak.nama as nama_anak, 
            parents.nama as nama_parent, 
            jenis_les.nama_les as nama_les_snapshot
        ')
        ->join('anak', 'pembayaran.anak_id = anak.id', 'left')
        ->join('parents', 'pembayaran.parent_id = parents.id', 'left')
        ->join('jenis_les', 'pembayaran.jenis_les_id = jenis_les.id', 'left')
        ->where('pembayaran.status', 'success');
    
        // Terapkan filter jika ada
        if (!empty($filter['tanggal_mulai'])) {
            $builder->where('pembayaran.tanggal >=', $filter['tanggal_mulai'] . ' 00:00:00');
        }
        
        if (!empty($filter['tanggal_selesai'])) {
            $builder->where('pembayaran.tanggal <=', $filter['tanggal_selesai'] . ' 23:59:59');
        }
        
        if (!empty($filter['metode'])) {
            $builder->where('pembayaran.metode_pembayaran', $filter['metode']);
        }
        
        if (!empty($filter['nama_anak'])) {
            $builder->like('anak.nama', $filter['nama_anak']);
        }
        
        if (!empty($filter['jenis_les'])) {
            $builder->where('pembayaran.jenis_les_id', $filter['jenis_les']);
        }
    
        // Tambahkan groupBy untuk menghindari duplikasi data
        $builder->groupBy('pembayaran.id');
        
        // Tambahkan orderBy untuk mengurutkan data
        $builder->orderBy('pembayaran.tanggal', 'DESC');
    
        // Get per_page limit from request or session
        $perPageInput = $this->request->getGet('per_page') ?? session()->get('per_page') ?? '50';
        if (!in_array($perPageInput, ['50', '100', '200', 'all'])) {
            $perPageInput = '50';
        }
        session()->set('per_page', $perPageInput);
        $perPage = ($perPageInput === 'all') ? 999999 : (int) $perPageInput;
        
        $data = [
            'title' => 'Riwayat Pembayaran (Valid)',
            'nama' => session()->get('nama'),
            'pembayaran' => $builder->paginate($perPage),
            'pager' => $this->pembayaranModel->pager,
            'filter' => $filter,
            'perPage' => $perPageInput,
            'active' => 'riwayat-pembayaran'
        ];
    
        return view('admin/pembayaran/riwayat', $data);
    }

    public function exportExcel()
    {
        // Ambil parameter filter dari URL
        $filter = [
            'tanggal_mulai' => $this->request->getGet('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getGet('tanggal_selesai'),
            'metode' => $this->request->getGet('metode'),
            'nama_anak' => $this->request->getGet('nama_anak'),
            'jenis_les' => $this->request->getGet('jenis_les')
        ];
    
        // Buat query dasar - pastikan select pembayaran.* agar ID tidak tertukar dengan tabel join
        $builder = $this->pembayaranModel->select('pembayaran.*')->where('pembayaran.status', 'success');
    
        // Terapkan filter jika ada
        if (!empty($filter['tanggal_mulai'])) {
            $builder->where('pembayaran.tanggal >=', $filter['tanggal_mulai'] . ' 00:00:00');
        }
        
        if (!empty($filter['tanggal_selesai'])) {
            $builder->where('pembayaran.tanggal <=', $filter['tanggal_selesai'] . ' 23:59:59');
        }
        
        if (!empty($filter['metode'])) {
            $builder->where('pembayaran.metode_pembayaran', $filter['metode']);
        }
        
        // Gunakan join untuk mendapatkan data anak dan jenis les
        $builder->join('anak', 'pembayaran.anak_id = anak.id', 'left');
        
        if (!empty($filter['nama_anak'])) {
            $builder->like('anak.nama', $filter['nama_anak']);
        }
        
        // Filter berdasarkan jenis les
        if (!empty($filter['jenis_les'])) {
            $builder->where('anak.jenis_les_id', $filter['jenis_les']);
        }
    
        // Tambahkan orderBy untuk mengurutkan data
        $builder->orderBy('pembayaran.id', 'ASC');
    
        // Ambil data pembayaran
        $pembayaran = $builder->findAll();
        
        // Tambahkan informasi nama anak, parent, dan jenis les
        $dataExport = [];
        $totalDana = 0; // Variabel untuk menghitung total dana
        
        foreach ($pembayaran as $p) {
            $anak = $this->anakModel->find($p['anak_id']);
            $parent = $this->parentModel->find($p['parent_id']);
            
            // Tambahkan jenis les - gunakan jenis_les_id dan ambil nama dari tabel jenis_les
            $jenisLesModel = new \App\Models\JenisLesModel();
            $jenisLes = $anak ? $jenisLesModel->find($anak['jenis_les_id']) : null;
            
            // Tambahkan total dana
            $totalDana += $p['total'];
            
            // Buat data untuk export
            $dataExport[] = [
                'id' => $p['id'],
                'tanggal' => $p['tanggal'],
                'nama_anak' => $anak ? $anak['nama'] : 'Tidak ditemukan',
                'nama_parent' => $parent ? $parent['nama'] : 'Tidak ditemukan',
                'jenis_les' => $jenisLes ? $jenisLes['nama_les'] : 'Tidak ditemukan',
                'jumlah_pertemuan' => $p['jumlah_pertemuan'],
                'total' => $p['total'],
                'metode_pembayaran' => $p['metode_pembayaran'],
                'berlaku_sampai' => $p['berlaku_sampai'],
                'bukti_pembayaran' => r2_url($p['bukti_pembayaran'], 'bukti_pembayaran')
            ];
        }
        
        // Buat file Excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set judul kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'ID');
        $sheet->setCellValue('C1', 'Tanggal');
        $sheet->setCellValue('D1', 'Nama Anak');
        $sheet->setCellValue('E1', 'Nama Orang Tua');
        $sheet->setCellValue('F1', 'Jenis Les');
        $sheet->setCellValue('G1', 'Jumlah Pertemuan');
        $sheet->setCellValue('H1', 'Total');
        $sheet->setCellValue('I1', 'Metode Pembayaran');
        $sheet->setCellValue('J1', 'Status');
        $sheet->setCellValue('K1', 'Berlaku Sampai');
        $sheet->setCellValue('L1', 'URL Bukti Pembayaran');
        
        // Style header
        $styleHeader = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FF4F81BD', // Biru profesional
                ],
            ],
        ];
        
        $sheet->getStyle('A1:L1')->applyFromArray($styleHeader);
        $sheet->getRowDimension(1)->setRowHeight(30);
        
        // Auto filter untuk setiap kolom
        $sheet->setAutoFilter('A1:L1');
        
        // Isi data
        $row = 2;
        $no = 1;
        $totalPertemuan = 0; // Variabel untuk hitung total pertemuan
        
        foreach ($dataExport as $p) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $p['id']);
            
            // Format Tanggal sebagai Date Serial Excel agar bisa difilter bulan/tahun
            $excelDate = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(strtotime($p['tanggal']));
            $sheet->setCellValue('C' . $row, $excelDate);
            $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('dd-mm-yyyy');

            $sheet->setCellValue('D' . $row, $p['nama_anak']);
            $sheet->setCellValue('E' . $row, $p['nama_parent']);
            $sheet->setCellValue('F' . $row, $p['jenis_les']);
            $sheet->setCellValue('G' . $row, $p['jumlah_pertemuan']);
            $totalPertemuan += $p['jumlah_pertemuan'];
            
            // Set nilai total sebagai angka
            $sheet->setCellValue('H' . $row, $p['total']);
            $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('#,##0');
            
            // Format metode pembayaran
            $metode = '';
            switch($p['metode_pembayaran']) {
                case 'transfer_bca': $metode = 'Transfer BCA'; break;
                case 'transfer_bri': $metode = 'Transfer BRI'; break;
                case 'transfer_mandiri': $metode = 'Transfer Mandiri'; break;
                case 'cash': $metode = 'Tunai'; break;
                default: $metode = $p['metode_pembayaran'];
            }
            $sheet->setCellValue('I' . $row, $metode);
            $sheet->setCellValue('J' . $row, 'Sukses');
            
            // Format Tanggal Berlaku Sampai
            if (!empty($p['berlaku_sampai'])) {
                $excelDateBerlaku = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(strtotime($p['berlaku_sampai']));
                $sheet->setCellValue('K' . $row, $excelDateBerlaku);
                $sheet->getStyle('K' . $row)->getNumberFormat()->setFormatCode('dd-mm-yyyy');
            } else {
                $sheet->setCellValue('K' . $row, '-');
            }

            $sheet->setCellValue('L' . $row, $p['bukti_pembayaran']);
            
            // Tambahkan link pada URL bukti jika ada
            if (!empty($p['bukti_pembayaran']) && $p['bukti_pembayaran'] !== '-') {
                $sheet->getCell('L' . $row)->getHyperlink()->setUrl($p['bukti_pembayaran']);
            }
            
            // Style border untuk baris data
            $sheet->getStyle('A' . $row . ':L' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            
            $row++;
        }
        
        // Tambahkan baris TOTAL di paling bawah
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->setCellValue('A' . $row, 'TOTAL KESELURUHAN');
        $sheet->setCellValue('G' . $row, $totalPertemuan);
        $sheet->setCellValue('H' . $row, $totalDana);
        
        // Style untuk baris TOTAL
        $styleTotal = [
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFFFF00', // Kuning terang untuk total
                ],
            ],
        ];
        $sheet->getStyle('A' . $row . ':L' . $row)->applyFromArray($styleTotal);
        
        // Format angka total
        $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('#,##0');
        
        // Set alignment center untuk kolom G
        $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Auto size columns
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Set nama file
        $filename = 'Riwayat_Pembayaran_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        // Set header untuk download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        // Tulis ke output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }
}
