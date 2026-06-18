<?php

namespace App\Controllers;

use App\Models\PembayaranModel;
use App\Models\AnakModel;
use App\Models\JenisLesModel;
use App\Models\SettingModel;

class Admin extends BaseController
{
    protected $pembayaranModel;
    protected $anakModel;
    protected $jenisLesModel;
    protected $coachModel;

    public function __construct()
    {
        $this->pembayaranModel = new PembayaranModel();
        $this->anakModel = new AnakModel();
        $this->jenisLesModel = new JenisLesModel();
        $this->coachModel = new \App\Models\CoachModel();
    }

    public function dashboard()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        $db = \Config\Database::connect();
        $today = date('Y-m-d');
        $startDate40Hari = date('Y-m-d', strtotime('-40 days'));
        $startMonth = date('Y-m-01 00:00:00');
        $endMonth = date('Y-m-t 23:59:59');

        // Ambil data untuk dashboard
        $totalAnak = $this->anakModel->countAll();
        $totalPembayaran = $this->pembayaranModel->where('status', 'success')->countAllResults();
        $totalJenisLes = $this->jenisLesModel->countAll();
        $pendingPayments = $this->pembayaranModel->countPendingPayments();

        // Siswa aktif = anak yang latihan (hadir) dalam 40 hari terakhir.
        $siswaAktif = $db->table('latihan_attendance la')
            ->select('la.anak_id')
            ->join('schedules s', 's.id = la.schedule_id', 'left')
            ->where('la.status_kehadiran', 'hadir')
            ->groupStart()
                ->where('s.tanggal >=', $startDate40Hari)
                ->orWhere('DATE(la.created_at) >=', $startDate40Hari)
            ->groupEnd()
            ->groupBy('la.anak_id')
            ->countAllResults();

        $siswaPrivate = $db->table('anak a')
            ->join('jenis_les jl', 'jl.id = a.jenis_les_id', 'left')
            ->where('LOWER(TRIM(jl.nama_les))', 'private')
            ->countAllResults();

        $siswaReguler = $db->table('anak a')
            ->join('jenis_les jl', 'jl.id = a.jenis_les_id', 'left')
            ->groupStart()
                ->where('LOWER(TRIM(jl.nama_les))', 'reguler')
                ->orWhere('LOWER(TRIM(jl.nama_les))', 'regular')
            ->groupEnd()
            ->countAllResults();

        $peringkatRajin = $db->table('latihan_attendance la')
            ->select('a.nama, COUNT(la.id) AS total_hadir')
            ->join('anak a', 'a.id = la.anak_id', 'left')
            ->join('schedules s', 's.id = la.schedule_id', 'left')
            ->where('la.status_kehadiran', 'hadir')
            ->groupStart()
                ->where('s.tanggal >=', $startDate40Hari)
                ->orWhere('DATE(la.created_at) >=', $startDate40Hari)
            ->groupEnd()
            ->groupBy('la.anak_id, a.nama')
            ->orderBy('total_hadir', 'DESC')
            ->limit(7)
            ->get()
            ->getResultArray();

        $peringkatNama = [];
        $peringkatJumlah = [];
        foreach ($peringkatRajin as $row) {
            $peringkatNama[] = (string) ($row['nama'] ?? 'Tanpa Nama');
            $peringkatJumlah[] = (int) ($row['total_hadir'] ?? 0);
        }

        // Pendapatan bulan ini dari pembayaran approve admin (status success).
        // Nilai per pembayaran:
        // - private: coach 360rb, kolam 240rb
        // - reguler: coach 200rb, kolam 100rb
        $approvedPaymentsThisMonth = $db->table('pembayaran p')
            ->select('jl.nama_les')
            ->join('anak a', 'a.id = p.anak_id', 'left')
            ->join('jenis_les jl', 'jl.id = a.jenis_les_id', 'left')
            ->where('p.status', 'success')
            ->where('p.tanggal >=', $startMonth)
            ->where('p.tanggal <=', $endMonth)
            ->get()
            ->getResultArray();

        $pendapatanCoachBulanIni = 0;
        $pendapatanKolamBulanIni = 0;
        foreach ($approvedPaymentsThisMonth as $payment) {
            $namaLes = strtolower(trim((string) ($payment['nama_les'] ?? '')));
            if ($namaLes === 'private') {
                $pendapatanCoachBulanIni += 360000;
                $pendapatanKolamBulanIni += 240000;
            } elseif ($namaLes === 'reguler' || $namaLes === 'regular') {
                $pendapatanCoachBulanIni += 200000;
                $pendapatanKolamBulanIni += 100000;
            }
        }
        
        // Siswa Perlu Tagihan: sisa pertemuan <= 0 dan terakhir latihan dalam 50 hari terakhir.
        $startDate50Hari = date('Y-m-d', strtotime('-50 days'));
        $siswaPerluTagihan = $db->table('anak a')
            ->select('a.id, a.nama as nama_anak, a.sisa_pertemuan, p.nama as nama_parent, p.whatsapp, MAX(COALESCE(s.tanggal, DATE(la.created_at))) as last_latihan')
            ->join('parents p', 'p.id = a.parent_id', 'left')
            ->join('latihan_attendance la', 'la.anak_id = a.id', 'left')
            ->join('schedules s', 's.id = la.schedule_id', 'left')
            ->where('a.status', 'aktif')
            ->where('a.sisa_pertemuan <=', 0)
            ->where('la.status_kehadiran', 'hadir')
            ->groupBy('a.id, a.nama, a.sisa_pertemuan, p.nama, p.whatsapp')
            ->having('MAX(COALESCE(s.tanggal, DATE(la.created_at))) >=', $startDate50Hari)
            ->orderBy('last_latihan', 'DESC')
            ->get()
            ->getResultArray();

        // Siswa Hampir Expired Waktu: punya sisa pertemuan tapi sudah hampir 90 hari dari pembayaran terakhir (kurang dari 12 hari lagi).
        $tanggalBatasExpired = date('Y-m-d', strtotime('-78 days')); // 90 - 12 = 78 hari yang lalu
        $subQueryPembayaran = $db->table('pembayaran')
            ->select('anak_id, MAX(tanggal) as last_payment_date')
            ->where('status', 'success')
            ->groupBy('anak_id');

        $siswaHampirExpired = $db->table('anak a')
            ->select('a.id, a.nama as nama_anak, a.sisa_pertemuan, p.nama as nama_parent, p.whatsapp, pay.last_payment_date')
            ->join('parents p', 'p.id = a.parent_id', 'left')
            ->join("({$subQueryPembayaran->getCompiledSelect()}) pay", 'pay.anak_id = a.id', 'inner')
            ->where('a.status', 'aktif')
            ->where('a.sisa_pertemuan >', 0)
            ->where('pay.last_payment_date <=', $tanggalBatasExpired)
            ->orderBy('pay.last_payment_date', 'ASC')
            ->get()
            ->getResultArray();

        // Siswa Tidak Aktif > 100 Hari (tanpa latihan & pembayaran)
        $tanggalBatas100 = date('Y-m-d', strtotime('-100 days'));
        $siswaTidakAktif = $db->table('anak a')
            ->select('a.id, a.nama as nama_anak, p.nama as nama_parent, p.whatsapp, l_last.last_latihan, pay.last_payment_date')
            ->join('parents p', 'p.id = a.parent_id', 'left')
            ->join('(SELECT la.anak_id, MAX(COALESCE(s.tanggal, DATE(la.created_at))) AS last_latihan FROM latihan_attendance la LEFT JOIN schedules s ON s.id = la.schedule_id WHERE la.status_kehadiran = "hadir" GROUP BY la.anak_id) l_last', 'l_last.anak_id = a.id', 'left')
            ->join('(SELECT anak_id, MAX(tanggal) AS last_payment_date FROM pembayaran WHERE status = "success" GROUP BY anak_id) pay', 'pay.anak_id = a.id', 'left')
            ->where('(l_last.last_latihan <= "'.$tanggalBatas100.'" OR l_last.last_latihan IS NULL)')
            ->where('(DATE(pay.last_payment_date) <= "'.$tanggalBatas100.'" OR pay.last_payment_date IS NULL)')
            ->orderBy('a.id', 'DESC')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Dashboard Admin',
            'nama' => session()->get('nama'),
            'totalAnak' => $totalAnak,
            'totalPembayaran' => $totalPembayaran,
            'totalJenisLes' => $totalJenisLes,
            'pendingPayments' => $pendingPayments,
            'siswaAktif' => $siswaAktif,
            'siswaPrivate' => $siswaPrivate,
            'siswaReguler' => $siswaReguler,
            'pendapatanCoachBulanIni' => $pendapatanCoachBulanIni,
            'pendapatanKolamBulanIni' => $pendapatanKolamBulanIni,
            'periodeSiswaAktifDari' => $startDate40Hari,
            'periodeSiswaAktifSampai' => $today,
            'siswaPerluTagihan' => $siswaPerluTagihan,
            'siswaHampirExpired' => $this->anakModel->getNearExpiredPackages(30),
            'siswaTidakAktif' => $siswaTidakAktif,
            'grafikRingkasanSiswa' => [
                'labels' => ['Total Siswa', 'Siswa Aktif', 'Siswa Reguler', 'Siswa Private'],
                'data' => [(int) $totalAnak, (int) $siswaAktif, (int) $siswaReguler, (int) $siswaPrivate],
            ],
            'grafikPeringkatRajin' => [
                'labels' => $peringkatNama,
                'data' => $peringkatJumlah,
            ],
        ];
        
        return view('admin/dashboard', $data);
    }
    
    public function jenisLes()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }
        
        $jenisLesModel = new JenisLesModel();
        $data = [
            'title' => 'Kelola Jenis Les',
            'nama' => session()->get('nama'),
            'jenisLes' => $jenisLesModel->findAll()
        ];
        
        return view('admin/jenis_les', $data);
    }

    public function riwayat()
    {
        return view('admin/pembayaran/riwayat', [
            'title' => 'Riwayat Pembayaran',
            // Add any other data you need to pass to the view
        ]);
    }

    public function coach()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }
        
        $levelModel = new \App\Models\LevelModel();
        $levels = $levelModel->orderBy('urutan', 'ASC')->findAll();
        
        $settingModel = new SettingModel();
        $regSetting = $settingModel->getSetting('coach_registration_open');
        $registrationOpen = $regSetting ? $regSetting['value'] == '1' : false;
        
        $data = [
            'title' => 'Kelola Pelatih',
            'active' => 'coach',
            'nama' => session()->get('nama'),
            'coaches' => $this->coachModel->findAll(),
            'levels' => $levels,
            'registrationOpen' => $registrationOpen
        ];
        
        return view('admin/coach/index', $data);
    }

    public function coachSave()
    {
        $assignedLevels = $this->request->getPost('assigned_levels');
        $assignedLevelsStr = !empty($assignedLevels) ? implode(',', $assignedLevels) : null;

        $data = [
            'nama' => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
            'telepon' => $this->request->getPost('telepon'),
            'alamat' => $this->request->getPost('alamat'),
            'keahlian' => $this->request->getPost('keahlian') ?? '',
            'pengalaman' => $this->request->getPost('pengalaman'),
            'assigned_levels' => $assignedLevelsStr,
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT)
        ];

        if ($this->coachModel->save($data)) {
            session()->setFlashdata('success', 'Data pelatih berhasil ditambahkan');
        } else {
            session()->setFlashdata('error', 'Gagal menambahkan data pelatih');
        }

        return redirect()->to('admin/coach');
    }

    public function coachUpdate($id)
    {
        $assignedLevels = $this->request->getPost('assigned_levels');
        $assignedLevelsStr = !empty($assignedLevels) ? implode(',', $assignedLevels) : null;

        $data = [
            'id' => $id,
            'nama' => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
            'telepon' => $this->request->getPost('telepon'),
            'alamat' => $this->request->getPost('alamat'),
            'keahlian' => $this->request->getPost('keahlian') ?? '',
            'pengalaman' => $this->request->getPost('pengalaman'),
            'assigned_levels' => $assignedLevelsStr
        ];

        // Update password hanya jika diisi
        if ($password = $this->request->getPost('password')) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($this->coachModel->save($data)) {
            session()->setFlashdata('success', 'Data pelatih berhasil diperbarui');
        } else {
            session()->setFlashdata('error', 'Gagal memperbarui data pelatih');
        }

        return redirect()->to('admin/coach');
    }

    public function coachDelete($id)
    {
        if ($this->coachModel->delete($id)) {
            session()->setFlashdata('success', 'Data pelatih berhasil dihapus');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus data pelatih');
        }

        return redirect()->to('admin/coach');
    }

    public function toggleCoachRegistration()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        $settingModel = new SettingModel();
        $current = $settingModel->getSetting('coach_registration_open');
        $newValue = ($current && $current['value'] == '1') ? '0' : '1';
        $settingModel->updateSetting('coach_registration_open', $newValue);

        $status = $newValue == '1' ? 'dibuka' : 'ditutup';
        session()->setFlashdata('success', "Pendaftaran coach berhasil {$status}.");
        return redirect()->to('admin/coach');
    }

    public function cetakPembayaran($id)
    {
        $pembayaranModel = new \App\Models\PembayaranModel();
        $pembayaran = $pembayaranModel->getPembayaranDetail($id);
        
        if (!$pembayaran) {
            return redirect()->to('/admin/pembayaran')->with('error', 'Data pembayaran tidak ditemukan');
        }
        
        $data = [
            'pembayaran' => $pembayaran
        ];
        
        return view('admin/pembayaran/cetak', $data);
    }
}