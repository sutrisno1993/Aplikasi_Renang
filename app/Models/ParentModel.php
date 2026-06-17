<?php

namespace App\Models;

use CodeIgniter\Model;

class ParentModel extends Model
{
    protected $table = 'parents';  // Ubah dari 'parent' menjadi 'parents'
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama', 'alamat', 'whatsapp', 'password'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    // Mendapatkan semua parent
    public function getAllParents()
    {
        return $this->findAll();
    }
    
    // Mendapatkan parent berdasarkan ID
    public function getParentById($id)
    {
        return $this->find($id);
    }
    
    // Mendapatkan parent berdasarkan whatsapp
    public function getParentByWhatsapp($whatsapp)
    {
        return $this->where('whatsapp', $whatsapp)->first();
    }
}