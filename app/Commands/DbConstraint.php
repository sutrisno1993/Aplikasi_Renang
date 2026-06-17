<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class DbConstraint extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:constraint';
    protected $description = 'Add unique constraints to prevent duplicate entries in schedules.';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        CLI::write('Checking for duplicates in schedule_students...', 'yellow');
        $dupes = $db->query("SELECT schedule_id, anak_id, COUNT(*) as total FROM schedule_students GROUP BY schedule_id, anak_id HAVING COUNT(*) > 1")->getResultArray();
        
        if (!empty($dupes)) {
            CLI::error('Duplicates found in schedule_students. Please clean them up first.');
            foreach ($dupes as $d) {
                CLI::write("Schedule ID: {$d['schedule_id']}, Anak ID: {$d['anak_id']}, Total: {$d['total']}");
            }
            return;
        }

        CLI::write('Checking for duplicates in latihan_attendance...', 'yellow');
        $dupesAttr = $db->query("SELECT schedule_id, anak_id, COUNT(*) as total FROM latihan_attendance GROUP BY schedule_id, anak_id HAVING COUNT(*) > 1")->getResultArray();
        
        if (!empty($dupesAttr)) {
            CLI::error('Duplicates found in latihan_attendance. Please clean them up first.');
            foreach ($dupesAttr as $d) {
                CLI::write("Schedule ID: {$d['schedule_id']}, Anak ID: {$d['anak_id']}, Total: {$d['total']}");
            }
            return;
        }

        CLI::write('Adding unique constraints...', 'blue');

        try {
            $db->query("ALTER TABLE schedule_students ADD UNIQUE INDEX unique_schedule_anak (schedule_id, anak_id)");
            CLI::write('Index 1 (schedule_students) added successfully.', 'green');
        } catch (\Exception $e) {
            CLI::error('Failed to add index 1: ' . $e->getMessage());
        }

        try {
            $db->query("ALTER TABLE latihan_attendance ADD UNIQUE INDEX unique_schedule_anak_attendance (schedule_id, anak_id)");
            CLI::write('Index 2 (latihan_attendance) added successfully.', 'green');
        } catch (\Exception $e) {
            CLI::error('Failed to add index 2: ' . $e->getMessage());
        }

        CLI::write('Database constraints updated.', 'green');
    }
}
