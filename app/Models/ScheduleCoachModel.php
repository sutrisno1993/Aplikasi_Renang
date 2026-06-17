<?php

namespace App\Models;

use CodeIgniter\Model;

class ScheduleCoachModel extends Model
{
    protected $table = 'schedule_coaches';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'schedule_id',
        'coach_id',
        'status',
        'catatan',
        'created_at',
        'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    public function getScheduleCoaches($scheduleId)
    {
        return $this->select('schedule_coaches.*, coach.nama as nama_coach')
            ->join('coach', 'coach.id = schedule_coaches.coach_id')
            ->where('schedule_id', $scheduleId)
            ->findAll();
    }
}