<?php

namespace App\Models;

use CodeIgniter\Model;

class JadwalModel extends Model
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
        'jenis_latihan',
        'status_latihan',
        'created_at',
        'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    public function getJadwalWithRelations($id = null)
    {
        $builder = $this->select('schedules.*, GROUP_CONCAT(DISTINCT jl.nama_les) as jenis_les_nama')
            ->join('schedule_jenis_les as sjl', 'sjl.schedule_id = schedules.id', 'left')
            ->join('jenis_les as jl', 'jl.id = sjl.jenis_les_id', 'left')
            ->groupBy('schedules.id');
            
        if ($id !== null) {
            return $builder->where('schedules.id', $id)->first();
        }
        
        return $builder->findAll();
    }
    
    public function getAvailableSchedules()
    {
        return $this->select('schedules.*, coach.nama as nama_pelatih, GROUP_CONCAT(jenis_les.nama_les) as jenis_les_names')
                ->join('schedule_coaches', 'schedule_coaches.schedule_id = schedules.id', 'left')
                ->join('coach', 'coach.id = schedule_coaches.coach_id', 'left')
                ->join('schedule_jenis_les', 'schedule_jenis_les.schedule_id = schedules.id', 'left')
                ->join('jenis_les', 'jenis_les.id = schedule_jenis_les.jenis_les_id', 'left')
                ->where('schedules.status', 'aktif')
                ->groupBy('schedules.id')
                ->orderBy('schedules.tanggal', 'ASC')
                ->findAll();
    }
    
    public function getRiwayatJadwalWithRelations()
    {
        return $this->select('schedules.*, 
                             GROUP_CONCAT(DISTINCT jenis_les.nama_les) as jenis_les_nama,
                             GROUP_CONCAT(DISTINCT coach.nama) as coach_names')
                    ->join('schedule_jenis_les', 'schedule_jenis_les.schedule_id = schedules.id', 'left')
                    ->join('jenis_les', 'jenis_les.id = schedule_jenis_les.jenis_les_id', 'left')
                    ->join('schedule_coaches', 'schedule_coaches.schedule_id = schedules.id', 'left')
                    ->join('coach', 'coach.id = schedule_coaches.coach_id', 'left')
                    ->where('schedules.status', 'selesai')
                    ->groupBy('schedules.id')
                    ->orderBy('schedules.tanggal', 'DESC')
                    ->findAll();
    }
}