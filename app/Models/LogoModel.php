<?php

namespace App\Models;

use CodeIgniter\Model;

class LogoModel extends Model
{
    protected $table = 'logos';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama', 'file_path'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
