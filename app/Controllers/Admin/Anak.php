<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AnakModel;
use App\Models\ParentModel;
use App\Models\JenisLesModel;
use App\Models\PembayaranModel;
use App\Libraries\R2Client;

class Anak extends BaseController
{
    protected $anakModel;
    protected $parentModel;
    protected $jenisLesModel;
    protected $pembayaranModel;
    protected $r2;

    public function __construct()
    {
        $this->anakModel = new AnakModel();
        $this->parentModel = new ParentModel();
        $this->jenisLesModel = new JenisLesModel();
        $this->pembayaranModel = new PembayaranModel();
        $this->r2 = new R2Client();
    }

    public function index()
    {
        $anakModel = new \App\Models\AnakModel();
        $jenisLesModel = new \App\Models\JenisLesModel();
        
        // Get filter values
        $filters = [
            'jenis_kelamin' => $this->request->getGet('jenis_kelamin'),
            'jenis_les' => $this->request->getGet('jenis_les'),
            'sisa_pertemuan' => $this->request->getGet('sisa_pertemuan'),
            'nama' => $this->request->getGet('nama'),
            'anak_id' => $this->request->getGet('anak_id'),
            'panggilan' => $this->request->getGet('panggilan'),
            'aktif_50_hari' => $this->request->getGet('aktif_50_hari'),
            'tidak_aktif_100_hari' => $this->request->getGet('tidak_aktif_100_hari')
        ];
        
        // Base query with joins
        $latestPaymentSubquery = '(SELECT anak_id, MAX(id) AS last_payment_id FROM pembayaran GROUP BY anak_id) pembayaran_last';
        $latestLatihanSubquery = '(SELECT la.anak_id, MAX(s.tanggal) AS last_latihan FROM latihan_attendance la JOIN schedules s ON s.id = la.schedule_id WHERE la.status_kehadiran = "hadir" GROUP BY la.anak_id) l_last';

        $query = $anakModel->select('
            anak.*,
            parents.nama as nama_parent,
            parents.whatsapp,
            jenis_les.nama_les,
            p.berlaku_sampai,
            p.status as status_pembayaran,
            GREATEST(
                COALESCE(l_last.last_latihan, "1970-01-01"),
                COALESCE(DATE(p.tanggal), "1970-01-01")
            ) as last_activity
        ')
        ->join('parents', 'parents.id = anak.parent_id', 'left')
        ->join('jenis_les', 'jenis_les.id = anak.jenis_les_id', 'left')
        ->join($latestPaymentSubquery, 'pembayaran_last.anak_id = anak.id', 'left', false)
        ->join('pembayaran p', 'p.id = pembayaran_last.last_payment_id', 'left', false)
        ->join($latestLatihanSubquery, 'l_last.anak_id = anak.id', 'left', false);
        
        // Apply filters
        if ($filters['aktif_50_hari'] == 'on') {
            $tanggalBatas = date('Y-m-d', strtotime('-50 days'));
            $query->where('(l_last.last_latihan >= "' . $tanggalBatas . '" OR DATE(p.tanggal) >= "' . $tanggalBatas . '")');
        }

        if ($filters['tidak_aktif_100_hari'] == 'on') {
            $tanggalBatas100 = date('Y-m-d', strtotime('-100 days'));
            $query->where('
                (
                    (l_last.last_latihan < "' . $tanggalBatas100 . '" OR l_last.last_latihan IS NULL)
                    AND 
                    (DATE(p.tanggal) < "' . $tanggalBatas100 . '" OR p.tanggal IS NULL)
                )
            ');
        }
         
         if (!empty($filters['jenis_kelamin'])) {
             if ($filters['jenis_kelamin'] == 'L') {
                 $query->where('anak.jenis_kelamin', 'Laki-laki');
             } else if ($filters['jenis_kelamin'] == 'P') {
                 $query->where('anak.jenis_kelamin', 'Perempuan');
             }
         }
        if (!empty($filters['jenis_les'])) {
            $query->where('jenis_les.id', $filters['jenis_les']);
        }
        if (!empty($filters['anak_id'])) {
            $query->where('anak.id', $filters['anak_id']);
        }
        if (!empty($filters['nama'])) {
            $query->like('anak.nama', $filters['nama']);
        }
        if (!empty($filters['panggilan'])) {
            $query->like('anak.nama_panggilan', $filters['panggilan']);
        }
        if (!empty($filters['sisa_pertemuan'])) {
            switch($filters['sisa_pertemuan']) {
                case 'kurang2':
                    $query->where('anak.sisa_pertemuan <', 2);
                    break;
                case '2sampai5':
                    $query->where('anak.sisa_pertemuan >=', 2)
                          ->where('anak.sisa_pertemuan <=', 5);
                    break;
                case 'lebih5':
                    $query->where('anak.sisa_pertemuan >', 5);
                    break;
            }
        }
        
        $perPageInput = $this->request->getGet('per_page') ?? session()->get('per_page') ?? '50';
        if (!in_array($perPageInput, ['50', '100', '200', 'all'])) {
            $perPageInput = '50';
        }
        session()->set('per_page', $perPageInput);
        $perPage = ($perPageInput === 'all') ? 999999 : (int) $perPageInput;

        $anak_list = $query->paginate($perPage);
        foreach ($anak_list as &$a) {
            // Hindari hitung ulang FIFO untuk tiap baris pada halaman list (sangat berat).
            $a['sisa_pertemuan_display'] = (int) ($a['sisa_pertemuan'] ?? 0);
        }
        unset($a);

        $data = [
            'title' => 'Daftar Anak',
            'active' => 'anak',
            'anak' => $anak_list,
            'pager' => $anakModel->pager,
            'jenis_les' => $jenisLesModel->findAll(),
            'filters' => $filters,  // Kirim filter ke view
            'perPage' => $perPageInput
        ];
        
        return view('admin/anak/index', $data);
    }

    public function detail($id)
    {
        // Pastikan user sudah login sebagai admin
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        // Ambil data anak berdasarkan ID
      
        // Ubah dari:
   
        // Menjadi:
        $anak = $this->anakModel->select('anak.*, parents.nama as nama_parent, parents.whatsapp as whatsapp_parent, jenis_les.nama_les as nama_les, jenis_les.harga as harga_les')
            ->join('parents', 'parents.id = anak.parent_id')
            ->join('jenis_les', 'jenis_les.id = anak.jenis_les_id')
            ->where('anak.id', $id)
            ->first();

        if (!$anak) {
            return redirect()->to('/admin/anak')->with('error', 'Data anak tidak ditemukan');
        }

        $pembayaran = $this->pembayaranModel->where('anak_id', $id)
            ->orderBy('tanggal', 'ASC')
            ->findAll();

        $db = \Config\Database::connect();
        $kehadiran = $db->table('latihan_attendance')
            ->select('latihan_attendance.*, schedules.tanggal, schedules.jam_mulai, schedules.materi')
            ->join('schedules', 'schedules.id = latihan_attendance.schedule_id')
            ->where('latihan_attendance.anak_id', $id)
            ->orderBy('schedules.tanggal', 'ASC')
            ->orderBy('schedules.jam_mulai', 'ASC')
            ->get()->getResultArray();

        $this->anakModel->recalculateSisaPertemuan($id);
        $breakdown = $this->anakModel->getPaketBreakdown((int) $id);
        $kehadiran = $this->anakModel->annotateKehadiranWithPaket((int) $id, $kehadiran);
        $anak['sisa_pertemuan'] = $breakdown['sisa_total'];

        $data = [
            'title' => 'Detail Anak',
            'anak' => $anak,
            'pembayaran' => $pembayaran,
            'kehadiran' => $kehadiran,
            'detail_sisa' => $breakdown['detail_sisa'],
            'breakdown' => $breakdown,
            'history_groups' => $breakdown['history_groups'],
        ];

        return view('admin/anak/detail', $data);
    }

    public function extendPaket()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $pembayaranId = $this->request->getPost('pembayaran_id');
        $newDate = $this->request->getPost('berlaku_sampai');

        if (!$pembayaranId || !$newDate) {
            return redirect()->back()->with('error', 'Data tidak lengkap');
        }

        $pembayaran = $this->pembayaranModel->find($pembayaranId);
        if (!$pembayaran) {
            return redirect()->back()->with('error', 'Pembayaran tidak ditemukan');
        }

        $update = $this->pembayaranModel->skipValidation(true)->update($pembayaranId, [
            'berlaku_sampai' => $newDate
        ]);

        if ($update) {
            $this->anakModel->recalculateSisaPertemuan($pembayaran['anak_id']);
            return redirect()->back()->with('success', 'Masa berlaku paket berhasil diperpanjang hingga ' . date('d-m-Y', strtotime($newDate)));
        }

        return redirect()->back()->with('error', 'Gagal memperbarui masa berlaku');
    }

    public function edit($id = null)
    {
        if ($id == null) {
            return redirect()->to('admin/anak')->with('error', 'ID Anak tidak ditemukan');
        }
        
        $anak = $this->anakModel->find($id);
        
        if (!$anak) {
            return redirect()->to('admin/anak')->with('error', 'Data anak tidak ditemukan');
        }

        // Ambil riwayat pembayaran
        $pembayaran = $this->pembayaranModel->where('anak_id', $id)
            ->orderBy('tanggal', 'ASC')
            ->findAll();

        // Ambil riwayat latihan (kehadiran)
        $db = \Config\Database::connect();
        $kehadiran = $db->table('latihan_attendance')
            ->select('latihan_attendance.*, schedules.tanggal, schedules.jam_mulai, schedules.materi')
            ->join('schedules', 'schedules.id = latihan_attendance.schedule_id')
            ->where('latihan_attendance.anak_id', $id)
            ->orderBy('schedules.tanggal', 'ASC')
            ->orderBy('schedules.jam_mulai', 'ASC')
            ->get()->getResultArray();

        // Tambahkan info Pertemuan Ke-X untuk setiap riwayat
        // Karena kita ingin riwayat historis, kita harus menghitung mundur atau berdasarkan total hadir saat itu
        $total_hadir_saat_ini = $db->table('latihan_attendance')
            ->where('anak_id', $id)
            ->where('status_kehadiran', 'hadir')
            ->countAllResults();

        // Total sesi dibayar
        $total_paket = $db->table('pembayaran')
            ->where('anak_id', $id)
            ->where('status', 'success')
            ->countAllResults();
        $total_sesi_dibayar = $total_paket * 4;

        $hadir_counter = 1; // Counter maju
        foreach ($kehadiran as &$k) {
            if ($k['status_kehadiran'] === 'hadir') {
                $sisa_saat_itu = $total_sesi_dibayar - $hadir_counter;
                
                if ($sisa_saat_itu >= 0) {
                    $k['pertemuan_ke'] = (($hadir_counter - 1) % 4) + 1;
                    $k['paket_ke'] = ceil($hadir_counter / 4);
                    if ($hadir_counter == 0) $k['paket_ke'] = 1;
                } else {
                    $k['pertemuan_ke'] = $hadir_counter;
                    $k['paket_ke'] = ceil($hadir_counter / 4);
                }
                $hadir_counter++;
            } else {
                $k['pertemuan_ke'] = '-';
                $k['paket_ke'] = '-';
            }
        }
        unset($k);
        
        $this->anakModel->syncKuotaFromRiwayat((int) $id);
        $anak = $this->anakModel->find($id);
        $paketStatus = $this->anakModel->getPaketSelesaiStatus((int) $id);

        $data = [
            'title' => 'Edit Data Anak',
            'active' => 'anak',
            'anak' => $anak,
            'jenisLes' => $this->jenisLesModel->findAll(),
            'pembayaran' => $pembayaran,
            'kehadiran' => $kehadiran,
            'paketStatus' => $paketStatus,
            'bolehPindahPaket' => $paketStatus['paket_selesai'],
        ];
        
        return view('admin/anak/edit', $data);
    }

    public function deletePembayaran($id)
    {
        $p = $this->pembayaranModel->find($id);
        if (!$p) return redirect()->back()->with('error', 'Data tidak ditemukan');

        $anakId = $p['anak_id'];
        $jumlah = (int)$p['jumlah_pertemuan'];

        $db = \Config\Database::connect();
        $db->transStart();

        // Hapus bukti di R2 jika ada
        if (!empty($p['bukti_pembayaran'])) {
            $this->r2->delete($p['bukti_pembayaran']);
        }

        $this->pembayaranModel->delete($id);

        $syncMsg = '';
        if ($p['status'] === 'success') {
            $sync = $this->anakModel->syncKuotaFromRiwayat($anakId);
            $syncMsg = ' ' . $sync['message'];
        }

        $db->transComplete();

        return redirect()->back()->with('success', 'Riwayat pembayaran dihapus.' . $syncMsg);
    }

    public function deleteKehadiran($id)
    {
        $db = \Config\Database::connect();
        $k = $db->table('latihan_attendance')->where('id', $id)->get()->getRowArray();
        if (!$k) return redirect()->back()->with('error', 'Data tidak ditemukan');

        $anakId = $k['anak_id'];

        $scheduleId = (int) ($k['schedule_id'] ?? 0);

        $db->transStart();
        $db->table('latihan_attendance')->where('id', $id)->delete();

        $sync = $this->anakModel->syncKuotaFromRiwayat($anakId, $scheduleId > 0 ? $scheduleId : null);

        $db->transComplete();

        return redirect()->back()->with('success', 'Riwayat latihan dihapus. ' . $sync['message']);
    }
    
    public function update($id = null)
    {
        if ($id == null) {
            return redirect()->to('admin/anak')->with('error', 'ID Anak tidak ditemukan');
        }
        
        $anak = $this->anakModel->find($id);
        
        if (!$anak) {
            return redirect()->to('admin/anak')->with('error', 'Data anak tidak ditemukan');
        }
        
        $rules = [
            'nama' => 'required|min_length[3]',
            'nama_panggilan' => 'permit_empty',
            'asal_sekolah' => 'permit_empty',
            'riwayat_penyakit' => 'permit_empty',
            'tanggal_lahir' => 'required|valid_date',
            'jenis_kelamin' => 'required|in_list[Laki-laki,Perempuan]',
            'jenis_les_id' => 'required|numeric',
            'status' => 'required|in_list[aktif,non-aktif]',
            'foto' => 'permit_empty|is_image[foto]|max_size[foto,2048]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $newJenisLesId = (int) $this->request->getPost('jenis_les_id');
        $currentJenisLesId = (int) $anak['jenis_les_id'];
        $jenisLesBerubah = $newJenisLesId !== $currentJenisLesId;

        if ($jenisLesBerubah) {
            $transferCheck = $this->anakModel->validateTransferJenisLes((int) $id, $newJenisLesId, $currentJenisLesId);
            if (!$transferCheck['allowed']) {
                return redirect()->back()->withInput()->with('error', $transferCheck['message']);
            }
        }
        
        $data = [
            'nama' => $this->request->getPost('nama'),
            'nama_panggilan' => $this->request->getPost('nama_panggilan'),
            'asal_sekolah' => $this->request->getPost('asal_sekolah'),
            'riwayat_penyakit' => $this->request->getPost('riwayat_penyakit'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'jenis_les_id' => $newJenisLesId,
            'status' => $this->request->getPost('status'),
        ];
        
        // Handle foto upload
        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            // Delete old file if exists
            $oldFoto = $this->request->getPost('old_foto');
            if (!empty($oldFoto)) {
                $this->r2->delete($oldFoto);
            }
            
            // Upload to Cloudflare R2
            $key = 'anak/' . $foto->getRandomName();
            $uploadedPath = $this->r2->upload($key, fopen($foto->getTempName(), 'r'), $foto->getMimeType());
            if ($uploadedPath) {
                $data['foto'] = $key;
            }
        }
        
        $this->anakModel->update($id, $data);
        $sync = $this->anakModel->syncKuotaFromRiwayat((int) $id);

        $successMessage = $jenisLesBerubah
            ? 'Data anak diperbarui. Jenis les dipindah. ' . $sync['message']
            : 'Data anak diperbarui. ' . $sync['message'];
        
        return redirect()->to('admin/anak/detail/' . $id)->with('success', $successMessage);
    }

    public function delete($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        $anak = $this->anakModel->find($id);
        if (!$anak) {
            return redirect()->to('admin/anak')->with('error', 'Data anak tidak ditemukan');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Fetch payments to delete R2 files
            $payments = $db->table('pembayaran')->where('anak_id', $id)->get()->getResultArray();
            foreach ($payments as $p) {
                if (!empty($p['bukti_pembayaran'])) {
                    $this->r2->delete($p['bukti_pembayaran']);
                }
            }

            // Delete related records manually to avoid Foreign Key Constraint violations
            $db->table('pembayaran')->where('anak_id', $id)->delete();
            $db->table('schedule_students')->where('anak_id', $id)->delete();
            $db->table('latihan_attendance')->where('anak_id', $id)->delete();
            $db->table('jadwal_peserta')->where('anak_id', $id)->delete();

            // Hapus foto dari Cloudflare R2 jika ada
            if (!empty($anak['foto'])) {
                $this->r2->delete($anak['foto']);
            }

            $this->anakModel->delete($id);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Gagal menghapus dari database');
            }

            return redirect()->to('admin/anak')->with('success', 'Data anak beserta riwayat pembayaran dan jadwal berhasil dihapus permanen');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to('admin/anak')->with('error', 'Gagal menghapus data anak: ' . $e->getMessage());
        }
    }

    public function cetakKartuView()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        $data = [
            'title' => 'Menu Cetak Kartu',
            'active' => 'cetak-kartu'
        ];

        return view('admin/anak/cetak_kartu_menu', $data);
    }

    public function cetakKartu()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        $anakIds = $this->request->getPost('anak_id');

        if (empty($anakIds) || !is_array($anakIds)) {
            return redirect()->back()->with('error', 'Pilih minimal satu anak untuk dicetak kartunya.');
        }

        $anakList = $this->anakModel->select('anak.*, jenis_les.nama_les, parents.nama as nama_parent, parents.whatsapp')
            ->join('jenis_les', 'jenis_les.id = anak.jenis_les_id', 'left')
            ->join('parents', 'parents.id = anak.parent_id', 'left')
            ->whereIn('anak.id', $anakIds)
            ->findAll();

        if (empty($anakList)) {
            return redirect()->back()->with('error', 'Data anak tidak ditemukan.');
        }

        $data = [
            'title' => 'Cetak Kartu Peserta',
            'anakList' => $anakList
        ];

        return view('admin/anak/cetak_kartu', $data);
    }
}
