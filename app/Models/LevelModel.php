<?php

namespace App\Models;

use CodeIgniter\Model;

class LevelModel extends Model
{
    protected $table      = 'swimming_levels';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['nama_level', 'deskripsi', 'urutan'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getAllLevelsOrdered()
    {
        return $this->orderBy('urutan', 'ASC')->findAll();
    }
}
