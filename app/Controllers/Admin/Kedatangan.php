<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KehadiranModel;
use App\Models\AnakModel;
use App\Models\ScheduleModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Kedatangan extends BaseController
{
    protected $kehadiranModel;
    protected $anakModel;
    protected $scheduleModel;
    protected $settingModel;
    protected $logModel;

    public function __construct()
    {
        $this->kehadiranModel = new KehadiranModel();
        $this->anakModel = new AnakModel();
        $this->scheduleModel = new ScheduleModel();
        $this->settingModel = new \App\Models\SettingModel();
        $this->logModel = new \App\Models\ActivityLogModel();
    }

    public function index()
    {
        $status = $this->request->getGet('status') ?: 'aktif';
        
        // Ambil daftar jadwal berdasarkan status
        $builder = $this->scheduleModel;
        
        if ($status == 'selesai') {
            $builder->where('status', 'selesai')
                   ->orderBy('tanggal', 'DESC')
                   ->orderBy('jam_mulai', 'DESC');
        } else {
            $builder->where('status', 'aktif')
                   ->orderBy('tanggal', 'ASC')
                   ->orderBy('jam_mulai', 'ASC');
        }
        
        $jadwal = $builder->findAll();
        
        // Ambil daftar anak
        $anak = $this->anakModel->findAll();
        
        $data = [
            'title' => 'Kelola Kedatangan',
            'active' => 'kedatangan',
            'jadwal' => $jadwal,
            'anak' => $anak,
            'current_status' => $status
        ];
        
        return view('admin/kedatangan/index', $data);
    }

    public function getSchedulesByDate()
    {        $tanggal = $this->request->getGet('tanggal');
        if (!$tanggal) return $this->response->setJSON([]);

        $jadwal = $this->scheduleModel->where('tanggal', $tanggal)->findAll();
        return $this->response->setJSON($jadwal);
    }

    public function saveBulkManual()
    {
        $schedule_id = $this->request->getPost('schedule_id');
        $id_anak_list_raw = $this->request->getPost('id_anak_list');

        if (!$schedule_id || !$id_anak_list_raw) {
            return redirect()->back()->with('error', 'Data tidak lengkap');
        }

        // Parsing ID anak: Ambil angka saja dari string (bisa dipisah koma, spasi, atau baris baru)
        preg_match_all('/\d+/', $id_anak_list_raw, $matches);
        $id_anak_array = array_unique($matches[0]);

        if (empty($id_anak_array)) {
            return redirect()->back()->with('error', 'Tidak ada ID anak yang valid ditemukan');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $success_count = 0;
        $failed_count = 0;
        $errors = [];

        foreach ($id_anak_array as $anak_id) {
            // 1. Cek apakah anak ada
            $anak = $this->anakModel->find($anak_id);
            if (!$anak) {
                $failed_count++;
                $errors[] = "ID #{$anak_id} tidak ditemukan";
                continue;
            }

            // 2. Cek apakah sudah terdaftar atau sudah absen di jadwal ini (Constraint Ganda)
            $existingAttendance = $this->kehadiranModel->where([
                'schedule_id' => $schedule_id,
                'anak_id' => $anak_id
            ])->first();

            $existingStudent = $db->table('schedule_students')->where([
                'schedule_id' => $schedule_id,
                'anak_id' => $anak_id
            ])->get()->getRow();

            if ($existingAttendance || $existingStudent) {
                $failed_count++;
                $errors[] = "ID #{$anak_id} ({$anak['nama']}) sudah terdaftar/absen di jadwal ini";
                continue;
            }

            // 3. Simpan pendaftaran ke schedule_students (Status hadir)
            $db->table('schedule_students')->insert([
                'schedule_id' => $schedule_id,
                'anak_id' => $anak_id,
                'status' => 'hadir',
                'catatan' => 'Input Manual Bulk',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // 4. Simpan kehadiran ke latihan_attendance
            $this->kehadiranModel->insert([
                'schedule_id' => $schedule_id,
                'anak_id' => $anak_id,
                'jenis_les_id' => $anak['jenis_les_id'], // Snapshot status siswa saat ini
                'status_kehadiran' => 'hadir',
                'catatan' => 'Input Manual Bulk',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // 5. Update sisa pertemuan menggunakan sinkronisasi total
            $this->anakModel->recalculateSisaPertemuan($anak_id);

            $success_count++;
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat menyimpan data');
        }

        $msg = "Berhasil memproses {$success_count} absensi.";
        if ($failed_count > 0) {
            $msg .= " Gagal: {$failed_count}. Alasan: " . implode(', ', array_slice($errors, 0, 3)) . (count($errors) > 3 ? '...' : '');
        }

        // Log Aktivitas
        $this->logModel->addLog('Input Bulk Absensi', "Memproses {$success_count} anak ke jadwal #{$schedule_id}.");

        return redirect()->to('admin/kedatangan')->with($failed_count > 0 ? 'error' : 'success', $msg);
    }

    public function absensi($jadwal_id)
    {
        $jadwal = $this->scheduleModel->find($jadwal_id);
        if (!$jadwal) {
            return redirect()->to('admin/kedatangan')->with('error', 'Jadwal tidak ditemukan');
        }

        // Ambil daftar anak yang terdaftar di jadwal ini (PASTI UNIK)
        $pesertaRaw = $this->anakModel->getPesertaJadwal($jadwal_id);
        
        // Filter unik berdasarkan ID di tingkat PHP untuk keamanan mutlak
        $peserta = [];
        $ids_seen = [];
        foreach ($pesertaRaw as $p) {
            $id = (int)$p['id'];
            if (!isset($ids_seen[$id])) {
                $ids_seen[$id] = true;
                $peserta[] = $p;
            }
        }
        
        // Tambahkan detail sisa untuk setiap peserta
        foreach ($peserta as &$p) {
            $detail = $this->anakModel->getDetailedSisa($p['id']);
            $p['sisa_pertemuan_display'] = $detail['sisa_display'];
            $p['pertemuan_ke'] = $detail['pertemuan_ke'];
            $p['paket_ke'] = $detail['paket_ke'];
        }
        unset($p); // Break reference
        
        $kehadiran = $this->kehadiranModel->where('schedule_id', $jadwal_id)->findAll();
        
        // Hitung ringkasan jumlah berdasarkan daftar yang sudah dibersihkan
        $count_private = 0;
        $count_reguler = 0;
        foreach ($peserta as $p) {
            if (str_contains(strtolower($p['jenis_les_nama'] ?? ''), 'private')) {
                $count_private++;
            } else {
                $count_reguler++;
            }
        }

        // Ambil ID anak yang sudah terdaftar
        $terdaftar_ids = array_column($peserta, 'id');
        
        // Ambil semua anak yang belum terdaftar untuk dropdown modal
        if (empty($terdaftar_ids)) {
            $semua_anak = $this->anakModel->findAll();
        } else {
            $semua_anak = $this->anakModel->whereNotIn('id', $terdaftar_ids)->findAll();
        }

        $data = [
            'title' => 'Absensi Kedatangan',
            'active' => 'kedatangan',
            'jadwal' => $jadwal,
            'peserta' => $peserta,
            'kehadiran' => $kehadiran,
            'semua_anak' => $semua_anak,
            'summary' => [
                'total' => count($peserta),
                'private' => $count_private,
                'reguler' => $count_reguler
            ],
            'coaches_jadwal' => \Config\Database::connect()
                ->table('schedule_coaches sc')
                ->select('c.id, c.nama, c.keahlian, sc.status as status_hadir')
                ->join('coach c', 'c.id = sc.coach_id')
                ->where('sc.schedule_id', $jadwal_id)
                ->get()
                ->getResultArray()
        ];

        return view('admin/kedatangan/absensi', $data);
    }

    public function saveCoachAbsensi()
    {
        $jadwal_id = $this->request->getPost('jadwal_id');
        $coach_status = $this->request->getPost('coach_status') ?? [];

        if (!$jadwal_id) {
            return redirect()->back()->with('error', 'ID Jadwal tidak valid.');
        }

        $db = \Config\Database::connect();

        foreach ($coach_status as $coach_id => $status) {
            $db->table('schedule_coaches')
               ->where('schedule_id', $jadwal_id)
               ->where('coach_id', $coach_id)
               ->update(['status' => $status]);
        }

        return redirect()->to('admin/kedatangan/absensi/' . $jadwal_id)
                        ->with('success', 'Absensi pelatih berhasil disimpan.');
    }

    /**
     * Endpoint untuk mengambil data absensi secara real-time (AJAX)
     */
    public function getAbsensiData($jadwal_id)
    {
        // Ambil daftar anak yang terdaftar di jadwal ini (PASTI UNIK)
        $pesertaRaw = $this->anakModel->getPesertaJadwal($jadwal_id);
        
        $peserta = [];
        $ids_seen = [];
        foreach ($pesertaRaw as $p) {
            $id = (int)$p['id'];
            if (!isset($ids_seen[$id])) {
                $ids_seen[$id] = true;
                $peserta[] = $p;
            }
        }
        
        foreach ($peserta as &$p) {
            $detail = $this->anakModel->getDetailedSisa($p['id']);
            $p['sisa_pertemuan_display'] = $detail['sisa_display'];
            $p['pertemuan_ke'] = $detail['pertemuan_ke'];
            $p['paket_ke'] = $detail['paket_ke'];
        }
        unset($p);
        
        $kehadiran = $this->kehadiranModel->where('schedule_id', $jadwal_id)->findAll();
        
        $count_private = 0;
        $count_reguler = 0;
        foreach ($peserta as $p) {
            if (str_contains(strtolower($p['jenis_les_nama'] ?? ''), 'private')) {
                $count_private++;
            } else {
                $count_reguler++;
            }
        }

        return $this->response->setJSON([
            'peserta' => $peserta,
            'kehadiran' => $kehadiran,
            'summary' => [
                'total' => count($peserta),
                'private' => $count_private,
                'reguler' => $count_reguler
            ]
        ]);
    }

    public function tambahPesertaManual()
    {
        $jadwal_id = $this->request->getPost('jadwal_id');
        $anak_id = $this->request->getPost('anak_id');
        
        // Debug log untuk melihat data yang diterima
        log_message('debug', 'Received jadwal_id: ' . $jadwal_id);
        log_message('debug', 'Received anak_id: ' . $anak_id);
        
        // Validasi input
        if (!$jadwal_id || !$anak_id) {
            log_message('error', 'Data tidak lengkap - jadwal_id: ' . $jadwal_id . ', anak_id: ' . $anak_id);
            return redirect()->back()->with('error', 'Data tidak lengkap');
        }

        // Cek apakah jadwal ada
        $jadwal = $this->scheduleModel->find($jadwal_id);
        if (!$jadwal) {
            log_message('error', 'Jadwal tidak ditemukan dengan ID: ' . $jadwal_id);
            return redirect()->back()->with('error', 'Jadwal tidak ditemukan');
        }

        // Cek apakah anak ada
        $anak = $this->anakModel->find($anak_id);
        if (!$anak) {
            log_message('error', 'Anak tidak ditemukan dengan ID: ' . $anak_id);
            return redirect()->back()->with('error', 'Anak tidak ditemukan');
        }

        try {
            $db = \Config\Database::connect();
            
            // Start transaction
            $db->transStart();
            
            // Cek apakah sudah terdaftar atau sudah absen di jadwal ini (Constraint Ganda)
            $existingAttendance = $this->kehadiranModel->where([
                'schedule_id' => $jadwal_id,
                'anak_id' => $anak_id
            ])->first();
            
            $existingStudent = $db->table('schedule_students')->where([
                'schedule_id' => $jadwal_id,
                'anak_id' => $anak_id
            ])->get()->getRow();
            
            if ($existingAttendance || $existingStudent) {
                $db->transRollback();
                log_message('error', 'Anak sudah terdaftar atau hadir di jadwal ini - jadwal_id: ' . $jadwal_id . ', anak_id: ' . $anak_id);
                return redirect()->back()->with('error', 'Anak sudah terdaftar atau hadir di jadwal ini');
            }
            
            // Tambahkan ke tabel schedule_students dengan status belum_hadir
            $scheduleStudentData = [
                'schedule_id' => $jadwal_id,
                'anak_id' => $anak_id,
                'status' => 'belum_hadir',
                'catatan' => 'Ditambahkan manual oleh admin',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $db->table('schedule_students')->insert($scheduleStudentData);
            log_message('debug', 'Berhasil menambahkan ke schedule_students');
            
            // Catatan: Tidak lagi memotong sisa_pertemuan di sini.
            // Sisa pertemuan baru akan dipotong saat admin menekan 'Simpan Semua' atau 'Check-in'
            
            // Complete transaction
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Gagal menyimpan data');
            }
            
            return redirect()->to('admin/kedatangan/absensi/' . $jadwal_id)
                            ->with('success', 'Peserta berhasil ditambahkan manual');
                            
        } catch (\Exception $e) {
            log_message('error', 'Error saat menambah peserta manual: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menambah peserta');
        }
    }

    public function checkin()
    {
        $jadwal_id = (int) $this->request->getPost('jadwal_id');
        $raw = (string) $this->request->getPost('anak_id_scan');
        $anak_id = (int) preg_replace('/\D+/', '', $raw);

        if ($jadwal_id <= 0 || $anak_id <= 0) {
            return redirect()->back()->with('error', 'ID jadwal atau ID anak tidak valid.');
        }

        $jadwal = $this->scheduleModel->find($jadwal_id);
        if (!$jadwal) {
            return redirect()->back()->with('error', 'Jadwal tidak ditemukan.');
        }

        $anak = $this->anakModel->find($anak_id);
        if (!$anak) {
            return redirect()->back()->with('error', 'Anak tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $now = date('Y-m-d H:i:s');

            // Cek apakah sudah absen di latihan_attendance (Constraint Ganda)
            $existingAttendance = $this->kehadiranModel->where([
                'schedule_id' => $jadwal_id,
                'anak_id' => $anak_id
            ])->first();

            if ($existingAttendance && $existingAttendance['status_kehadiran'] === 'hadir') {
                $db->transRollback();
                return redirect()->back()->with('error', 'Anak sudah diabsen hadir di jadwal ini.');
            }

            $existingStudent = $db->table('schedule_students')->where([
                'schedule_id' => $jadwal_id,
                'anak_id' => $anak_id
            ])->limit(1)->get()->getRowArray();

            if ($existingStudent) {
                $db->table('schedule_students')->where('id', $existingStudent['id'])->update([
                    'status' => 'hadir',
                    'catatan' => 'Check-in dadakan',
                    'updated_at' => $now
                ]);
            } else {
                // Pastikan benar-benar tidak ada duplikat sebelum insert (Double check)
                $count = $db->table('schedule_students')->where([
                    'schedule_id' => $jadwal_id,
                    'anak_id' => $anak_id
                ])->countAllResults();

                if ($count == 0) {
                    $db->table('schedule_students')->insert([
                        'schedule_id' => $jadwal_id,
                        'anak_id' => $anak_id,
                        'status' => 'hadir',
                        'catatan' => 'Check-in dadakan',
                        'created_at' => $now,
                        'updated_at' => $now
                    ]);
                }
            }

            $shouldDecrement = false;
            if ($existingAttendance) {
                if (($existingAttendance['status_kehadiran'] ?? null) !== 'hadir') {
                    $updateAtt = [
                        'status_kehadiran' => 'hadir',
                        'catatan' => 'Check-in dadakan',
                        'updated_at' => $now
                    ];
                    // Tambahkan snapshot jika belum ada (data lama)
                    if (empty($existingAttendance['jenis_les_id'])) {
                        $updateAtt['jenis_les_id'] = $anak['jenis_les_id'];
                    }
                    $this->kehadiranModel->update($existingAttendance['id'], $updateAtt);
                    $shouldDecrement = true;
                }
            } else {
                $this->kehadiranModel->insert([
                    'schedule_id' => $jadwal_id,
                    'anak_id' => $anak_id,
                    'jenis_les_id' => $anak['jenis_les_id'], // Snapshot status siswa saat ini
                    'status_kehadiran' => 'hadir',
                    'catatan' => 'Check-in dadakan',
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
                $shouldDecrement = true;
            }

            if ($shouldDecrement) {
                // Update sisa pertemuan menggunakan sinkronisasi total
                $this->anakModel->recalculateSisaPertemuan($anak_id);
            }

            $db->transComplete();
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal check-in: ' . $e->getMessage());
        }

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal check-in.');
        }

        return redirect()->back()->with('success', 'Check-in berhasil: #' . $anak_id . ' (' . ($anak['nama'] ?? '-') . ')');
    }

    public function searchAnak()
    {
        $q = trim((string) $this->request->getGet('q'));
        if ($q === '') {
            return $this->response->setJSON(['data' => []]);
        }

        $builder = $this->anakModel->builder();
        $builder->select('id, nama, nama_panggilan, sisa_pertemuan');

        if (ctype_digit($q)) {
            // Jika input berupa angka (barcode ID), gunakan exact match
            $builder->where('id', (int) $q);
        } else {
            $builder->groupStart()
                ->like('nama', $q)
                ->orLike('nama_panggilan', $q)
                ->groupEnd();
        }

        $rows = $builder->orderBy('id', 'DESC')->limit(20)->get()->getResultArray();
        return $this->response->setJSON(['data' => $rows]);
    }

    public function saveAbsensi()
    {
        $jadwal_id = $this->request->getPost('jadwal_id');
        $anak_id = $this->request->getPost('anak_id');
        $status = $this->request->getPost('status_kehadiran');
        $catatan = $this->request->getPost('catatan');

        // Mulai transaksi database
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Ambil data anak untuk snapshot status
            $anak = $this->anakModel->find($anak_id);
            if (!$anak) throw new \Exception('Data anak tidak ditemukan');

            // Cek apakah sudah ada di pendaftaran (schedule_students)
            $existingStudent = $db->table('schedule_students')->where([
                'schedule_id' => $jadwal_id,
                'anak_id' => $anak_id
            ])->get()->getRow();

            // Cek apakah sudah ada absensi
            $existingAttendance = $this->kehadiranModel->where([
                'schedule_id' => $jadwal_id,
                'anak_id' => $anak_id
            ])->first();

            // Hanya proses jika status kehadiran adalah 'hadir'
            if ($status === 'hadir') {
                // Update atau insert ke tabel schedule_students
                if ($existingStudent) {
                    $db->table('schedule_students')->where('id', $existingStudent->id)->update([
                        'status' => 'hadir',
                        'catatan' => $catatan,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                } else {
                    $db->table('schedule_students')->insert([
                        'schedule_id' => $jadwal_id,
                        'anak_id' => $anak_id,
                        'status' => 'hadir',
                        'catatan' => $catatan,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }

                // Update atau insert ke tabel latihan_attendance
                if ($existingAttendance) {
                    $updateAtt = [
                        'status_kehadiran' => $status,
                        'catatan' => $catatan,
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    // Tambahkan snapshot jika belum ada (data lama)
                    if (empty($existingAttendance['jenis_les_id'])) {
                        $updateAtt['jenis_les_id'] = $anak['jenis_les_id'];
                    }
                    $this->kehadiranModel->update($existingAttendance['id'], $updateAtt);
                } else {
                    $this->kehadiranModel->insert([
                        'schedule_id' => $jadwal_id,
                        'anak_id' => $anak_id,
                        'jenis_les_id' => $anak['jenis_les_id'], // Snapshot status siswa saat ini
                        'status_kehadiran' => $status,
                        'catatan' => $catatan,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }

                // Kurangi sisa pertemuan (Gunakan sistem sinkronisasi total)
                $wasHadir = $existingAttendance && $existingAttendance['status_kehadiran'] === 'hadir';
                if (!$wasHadir) {
                    $this->anakModel->recalculateSisaPertemuan($anak_id);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Gagal menyimpan absensi');
            }

            return redirect()->to('admin/kedatangan/absensi/' . $jadwal_id)
                            ->with('success', 'Absensi berhasil disimpan');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to('admin/kedatangan/absensi/' . $jadwal_id)
                            ->with('error', 'Gagal menyimpan absensi: ' . $e->getMessage());
        }
    }

    public function updateAbsensi($id)
    {
        $status = $this->request->getPost('status_kehadiran');
        $catatan = $this->request->getPost('catatan');

        $kehadiran = $this->kehadiranModel->find($id);
        if (!$kehadiran) {
            return redirect()->back()->with('error', 'Data kehadiran tidak ditemukan');
        }

        $this->kehadiranModel->update($id, [
            'status_kehadiran' => $status,
            'catatan' => $catatan
        ]);

        return redirect()->back()->with('success', 'Status kehadiran berhasil diperbarui');
    }

    public function deleteAbsensi($id)
    {
        $kehadiran = $this->kehadiranModel->find($id);
        if (!$kehadiran) {
            return redirect()->back()->with('error', 'Data kehadiran tidak ditemukan');
        }

        $anak_id = $kehadiran['anak_id'];
        $jadwal_id = $kehadiran['schedule_id'];

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Hapus dari latihan_attendance
        $this->kehadiranModel->delete($id);

        // 2. Hapus dari schedule_students agar hilang dari daftar absensi
        $db->table('schedule_students')->where([
            'schedule_id' => $jadwal_id,
            'anak_id' => $anak_id
        ])->delete();

        // 3. Update sisa pertemuan menggunakan sinkronisasi total
        $this->anakModel->recalculateSisaPertemuan($anak_id);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal menghapus data absensi');
        }

        return redirect()->back()->with('success', 'Data kehadiran dan pendaftaran berhasil dihapus');
    }

    public function deletePeserta($jadwal_id, $anak_id)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Hapus dari schedule_students
        $db->table('schedule_students')->where([
            'schedule_id' => $jadwal_id,
            'anak_id' => $anak_id
        ])->delete();

        // 2. Hapus dari latihan_attendance (jika ada)
        $this->kehadiranModel->where([
            'schedule_id' => $jadwal_id,
            'anak_id' => $anak_id
        ])->delete();

        // 3. Sinkronisasi sisa pertemuan
        $this->anakModel->recalculateSisaPertemuan($anak_id);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal menghapus peserta');
        }

        return redirect()->back()->with('success', 'Peserta berhasil dihapus dari daftar');
    }

    public function buka($jadwal_id)
    {
        $jadwal = $this->scheduleModel->find($jadwal_id);
        if (!$jadwal) {
            return redirect()->to('admin/kedatangan')->with('error', 'Jadwal tidak ditemukan');
        }

        $this->scheduleModel->update($jadwal_id, ['status' => 'aktif']);
        return redirect()->to('admin/kedatangan/absensi/' . $jadwal_id)->with('success', 'Jadwal dibuka kembali.');
    }

    public function exportExcel($jadwal_id)
    {
        // Load library PhpSpreadsheet
        require_once ROOTPATH . 'vendor/autoload.php';
        
        // Ambil data jadwal
        $jadwal = $this->scheduleModel->find($jadwal_id);
        if (!$jadwal) {
            return redirect()->back()->with('error', 'Jadwal tidak ditemukan');
        }
        
        // Ambil data peserta dan kehadiran
        $peserta = $this->anakModel->getPesertaJadwal($jadwal_id);
        $kehadiran = $this->kehadiranModel->where('schedule_id', $jadwal_id)->findAll();
        
        // Buat objek spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set judul
        $sheet->setCellValue('A1', 'DAFTAR ABSENSI KEDATANGAN');
        $sheet->setCellValue('A2', 'Tanggal: ' . date('d-m-Y', strtotime($jadwal['tanggal'])));
        $sheet->setCellValue('A3', 'Waktu: ' . date('H:i', strtotime($jadwal['jam_mulai'])) . ' - ' . date('H:i', strtotime($jadwal['jam_selesai'])));
        
        // Header tabel
        $sheet->setCellValue('A5', 'No');
        $sheet->setCellValue('B5', 'ID');
        $sheet->setCellValue('C5', 'Nama Anak');
        $sheet->setCellValue('D5', 'Nama Panggilan');
        $sheet->setCellValue('E5', 'Jenis Les');
        $sheet->setCellValue('F5', 'Status Kehadiran');
        $sheet->setCellValue('G5', 'Sisa Pertemuan');
        
        // Isi data
        $row = 6;
        foreach ($peserta as $i => $p) {
            $kehadiran_siswa = array_filter($kehadiran, function($k) use ($p) {
                return $k['anak_id'] == $p['id'];
            });
            $kehadiran_siswa = !empty($kehadiran_siswa) ? reset($kehadiran_siswa) : null;
            
            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $p['id']);
            $sheet->setCellValue('C' . $row, $p['nama']);
            $sheet->setCellValue('D' . $row, $p['nama_panggilan']);
            $sheet->setCellValue('E' . $row, $p['jenis_les_nama'] ?? 'Belum terdaftar');
            $sheet->setCellValue('F' . $row, ucfirst($kehadiran_siswa['status_kehadiran'] ?? 'Belum ada'));
            $sheet->setCellValue('G' . $row, $p['sisa_pertemuan'] ?? 0);
            
            $row++;
        }
        
        // Styling
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        
        $sheet->getStyle('A5:G' . ($row-1))->applyFromArray($styleArray);
        $sheet->getStyle('A1:A3')->getFont()->setBold(true);
        $sheet->getStyle('A5:G5')->getFont()->setBold(true);
        
        // Auto size columns
        foreach(range('A','G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Set nama file
        $filename = 'Absensi_' . date('d-m-Y', strtotime($jadwal['tanggal'])) . '.xlsx';
        
        // Header untuk download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'. $filename .'"');
        header('Cache-Control: max-age=0');
        
        // Export ke Excel
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }

    public function edit()
    {
        $tanggal = $this->request->getGet('tanggal');

        // Ambil daftar jadwal yang sudah selesai
        $builder = $this->scheduleModel->where('status', 'selesai');

        if (!empty($tanggal)) {
            $builder->where('tanggal', $tanggal);
        }

        $jadwal = $builder->orderBy('tanggal', 'DESC')
                          ->orderBy('jam_mulai', 'DESC')
                          ->findAll();
        
        $data = [
            'title' => 'Edit Kedatangan (Jadwal Selesai)',
            'active' => 'edit-kedatangan',
            'jadwal' => $jadwal,
            'filter' => [
                'tanggal' => $tanggal
            ]
        ];
        
        return view('admin/kedatangan/edit', $data);
    }

    public function editAbsensi($jadwal_id)
    {
        $jadwal = $this->scheduleModel->find($jadwal_id);
        if (!$jadwal) {
            return redirect()->to('admin/kedatangan/edit')->with('error', 'Jadwal tidak ditemukan');
        }

        // Ambil data kehadiran untuk jadwal ini
        $db = \Config\Database::connect();
        $kehadiran = $db->table('latihan_attendance')
            ->select('latihan_attendance.*, anak.nama, anak.nama_panggilan, anak.sisa_pertemuan')
            ->join('anak', 'anak.id = latihan_attendance.anak_id')
            ->where('latihan_attendance.schedule_id', $jadwal_id)
            ->get()
            ->getResultArray();

        foreach ($kehadiran as &$k) {
            $detail = $this->anakModel->getDetailedSisa($k['anak_id']);
            $k['sisa_pertemuan_display'] = $detail['sisa_display'];
            $k['pertemuan_ke'] = $detail['pertemuan_ke'];
            $k['paket_ke'] = $detail['paket_ke'];
        }
        unset($k);
            
        // Ambil jenis les yang diizinkan pada jadwal ini (jika ada)
        $allowedJenisLesIds = array_map(
            'intval',
            array_column(
                $db->table('schedule_jenis_les')
                    ->select('jenis_les_id')
                    ->where('schedule_id', (int) $jadwal_id)
                    ->get()
                    ->getResultArray(),
                'jenis_les_id'
            )
        );

        // Exclude anak yang sudah ada di attendance jadwal ini agar tidak duplikat di modal
        $existingAnakIds = array_map('intval', array_column($kehadiran, 'anak_id'));

        // Ambil semua anak untuk dropdown tambah manual dengan nama jenis les
        $anakBuilder = $db->table('anak')
            ->select('anak.*, jenis_les.nama_les')
            ->join('jenis_les', 'jenis_les.id = anak.jenis_les_id', 'left')
            ->orderBy('anak.nama', 'ASC');

        $jenisLesFilterWarning = false;
        if (!empty($allowedJenisLesIds)) {
            $anakBuilder->whereIn('anak.jenis_les_id', $allowedJenisLesIds);
        }
        if (!empty($existingAnakIds)) {
            $anakBuilder->whereNotIn('anak.id', $existingAnakIds);
        }

        $semua_anak = $anakBuilder->get()->getResultArray();

        // Production sering punya schedule_jenis_les ketat; fallback agar modal tidak kosong.
        if (empty($semua_anak) && !empty($allowedJenisLesIds)) {
            $jenisLesFilterWarning = true;
            $fallbackBuilder = $db->table('anak')
                ->select('anak.*, jenis_les.nama_les')
                ->join('jenis_les', 'jenis_les.id = anak.jenis_les_id', 'left')
                ->orderBy('anak.nama', 'ASC');
            if (!empty($existingAnakIds)) {
                $fallbackBuilder->whereNotIn('anak.id', $existingAnakIds);
            }
            $semua_anak = $fallbackBuilder->get()->getResultArray();
        }

        foreach ($semua_anak as &$sa) {
            $detail_sa = $this->anakModel->getDetailedSisa($sa['id']);
            $sa['sisa_pertemuan_display'] = $detail_sa['sisa_display'];
        }

        $data = [
            'title' => 'Edit Detail Kedatangan',
            'active' => 'edit-kedatangan',
            'jadwal' => $jadwal,
            'kehadiran' => $kehadiran,
            'semua_anak' => $semua_anak,
            'allowed_jenis_les_ids' => $allowedJenisLesIds,
            'jenis_les_filter_warning' => $jenisLesFilterWarning,
        ];

        return view('admin/kedatangan/edit_absensi', $data);
    }

    public function saveEditAbsensi()
    {
        $jadwal_id = (int) $this->request->getPost('jadwal_id');
        $anak_id = (int) $this->request->getPost('anak_id');
        $catatan = $this->request->getPost('catatan');

        if ($jadwal_id <= 0 || $anak_id <= 0) {
            return redirect()->back()->with('error', 'Data tidak lengkap');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $jadwal = $this->scheduleModel->find($jadwal_id);
            if (!$jadwal) {
                throw new \Exception('Jadwal tidak ditemukan');
            }

            $anak = $this->anakModel->find($anak_id);
            if (!$anak) {
                throw new \Exception('Data anak tidak ditemukan');
            }

            $jenisLesId = $this->resolveAttendanceJenisLesId($db, $jadwal_id, $anak);
            if ($jenisLesId <= 0) {
                throw new \Exception('Jenis les untuk absensi tidak ditemukan. Lengkapi jenis les anak atau mapping jadwal.');
            }

            // Cek apakah sudah ada di kehadiran (Constraint Ganda utama)
            $existingAttendance = $this->kehadiranModel->where([
                'schedule_id' => $jadwal_id,
                'anak_id' => $anak_id
            ])->first();

            $existingStudent = $db->table('schedule_students')->where([
                'schedule_id' => $jadwal_id,
                'anak_id' => $anak_id
            ])->get()->getRowArray();

            if ($existingAttendance) {
                $db->transRollback();
                return redirect()->back()->with('error', 'Anak sudah hadir di jadwal ini');
            }

            $now = date('Y-m-d H:i:s');
            $catatanFinal = $catatan ?: 'Ditambahkan manual via Edit Kedatangan';

            // Jika sudah terdaftar di schedule_students, cukup update jadi hadir.
            if ($existingStudent) {
                $studentUpdate = $this->buildScheduleStudentRow($db, [
                    'status' => 'hadir',
                    'catatan' => $catatanFinal,
                    'updated_at' => $now,
                ]);
                if (!$db->table('schedule_students')->where('id', (int) $existingStudent['id'])->update($studentUpdate)) {
                    throw new \Exception($this->dbErrorMessage($db, 'Gagal update schedule_students.'));
                }
            } else {
                $studentInsert = $this->buildScheduleStudentRow($db, [
                    'schedule_id' => $jadwal_id,
                    'anak_id' => $anak_id,
                    'status' => 'hadir',
                    'catatan' => $catatanFinal,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                if (!$db->table('schedule_students')->insert($studentInsert)) {
                    throw new \Exception($this->dbErrorMessage($db, 'Gagal insert schedule_students.'));
                }
            }

            // Tambahkan ke latihan_attendance + snapshot jenis les (wajib di production).
            $attendanceInsert = $this->buildAttendanceRow($db, [
                'schedule_id' => $jadwal_id,
                'anak_id' => $anak_id,
                'jenis_les_id' => $jenisLesId,
                'status_kehadiran' => 'hadir',
                'catatan' => $catatanFinal,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            if (!$db->table('latihan_attendance')->insert($attendanceInsert)) {
                throw new \Exception($this->dbErrorMessage($db, 'Gagal insert latihan_attendance.'));
            }

            $sync = $this->anakModel->syncKuotaFromRiwayat((int) $anak_id);

            $this->logModel->addLog('Tambah Anak Manual', "Menambahkan anak ID #{$anak_id} ke jadwal ID #{$jadwal_id}. {$sync['message']}");

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Gagal menyimpan data');
            }

            return redirect()->back()->with('success', 'Peserta berhasil ditambahkan. ' . $sync['message']);

        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'saveEditAbsensi gagal: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function deleteEditAbsensi($id)
    {
        $kehadiran = $this->kehadiranModel->find($id);
        if (!$kehadiran) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        $anak_id = $kehadiran['anak_id'];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Hapus dari latihan_attendance
            $this->kehadiranModel->delete($id);

            // Hapus juga dari schedule_students untuk konsistensi (Constraint Ganda)
            $db->table('schedule_students')->where([
                'schedule_id' => $kehadiran['schedule_id'],
                'anak_id' => $anak_id
            ])->delete();

            // Update sisa pertemuan menggunakan sinkronisasi total
            $this->anakModel->recalculateSisaPertemuan($anak_id);

            // Log Aktivitas
            $this->logModel->addLog('Hapus Absensi Manual', "Menghapus anak ID #{$anak_id} dari jadwal ID #{$kehadiran['schedule_id']}. Sisa pertemuan disinkronisasi.");

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Gagal menghapus data');
            }

            return redirect()->back()->with('success', 'Peserta berhasil dihapus (Sisa pertemuan bertambah 1)');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function cetakLaporan($jadwal_id)
    {
        $jadwal = $this->scheduleModel->find($jadwal_id);
        if (!$jadwal) {
            return redirect()->back()->with('error', 'Jadwal tidak ditemukan');
        }

        $db = \Config\Database::connect();
        $kehadiran = $db->table('latihan_attendance')
            ->select('latihan_attendance.*, anak.nama, anak.nama_panggilan, anak.sisa_pertemuan, jenis_les.nama_les')
            ->join('anak', 'anak.id = latihan_attendance.anak_id')
            ->join('jenis_les', 'jenis_les.id = anak.jenis_les_id', 'left')
            ->where('latihan_attendance.schedule_id', $jadwal_id)
            ->get()
            ->getResultArray();

        // Hitung Pertemuan Ke-X untuk setiap anak
        foreach ($kehadiran as &$k) {
            // EKSEKUSI DULU: Pastikan sisa pertemuan aktual sebelum dikirim/dicetak
            $detail = $this->anakModel->getDetailedSisa($k['anak_id']);
            $k['sisa_pertemuan'] = $detail['sisa_display'];
            $k['pertemuan_ke'] = $detail['pertemuan_ke'];
            $k['paket_ke'] = $detail['paket_ke'];
        }
        unset($k);

        // Hitung Ringkasan
        $count_private = 0;
        $count_reguler = 0;
        foreach ($kehadiran as $k) {
            if (str_contains(strtolower($k['nama_les'] ?? ''), 'private')) {
                $count_private++;
            } else {
                $count_reguler++;
            }
        }

        $data = [
            'title' => 'Laporan Latihan Renang',
            'jadwal' => $jadwal,
            'kehadiran' => $kehadiran,
            'summary' => [
                'total' => count($kehadiran),
                'private' => $count_private,
                'reguler' => $count_reguler
            ]
        ];

        return view('admin/kedatangan/cetak_laporan', $data);
    }

    /**
     * Kirim Laporan WA dari Riwayat (Eksekusi perhitungan aktual dulu)
     */
    public function kirimWaRiwayat($id, $target)
    {
        $db = \Config\Database::connect();
        $k = $db->table('latihan_attendance')
            ->select('latihan_attendance.*, schedules.tanggal, schedules.jam_mulai, anak.nama as nama_anak, parents.whatsapp as whatsapp_parent')
            ->join('schedules', 'schedules.id = latihan_attendance.schedule_id')
            ->join('anak', 'anak.id = latihan_attendance.anak_id')
            ->join('parents', 'parents.id = anak.parent_id', 'left')
            ->where('latihan_attendance.id', $id)
            ->get()->getRowArray();

        if (!$k) {
            return redirect()->back()->with('error', 'Data riwayat tidak ditemukan');
        }

        // 1. EKSEKUSI PERHITUNGAN AKTUAL
        $detail = $this->anakModel->getDetailedSisa($k['anak_id']);
        $sisa_display = $detail['sisa_display'];
        $pertemuan_ke = $detail['pertemuan_ke'];
        $paket_ke = $detail['paket_ke'];

        $nama_hari = $this->getNamaHari(date('w', strtotime($k['tanggal'])));
        $tanggal_indo = date('d/m/Y', strtotime($k['tanggal']));
        $waktu_indo = date('H:i', strtotime($k['jam_mulai']));

        if ($target === 'ortu') {
            $nomor_wa = $k['whatsapp_parent'] ?? '';
            if (!empty($nomor_wa)) {
                if (strpos($nomor_wa, '0') === 0) {
                    $nomor_wa = '62' . substr($nomor_wa, 1);
                }
            }

            $pesan = "*LAPORAN LATIHAN RENANG*\n\n"
                . "Halo Ayah/Bunda dari *{$k['nama_anak']}*,\n"
                . "Kami menginformasikan bahwa ananda telah selesai mengikuti latihan hari ini:\n\n"
                . "📅 Hari/Tgl: {$nama_hari}, {$tanggal_indo}\n"
                . "⏰ Waktu: {$waktu_indo} WIB\n"
                . "🏊 Paket ke: {$paket_ke}\n"
                . "🏊 Pertemuan ke: {$pertemuan_ke}\n"
                . "📊 Sisa Pertemuan: *{$sisa_display}*\n\n"
                . "Terima kasih atas kepercayaannya. Sampai jumpa di latihan berikutnya! 👋\n\n"
                . "--- _Pesan Otomatis Sistem_ ---\n"
                . "_Kehadiran berdasarkan absensi kartu oleh admin dan absensi tatapmuka oleh coach._\n"
                . "_Apabila ada ketidak sesuaian mohon japri saya 08981274514_";

            return redirect()->to("https://wa.me/{$nomor_wa}?text=" . rawurlencode($pesan));
        } else {
            $pesan = "*LAPORAN KEHADIRAN SISWA*\n\n"
                . "Nama: *{$k['nama_anak']}*\n"
                . "Status: HADIR ✅\n"
                . "Paket ke: {$paket_ke}\n"
                . "Pertemuan ke: {$pertemuan_ke}\n"
                . "Sisa Pertemuan: *{$sisa_display}*\n"
                . "Tanggal: {$nama_hari}, {$tanggal_indo}\n\n"
                . "--- _Pesan Otomatis Sistem_ ---\n"
                . "_Kehadiran berdasarkan absensi kartu oleh admin dan absensi tatapmuka oleh coach._\n"
                . "_Apabila ada ketidak sesuaian mohon japri saya 08981274514_";

            return redirect()->to("https://wa.me/?text=" . rawurlencode($pesan));
        }
    }

    /**
     * Kirim Laporan WA Group Langsung dari List Kedatangan
     */
    public function kirimWaGroupJadwal($jadwal_id)
    {
        $jadwal = $this->scheduleModel->find($jadwal_id);
        if (!$jadwal) {
            return redirect()->back()->with('error', 'Jadwal tidak ditemukan');
        }

        $db = \Config\Database::connect();
        $kehadiran = $db->table('latihan_attendance')
            ->select('latihan_attendance.*, anak.nama, anak.sisa_pertemuan, jenis_les.nama_les')
            ->join('anak', 'anak.id = latihan_attendance.anak_id')
            ->join('jenis_les', 'jenis_les.id = anak.jenis_les_id', 'left')
            ->where('latihan_attendance.schedule_id', $jadwal_id)
            ->get()
            ->getResultArray();

        if (empty($kehadiran)) {
            return redirect()->back()->with('error', 'Belum ada data absensi untuk jadwal ini');
        }

        $hari = $this->getNamaHari(date('w', strtotime($jadwal['tanggal'])));
        $tanggal = date('d/m/Y', strtotime($jadwal['tanggal']));
        $waktu = date('H:i', strtotime($jadwal['jam_mulai'])) . ' - ' . date('H:i', strtotime($jadwal['jam_selesai']));

        $text = "*LAPORAN LATIHAN RENANG*\n";
        $text .= "*11 MARET SPORT CENTER*\n\n";
        $text .= "Alhamdulillah, latihan hari ini telah selesai dilaksanakan pada:\n";
        $text .= "📅 Hari: {$hari}\n";
        $text .= "🗓️ Tanggal: {$tanggal}\n";
        $text .= "⏰ Waktu: {$waktu} WIB\n";
        $text .= "🏊 Materi: " . ($jadwal['materi'] ?: '-') . "\n\n";
        $text .= "*DAFTAR PESERTA LATIHAN:*\n";

        $i = 1;
        foreach ($kehadiran as $k) {
            // EKSEKUSI DULU: Sinkronisasi sisa pertemuan aktual
            $detail = $this->anakModel->getDetailedSisa($k['anak_id']);
            $sisa_display = $detail['sisa_display'];
            $pertemuan_ke = $detail['pertemuan_ke'];
            $paket_ke = $detail['paket_ke'];

            $nama_les = $k['nama_les'] ?: '-';
            $text .= "{$i}. *{$k['nama']}* ({$nama_les}) - Pkt:{$paket_ke} Ke-{$pertemuan_ke} (Sisa: *{$sisa_display}*)\n";
            $i++;
        }

        $text .= "\nTerima kasih atas semangat dan kerja kerasnya hari ini! Sampai jumpa di jadwal latihan berikutnya. 🙏😊\n\n";
        $text .= "_*Catatan:* Apabila terdapat ketidaksesuaian data pada laporan di atas, mohon segera menghubungi (japri) Admin 08981274514 untuk proses koreksi. Terima kasih._";

        return redirect()->to("https://wa.me/?text=" . rawurlencode($text));
    }

    public function riwayat()
    {
        $db = \Config\Database::connect();
        
        // Ambil parameter filter dari request
        $tanggalMulai = $this->request->getGet('tanggal_mulai');
        $tanggalSelesai = $this->request->getGet('tanggal_selesai');
        $statusKehadiran = $this->request->getGet('status_kehadiran');
        $namaAnak = $this->request->getGet('nama_anak');
        
        // Buat query dasar
        $builder = $db->table('latihan_attendance')
            ->select('latihan_attendance.*, schedules.tanggal, schedules.jam_mulai, schedules.jam_selesai, anak.nama as nama_anak, anak.nama_panggilan, anak.sisa_pertemuan, parents.whatsapp as whatsapp_parent')
            ->join('schedules', 'schedules.id = latihan_attendance.schedule_id')
            ->join('anak', 'anak.id = latihan_attendance.anak_id')
            ->join('parents', 'parents.id = anak.parent_id', 'left');
        
        // Terapkan filter jika ada
        if (!empty($tanggalMulai)) {
            $builder->where('schedules.tanggal >=', date('Y-m-d', strtotime($tanggalMulai)));
        }
        
        if (!empty($tanggalSelesai)) {
            $builder->where('schedules.tanggal <=', date('Y-m-d', strtotime($tanggalSelesai)));
        }
        
        if (!empty($statusKehadiran)) {
            $builder->where('latihan_attendance.status_kehadiran', $statusKehadiran);
        }
        
        if (!empty($namaAnak)) {
            $builder->like('anak.nama', $namaAnak);
        }
        
        // Urutkan data
        $builder->orderBy('schedules.tanggal', 'DESC');
        
        // Eksekusi query
        $query = $builder->get();
        $kehadiran = $query->getResultArray();
        
        // Tambahkan nama hari dalam bahasa Indonesia untuk setiap data
        foreach ($kehadiran as $key => $k) {
            $timestamp = strtotime($k['tanggal']);
            $hari = $this->getNamaHari(date('w', $timestamp));
            $kehadiran[$key]['nama_hari'] = $hari;
        }
        
        $data = [
            'title' => 'Riwayat Kedatangan',
            'active' => 'kedatangan',
            'kehadiran' => $kehadiran,
            // Tambahkan parameter filter untuk digunakan di view
            'filter' => [
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
                'status_kehadiran' => $statusKehadiran,
                'nama_anak' => $namaAnak
            ]
        ];
        
        return view('admin/kedatangan/riwayat', $data);
    }

    public function editKehadiran($id)
    {
        $db = \Config\Database::connect();
        $kehadiran = $db->table('latihan_attendance la')
            ->select('la.*, a.nama as nama_anak, a.nama_panggilan, s.tanggal, s.jam_mulai, s.jam_selesai, s.materi')
            ->join('anak a', 'a.id = la.anak_id')
            ->join('schedules s', 's.id = la.schedule_id')
            ->where('la.id', (int) $id)
            ->get()
            ->getRowArray();

        if (!$kehadiran) {
            return redirect()->to('admin/kedatangan/riwayat')->with('error', 'Data kehadiran tidak ditemukan.');
        }

        $jenisLes = $db->table('jenis_les')
            ->select('id, nama_les')
            ->orderBy('nama_les', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Edit Pertemuan',
            'active' => 'kedatangan',
            'kehadiran' => $kehadiran,
            'jenis_les' => $jenisLes,
        ];

        return view('admin/kedatangan/edit_kehadiran', $data);
    }

    public function updateKehadiran($id)
    {
        $id = (int) $id;
        $tanggalBaru = (string) $this->request->getPost('tanggal');
        $jenisLesId = (int) $this->request->getPost('jenis_les_id');
        $statusKehadiran = (string) ($this->request->getPost('status_kehadiran') ?: 'hadir');
        $catatan = trim((string) $this->request->getPost('catatan'));

        if ($id <= 0 || $tanggalBaru === '' || $jenisLesId <= 0) {
            return redirect()->back()->withInput()->with('error', 'Data tidak lengkap.');
        }

        $timestampTanggal = strtotime($tanggalBaru);
        if ($timestampTanggal === false) {
            return redirect()->back()->withInput()->with('error', 'Format tanggal tidak valid.');
        }
        $tanggalBaru = date('Y-m-d', $timestampTanggal);

        if (!in_array($statusKehadiran, ['hadir', 'izin', 'tidak_hadir'], true)) {
            $statusKehadiran = 'hadir';
        }

        $db = \Config\Database::connect();
        $attendance = $db->table('latihan_attendance')->where('id', $id)->get()->getRowArray();
        if (!$attendance) {
            return redirect()->to('admin/kedatangan/riwayat')->with('error', 'Data kehadiran tidak ditemukan.');
        }

        $oldSchedule = $this->scheduleModel->find((int) $attendance['schedule_id']);
        if (!$oldSchedule) {
            return redirect()->back()->withInput()->with('error', 'Jadwal asal tidak ditemukan.');
        }

        $db->transStart();
        try {
            // Perubahan tanggal dilakukan per pertemuan: pindahkan ke schedule lain dengan jam yang sama.
            $newSchedule = $db->table('schedules')
                ->where('tanggal', $tanggalBaru)
                ->where('jam_mulai', $oldSchedule['jam_mulai'])
                ->where('jam_selesai', $oldSchedule['jam_selesai'])
                ->where('materi', $oldSchedule['materi'])
                ->get()
                ->getRowArray();

            if (!$newSchedule) {
                $insertSchedule = [
                    'tanggal' => $tanggalBaru,
                    'jam_mulai' => $oldSchedule['jam_mulai'],
                    'jam_selesai' => $oldSchedule['jam_selesai'],
                    'materi' => $oldSchedule['materi'],
                    'kapasitas' => $oldSchedule['kapasitas'] ?? 20,
                    'status' => $oldSchedule['status'] ?? 'selesai',
                    'jenis_latihan' => $oldSchedule['jenis_latihan'] ?? null,
                    'status_latihan' => $oldSchedule['status_latihan'] ?? null,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                $db->table('schedules')->insert($insertSchedule);
                $newScheduleId = (int) $db->insertID();
            } else {
                $newScheduleId = (int) $newSchedule['id'];
            }

            $anakId = (int) $attendance['anak_id'];
            $oldScheduleId = (int) $attendance['schedule_id'];

            $duplicateAttendance = $db->table('latihan_attendance')
                ->where('schedule_id', $newScheduleId)
                ->where('anak_id', $anakId)
                ->where('id !=', $id)
                ->get()
                ->getRowArray();
            if ($duplicateAttendance) {
                throw new \RuntimeException('Anak sudah memiliki absensi pada jadwal tujuan (tanggal tersebut).');
            }

            $db->table('latihan_attendance')
                ->where('id', $id)
                ->update([
                    'schedule_id' => $newScheduleId,
                    'jenis_les_id' => $jenisLesId,
                    'status_kehadiran' => $statusKehadiran,
                    'catatan' => $catatan !== '' ? $catatan : ($attendance['catatan'] ?? null),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            $existingStudentTarget = $db->table('schedule_students')
                ->where('schedule_id', $newScheduleId)
                ->where('anak_id', $anakId)
                ->get()
                ->getRowArray();

            $existingStudentSource = $db->table('schedule_students')
                ->where('schedule_id', $oldScheduleId)
                ->where('anak_id', $anakId)
                ->get()
                ->getRowArray();

            if ($existingStudentSource) {
                if ($existingStudentTarget) {
                    $db->table('schedule_students')->where('id', (int) $existingStudentSource['id'])->delete();
                } else {
                    $db->table('schedule_students')
                        ->where('id', (int) $existingStudentSource['id'])
                        ->update([
                            'schedule_id' => $newScheduleId,
                            'status' => $statusKehadiran === 'hadir' ? 'hadir' : 'belum_hadir',
                            'catatan' => $catatan !== '' ? $catatan : ($existingStudentSource['catatan'] ?? null),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                }
            } elseif (!$existingStudentTarget) {
                $db->table('schedule_students')->insert([
                    'schedule_id' => $newScheduleId,
                    'anak_id' => $anakId,
                    'status' => $statusKehadiran === 'hadir' ? 'hadir' : 'belum_hadir',
                    'catatan' => $catatan !== '' ? $catatan : 'Sinkron dari edit kehadiran',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            $this->anakModel->syncKuotaFromRiwayat($anakId);
            $this->logModel->addLog(
                'Edit Kehadiran',
                "Edit kehadiran #{$id}: anak #{$anakId}, jadwal {$oldScheduleId} -> {$newScheduleId}, tanggal {$tanggalBaru}, jenis_les #{$jenisLesId}."
            );

            $db->transComplete();
            if ($db->transStatus() === false) {
                throw new \RuntimeException('Gagal menyimpan perubahan kehadiran.');
            }

            return redirect()->to('admin/kedatangan/riwayat')->with('success', 'Pertemuan berhasil diperbarui.');
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal update pertemuan: ' . $e->getMessage());
        }
    }

    /** @var array<string, list<string>> */
    private static array $tableColumnsCache = [];

    private function getTableColumns($db, string $table): array
    {
        if (!isset(self::$tableColumnsCache[$table])) {
            self::$tableColumnsCache[$table] = $db->getFieldNames($table) ?: [];
        }

        return self::$tableColumnsCache[$table];
    }

    private function tableHasColumn($db, string $table, string $column): bool
    {
        return in_array($column, $this->getTableColumns($db, $table), true);
    }

    private function getScheduleJenisLesIds($db, int $scheduleId): array
    {
        if ($scheduleId <= 0) {
            return [];
        }

        return array_values(array_unique(array_map('intval', array_column(
            $db->table('schedule_jenis_les')
                ->select('jenis_les_id')
                ->where('schedule_id', $scheduleId)
                ->get()
                ->getResultArray(),
            'jenis_les_id'
        ))));
    }

    /**
     * Resolve jenis_les_id untuk latihan_attendance (production biasanya NOT NULL + FK).
     */
    private function resolveAttendanceJenisLesId($db, int $scheduleId, array $anak): int
    {
        $anakJenis = (int) ($anak['jenis_les_id'] ?? 0);
        $allowed = $this->getScheduleJenisLesIds($db, $scheduleId);

        if ($anakJenis > 0 && (empty($allowed) || in_array($anakJenis, $allowed, true))) {
            return $anakJenis;
        }

        if (!empty($allowed)) {
            return (int) $allowed[0];
        }

        if ($anakJenis > 0) {
            return $anakJenis;
        }

        $pay = $db->table('pembayaran')
            ->select('jenis_les_id')
            ->where('anak_id', (int) ($anak['id'] ?? 0))
            ->where('status', 'success')
            ->where('jenis_les_id IS NOT NULL', null, false)
            ->orderBy('tanggal', 'DESC')
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();

        return !empty($pay['jenis_les_id']) ? (int) $pay['jenis_les_id'] : 0;
    }

    private function buildScheduleStudentRow($db, array $base): array
    {
        if ($this->tableHasColumn($db, 'schedule_students', 'enrollment_status') && !isset($base['enrollment_status'])) {
            $base['enrollment_status'] = 'aktif';
        }

        $columns = $this->getTableColumns($db, 'schedule_students');

        return array_intersect_key($base, array_flip($columns));
    }

    private function buildAttendanceRow($db, array $base): array
    {
        $columns = $this->getTableColumns($db, 'latihan_attendance');

        return array_intersect_key($base, array_flip($columns));
    }

    private function dbErrorMessage($db, string $prefix): string
    {
        $err = $db->error();
        if (!empty($err['message'])) {
            return trim($prefix . ' ' . $err['message']);
        }

        return $prefix;
    }

    // Fungsi helper untuk mendapatkan nama hari dalam bahasa Indonesia
    private function getNamaHari($dayOfWeek)
    {
        $namaHari = [
            '0' => 'Minggu',
            '1' => 'Senin',
            '2' => 'Selasa',
            '3' => 'Rabu',
            '4' => 'Kamis',
            '5' => 'Jumat',
            '6' => 'Sabtu'
        ];
        
        return $namaHari[$dayOfWeek];
    }

    public function tambahPesertaManualForm($jadwal_id)
    {
        $jadwal = $this->scheduleModel->find($jadwal_id);
        if (!$jadwal) {
            return redirect()->to('admin/kedatangan')->with('error', 'Jadwal tidak ditemukan');
        }
        
        $data = [
            'jadwal' => $jadwal,
            'semua_anak' => $this->anakModel->findAll()
        ];
        
        return view('admin/kedatangan/tambah_peserta_manual', $data);
    }


    public function saveAllAbsensi()
    {
        $jadwal_id = $this->request->getPost('jadwal_id');
        $anak_ids = $this->request->getPost('anak_id');
        $status_kehadiran = $this->request->getPost('status_kehadiran');
        $selected_students = $this->request->getPost('selected_students');
        $aksi = (string) $this->request->getPost('aksi');

        // Mulai transaksi database
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Jika aksi adalah 'selesai', kita hanya perlu mengupdate status jadwal
            if ($aksi === 'selesai') {
                $db->table('schedules')
                   ->where('id', $jadwal_id)
                   ->update(['status' => 'selesai']);
                
                $db->transComplete();
                // Jika status 'selesai', arahkan ke halaman cetak laporan
                return redirect()->to('admin/kedatangan/cetak-laporan/' . $jadwal_id)->with('success', 'Jadwal diselesaikan. Silakan kirim laporan WA.');
            }

            // Logika simpan (jika selected_students tidak kosong)
            if (!empty($selected_students)) {
                foreach($selected_students as $anak_id) {
                    // Ambil data anak untuk mendapatkan jenis_les_id saat ini sebagai snapshot
                    $anak = $this->anakModel->find($anak_id);
                    $jenis_les_id = $anak ? $anak['jenis_les_id'] : 0;

                    // Hanya proses jika status kehadiran adalah 'hadir'
                    if ($status_kehadiran[$anak_id] === 'hadir') {
                    // Update atau insert ke tabel schedule_students
                    $existingStudent = $db->table('schedule_students')->where([
                        'schedule_id' => $jadwal_id,
                        'anak_id' => $anak_id
                    ])->get()->getRow();

                    if ($existingStudent) {
                        $db->table('schedule_students')->where('id', $existingStudent->id)->update([
                            'status' => 'hadir',
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    } else {
                        $db->table('schedule_students')->insert([
                            'schedule_id' => $jadwal_id,
                            'anak_id' => $anak_id,
                            'status' => 'hadir',
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    }

                    // Update atau insert ke tabel latihan_attendance
                    $existingAttendance = $this->kehadiranModel->where([
                        'schedule_id' => $jadwal_id,
                        'anak_id' => $anak_id
                    ])->first();

                    if ($existingAttendance) {
                        $this->kehadiranModel->update($existingAttendance['id'], [
                            'status_kehadiran' => $status_kehadiran[$anak_id],
                            'jenis_les_id' => $jenis_les_id, // Update snapshot jika ada perubahan status
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    } else {
                        $this->kehadiranModel->insert([
                            'schedule_id' => $jadwal_id,
                            'anak_id' => $anak_id,
                            'jenis_les_id' => $jenis_les_id, // Simpan snapshot jenis les saat ini
                            'status_kehadiran' => $status_kehadiran[$anak_id],
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    }

                    // Kurangi sisa pertemuan (Gunakan sistem sinkronisasi total)
                    $this->anakModel->recalculateSisaPertemuan($anak_id);
                }
            }
        } // Penutup if (!empty($selected_students))

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Gagal menyimpan absensi');
            }

            return redirect()->back()->with('success', 'Data absensi berhasil disimpan');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal menyimpan absensi: ' . $e->getMessage());
        }
    }
}
