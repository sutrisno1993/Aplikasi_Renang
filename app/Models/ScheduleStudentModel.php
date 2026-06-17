<?php

namespace App\Models;

use CodeIgniter\Model;

class ScheduleStudentModel extends Model
{
    protected $table = 'schedule_students';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'schedule_id',
        'anak_id',
        'status',
        'enrollment_status',
        'catatan',
        'created_at',
        'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    public function getScheduleStudents($scheduleId)
    {
        return $this->select('schedule_students.*, anak.nama as nama_anak')
            ->join('anak', 'anak.id = schedule_students.anak_id')
            ->where('schedule_id', $scheduleId)
            ->findAll();
    }
    
    public function getParentSchedules($parentId)
    {
        return $this->select('schedules.*, anak.nama as nama_anak, jenis_les.nama_les, GROUP_CONCAT(DISTINCT coach.nama) as nama_pelatih')
            ->join('schedules', 'schedules.id = schedule_students.schedule_id')
            ->join('anak', 'anak.id = schedule_students.anak_id')
            ->join('schedule_coaches', 'schedule_coaches.schedule_id = schedules.id', 'left')
            ->join('coach', 'coach.id = schedule_coaches.coach_id', 'left')
            ->join('schedule_jenis_les', 'schedule_jenis_les.schedule_id = schedules.id', 'left')
            ->join('jenis_les', 'jenis_les.id = schedule_jenis_les.jenis_les_id', 'left')
            ->where('anak.parent_id', $parentId)
            ->where('schedules.status', 'aktif')
            ->groupBy('schedules.id, anak.nama, jenis_les.nama_les')
            ->orderBy('schedules.tanggal', 'ASC')
            ->orderBy('schedules.jam_mulai', 'ASC')
            ->findAll();
    }
}