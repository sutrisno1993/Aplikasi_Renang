<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AnakModel;

class Report extends BaseController
{
    protected $db;
    protected AnakModel $anakModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->anakModel = new AnakModel();
    }

    public function keuangan()
    {
        $tgl_mulai = $this->request->getGet('tgl_mulai') ?: date('Y-m-01');
        $tgl_selesai = $this->request->getGet('tgl_selesai') ?: date('Y-m-d');

        $pembayaran = $this->db->table('pembayaran')
            ->select('pembayaran.*, anak.nama as nama_anak, jenis_les.nama_les')
            ->join('anak', 'anak.id = pembayaran.anak_id', 'left')
            ->join('jenis_les', 'jenis_les.id = anak.jenis_les_id', 'left')
            ->where('DATE(pembayaran.tanggal) >=', $tgl_mulai)
            ->where('DATE(pembayaran.tanggal) <=', $tgl_selesai)
            ->where('pembayaran.status', 'success')
            ->orderBy('pembayaran.tanggal', 'DESC')
            ->get()->getResultArray();

        $summary = $this->db->table('pembayaran')
            ->selectSum('total', 'total_pendapatan')
            ->selectSum('earn_owner', 'total_owner')
            ->selectSum('earn_coach', 'total_coach')
            ->where('DATE(tanggal) >=', $tgl_mulai)
            ->where('DATE(tanggal) <=', $tgl_selesai)
            ->where('status', 'success')
            ->get()->getRowArray();

        $data = [
            'title' => 'Laporan Keuangan & Bagi Hasil',
            'active' => 'report-keuangan',
            'pembayaran' => $pembayaran,
            'summary' => $summary,
            'filter' => ['tgl_mulai' => $tgl_mulai, 'tgl_selesai' => $tgl_selesai]
        ];

        return view('admin/report/keuangan', $data);
    }

    public function kehadiran()
    {
        $tgl_mulai = $this->request->getGet('tgl_mulai') ?: date('Y-m-01');
        $tgl_selesai = $this->request->getGet('tgl_selesai') ?: date('Y-m-d');

        $kehadiran = $this->db->table('latihan_attendance')
            ->select('latihan_attendance.*, schedules.tanggal, schedules.jam_mulai, anak.nama as nama_anak, jenis_les.nama_les')
            ->join('schedules', 'schedules.id = latihan_attendance.schedule_id')
            ->join('anak', 'anak.id = latihan_attendance.anak_id')
            ->join('jenis_les', 'jenis_les.id = anak.jenis_les_id', 'left')
            ->where('schedules.tanggal >=', $tgl_mulai)
            ->where('schedules.tanggal <=', $tgl_selesai)
            ->orderBy('schedules.tanggal', 'DESC')
            ->get()->getResultArray();

        $stat = $this->db->table('latihan_attendance')
            ->select('status_kehadiran, COUNT(*) as jumlah')
            ->join('schedules', 'schedules.id = latihan_attendance.schedule_id')
            ->where('schedules.tanggal >=', $tgl_mulai)
            ->where('schedules.tanggal <=', $tgl_selesai)
            ->groupBy('status_kehadiran')
            ->get()->getResultArray();

        $data = [
            'title' => 'Laporan Kehadiran Siswa',
            'active' => 'report-kehadiran',
            'kehadiran' => $kehadiran,
            'stat' => $stat,
            'filter' => ['tgl_mulai' => $tgl_mulai, 'tgl_selesai' => $tgl_selesai]
        ];

        return view('admin/report/kehadiran', $data);
    }

    public function siswa()
    {
        $siswa_aktif = $this->db->table('anak')
            ->select('anak.*, jenis_les.nama_les, parents.nama as nama_parent, parents.whatsapp')
            ->join('jenis_les', 'jenis_les.id = anak.jenis_les_id', 'left')
            ->join('parents', 'parents.id = anak.parent_id', 'left')
            ->where('anak.status', 'aktif')
            ->get()->getResultArray();

        $per_paket = $this->db->table('anak')
            ->select('jenis_les.nama_les, COUNT(*) as jumlah')
            ->join('jenis_les', 'jenis_les.id = anak.jenis_les_id', 'left')
            ->groupBy('anak.jenis_les_id')
            ->get()->getResultArray();

        $data = [
            'title' => 'Laporan Analisis Data Siswa',
            'active' => 'report-siswa',
            'siswa' => $siswa_aktif,
            'per_paket' => $per_paket
        ];

        return view('admin/report/siswa', $data);
    }

    public function pembayaran()
    {
        // Query untuk mengambil list bulan terjadinya pembayaran
        $builder = $this->db->table('pembayaran');
        $builder->select("DATE_FORMAT(tanggal, '%Y-%m') as bulan_val, DATE_FORMAT(tanggal, '%M %Y') as bulan_label");
        $builder->where('status', 'success');
        $builder->groupBy('bulan_val');
        $builder->orderBy('bulan_val', 'ASC');
        $list_bulan = $builder->get()->getResultArray();

        $data_pembayaran = [];
        foreach ($list_bulan as $b) {
            $bulan = $b['bulan_val'];
            
            // Query per jenis les untuk bulan tersebut
            $per_paket = $this->db->table('pembayaran p')
                ->select('jl.nama_les, COUNT(*) as jumlah_paket')
                ->join('anak a', 'a.id = p.anak_id')
                ->join('jenis_les jl', 'jl.id = a.jenis_les_id')
                ->where("DATE_FORMAT(p.tanggal, '%Y-%m') =", $bulan)
                ->where('p.status', 'success')
                ->groupBy('jl.id')
                ->get()->getResultArray();

            $data_pembayaran[] = [
                'bulan_val' => $b['bulan_val'],
                'bulan_label' => $b['bulan_label'],
                'per_paket' => $per_paket
            ];
        }

        $data = [
            'title' => 'Laporan Pembayaran Bulanan',
            'active' => 'report-pembayaran',
            'data_pembayaran' => $data_pembayaran
        ];

        return view('admin/report/pembayaran', $data);
    }

    public function pembayaranDetail($bulan)
    {
        $pembayaran = $this->db->table('pembayaran p')
            ->select('p.*, a.nama as nama_anak, jl.nama_les')
            ->join('anak a', 'a.id = p.anak_id')
            ->join('jenis_les jl', 'jl.id = a.jenis_les_id')
            ->where("DATE_FORMAT(p.tanggal, '%Y-%m') =", $bulan)
            ->where('p.status', 'success')
            ->orderBy('p.tanggal', 'ASC')
            ->get()->getResultArray();

        $summary = $this->db->table('pembayaran p')
            ->selectSum('p.total', 'total_pendapatan')
            ->selectSum('p.earn_owner', 'total_owner')
            ->selectSum('p.earn_coach', 'total_coach')
            ->where("DATE_FORMAT(p.tanggal, '%Y-%m') =", $bulan)
            ->where('p.status', 'success')
            ->get()->getRowArray();

        $data = [
            'title' => 'Detail Laporan Pembayaran - ' . date('F Y', strtotime($bulan . '-01')),
            'active' => 'report-pembayaran',
            'pembayaran' => $pembayaran,
            'summary' => $summary,
            'bulan' => $bulan
        ];

        return view('admin/report/pembayaran_detail', $data);
    }

    public function paketExpired()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        if (session()->get('role') === 'boss') {
            return view('admin/report/ready_soon', [
                'title' => 'Laporan Penghitungan Paket Expired',
                'nama' => session()->get('nama'),
                'active' => 'report-expired'
            ]);
        }

        $filter = [
            'tgl_mulai' => $this->request->getGet('tgl_mulai') ?: '',
            'tgl_selesai' => $this->request->getGet('tgl_selesai') ?: '',
            'nama_anak' => $this->request->getGet('nama_anak') ?? '',
            'hanya_hangus' => $this->request->getGet('hanya_hangus') !== null ? $this->request->getGet('hanya_hangus') : (empty($_GET) ? '1' : '0'),
        ];

        $report = $this->anakModel->collectExpiredPackagesReport($filter);

        $data = [
            'title' => 'Monitoring Paket Expired',
            'active' => 'report-paket-expired',
            'rows' => $report['rows'],
            'summary' => $report['summary'],
            'filter' => $filter,
        ];

        return view('admin/report/paket_expired', $data);
    }
}
