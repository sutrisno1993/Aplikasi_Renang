<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestDelete extends BaseCommand
{
    protected $group       = 'Test';
    protected $name        = 'test:delete';
    protected $description = 'Test delete anak';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        // Find anak to delete
        $anak = $db->table('anak')->orderBy('id', 'DESC')->limit(1)->get()->getRowArray();
        
        if (!$anak) {
            CLI::error("No anak found.");
            return;
        }
        
        $id = $anak['id'];
        CLI::write("Testing delete for anak ID: $id", 'yellow');
        
        $db->transStart();
        
        try {
            $db->table('pembayaran')->where('anak_id', $id)->delete();
            $db->table('schedule_students')->where('anak_id', $id)->delete();
            $db->table('latihan_attendance')->where('anak_id', $id)->delete();
            
            // Try to delete anak
            $db->table('anak')->where('id', $id)->delete();
            
            $db->transRollback(); // Always rollback in test
            
            if ($db->transStatus() === false) {
                CLI::error("Transaction failed!");
            } else {
                CLI::write("Success deleting from DB (Rolled back successfully)!", 'green');
            }
        } catch (\Exception $e) {
            CLI::error("Error: " . $e->getMessage());
        }
    }
}
