<?php

namespace App\Models;

use CodeIgniter\Model;

class UjianKenaikanModel extends Model
{
    protected $table      = 'ujian_kenaikan';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'anak_id',
        'level_asal_id',
        'level_tujuan_id',
        'examiner_id',
        'tanggal',
        'status_kelulusan',
        'teknik_kaki',
        'teknik_tangan',
        'teknik_pernapasan',
        'keberanian',
        'disiplin',
        'sikap_fokus',
        'tournament_name',
        'prestasi',
        'catatan_evaluasi'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get exam history for a student.
     */
    public function getExamsByAnakId(int $anakId)
    {
        return $this->select('ujian_kenaikan.*, lvl_asal.nama_level as level_asal_nama, lvl_tujuan.nama_level as level_tujuan_nama, coach.nama as nama_examiner')
                    ->join('swimming_levels lvl_asal', 'lvl_asal.id = ujian_kenaikan.level_asal_id', 'left')
                    ->join('swimming_levels lvl_tujuan', 'lvl_tujuan.id = ujian_kenaikan.level_tujuan_id', 'left')
                    ->join('coach', 'coach.id = ujian_kenaikan.examiner_id', 'left')
                    ->where('ujian_kenaikan.anak_id', $anakId)
                    ->orderBy('ujian_kenaikan.tanggal', 'DESC')
                    ->findAll();
    }
}
