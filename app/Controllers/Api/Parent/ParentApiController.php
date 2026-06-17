<?php

namespace App\Controllers\Api\Parent;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\ParentModel;
use App\Models\AnakModel;
use App\Models\PembayaranModel;
use App\Models\ScheduleModel;
use App\Models\JenisLesModel;

class ParentApiController extends BaseController
{
    use ResponseTrait;

    protected $parentModel;
    protected $anakModel;
    protected $pembayaranModel;
    protected $scheduleModel;
    protected $jenisLesModel;

    public function __construct()
    {
        $this->parentModel = new ParentModel();
        $this->anakModel = new AnakModel();
        $this->pembayaranModel = new PembayaranModel();
        $this->scheduleModel = new ScheduleModel();
        $this->jenisLesModel = new JenisLesModel();
    }

    /**
     * LOGIN API
     * POST /api/parent/login
     */
    public function login()
    {
        $input = $this->request->getJSON(true) ?? $this->request->getPost();

        $whatsapp = $input['whatsapp'] ?? null;
        $password = $input['password'] ?? null;

        if (empty($whatsapp) || empty($password)) {
            return $this->fail('WhatsApp and Password are required', 400);
        }

        $parent = $this->parentModel->where('whatsapp', $whatsapp)->first();

        if (!$parent || !password_verify($password, $parent['password'])) {
            return $this->failUnauthorized('Nomor WhatsApp atau Password salah');
        }

        // Return user data (In production, you should use JWT)
        return $this->respond([
            'status' => 200,
            'message' => 'Login Berhasil',
            'data' => [
                'parent_id' => $parent['id'],
                'nama' => $parent['nama'],
                'whatsapp' => $parent['whatsapp'],
                'alamat' => $parent['alamat']
            ]
        ]);
    }

    /**
     * DASHBOARD API
     * GET /api/parent/dashboard?parent_id=X
     */
    public function dashboard()
    {
        $parent_id = $this->request->getGet('parent_id');
        
        if (!$parent_id) {
            return $this->fail('Parent ID is required', 400);
        }

        $db = \Config\Database::connect();
        $anak = $this->anakModel->where('parent_id', $parent_id)->findAll();
        
        $latihan_attendance = $db->table('schedule_students ss')
            ->select('s.tanggal, s.jam_mulai, s.jam_selesai, a.nama as nama_anak, ss.status as status_kehadiran, ss.anak_id, s.materi')
            ->join('schedules s', 's.id = ss.schedule_id')
            ->join('anak a', 'a.id = ss.anak_id')
            ->where('a.parent_id', $parent_id)
            ->where('ss.status !=', 'belum_hadir')
            ->orderBy('s.tanggal', 'DESC')
            ->get()
            ->getResultArray();

        $anak_with_history = [];
        foreach ($anak as $a) {
            $child_id = $a['id'];
            
            $this->anakModel->recalculateSisaPertemuan($child_id);
            $breakdown = $this->anakModel->getPaketBreakdown($child_id);
            $detail_sisa = $breakdown['detail_sisa'];

            $history_groups = [];
            foreach ($breakdown['history_groups'] as $group) {
                $sessions = [];
                foreach ($group['sessions'] as $num => $session) {
                    if (is_array($session) && !empty($session['slot_status']) && $session['slot_status'] === 'hangus') {
                        $sessions[] = ['pert_ke' => $num, 'tanggal' => null, 'status' => 'hangus'];
                    } elseif (is_array($session) && !empty($session['tanggal'])) {
                        $sessions[] = [
                            'pert_ke' => $num,
                            'tanggal' => $session['tanggal'],
                            'status' => 'hadir',
                        ];
                    } else {
                        $sessions[] = ['pert_ke' => $num, 'tanggal' => null, 'status' => 'belum'];
                    }
                }
                $history_groups[] = [
                    'label' => $group['label'],
                    'tanggal_bayar' => $group['payment']['tanggal'] ?? null,
                    'berlaku_sampai' => $group['berlaku_sampai'],
                    'is_expired' => $group['is_expired'],
                    'hangus' => $group['hangus'],
                    'status_label' => $group['status_label'],
                    'bukti' => !empty($group['payment']['bukti_pembayaran'])
                        ? r2_url($group['payment']['bukti_pembayaran'], 'pembayaran')
                        : null,
                    'sessions' => $sessions,
                ];
            }

            $anak_with_history[] = [
                'id' => $a['id'],
                'nama' => $a['nama'],
                'sisa_pertemuan' => $detail_sisa['sisa_display'],
                'berlaku_sampai' => $breakdown['berlaku_sampai'],
                'hangus_total' => $breakdown['hangus_total'],
                'history_groups' => $history_groups,
            ];
        }

        return $this->respond([
            'status' => 200,
            'data' => [
                'anak' => $anak_with_history,
                'total_notif' => 0 // Placeholder
            ]
        ]);
    }

    /**
     * DAFTARKAN ANAK BARU
     * POST /api/parent/anak/store
     */
    public function storeAnak()
    {
        $parent_id = $this->request->getPost('parent_id');
        if (!$parent_id) return $this->fail('Parent ID is required', 400);

        $data = [
            'parent_id' => $parent_id,
            'nama' => $this->request->getPost('nama'),
            'nama_panggilan' => $this->request->getPost('nama_panggilan'),
            'asal_school' => $this->request->getPost('asal_school'),
            'riwayat_penyakit' => $this->request->getPost('riwayat_penyakit'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'jenis_les_id' => $this->request->getPost('jenis_les_id'),
            'status' => 'aktif',
            'sisa_pertemuan' => 0
        ];

        if ($this->anakModel->insert($data)) {
            return $this->respondCreated(['status' => 201, 'message' => 'Anak berhasil didaftarkan']);
        }
        return $this->fail('Gagal mendaftarkan anak');
    }

    /**
     * GET JADWAL TERSEDIA (FILTER BY JENIS LES ANAK)
     * GET /api/parent/jadwal/tersedia?anak_id=X
     */
    public function getJadwalTersedia()
    {
        $anak_id = $this->request->getGet('anak_id');
        $anak = $this->anakModel->find($anak_id);
        if (!$anak) return $this->failNotFound('Anak tidak ditemukan');

        $db = \Config\Database::connect();
        $jadwal = $db->table('schedules s')
            ->select('s.*, GROUP_CONCAT(jl.nama_les) as jenis_les_names')
            ->join('schedule_jenis_les sjl', 'sjl.schedule_id = s.id')
            ->join('jenis_les jl', 'jl.id = sjl.jenis_les_id')
            ->where('sjl.jenis_les_id', $anak['jenis_les_id'])
            ->where('s.status', 'aktif')
            ->where('s.tanggal >=', date('Y-m-d'))
            ->groupBy('s.id')
            ->get()->getResultArray();

        return $this->respond(['status' => 200, 'data' => $jadwal]);
    }

    /**
     * DAFTAR JADWAL (BOOKING)
     * POST /api/parent/jadwal/daftar
     */
    public function daftarJadwal()
    {
        $db = \Config\Database::connect();
        $jadwal_id = $this->request->getPost('jadwal_id');
        $anak_id = $this->request->getPost('anak_id');

        // Validasi Kapasitas
        $jadwal = $this->scheduleModel->find($jadwal_id);
        $terdaftar = $db->table('schedule_students')->where('schedule_id', $jadwal_id)->countAllResults();
        
        if ($terdaftar >= $jadwal['kapasitas']) {
            return $this->fail('Kapasitas penuh', 400);
        }

        $data = [
            'schedule_id' => $jadwal_id,
            'anak_id' => $anak_id,
            'status' => 'belum_hadir',
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($db->table('schedule_students')->insert($data)) {
            return $this->respondCreated(['status' => 201, 'message' => 'Berhasil mendaftar ke jadwal']);
        }
        return $this->fail('Gagal mendaftar');
    }

    /**
     * UPLOAD PEMBAYARAN
     * POST /api/parent/pembayaran/store
     */
    public function storePembayaran()
    {
        $anak_id = (int) $this->request->getPost('anak_id');
        $parent_id = (int) $this->request->getPost('parent_id');

        if ($anak_id <= 0 || $parent_id <= 0) {
            return $this->fail('anak_id dan parent_id wajib diisi', 400);
        }

        $paymentCheck = $this->pembayaranModel->validateParentCanPay($anak_id, $parent_id);
        if (!$paymentCheck['allowed']) {
            return $this->fail($paymentCheck['message'], 400);
        }

        $file = $this->request->getFile('bukti');
        if (!$file->isValid()) return $this->fail('File tidak valid', 400);

        // Upload ke R2 via helper r2_upload (asumsi helper tersedia di BaseController)
        $fileName = $file->getRandomName();
        // Logika upload r2 sesuai aplikasi Anda...
        // Untuk contoh ini kita asumsikan file diupload dan path disimpan
        
        $anak = $this->anakModel->find($anak_id);
        if (!$anak) return $this->failNotFound('Data anak tidak ditemukan');

        $data = [
            'anak_id' => $anak_id,
            'parent_id' => $parent_id,
            'jenis_les_id' => $anak['jenis_les_id'], // Simpan snapshot jenis les saat ini dari profil anak
            'tanggal' => date('Y-m-d H:i:s'),
            'jumlah_pertemuan' => $this->request->getPost('jumlah_pertemuan'),
            'total' => $this->request->getPost('total'),
            'metode_pembayaran' => $this->request->getPost('metode_pembayaran'),
            'bukti_pembayaran' => $fileName,
            'status' => 'pending'
        ];

        if ($this->pembayaranModel->insert($data)) {
            return $this->respondCreated(['status' => 201, 'message' => 'Pembayaran berhasil dikirim, menunggu approval']);
        }
        return $this->fail('Gagal mengirim pembayaran');
    }

    /**
     * HISTORY PEMBAYARAN
     * GET /api/parent/pembayaran?parent_id=X
     */
    public function getPembayaran()
    {
        $parent_id = $this->request->getGet('parent_id');
        if (!$parent_id) return $this->fail('Parent ID is required', 400);

        $data = $this->pembayaranModel->where('parent_id', $parent_id)->orderBy('tanggal', 'DESC')->findAll();
        
        foreach ($data as &$d) {
            $d['bukti_url'] = r2_url($d['bukti_pembayaran'], 'pembayaran');
        }

        return $this->respond(['status' => 200, 'data' => $data]);
    }

    /**
     * GET JENIS LES (UNTUK DROPDOWN)
     * GET /api/parent/jenis-les
     */
    public function getJenisLes()
    {
        return $this->respond(['status' => 200, 'data' => $this->jenisLesModel->findAll()]);
    }
}

