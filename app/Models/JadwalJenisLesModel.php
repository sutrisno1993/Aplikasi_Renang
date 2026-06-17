<?php

namespace App\Models;

use CodeIgniter\Model;

class JadwalJenisLesModel extends Model
{
    protected $table = 'schedule_jenis_les';
    protected $primaryKey = 'id';
    protected $allowedFields = ['schedule_id', 'jenis_les_id'];
    protected $useTimestamps = false; // Ubah menjadi false jika tidak ingin menggunakan timestamp
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Get all jenis les for a specific schedule
    public function getJenisLesForSchedule($scheduleId)
    {
        return $this->select('jenis_les.*')
                    ->join('jenis_les', 'jenis_les.id = schedule_jenis_les.jenis_les_id')
                    ->where('schedule_id', $scheduleId)
                    ->findAll();
    }
}