<?php

namespace App\Controllers;

use App\Models\ParentModel;
use App\Models\AnakModel;
use App\Models\JenisLesModel;
use App\Models\JadwalModel;
use App\Models\ScheduleStudentModel;
use App\Models\PembayaranModel;
use App\Libraries\R2Client;

class ParentAuth extends BaseController
{
    protected $r2;

    public function __construct()
    {
        $this->r2 = new R2Client();
    }
    private const PERTEMUAN_PAKET = 4;
    private const TARIF_PRIVATE_PER_PERTEMUAN = 150000;
    private const TARIF_REGULER_PER_PERTEMUAN = 75000;

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

    public function index()
    {
        $data = [
            'title' => 'Login Orang Tua/Wali'
        ];
        return view('parent/login', $data);
    }

    public function register()
    {
        $data = [
            'title' => 'Register Orang Tua/Wali'
        ];
        return view('parent/register', $data);
    }

    // Add this save method to handle registration form submission
    public function save()
    {
        // Validate form input
        $rules = [
            'nama' => 'required',
            'alamat' => 'required',
            'whatsapp' => 'required|is_unique[parents.whatsapp]',
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]'
        ];

        if ($this->validate($rules)) {
            $parentModel = new ParentModel();
            
            $whatsapp = $this->request->getVar('whatsapp');
            
            // Normalisasi WhatsApp sebelum simpan (agar ID sinkron antara login & data)
            $digits = preg_replace('/\D+/', '', (string) $whatsapp);
            if (str_starts_with($digits, '62')) {
                $digits = '0' . substr($digits, 2);
            } elseif (str_starts_with($digits, '8')) {
                $digits = '0' . $digits;
            }
            
            $data = [
                'nama' => $this->request->getVar('nama'),
                'alamat' => $this->request->getVar('alamat'),
                'whatsapp' => $digits,
                'password' => $this->request->getVar('password')
            ];
            
            $parentModel->save($data);
            
            session()->setFlashdata('success', 'Registrasi berhasil. Silakan login.');
            return redirect()->to('/parent/login');
        } else {
            $data = [
                'title' => 'Register Orang Tua/Wali',
                'validation' => $this->validator
            ];
            return view('parent/register', $data);
        }
    }

    public function login()
    {
        $session = session();
        $parentModel = new ParentModel();
        
        $whatsapp = $this->request->getVar('whatsapp');
        $password = $this->request->getVar('password');
        
        $normalizePhone = static function (?string $value): string {
            $digits = preg_replace('/\D+/', '', (string) $value);

            if (str_starts_with($digits, '62')) {
                $digits = '0' . substr($digits, 2);
            } elseif (str_starts_with($digits, '8')) {
                $digits = '0' . $digits;
            }

            return $digits;
        };

        $whatsappNormalized = $normalizePhone($whatsapp);
        $parent = $parentModel->where('whatsapp', $whatsappNormalized !== '' ? $whatsappNormalized : (string) $whatsapp)->first();
        if (!$parent && $whatsappNormalized !== '' && $whatsappNormalized !== (string) $whatsapp) {
            $parent = $parentModel->where('whatsapp', (string) $whatsapp)->first();
        }
        
        if ($parent) {
            $passwordOk = $password === $parent['password'];
            $phoneAsPasswordOk = $normalizePhone($password) !== '' && $normalizePhone($password) === $normalizePhone($parent['whatsapp']);

            if ($passwordOk || $phoneAsPasswordOk) {
                $ses_data = [
                    'parent_id' => $parent['id'],
                    'parent_nama' => $parent['nama'],
                    'parent_whatsapp' => $parent['whatsapp'],
                    'parent_isLoggedIn' => TRUE
                ];
                
                $session->set($ses_data);
                return redirect()->to('/parent/dashboard');
            } else {
                $session->setFlashdata('msg', 'Password salah (bisa juga pakai nomor WhatsApp sebagai password)');
                return redirect()->to('/parent/login');
            }
        } else {
            $session->setFlashdata('msg', 'Nomor WhatsApp tidak ditemukan');
            return redirect()->to('/parent/login');
        }
    }

    public function dashboard()
    {
        if (!session()->get('parent_isLoggedIn')) {
            return redirect()->to('/parent/login');
        }
        
        $anakModel = new AnakModel();
        $jenisLesModel = new JenisLesModel();
        $pembayaranModel = new \App\Models\PembayaranModel();
        
        $parent_id = session()->get('parent_id'); 
        $anak = $anakModel->where('parent_id', $parent_id)->findAll();
        
        // Query untuk pembayaran
        $pembayaran = $pembayaranModel
            ->select('pembayaran.*, anak.nama as nama_anak')
            ->join('anak', 'anak.id = pembayaran.anak_id')
            ->where('anak.parent_id', $parent_id)
            ->orderBy('pembayaran.tanggal', 'DESC')
            ->findAll();
    
        $scheduleModel = new \App\Models\JadwalModel();
        $scheduleStudentModel = new \App\Models\ScheduleStudentModel();
        
        // Ambil jadwal yang tersedia
        $jadwal = $scheduleModel->getAvailableSchedules();
        
        // Modifikasi query untuk jadwal terdaftar
        $db = \Config\Database::connect();
        $jadwal_terdaftar = $db->table('schedule_students ss')
            ->select('s.tanggal, s.jam_mulai, s.jam_selesai, a.nama as nama_anak, jl.nama_les, s.materi')
            ->join('schedules s', 's.id = ss.schedule_id')
            ->join('anak a', 'a.id = ss.anak_id')
            ->join('jenis_les jl', 'jl.id = a.jenis_les_id')
            ->where('a.parent_id', $parent_id)
            ->groupBy('s.tanggal, s.jam_mulai, s.jam_selesai, a.nama, jl.nama_les, s.materi')
            ->orderBy('s.tanggal', 'ASC')
            ->orderBy('s.jam_mulai', 'ASC')
            ->get()
            ->getResultArray();
        
        // Get attendance data
        $latihan_attendance = $db->table('schedule_students ss')
            ->select('s.tanggal, s.jam_mulai, s.jam_selesai, a.nama as nama_anak, ss.status as status_kehadiran, ss.anak_id, s.materi')
            ->join('schedules s', 's.id = ss.schedule_id')
            ->join('anak a', 'a.id = ss.anak_id')
            ->where('a.parent_id', $parent_id)
            ->where('ss.status !=', 'belum_hadir')
            ->orderBy('s.tanggal', 'DESC')
            ->get()
            ->getResultArray();
        
        $anakList = $anakModel->getAnakWithJenisLesByParentId($parent_id);
        foreach ($anakList as &$child) {
            $anakModel->recalculateSisaPertemuan((int) $child['id']);
            $bd = $anakModel->getPaketBreakdown((int) $child['id']);
            $child['sisa_pertemuan'] = $bd['sisa_total'];
            $child['berlaku_sampai'] = $bd['berlaku_sampai'];
            $child['has_expired_hangus'] = $bd['has_expired_hangus'];
            $child['hangus_total'] = $bd['hangus_total'];
            $child['paket_breakdown'] = $bd;
            $child['history_groups'] = $bd['history_groups'];
            $child['has_pending_pembayaran'] = $pembayaranModel->hasPendingForAnak((int) $child['id']);
            $sisa = (int) ($child['sisa_pertemuan'] ?? 0);
            $child['can_pay'] = $sisa <= 0 && !$child['has_pending_pembayaran'];
        }
        unset($child);

        $data = [
            'title' => 'Dashboard Orang Tua/Wali',
            'anak' => $anakList,
            'jenis_les' => $jenisLesModel->findAll(),
            'pembayaran' => $pembayaran,
            'jadwal' => $jadwal,
            'jadwal_terdaftar' => $jadwal_terdaftar,
            'latihan_attendance' => $latihan_attendance,
            'near_expired' => $anakModel->getNearExpiredPackages(30, (int) $parent_id)
        ];

        return view('parent/dashboard', $data);
    }
    
    public function tambahAnak()
    {
        if (!session()->get('parent_isLoggedIn')) {
            return redirect()->to('/parent/login');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'nama' => 'required|min_length[3]',
            'nama_panggilan' => 'permit_empty|min_length[2]',
            'asal_sekolah' => 'permit_empty',
            'tanggal_lahir' => 'required|valid_date',
            'jenis_kelamin' => 'required|in_list[Laki-laki,Perempuan]',
            'jenis_les_id' => 'required|numeric',
            'riwayat_penyakit' => 'permit_empty',
            'foto' => [
                'rules' => 'permit_empty|max_size[foto,1024]|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'max_size' => 'Ukuran foto terlalu besar (max 1MB)',
                    'is_image' => 'File yang dipilih bukan gambar',
                    'mime_in' => 'Format foto harus JPG/JPEG/PNG'
                ]
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $foto = $this->request->getFile('foto');
        $namaFoto = '';
        
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $tempPath = $foto->getTempName();
            try {
                \Config\Services::image()->withFile($tempPath)->resize(800, 800, true, 'auto')->save($tempPath, 70);
            } catch (\Exception $e) { }
            
            $key = 'anak/' . $foto->getRandomName();
            $uploadedPath = $this->r2->upload($key, fopen($tempPath, 'r'), $foto->getMimeType());
            if ($uploadedPath) {
                $namaFoto = $key;
            }
        }

        $anakModel = new \App\Models\AnakModel();
        $data = [
            'parent_id' => session()->get('parent_id'),
            'nama' => $this->request->getPost('nama'),
            'nama_panggilan' => $this->request->getPost('nama_panggilan'),
            'asal_sekolah' => $this->request->getPost('asal_sekolah'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'jenis_les_id' => $this->request->getPost('jenis_les_id'),
            'riwayat_penyakit' => $this->request->getPost('riwayat_penyakit'),
            'foto' => $namaFoto,
            'status' => 'menunggu',
            'sisa_pertemuan' => 0
        ];

        try {
            $anakModel->insert($data);
            return redirect()->to('/parent/dashboard')->with('success', 'Data anak berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data');
        }
    }
    
    public function updateAnak()
    {
        if (!session()->get('parent_isLoggedIn')) {
            return redirect()->to('/parent/login');
        }
        
        $anakModel = new AnakModel();
        $id = $this->request->getVar('id');
        
        // Pastikan anak ini milik parent yang sedang login
        $anak = $anakModel->find($id);
        if ($anak['parent_id'] != session()->get('parent_id')) {
            session()->setFlashdata('error', 'Anda tidak memiliki akses untuk mengubah data anak ini.');
            return redirect()->to('/parent/dashboard');
        }
        
        $newJenisLesId = (int) $this->request->getVar('jenis_les_id');
        $currentJenisLesId = (int) $anak['jenis_les_id'];

        if ($newJenisLesId !== $currentJenisLesId) {
            $transferCheck = $anakModel->validateTransferJenisLes((int) $id, $newJenisLesId, $currentJenisLesId);
            if (!$transferCheck['allowed']) {
                session()->setFlashdata('error', $transferCheck['message']);
                return redirect()->to('/parent/dashboard');
            }
        }

        // Inisialisasi data
        $data = [
            'nama' => $this->request->getVar('nama'),
            'tanggal_lahir' => $this->request->getVar('tanggal_lahir'),
            'jenis_kelamin' => $this->request->getVar('jenis_kelamin'),
            'jenis_les_id' => $newJenisLesId,
        ];
        
        // Proses upload foto jika ada
        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            // Hapus foto lama jika ada
            $oldFoto = $anak['foto'];
            if (!empty($oldFoto)) {
                $this->r2->delete($oldFoto);
            }
            
            // Upload to Cloudflare R2
             $tempPath = $foto->getTempName();
             try {
                 \Config\Services::image()->withFile($tempPath)->resize(800, 800, true, 'auto')->save($tempPath, 70);
             } catch (\Exception $e) { }

             $key = 'anak/' . $foto->getRandomName();
             $uploadedPath = $this->r2->upload($key, fopen($tempPath, 'r'), $foto->getMimeType());
             if ($uploadedPath) {
                 $data['foto'] = $key;
             }
        }
        
        $anakModel->update($id, $data);
        
        session()->setFlashdata('success', 'Data anak berhasil diperbarui.');
        return redirect()->to('/parent/dashboard');
    }
    
    public function hapusAnak($id)
    {
        if (!session()->get('parent_isLoggedIn')) {
            return redirect()->to('/parent/login');
        }
        
        $anakModel = new AnakModel();
        
        // Pastikan anak ini milik parent yang sedang login
        $anak = $anakModel->find($id);
        if ($anak['parent_id'] != session()->get('parent_id')) {
            session()->setFlashdata('error', 'Anda tidak memiliki akses untuk menghapus data anak ini.');
            return redirect()->to('/parent/dashboard');
        }
        
        $anakModel->delete($id);
        
        session()->setFlashdata('success', 'Data anak berhasil dihapus.');
        return redirect()->to('/parent/dashboard');
    }

    public function logout()
    {
        $session = session();
        $session->remove(['parent_id', 'parent_nama', 'parent_whatsapp', 'parent_isLoggedIn']); // Update this line
        return redirect()->to('/parent/login');
    }

    public function daftar_jadwal()
    {
        // Tambahkan debugging
        $postData = $this->request->getPost();
        log_message('debug', 'POST Data: ' . json_encode($postData));
        
        // Cek apakah parent sudah login
        if (!session()->get('parent_isLoggedIn')) {
            return redirect()->to('/parent/login');
        }
    
        // Validasi input
        $rules = [
            'schedule_id' => 'required|numeric',
            'anak_id' => 'required|numeric'
        ];
    
        if (!$this->validate($rules)) {
            session()->setFlashdata('error', 'Data tidak valid');
            return redirect()->to('/parent/dashboard')->withInput();
        }
    
        $schedule_id = $this->request->getPost('schedule_id');
        $anak_id = $this->request->getPost('anak_id');
    
        // Inisialisasi model yang diperlukan
        $scheduleModel = new JadwalModel();
        $anakModel = new AnakModel();
        $scheduleStudentModel = new \App\Models\ScheduleStudentModel();
    
        // Cek apakah jadwal masih tersedia
        $jadwal = $scheduleModel->find($schedule_id);
        if (!$jadwal || $jadwal['status'] !== 'aktif') {
            session()->setFlashdata('error', 'Jadwal tidak tersedia');
            return redirect()->to('/parent/dashboard')->withInput();
        }
    
        // Cek apakah anak adalah milik parent yang sedang login
        $anak = $anakModel->find($anak_id);
        if (!$anak || $anak['parent_id'] != session()->get('parent_id')) {
            session()->setFlashdata('error', 'Data anak tidak valid');
            return redirect()->to('/parent/dashboard')->withInput();
        }
    
        // Cek apakah anak sudah terdaftar di jadwal ini (Cek di kedua tabel)
        $existingStudent = $scheduleStudentModel->where([
            'schedule_id' => $schedule_id,
            'anak_id' => $anak_id
        ])->first();

        $db = \Config\Database::connect();
        $existingAttendance = $db->table('latihan_attendance')->where([
            'schedule_id' => $schedule_id,
            'anak_id' => $anak_id
        ])->get()->getRow();
    
        if ($existingStudent || $existingAttendance) {
            session()->setFlashdata('error', 'Anak sudah terdaftar pada jadwal ini');
            return redirect()->to('/parent/dashboard')->withInput();
        }
    
        // Cek kapasitas jadwal
        $totalTerdaftar = $scheduleStudentModel->where('schedule_id', $schedule_id)->countAllResults();
        if ($totalTerdaftar >= $jadwal['kapasitas']) {
            session()->setFlashdata('error', 'Jadwal sudah penuh');
            return redirect()->to('/parent/dashboard')->withInput();
        }
    
        // Cek sisa pertemuan anak
        if ($anak['sisa_pertemuan'] <= 0) {
            session()->setFlashdata('error', 'Sisa pertemuan tidak mencukupi');
            return redirect()->to('/parent/dashboard')->withInput();
        }
    
        // Proses pendaftaran
        $data = [
            'schedule_id' => $schedule_id,
            'anak_id' => $anak_id,
            'status' => 'terdaftar',
            'created_at' => date('Y-m-d H:i:s')
        ];
    
        try {
            $db = \Config\Database::connect();
            $db->transStart();
    
            // Insert ke tabel schedule_students
            $scheduleStudentModel->insert($data);
    
            // Update sisa pertemuan anak
            $anakModel->update($anak_id, [
                'sisa_pertemuan' => $anak['sisa_pertemuan'] - 1
            ]);
    
            $db->transComplete();
    
            if ($db->transStatus() === false) {
                throw new \Exception('Gagal mendaftar jadwal');
            }
    
            session()->setFlashdata('success', 'Berhasil mendaftar jadwal');
            return redirect()->to('/parent/dashboard');
    
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
            return redirect()->to('/parent/dashboard')->withInput();
        }
    }
    
    public function konfirmasiPembayaran()
    {
        // Check if user is logged in
        if (!session()->get('parent_isLoggedIn')) {
            return redirect()->to('/parent/login');
        }

        $anak_id = (int) $this->request->getPost('anak_id');
        if ($anak_id <= 0) {
            return redirect()->back()->with('error', 'Data anak tidak valid.');
        }

        $parent_id = (int) session()->get('parent_id');
        $pembayaranModel = new PembayaranModel();
        $paymentCheck = $pembayaranModel->validateParentCanPay($anak_id, $parent_id);
        if (!$paymentCheck['allowed']) {
            return redirect()->back()->with('error', $paymentCheck['message']);
        }

        $anakModel = new AnakModel();
        $anak = $anakModel
            ->select('anak.*, jenis_les.nama_les')
            ->join('jenis_les', 'jenis_les.id = anak.jenis_les_id', 'left')
            ->where('anak.id', $anak_id)
            ->where('anak.parent_id', $parent_id)
            ->first();

        if (!$anak) {
            return redirect()->back()->with('error', 'Data anak tidak ditemukan atau bukan milik akun Anda.');
        }

        $tarifPerPertemuan = $this->getTarifPerPertemuanByNamaLes((string) ($anak['nama_les'] ?? ''));
        if ($tarifPerPertemuan <= 0) {
            return redirect()->back()->with('error', 'Jenis les anak belum valid. Hubungi admin untuk pengaturan tarif.');
        }

        // Selalu paksa paket 4 pertemuan.
        $jumlah_pertemuan = self::PERTEMUAN_PAKET;
        $total = $tarifPerPertemuan * self::PERTEMUAN_PAKET;
        if ($total <= 0) {
            return redirect()->back()->with('error', 'Nominal pembayaran tidak valid.');
        }

        $metode_pembayaran = $this->request->getPost('metode_pembayaran');
        if (empty($metode_pembayaran)) {
            return redirect()->back()->with('error', 'Metode pembayaran wajib diisi.');
        }
        
        // Prepare data for insertion
        $data = [
            'anak_id' => $anak_id,
            'parent_id' => session()->get('parent_id'), 
            'jenis_les_id' => $anak['jenis_les_id'], // Simpan snapshot jenis les saat ini
            'jumlah_pertemuan' => $jumlah_pertemuan,
            'total' => $total,
            'metode_pembayaran' => $metode_pembayaran,
            'tanggal' => date('Y-m-d H:i:s'),
            'status' => 'pending'
        ];
        
        // Handle file upload for payment proof if provided
        $bukti = $this->request->getFile('bukti_pembayaran');
        if ($bukti && $bukti->isValid() && !$bukti->hasMoved()) {
            // Upload to Cloudflare R2
            $tempPath = $bukti->getTempName();
            try {
                \Config\Services::image()->withFile($tempPath)->resize(800, 800, true, 'auto')->save($tempPath, 70);
            } catch (\Exception $e) { }

            $key = 'bukti_pembayaran/' . $bukti->getRandomName();
            $uploadedPath = $this->r2->upload($key, fopen($tempPath, 'r'), $bukti->getMimeType());
            
            if ($uploadedPath) {
                // Add the file name to the data array
                $data['bukti_pembayaran'] = $key;
            }
        }
        
        $pembayaranModel->insert($data);
        
        // S
        session()->setFlashdata('success', 'Pembayaran berhasil dikonfirmasi. Silakan tunggu verifikasi dari admin.');
        
        // Redirect back to dashboard
        return redirect()->to('/parent/dashboard');
    }

    public function uploadBukti()
    {
        // Check if user is logged in
        if (!session()->get('parent_isLoggedIn')) {
            return redirect()->to('/parent/login');
        }
        
        // Get the payment ID
        $pembayaran_id = $this->request->getPost('pembayaran_id');
        
        // Handle file upload for payment proof
        $bukti = $this->request->getFile('bukti_pembayaran');
        
        if ($bukti && $bukti->isValid() && !$bukti->hasMoved()) {
              // Upload to Cloudflare R2
              $tempPath = $bukti->getTempName();
              try {
                  \Config\Services::image()->withFile($tempPath)->resize(800, 800, true, 'auto')->save($tempPath, 70);
              } catch (\Exception $e) { }

              $key = 'bukti_pembayaran/' . $bukti->getRandomName();
              $uploadedPath = $this->r2->upload($key, fopen($tempPath, 'r'), $bukti->getMimeType());
              
              if ($uploadedPath) {
                  // Update the payment record in the database
                  $pembayaranModel = new \App\Models\PembayaranModel();
                  $pembayaranModel->update($pembayaran_id, [
                      'bukti_pembayaran' => $key,
                      'status' => 'pending'
                  ]);
                
                // Set success message
                session()->setFlashdata('success', 'Bukti pembayaran berhasil diunggah.');
            } else {
                session()->setFlashdata('error', 'Gagal mengunggah bukti pembayaran ke Cloudflare.');
            }
        } else {
            // Set error message
            session()->setFlashdata('error', 'Gagal mengunggah bukti pembayaran. Silakan coba lagi.');
        }
        
        // Redirect back to dashboard
        return redirect()->to('/parent/dashboard');
    }
    // Tambahkan method getHargaJenisLes di sini
    public function getHargaJenisLes($id)
    {
        $jenisLesModel = new \App\Models\JenisLesModel();
        $jenisLes = $jenisLesModel->find($id);
        
        return $this->response->setJSON([
            'harga' => $jenisLes['harga'] ?? 0
        ]);
    }

    public function getJadwalDetail($jadwal_id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Akses tidak diizinkan']);
        }

        $db = \Config\Database::connect();
        
        // Ambil data jadwal
        $jadwal = $db->table('schedules')
            ->where('id', $jadwal_id)
            ->get()
            ->getRowArray();
            
        // Ambil data coach
        $coaches = $db->table('schedule_coaches sc')
            ->select('c.nama, c.keahlian')
            ->join('coach c', 'c.id = sc.coach_id')
            ->where('sc.schedule_id', $jadwal_id)
            ->get()
            ->getResultArray();
        
        // Ambil data peserta
        $peserta = $db->table('schedule_students ss')
            ->select('a.nama as nama_anak, jl.nama_les')
            ->join('anak a', 'a.id = ss.anak_id')
            ->join('jenis_les jl', 'jl.id = a.jenis_les_id')
            ->where('ss.schedule_id', $jadwal_id)
            ->get()
            ->getResultArray();
        
        $response = [
            'status' => 'success',
            'jadwal' => $jadwal,
            'coaches' => $coaches,
            'peserta' => $peserta
        ];
        
        return $this->response->setJSON($response);
    }

    public function getRiwayatLatihan()
    {
        if (!session()->get('parent_isLoggedIn')) {
            return redirect()->to('/parent/login');
        }

        $parent_id = session()->get('parent_id');
        $db = \Config\Database::connect();
        
        // Ambil data anak dari parent yang login
        $anakModel = new AnakModel();
        $anak = $anakModel->where('parent_id', $parent_id)->findAll();
        
        $riwayatPerAnak = [];
        
        foreach ($anak as $a) {
            // Query yang sudah terbukti berhasil
            $query = $db->table('latihan_attendance la')
                ->select('la.*, s.tanggal, s.jam_mulai, s.jam_selesai, s.materi, c.nama as nama_coach, a.nama as nama_anak')
                ->join('schedules s', 's.id = la.schedule_id', 'left')
                ->join('schedule_coaches sc', 'sc.schedule_id = s.id', 'left')
                ->join('coach c', 'c.id = sc.coach_id', 'left')
                ->join('anak a', 'a.id = la.anak_id', 'left')
                ->where([
                    'la.anak_id' => $a['id'],
                    'la.status_kehadiran' => 'hadir'
                ])
                ->orderBy('s.tanggal DESC, s.jam_mulai DESC');
                
            $riwayatPerAnak[$a['id']] = [
                'nama_anak' => $a['nama'],
                'riwayat' => $query->get()->getResultArray()
            ];
        }
        
        return view('parent/riwayat_latihan', [
            'title' => 'Riwayat Latihan',
            'riwayat_per_anak' => $riwayatPerAnak
        ]);
    }
}
