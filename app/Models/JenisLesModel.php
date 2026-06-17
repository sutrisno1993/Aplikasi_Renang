<?php

namespace App\Models;

use CodeIgniter\Model;

class JenisLesModel extends Model
{
    protected $table      = 'jenis_les';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = ['nama_les', 'harga', 'earn_owner', 'earn_coach', 'keterangan', 'created_at', 'updated_at', 'deleted_at'];
    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}