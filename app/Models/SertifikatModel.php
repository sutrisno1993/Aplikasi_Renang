<?php

namespace App\Models;

use CodeIgniter\Model;

class SertifikatModel extends Model
{
    protected $table      = 'sertifikat_digital';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'ujian_id',
        'anak_id',
        'level_id',
        'nomor_sertifikat'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get certificate by student and level.
     */
    public function getCertificate(int $anakId, int $levelId)
    {
        return $this->where('anak_id', $anakId)
                    ->where('level_id', $levelId)
                    ->first();
    }

    /**
     * Generate a unique certificate number.
     * Format: CERT/{YEAR}/{LEVEL}/{AUTO_INCREMENT_ID}
     */
    public function generateCertificateNumber(int $anakId, int $levelId): string
    {
        $year = date('Y');
        $random = mt_rand(1000, 9999);
        return "CERT/" . $year . "/LVL" . $levelId . "/" . $anakId . "-" . $random;
    }
}
