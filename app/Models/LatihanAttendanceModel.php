<?php

namespace App\Models;

use CodeIgniter\Model;

class LatihanAttendanceModel extends Model
{
    protected $table = 'latihan_attendance';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'schedule_id',
        'anak_id',
        'status_kehadiran',
        'catatan',
        'created_at',
        'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    public function getAttendanceWithDetail($scheduleId = null)
    {
        $builder = $this->select('latihan_attendance.*, anak.nama as nama_anak, schedules.tanggal, schedules.jam_mulai, schedules.jam_selesai')
            ->join('anak', 'anak.id = latihan_attendance.anak_id')
            ->join('schedules', 'schedules.id = latihan_attendance.schedule_id');
            
        if ($scheduleId !== null) {
            return $builder->where('latihan_attendance.schedule_id', $scheduleId)->findAll();
        }
        
        return $builder->findAll();
    }
}