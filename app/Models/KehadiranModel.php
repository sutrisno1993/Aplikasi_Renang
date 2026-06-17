<?php

namespace App\Models;

use CodeIgniter\Model;

class KehadiranModel extends Model
{
    protected $table = 'latihan_attendance';
    protected $primaryKey = 'id';
    protected $allowedFields = ['schedule_id', 'anak_id', 'jenis_les_id', 'status_kehadiran', 'catatan'];
    protected $useTimestamps = true;
    
    public function getKehadiran()
    {
        return $this->select('latihan_attendance.*, schedules.tanggal, schedules.jam_mulai, schedules.jam_selesai, anak.nama as nama_anak')
                ->join('schedules', 'schedules.id = latihan_attendance.schedule_id')
                ->join('anak', 'anak.id = latihan_attendance.anak_id')
                ->orderBy('schedules.tanggal', 'DESC')
                ->findAll();
    }
}