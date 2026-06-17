<?php

namespace App\Controllers\Api\Admin;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;

class AdminApiController extends BaseController
{
    use ResponseTrait;

    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function test()
    {
        return $this->respond([
            'status' => 200,
            'message' => 'Admin API Controller is reachable'
        ]);
    }

    /**
     * LOGIN API ADMIN
     * POST /api/admin/login
     * Sederhana, tidak menggunakan session agar tidak mengganggu session web.
     */
    public function login()
    {
        // Mendukung input format JSON (Postman) maupun Form-Data (Flutter/Web)
        $input = $this->request->getJSON(true) ?? $this->request->getPost();
        
        $email = $input['email'] ?? null;
        $password = $input['password'] ?? null;

        if (empty($email) || empty($password)) {
            return $this->fail('Email and Password are required', 400);
        }

        $user = $this->userModel->getUserByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            return $this->failUnauthorized('Email atau Password salah');
        }

        // Return user data
        return $this->respond([
            'status' => 200,
            'message' => 'Login Admin Berhasil',
            'data' => [
                'admin_id' => (int) $user['id'],
                'nama' => $user['nama'],
                'email' => $user['email'],
                'role' => $user['role']
            ]
        ]);
    }

    /**
     * GET ACTIVE SCHEDULES
     * GET /api/admin/jadwal-aktif
     */
    public function getJadwalAktif()
    {
        $db = \Config\Database::connect();
        
        // Ambil jadwal yang statusnya 'aktif' dengan join untuk mendapatkan informasi tambahan jika perlu
        $jadwal = $db->table('schedules')
                     ->where('status', 'aktif')
                     ->orderBy('tanggal', 'ASC')
                     ->orderBy('jam_mulai', 'ASC')
                     ->get()
                     ->getResultArray();

        // Format data untuk mempermudah pembacaan di Mobile/Postman
        foreach ($jadwal as &$j) {
            $j['id'] = (int) $j['id'];
            // Tambahkan label hari dalam Bahasa Indonesia jika perlu
            $timestamp = strtotime($j['tanggal']);
            $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            $j['nama_hari'] = $hari[date('w', $timestamp)];
        }

        return $this->respond([
            'status' => 200,
            'message' => 'Daftar Jadwal Aktif',
            'count' => count($jadwal),
            'data' => $jadwal
        ]);
    }

    /**
     * CHECKIN API
     * POST /api/admin/checkin
     * Digunakan untuk barcode scanner di mobile
     */
    public function checkin()
    {
        // Mendukung input format JSON maupun Form-Data
        $input = $this->request->getJSON(true) ?? $this->request->getPost();

        // Debug log untuk melihat apa yang diterima server
        log_message('debug', 'API Checkin Input: ' . json_encode($input));

        $jadwal_id = isset($input['jadwal_id']) ? (int) $input['jadwal_id'] : 0;
        $raw = isset($input['anak_id_scan']) ? (string) $input['anak_id_scan'] : '';
        $anak_id = (int) preg_replace('/\D+/', '', $raw);

        if ($jadwal_id <= 0 || $anak_id <= 0) {
            return $this->fail('ID jadwal atau ID anak tidak valid. Terima: Jadwal=' . $jadwal_id . ', AnakRaw=' . $raw, 400);
        }

        $scheduleModel = new \App\Models\ScheduleModel();
        $anakModel = new \App\Models\AnakModel();
        $kehadiranModel = new \App\Models\KehadiranModel();

        $jadwal = $scheduleModel->find($jadwal_id);
        if (!$jadwal) {
            return $this->fail('Jadwal tidak ditemukan.', 404);
        }

        $anak = $anakModel->find($anak_id);
        if (!$anak) {
            return $this->fail('Anak tidak ditemukan.', 404);
        }

        $db = \Config\Database::connect();
        
        // Matikan Foreign Key Check di awal koneksi (Sangat penting untuk hosting yang ketat)
        $db->query("SET FOREIGN_KEY_CHECKS = 0;");
        
        $db->transBegin();

        try {
            $now = date('Y-m-d H:i:s');

            // 1. Cek Kehadiran (latihan_attendance)
            $existingAttendance = $db->table('latihan_attendance')->where([
                'schedule_id' => $jadwal_id,
                'anak_id' => $anak_id
            ])->get()->getRowArray();

            if ($existingAttendance && ($existingAttendance['status_kehadiran'] ?? '') === 'hadir') {
                $db->query("SET FOREIGN_KEY_CHECKS = 1;");
                $db->transRollback();
                return $this->fail('Anak sudah diabsen hadir di jadwal ini.', 400);
            }

            // 2. Ambil jenis_les_id yang paling akurat dan valid
            $jenis_les_id = (int) ($anak['jenis_les_id'] ?? 0);
            
            if ($jenis_les_id <= 0) {
                $pay = $db->table('pembayaran')
                    ->select('jenis_les_id')
                    ->where('anak_id', $anak_id)
                    ->where('status', 'success')
                    ->orderBy('tanggal', 'DESC')
                    ->get()
                    ->getRowArray();
                
                $jenis_les_id = (int) ($pay['jenis_les_id'] ?? 0);
            }

            // Default ke 1 (Reguler) jika benar-benar tidak ada, agar tidak gagal simpan
            if ($jenis_les_id <= 0) {
                $jenis_les_id = 1; 
            }

            // 3. Update/Insert ke schedule_students (Data pendaftaran jadwal)
            $existingStudent = $db->table('schedule_students')->where([
                'schedule_id' => $jadwal_id,
                'anak_id' => $anak_id
            ])->get()->getRowArray();

            $dataSS = [
                'status' => 'hadir',
                'catatan' => 'Check-in via Mobile',
                'updated_at' => $now
            ];

            if ($existingStudent) {
                $db->table('schedule_students')->where('id', $existingStudent['id'])->update($dataSS);
            } else {
                $dataSS['schedule_id'] = $jadwal_id;
                $dataSS['anak_id'] = $anak_id;
                $dataSS['created_at'] = $now;
                $dataSS['catatan'] = 'Check-in via Mobile (Dadakan)';
                
                // Cek kolom enrollment_status secara manual dengan query mentah agar tidak error
                $db->query("UPDATE schedule_students SET enrollment_status = 'aktif' WHERE id = 0"); // Hanya trigger agar CI4 tau kolomnya
                
                $db->table('schedule_students')->insert($dataSS);
            }

            // 4. Update/Insert ke latihan_attendance (Data absensi harian)
            if ($existingAttendance) {
                $db->table('latihan_attendance')->where('id', $existingAttendance['id'])->update([
                    'status_kehadiran' => 'hadir',
                    'catatan' => 'Check-in via Mobile',
                    'jenis_les_id' => $jenis_les_id,
                    'updated_at' => $now
                ]);
            } else {
                $db->table('latihan_attendance')->insert([
                    'schedule_id' => $jadwal_id,
                    'anak_id' => $anak_id,
                    'jenis_les_id' => $jenis_les_id,
                    'status_kehadiran' => 'hadir',
                    'catatan' => 'Check-in via Mobile',
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }

            // Sinkronisasi sisa pertemuan
            $anakModel->recalculateSisaPertemuan($anak_id);

            $db->transCommit();
            
            // Aktifkan kembali Foreign Key Check
            $db->query("SET FOREIGN_KEY_CHECKS = 1;");

            // Kirim Sinyal Real-time via Pusher
            $this->triggerPusherUpdate($jadwal_id);

            return $this->respond([
                'status' => 200,
                'message' => 'Check-in berhasil: ' . $anak['nama'],
                'data' => [
                    'nama_anak' => $anak['nama'],
                    'jadwal_id' => $jadwal_id,
                    'waktu' => $now
                ]
            ]);

        } catch (\Exception $e) {
            $db->query("SET FOREIGN_KEY_CHECKS = 1;");
            $db->transRollback();
            return $this->fail('Sistem Error: ' . $e->getMessage(), 500);
        }
    }

    private function triggerPusherUpdate($jadwal_id)
    {
        try {
            // Kita gunakan CURL manual untuk memicu Pusher API (Sangat Ringan & Tanpa Library)
            $app_id = "2161532";
            $key = "28be70c660847570524b";
            $secret = "f7d3edcce008fe5db03c";
            $cluster = "ap1";
            
            $channel = "absensi-channel";
            $event = "update-event";
            $data = json_encode(['jadwal_id' => $jadwal_id, 'timestamp' => time()]);
            
            $path = "/apps/$app_id/events";
            $body = json_encode([
                'name' => $event,
                'channels' => [$channel],
                'data' => $data
            ]);
            
            $auth_timestamp = time();
            $auth_version = '1.0';
            $body_md5 = md5($body);
            
            $string_to_sign = "POST\n$path\nauth_key=$key&auth_timestamp=$auth_timestamp&auth_version=$auth_version&body_md5=$body_md5";
            $auth_signature = hash_hmac('sha256', $string_to_sign, $secret);
            
            $url = "https://api-$cluster.pusher.com$path?auth_key=$key&auth_timestamp=$auth_timestamp&auth_version=$auth_version&body_md5=$body_md5&auth_signature=$auth_signature";
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_exec($ch);
            curl_close($ch);
        } catch (\Exception $e) {
            log_message('error', 'Pusher Trigger Error: ' . $e->getMessage());
        }
    }

    /**
     * DAFTAR ANAK KE JADWAL (ADMIN MOBILE)
     * POST /api/admin/daftar-anak
     */
    public function daftarAnakKeJadwal()
    {
        $input = $this->request->getJSON(true) ?? $this->request->getPost();

        $jadwal_id = isset($input['jadwal_id']) ? (int) $input['jadwal_id'] : 0;
        $raw = isset($input['anak_id_scan']) ? (string) $input['anak_id_scan'] : '';
        $anak_id = (int) preg_replace('/\D+/', '', $raw);

        if ($jadwal_id <= 0 || $anak_id <= 0) {
            return $this->fail('ID jadwal atau ID anak tidak valid.', 400);
        }

        $db = \Config\Database::connect();
        
        // 1. Cek apakah anak ada
        $anak = $db->table('anak')->where('id', $anak_id)->get()->getRowArray();
        if (!$anak) {
            return $this->fail('Anak tidak ditemukan.', 404);
        }

        // 2. Cek apakah jadwal ada
        $jadwal = $db->table('schedules')->where('id', $jadwal_id)->get()->getRowArray();
        if (!$jadwal) {
            return $this->fail('Jadwal tidak ditemukan.', 404);
        }

        // 3. Cek apakah sudah terdaftar
        $existing = $db->table('schedule_students')->where([
            'schedule_id' => $jadwal_id,
            'anak_id' => $anak_id
        ])->get()->getRowArray();

        if ($existing) {
            return $this->fail('Anak sudah terdaftar di jadwal ini.', 400);
        }

        // 4. Daftarkan
        $now = date('Y-m-d H:i:s');
        $db->query("SET FOREIGN_KEY_CHECKS = 0;");
        
        $data = [
            'schedule_id' => $jadwal_id,
            'anak_id' => $anak_id,
            'status' => 'belum_absen',
            'enrollment_status' => 'aktif',
            'created_at' => $now,
            'updated_at' => $now
        ];

        $insert = $db->table('schedule_students')->insert($data);
        $db->query("SET FOREIGN_KEY_CHECKS = 1;");

        if ($insert) {
            // Kirim Sinyal Real-time via Pusher
            $this->triggerPusherUpdate($jadwal_id);

            return $this->respond([
                'status' => 200,
                'message' => 'Berhasil mendaftarkan ' . $anak['nama'] . ' ke jadwal.',
                'data' => [
                    'nama_anak' => $anak['nama'],
                    'jadwal_id' => $jadwal_id,
                    'tanggal' => $jadwal['tanggal']
                ]
            ]);
        } else {
            return $this->fail('Gagal mendaftarkan anak ke jadwal.', 500);
        }
    }
}
