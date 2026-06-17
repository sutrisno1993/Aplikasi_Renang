<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'admin';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama', 'email', 'password', 'role'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    // Get user by email
    public function getUserByEmail($email)
    {
        return $this->where('email', $email)->first();
    }
}