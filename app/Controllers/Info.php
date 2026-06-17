<?php

namespace App\Controllers;

use App\Models\AnakModel;
use App\Models\KehadiranModel;
use App\Models\PembayaranModel;

class Info extends BaseController
{
    public function index()
    {
        $anakModel = new AnakModel();
        $db = \Config\Database::connect();

        // 1. Ambil data anak beserta tanggal aktif terakhir (dari latihan_attendance)
        $siswa = $anakModel->select('
            anak.id,
            anak.nama,
            anak.nama_panggilan,
            anak.sisa_pertemuan,
            anak.foto,
            MAX(s.tanggal) as tanggal_aktif_terakhir
        ')
            ->join('latihan_attendance la', 'la.anak_id = anak.id', 'left')
            ->join('schedules s', 's.id = la.schedule_id', 'left')
            ->groupBy('anak.id')
            ->findAll();

        $today = date('Y-m-d');
        $kategori1 = []; // Aktif dalam 30 hari terakhir
        $kategori2 = []; // Tidak aktif > 30 hari

        foreach ($siswa as $row) {
            $anakModel->recalculateSisaPertemuan((int) $row['id']);
            $bd = $anakModel->getPaketBreakdown((int) $row['id']);
            
            $dataSiswa = [
                'id' => $row['id'],
                'nama' => $row['nama'],
                'nama_panggilan' => $row['nama_panggilan'],
                'sisa_pertemuan' => $bd['sisa_total'],
                'berlaku_sampai' => $bd['berlaku_sampai'],
                'is_expired' => $bd['has_expired_hangus'],
                'hangus_total' => $bd['hangus_total'],
                'paket_ringkas' => $bd['paket'],
                'foto' => $row['foto'],
                'tanggal_aktif_terakhir' => $row['tanggal_aktif_terakhir'] ?? '1970-01-01', // Default jika belum pernah latihan
            ];

            // Hitung selisih hari
            $lastActive = new \DateTime($dataSiswa['tanggal_aktif_terakhir']);
            $currentDate = new \DateTime($today);
            $diff = $currentDate->diff($lastActive)->days;
            
            // Jika tanggal aktif terakhir adalah masa depan (tidak mungkin, tapi untuk amannya) atau hari ini, diff tetap 0
            if ($lastActive > $currentDate) {
                $diff = 0;
            }

            if ($diff <= 30 && $row['tanggal_aktif_terakhir'] !== null) {
                $kategori1[] = $dataSiswa;
            } else {
                $kategori2[] = $dataSiswa;
            }
        }

        // 2. Urutkan Kategori 1 secara Ascending (tanggal aktif terlama ke terbaru)
        usort($kategori1, function($a, $b) {
            return strcmp($a['tanggal_aktif_terakhir'], $b['tanggal_aktif_terakhir']);
        });

        // 3. Urutkan Kategori 2 secara Ascending (tanggal aktif terlama ke terbaru)
        usort($kategori2, function($a, $b) {
            return strcmp($a['tanggal_aktif_terakhir'], $b['tanggal_aktif_terakhir']);
        });

        // 4. Gabungkan: Kategori 1 di atas, Kategori 2 di bawah
        $siswaSorted = array_merge($kategori1, $kategori2);

        $data = [
            'title' => 'Monitoring Sisa Pertemuan',
            'siswa' => $siswaSorted,
        ];

        return view('info/index', $data);
    }

    public function detail($id)
    {
        $anakModel = new AnakModel();
        $kehadiranModel = new KehadiranModel();
        $pembayaranModel = new PembayaranModel();

        $anak = $anakModel->where('anak.id', $id)->first();

        if (!$anak) {
            return redirect()->to('/info');
        }

        $anakModel->recalculateSisaPertemuan((int) $id);
        $breakdown = $anakModel->getPaketBreakdown((int) $id);
        $anak['sisa_pertemuan'] = $breakdown['sisa_total'];
        $anak['berlaku_sampai'] = $breakdown['berlaku_sampai'];
        $anak['is_expired'] = $breakdown['has_expired_hangus'];
        $anak['hangus_total'] = $breakdown['hangus_total'];

        $pembayaran = $pembayaranModel->where('anak_id', $id)
            ->orderBy('tanggal', 'DESC')
            ->findAll();

        $latihan = $kehadiranModel->select('latihan_attendance.*, schedules.tanggal, schedules.jam_mulai, schedules.jam_selesai')
            ->join('schedules', 'schedules.id = latihan_attendance.schedule_id')
            ->where('latihan_attendance.anak_id', $id)
            ->orderBy('schedules.tanggal', 'DESC')
            ->get()->getResultArray();

        $latihan = $anakModel->annotateKehadiranWithPaket((int) $id, $latihan);

        $data = [
            'title' => 'Detail Siswa - ' . $anak['nama'],
            'anak' => $anak,
            'pembayaran' => $pembayaran,
            'latihan' => $latihan,
            'breakdown' => $breakdown,
            'history_groups' => $breakdown['history_groups'],
            'detail_sisa' => $breakdown['detail_sisa'],
        ];

        return view('info/detail', $data);
    }
}
