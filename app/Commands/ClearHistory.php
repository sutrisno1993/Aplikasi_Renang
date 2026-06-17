<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ClearHistory extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:clear-history';
    protected $description = 'Clear riwayat latihan and pembayaran';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        try {
            CLI::write("Clearing latihan_attendance...", "yellow");
            $db->table('latihan_attendance')->emptyTable();
            
            CLI::write("Clearing pembayaran...", "yellow");
            $db->table('pembayaran')->emptyTable();
            
            CLI::write("Data riwayat latihan dan pembayaran berhasil dikosongkan!", "green");
        } catch (\Exception $e) {
            CLI::error("Error: " . $e->getMessage());
            
            // If it failed because database 'maretspo_db_renang' doesn't exist yet locally,
            // let's try with the old name for local testing
            CLI::write("Trying with maresport_db if it exists...", "yellow");
            try {
                $customDb = \Config\Database::connect([
                    'DBDriver' => 'MySQLi',
                    'hostname' => 'localhost',
                    'username' => 'root',
                    'password' => '',
                    'database' => 'maresport_db'
                ]);
                $customDb->table('latihan_attendance')->emptyTable();
                $customDb->table('pembayaran')->emptyTable();
                CLI::write("Data riwayat berhasil dikosongkan dari maresport_db lokal!", "green");
            } catch (\Exception $ex) {
                CLI::error("Kedua percobaan gagal: " . $ex->getMessage());
            }
        }
    }
}
