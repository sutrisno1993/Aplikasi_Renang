<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KehadiranModel;
use App\Models\AnakModel;
use App\Models\ScheduleModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Kedatangan1 extends BaseController
{
    protected $kehadiranModel;
    protected $anakModel;
    protected $scheduleModel;

    public function __construct()
    {
        $this->kehadiranModel = new KehadiranModel();
        $this->anakModel = new AnakModel();
        $this->scheduleModel = new ScheduleModel();
    }
    
    public function tambahPesertaManualForm($jadwal_id)
    {
        $jadwal = $this->scheduleModel->find($jadwal_id);
        if (!$jadwal) {
            return redirect()->to('admin/kedatangan')->with('error', 'Jadwal tidak ditemukan');
        }
        
        // Dapatkan daftar anak yang sudah terdaftar di jadwal ini
        $db = \Config\Database::connect();
        $builder = $db->table('latihan_attendance');
        $builder->select('anak_id');
        $builder->where('schedule_id', $jadwal_id);
        $query = $builder->get();
        $terdaftar = $query->getResultArray();
        
        // Ekstrak ID anak yang sudah terdaftar
        $terdaftar_ids = [];
        foreach ($terdaftar as $t) {
            $terdaftar_ids[] = $t['anak_id'];
        }
        
        // Filter anak yang memiliki sisa pertemuan > 0 dan belum terdaftar di jadwal ini
        $builder = $this->anakModel->builder();
        $builder->where('sisa_pertemuan >', 0);
        
        // Jika ada anak yang sudah terdaftar, exclude mereka dari hasil
        if (!empty($terdaftar_ids)) {
            $builder->whereNotIn('id', $terdaftar_ids);
        }
        
        $semua_anak = $builder->get()->getResultArray();
        
        $data = [
            'jadwal' => $jadwal,
            'semua_anak' => $semua_anak
        ];
        
        return view('admin/kedatangan/tambah_peserta_manual', $data);
    }
}