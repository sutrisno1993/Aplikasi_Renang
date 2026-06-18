<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JadwalModel;
use App\Models\JenisLesModel;
use App\Models\JadwalJenisLesModel;
use App\Models\CoachModel; // Tambahkan ini

class JadwalController extends BaseController
{
    public function index()
    {
        $jadwalModel = new JadwalModel();
        $jenisLesModel = new JenisLesModel();
        $coachModel = new CoachModel(); // Tambahkan ini
        
        $data = [
            'jadwal' => $jadwalModel->getJadwalWithRelations(),
            'jenis_les' => $jenisLesModel->findAll(),
            'coaches' => $coachModel->findAll() // Tambahkan ini
        ];
        
        return view('admin/jadwal/index', $data);
    }

    public function save()
    {
        // Validate form data
        $validation = $this->validate([
            'tanggal' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'jenis_les' => 'required',
            'materi' => 'required',
            'kapasitas' => 'required|numeric|greater_than[0]',
            'jenis_latihan' => 'required|in_list[private,group]',
            'coach_id' => 'permit_empty'
        ]);

        if (!$validation) {
            // Debug: Log validation errors
            log_message('debug', 'Validation errors: ' . json_encode($this->validator->getErrors()));
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }

        try {
            // Debug: Log received data
            log_message('debug', 'Data yang diterima dari form: ' . json_encode($this->request->getPost()));

            $data = [
                'tanggal' => $this->request->getPost('tanggal'),
                'jam_mulai' => $this->request->getPost('jam_mulai'),
                'jam_selesai' => $this->request->getPost('jam_selesai'),
                'materi' => $this->request->getPost('materi'),
                'kapasitas' => $this->request->getPost('kapasitas'),
                'jenis_latihan' => $this->request->getPost('jenis_latihan'),
                'status' => 'aktif',
                'status_latihan' => 'belum_mulai'
            ];

            // Debug: Log data to be saved
            log_message('debug', 'Data yang akan disimpan: ' . json_encode($data));

            $db = \Config\Database::connect();
            $db->transStart();

            // Insert into jadwal table
            $jadwalModel = new JadwalModel();
            $schedule_id = $jadwalModel->insert($data);

            if (!$schedule_id) {
                throw new \Exception('Gagal menyimpan jadwal: ' . print_r($jadwalModel->errors(), true));
            }

            // Debug: Log new schedule ID
            log_message('debug', 'ID jadwal baru: ' . $schedule_id);

            // Insert jenis_les relations
            $jenis_les = $this->request->getPost('jenis_les');
            $jadwalJenisLesModel = new JadwalJenisLesModel();
            
            foreach ($jenis_les as $les_id) {
                $result = $jadwalJenisLesModel->insert([
                    'schedule_id' => $schedule_id,
                    'jenis_les_id' => $les_id
                ]);
                
                if (!$result) {
                    throw new \Exception('Gagal menyimpan relasi jenis les: ' . print_r($jadwalJenisLesModel->errors(), true));
                }
            }

            // Insert coach relations
            $coach_ids = $this->request->getPost('coach_id');
            if ($coach_ids) {
                foreach ($coach_ids as $coach_id) {
                    $db->table('schedule_coaches')->insert([
                        'schedule_id' => $schedule_id,
                        'coach_id' => $coach_id
                    ]);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi database gagal');
            }

            // Debug: Log success
            log_message('debug', 'Jadwal berhasil disimpan');
            return redirect()->to('admin/jadwal')->with('success', 'Jadwal berhasil ditambahkan');

        } catch (\Exception $e) {
            // Debug: Log exception
            log_message('error', 'Exception saat menyimpan jadwal: ' . $e->getMessage());
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan jadwal: ' . $e->getMessage());
        }
    }

    public function update($id)
    {
        // Validate form data
        $validation = $this->validate([
            'tanggal' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'jenis_les' => 'required',
            'materi' => 'required',
            'kapasitas' => 'required|numeric|greater_than[0]',
            'jenis_latihan' => 'required|in_list[private,group]',
            'coach_id' => 'permit_empty'
        ]);

        if (!$validation) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }

        // Get form data
        $data = [
            'tanggal' => $this->request->getPost('tanggal'),
            'jam_mulai' => $this->request->getPost('jam_mulai'),
            'jam_selesai' => $this->request->getPost('jam_selesai'),
            'materi' => $this->request->getPost('materi'),
            'kapasitas' => $this->request->getPost('kapasitas'),
            'status' => $this->request->getPost('status')
        ];

        // Start database transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update jadwal table
            $jadwalModel = new JadwalModel();
            $jadwalModel->update($id, $data);

            // Update jenis_les relations
            $jenis_les = $this->request->getPost('jenis_les');
            $jadwalJenisLesModel = new JadwalJenisLesModel();
            
            // Delete existing relations
            $jadwalJenisLesModel->where('schedule_id', $id)->delete();
            
            // Insert new relations
            foreach ($jenis_les as $les_id) {
                $jadwalJenisLesModel->insert([
                    'schedule_id' => $id,
                    'jenis_les_id' => $les_id
                ]);
            }

            // Update coach relations
            $db->table('schedule_coaches')->where('schedule_id', $id)->delete();
            $coach_ids = $this->request->getPost('coach_id');
            if ($coach_ids) {
                foreach ($coach_ids as $coach_id) {
                    $db->table('schedule_coaches')->insert([
                        'schedule_id' => $id,
                        'coach_id'    => $coach_id
                    ]);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Gagal menyimpan perubahan');
            }

            return redirect()->to('admin/jadwal')->with('success', 'Jadwal berhasil diperbarui');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui jadwal: ' . $e->getMessage());
        }
    }

    public function detail($id = null)
    {
        if ($id === null) {
            return redirect()->to('/admin/jadwal')->with('error', 'ID Jadwal tidak ditemukan.');
        }

        $jadwalModel = new JadwalModel();
        $jenisLesModel = new JenisLesModel();
        
        // Get jadwal data directly first
        $jadwal = $jadwalModel->find($id);
        
        if (!$jadwal) {
            return redirect()->to('/admin/jadwal')->with('error', 'Jadwal tidak ditemukan.');
        }
        
        // Get additional relations if needed
        $jadwalWithRelations = $jadwalModel->getJadwalWithRelations($id);
        
        // Get schedule students data
        $db = \Config\Database::connect();
        $schedule_students = $db->table('schedule_students')
            ->select('schedule_students.*, anak.nama as nama_anak, anak.nama_panggilan, anak.sisa_pertemuan, jenis_les.nama_les as jenis_les_nama')
            ->join('anak', 'anak.id = schedule_students.anak_id')
            ->join('jenis_les', 'jenis_les.id = anak.jenis_les_id')
            ->where('schedule_students.schedule_id', $id)
            ->get()
            ->getResultArray();
        
        // Merge all data
        $jadwalData = array_merge(
            $jadwal, 
            $jadwalWithRelations ?? [],
            ['schedule_students' => $schedule_students]
        );
        
        $data = [
            'title' => 'Detail Jadwal',
            'jadwal' => $jadwalData,
            'jenis_les' => $jenisLesModel->findAll(),
            'active' => 'jadwal'
        ];
        
        return view('admin/jadwal/detail', $data);
    }

    public function tambahPeserta($schedule_id)
    {
        $anak_id = $this->request->getPost('anak_id');
        if (!$anak_id) {
            return redirect()->back()->with('error', 'Anak wajib dipilih');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // 1. Cek duplikasi di schedule_students
            $existingStudent = $db->table('schedule_students')->where([
                'schedule_id' => $schedule_id,
                'anak_id' => $anak_id
            ])->get()->getRow();

            // 2. Cek duplikasi di latihan_attendance
            $existingAttendance = $db->table('latihan_attendance')->where([
                'schedule_id' => $schedule_id,
                'anak_id' => $anak_id
            ])->get()->getRow();

            if ($existingStudent || $existingAttendance) {
                return redirect()->back()->with('error', 'Anak sudah terdaftar atau hadir di jadwal ini');
            }

            // 3. Cek sisa pertemuan
            $anakModel = new \App\Models\AnakModel();
            $anak = $anakModel->find($anak_id);
            if ($anak['sisa_pertemuan'] <= 0) {
                return redirect()->back()->with('error', 'Sisa pertemuan anak habis');
            }

            // 4. Daftarkan
            $db->table('schedule_students')->insert([
                'schedule_id' => $schedule_id,
                'anak_id' => $anak_id,
                'status' => 'terdaftar',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // 5. Potong sisa pertemuan
            $anakModel->update($anak_id, [
                'sisa_pertemuan' => $anak['sisa_pertemuan'] - 1
            ]);

            $db->transComplete();
            return redirect()->back()->with('success', 'Peserta berhasil ditambahkan');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function delete($id = null)
    {
        if (!$id) {
            return redirect()->to('admin/jadwal')->with('error', 'ID Jadwal tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Hapus data relasi di schedule_students terlebih dahulu
            $db->table('schedule_students')
               ->where('schedule_id', $id)
               ->delete();

            // Hapus data relasi di schedule_coaches
            $db->table('schedule_coaches')
               ->where('schedule_id', $id)
               ->delete();

            // Hapus data relasi di schedule_jenis_les (bukan jadwal_jenis_les)
            $db->table('schedule_jenis_les')
               ->where('schedule_id', $id)
               ->delete();

            // Terakhir hapus data jadwal dari tabel schedules
            $db->table('schedules')
               ->where('id', $id)
               ->delete();

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Gagal menghapus jadwal dan relasinya');
            }

            return redirect()->to('admin/jadwal')->with('success', 'Jadwal berhasil dihapus.');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to('admin/jadwal')->with('error', 'Terjadi kesalahan saat menghapus jadwal: ' . $e->getMessage());
        }
    }

    public function riwayat()
    {
        $jadwalModel = new JadwalModel();
        $jenisLesModel = new JenisLesModel();
        $coachModel = new CoachModel();
        
        $data = [
            'jadwal' => $jadwalModel->getRiwayatJadwalWithRelations(),
            'jenis_les' => $jenisLesModel->findAll(),
            'coaches' => $coachModel->findAll()
        ];
        
        return view('admin/jadwal/riwayat_jadwal', $data);
    }
}
