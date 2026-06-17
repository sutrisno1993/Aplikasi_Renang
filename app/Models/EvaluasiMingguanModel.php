<?php

namespace App\Models;

use CodeIgniter\Model;

class EvaluasiMingguanModel extends Model
{
    protected $table      = 'evaluasi_mingguan';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'anak_id',
        'coach_id',
        'level_id',
        'tanggal',
        'teknik_kaki',
        'teknik_tangan',
        'teknik_pernapasan',
        'keberanian',
        'disiplin',
        'sikap_fokus',
        'catatan_coach'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get evaluations for a specific student with level and coach details.
     */
    public function getEvaluationsByAnakId(int $anakId)
    {
        return $this->select('evaluasi_mingguan.*, swimming_levels.nama_level, coach.nama as nama_coach')
                    ->join('swimming_levels', 'swimming_levels.id = evaluasi_mingguan.level_id', 'left')
                    ->join('coach', 'coach.id = evaluasi_mingguan.coach_id', 'left')
                    ->where('evaluasi_mingguan.anak_id', $anakId)
                    ->orderBy('evaluasi_mingguan.tanggal', 'DESC')
                    ->findAll();
    }
}
