<?php

namespace App\Models;

use CodeIgniter\Model;

class CoachModel extends Model
{
    protected $table = 'coach';
    protected $allowedFields = ['id', 'nama', 'email', 'telepon', 'alamat', 'keahlian', 'pengalaman', 'password', 'role', 'assigned_levels'];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getAllCoach(int $limit = 0, int $offset = 0)
    {
        return $this->select('id, nama, keahlian, pengalaman')
                    ->orderBy('nama', 'ASC')
                    ->get()
                    ->getResultArray();
    }
}