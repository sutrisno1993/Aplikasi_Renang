<?php

namespace App\Controllers\Parent;

use App\Controllers\BaseController;

class JadwalController extends BaseController
{
    public function detail($id)
    {
        $db = \Config\Database::connect();
        
        // Ambil data schedule (jadwal)
        $jadwal = $db->table('schedules')
            ->where('id', $id)
            ->get()
            ->getRowArray();
            
        if (!$jadwal) {
            return redirect()->to('parent/dashboard')->with('error', 'Jadwal tidak ditemukan');
        }

        // Ambil data anak dari parent yang login
        $anak = $db->table('anak a')
            ->select('a.*, jl.nama_les')
            ->join('jenis_les jl', 'jl.id = a.jenis_les_id')
            ->where('a.parent_id', session()->get('parent_id'))
            ->get()
            ->getResultArray();
            
        // Query untuk mendapatkan data coach
        $coaches = $db->table('schedule_coaches sc')
            ->select('c.nama, c.keahlian')
            ->join('coach c', 'c.id = sc.coach_id')
            ->where('sc.schedule_id', $id)
            ->get()
            ->getResultArray();
        
        // Query untuk mendapatkan data peserta
        $peserta = $db->table('schedule_students ss')
            ->select('a.nama as nama_anak, jl.nama_les')
            ->join('anak a', 'a.id = ss.anak_id')
            ->join('jenis_les jl', 'jl.id = a.jenis_les_id')
            ->where('ss.schedule_id', $id)
            ->get()
            ->getResultArray();
        
        $data = [
            'title' => 'Detail Jadwal Les',
            'jadwal' => $jadwal,
            'coaches' => $coaches,
            'peserta' => $peserta,
            'anak' => $anak
        ];

        return view('parent/jadwal/detail', $data);
    }

    public function daftar($id)
    {
        $db = \Config\Database::connect();
        
        // Ambil data jadwal dengan jenis les
        $jadwal = $db->table('schedules s')
            ->select('s.*, GROUP_CONCAT(jl.nama_les) as jenis_les_names')
            ->join('schedule_jenis_les sjl', 'sjl.schedule_id = s.id')
            ->join('jenis_les jl', 'jl.id = sjl.jenis_les_id')
            ->where('s.id', $id)
            ->groupBy('s.id')
            ->get()
            ->getRowArray();
            
        if (!$jadwal) {
            return redirect()->to('parent/dashboard')->with('error', 'Jadwal tidak ditemukan');
        }

        // Perbaikan query untuk mengambil data anak
        $anak = $db->table('anak a')
            ->select('a.*, jl.nama_les')
            ->join('jenis_les jl', 'jl.id = a.jenis_les_id')
            ->where('a.parent_id', session()->get('parent_id'))
            ->where('NOT EXISTS (
                SELECT 1 FROM schedule_students ss 
                WHERE ss.schedule_id = ' . $id . ' 
                AND ss.anak_id = a.id
            )')
            ->where('EXISTS (
                SELECT 1 FROM schedule_jenis_les sjl 
                WHERE sjl.schedule_id = ' . $id . ' 
                AND sjl.jenis_les_id = a.jenis_les_id
            )')
            ->get()
            ->getResultArray();

        // Tambahkan query untuk mendapatkan data coach
        $coaches = $db->table('schedule_coaches sc')
            ->select('c.nama, c.keahlian')
            ->join('coach c', 'c.id = sc.coach_id')
            ->where('sc.schedule_id', $id)
            ->get()
            ->getResultArray();

        // Tambahkan query untuk mendapatkan data peserta
        $peserta = $db->table('schedule_students ss')
            ->select('a.nama as nama_anak, jl.nama_les')
            ->join('anak a', 'a.id = ss.anak_id')
            ->join('jenis_les jl', 'jl.id = a.jenis_les_id')
            ->where('ss.schedule_id', $id)
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Daftar Jadwal Les',
            'jadwal' => $jadwal,
            'anak' => $anak,
            'coaches' => $coaches,
            'peserta' => $peserta // Tambahkan data peserta ke array data
        ];

        return view('parent/daftar_jadwal', $data);
    }

    public function prosesDaftar()
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $jadwal_id = $this->request->getPost('jadwal_id');
            $anak_id = $this->request->getPost('anak_id');
            
            // Validasi input
            if (empty($jadwal_id) || empty($anak_id)) {
                throw new \Exception('Silakan pilih minimal satu anak');
            }

            // Cek schedule (jadwal)
            $jadwal = $db->table('schedules')
                ->where('id', $jadwal_id)
                ->get()
                ->getRowArray();

            if (!$jadwal) {
                throw new \Exception('Jadwal tidak ditemukan');
            }

            if ($jadwal['status'] !== 'aktif') {
                throw new \Exception('Jadwal tidak aktif');
            }

            // Cek kapasitas
            $peserta_terdaftar = $db->table('schedule_students')
                ->where('schedule_id', $jadwal_id)
                ->countAllResults();

            if ($peserta_terdaftar + 1 > $jadwal['kapasitas']) { // Changed to +1 since we're adding one student
                throw new \Exception('Kapasitas jadwal sudah penuh');
            }

            // Cek apakah anak sudah terdaftar
            $sudah_terdaftar = $db->table('schedule_students')
                ->where('schedule_id', $jadwal_id)
                ->where('anak_id', $anak_id)
                ->countAllResults();

            if ($sudah_terdaftar > 0) {
                throw new \Exception('Anak sudah terdaftar di jadwal ini');
            }

            // Cek data anak
            $anak = $db->table('anak')
                ->where('id', $anak_id)
                ->where('parent_id', session()->get('parent_id'))
                ->get()
                ->getRowArray();

            if (!$anak) {
                throw new \Exception('Data anak tidak valid');
            }

            // Daftarkan anak ke jadwal
            $insert_data = [
                'schedule_id' => $jadwal_id,
                'anak_id' => $anak_id,
                'status' => 'belum_hadir',
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            if (!$db->table('schedule_students')->insert($insert_data)) {
                throw new \Exception('Gagal mendaftarkan anak ke jadwal');
            }

            // Hapus bagian pengurangan sisa pertemuan karena akan dilakukan saat attendance
            
            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Gagal menyimpan data');
            }

            return redirect()->back()->with('success', 'Berhasil mendaftarkan anak ke jadwal');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }
}