<?php

namespace App\Models;

use CodeIgniter\Model;

class JadwalPesertaModel extends Model
{
    protected $table = 'jadwal_peserta';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'jadwal_id',
        'anak_id',
        'status_kehadiran',
        'created_at',
        'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    // Method untuk mendapatkan jadwal peserta dengan detail
    public function getJadwalPesertaWithDetail($id = null)
    {
        $builder = $this->select('jadwal_peserta.*, anak.nama as nama_anak, jadwal.tanggal, jadwal.jam_mulai, jadwal.jam_selesai')
            ->join('anak', 'anak.id = jadwal_peserta.anak_id')
            ->join('jadwal', 'jadwal.id = jadwal_peserta.jadwal_id');
            
        if ($id !== null) {
            return $builder->where('jadwal_peserta.id', $id)->first();
        }
        
        return $builder->findAll();
    }
    
    // Method untuk cek apakah peserta sudah terdaftar di jadwal tertentu
    public function isAnakRegistered($jadwal_id, $anak_id)
    {
        return $this->where([
            'jadwal_id' => $jadwal_id,
            'anak_id' => $anak_id
        ])->countAllResults() > 0;
    }
}