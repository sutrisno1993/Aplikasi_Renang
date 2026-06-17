<?php

namespace App\Models;

use CodeIgniter\Model;

class ScheduleModel extends Model
{
    protected $table = 'schedules';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'materi',
        'kapasitas',
        'status',
        'created_at',
        'updated_at',
        'jenis_latihan',
        'status_latihan'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    public function getActiveSchedules()
    {
        return $this->where('status', 'aktif')
                   ->orderBy('tanggal', 'ASC')
                   ->orderBy('jam_mulai', 'ASC')
                   ->findAll();
    }
    
    public function getScheduleWithDetails($id = null)
    {
        $builder = $this->select('schedules.*');
        
        if ($id !== null) {
            return $builder->find($id);
        }
        
        return $builder->findAll();
    }
}