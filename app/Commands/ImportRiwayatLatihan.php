<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportRiwayatLatihan extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:import-riwayat-latihan';
    protected $description = 'Import riwayat latihan dari file XLS/XLSX. Kolom: Tanggal (DD/MM/YYYY) dan anak_id.';

    protected $usage = 'db:import-riwayat-latihan <file> [options]';
    protected $arguments = [
        'file' => 'Path ke file .xls/.xlsx',
    ];
    protected $options = [
        '--dry-run'   => 'Validasi dan hitung tanpa insert/update database',
        '--sql-out'   => 'Generate file .sql (tanpa konek DB tujuan) untuk dijalankan di server. Contoh: --sql-out="C:\path\import.sql"',
        '--sheet'     => 'Index sheet (1-based). Default: 1',
        '--start-row' => 'Baris mulai (1-based). Default: auto (2 jika ada header)',
        '--date-col'  => 'Kolom tanggal. Default: A',
        '--anak-col'  => 'Kolom anak_id. Default: B',
        '--batch'     => 'Ukuran batch transaksi. Default: 500',
    ];

    public function run(array $params)
    {
        $file = $params[0] ?? null;
        if (!$file) {
            CLI::error('File belum diberikan. Contoh: php spark db:import-riwayat-latihan "C:\path\data.xlsx"');
            return;
        }

        $file = (string) $file;
        if (!is_file($file)) {
            CLI::error('File tidak ditemukan: ' . $file);
            return;
        }

        $dryRun = (bool) CLI::getOption('dry-run');
        $sqlOut = trim((string) (CLI::getOption('sql-out') ?? ''));
        $sheetIndex = (int) (CLI::getOption('sheet') ?: 1);
        $batchSize = (int) (CLI::getOption('batch') ?: 500);
        $dateCol = strtoupper((string) (CLI::getOption('date-col') ?: 'A'));
        $anakCol = strtoupper((string) (CLI::getOption('anak-col') ?: 'B'));

        if ($sheetIndex <= 0) {
            CLI::error('--sheet harus >= 1');
            return;
        }

        if ($batchSize <= 0) {
            CLI::error('--batch harus > 0');
            return;
        }

        try {
            $spreadsheet = IOFactory::load($file);
        } catch (\Throwable $e) {
            CLI::error('Gagal membaca file Excel: ' . $e->getMessage());
            return;
        }

        $sheet = $spreadsheet->getSheet($sheetIndex - 1);
        if (!$sheet) {
            CLI::error('Sheet tidak ditemukan. Pastikan --sheet benar.');
            return;
        }

        $highestRow = (int) $sheet->getHighestRow();
        if ($highestRow <= 0) {
            CLI::error('Tidak ada data di sheet.');
            return;
        }

        $startRowOption = CLI::getOption('start-row');
        $startRow = $startRowOption ? (int) $startRowOption : 0;
        if ($startRowOption && $startRow <= 0) {
            CLI::error('--start-row harus >= 1');
            return;
        }

        if ($startRow === 0) {
            $maybeHeader = trim((string) $sheet->getCell($dateCol . '1')->getFormattedValue());
            $startRow = (stripos($maybeHeader, 'tanggal') !== false) ? 2 : 1;
        }

        $rawRows = [];
        $uniqueAnakIds = [];
        $uniqueDates = [];

        for ($row = $startRow; $row <= $highestRow; $row++) {
            $rawDate = trim((string) $sheet->getCell($dateCol . $row)->getFormattedValue());
            $rawAnak = trim((string) $sheet->getCell($anakCol . $row)->getFormattedValue());

            if ($rawDate === '' && $rawAnak === '') {
                continue;
            }

            $anakId = (int) preg_replace('/\D+/', '', $rawAnak);
            $dateYmd = $this->parseIndoDateToYmd($rawDate);

            $rawRows[] = [
                'row' => $row,
                'date_raw' => $rawDate,
                'date_ymd' => $dateYmd,
                'anak_raw' => $rawAnak,
                'anak_id' => $anakId,
            ];

            if ($anakId > 0) {
                $uniqueAnakIds[$anakId] = true;
            }
            if ($dateYmd !== null) {
                $uniqueDates[$dateYmd] = true;
            }
        }

        if (count($rawRows) === 0) {
            CLI::write('Tidak ada baris data untuk diproses.', 'yellow');
            return;
        }

        $uniqueAnakIds = array_keys($uniqueAnakIds);
        $uniqueDates = array_keys($uniqueDates);

        $existingAnakIds = [];
        $existingSchedulesByDate = [];
        
        if ($sqlOut === '') {
            $db = \Config\Database::connect();
            
            if (count($uniqueAnakIds) > 0) {
                $existingAnakRows = $db->table('anak')->select('id')->whereIn('id', $uniqueAnakIds)->get()->getResultArray();
                foreach ($existingAnakRows as $r) {
                    $existingAnakIds[(int) $r['id']] = true;
                }
            }

            if (count($uniqueDates) > 0) {
                $scheduleRows = $db->table('schedules')
                    ->select('id, tanggal, jam_mulai, jam_selesai, jenis_latihan')
                    ->whereIn('tanggal', $uniqueDates)
                    ->orderBy('tanggal', 'ASC')
                    ->orderBy('jam_mulai', 'ASC')
                    ->get()
                    ->getResultArray();

                foreach ($scheduleRows as $s) {
                    $tanggal = (string) $s['tanggal'];
                    if (!isset($existingSchedulesByDate[$tanggal])) {
                        $existingSchedulesByDate[$tanggal] = [];
                    }
                    $existingSchedulesByDate[$tanggal][] = $s;
                }
            }
        }

        $dedup = [];
        $invalidDate = 0;
        $invalidAnak = 0;
        $duplicateXls = 0;

        foreach ($rawRows as $r) {
            if ($r['date_ymd'] === null) {
                $invalidDate++;
                continue;
            }
            if ($r['anak_id'] <= 0) {
                $invalidAnak++;
                continue;
            }
            $key = $r['date_ymd'] . '|' . $r['anak_id'];
            if (isset($dedup[$key])) {
                $duplicateXls++;
                continue;
            }
            $dedup[$key] = [
                'date_ymd' => $r['date_ymd'],
                'anak_id' => $r['anak_id'],
                'source_row' => $r['row'],
            ];
        }

        $records = array_values($dedup);
        if (count($records) === 0) {
            CLI::write('Tidak ada data valid untuk diproses.', 'yellow');
            CLI::write('invalidDate=' . $invalidDate . ', invalidAnak=' . $invalidAnak . ', duplicateXls=' . $duplicateXls, 'yellow');
            return;
        }

        if ($sqlOut !== '') {
            try {
                $this->writeSqlFile($sqlOut, $records);
            } catch (\Throwable $e) {
                CLI::error('Gagal membuat file SQL: ' . $e->getMessage());
                return;
            }

            CLI::write('File SQL dibuat: ' . $sqlOut, 'green');
            CLI::write('Ringkasan:', 'green');
            CLI::write('- totalValid: ' . count($records));
            CLI::write('- invalidDate: ' . $invalidDate);
            CLI::write('- invalidAnak: ' . $invalidAnak);
            CLI::write('- duplicateXls: ' . $duplicateXls);
            return;
        }

        $db = \Config\Database::connect();

        $scheduleIdByDate = [];
        $createdSchedules = 0;
        $pickedScheduleFromMultiple = 0;
        $skippedMissingAnak = 0;
        $skippedExistingAttendance = 0;
        $insertedAttendance = 0;
        $insertedScheduleStudents = 0;
        $updatedScheduleStudents = 0;

        $now = date('Y-m-d H:i:s');

        $total = count($records);
        CLI::write('Total baris valid (setelah dedup): ' . $total, 'green');
        if ($dryRun) {
            CLI::write('Mode: DRY RUN (tidak menulis ke database)', 'yellow');
        }

        for ($offset = 0; $offset < $total; $offset += $batchSize) {
            $batch = array_slice($records, $offset, $batchSize);

            $batchScheduleIds = [];
            $batchAnakIds = [];
            $resolved = [];

            foreach ($batch as $rec) {
                $anakId = (int) $rec['anak_id'];
                if (!isset($existingAnakIds[$anakId])) {
                    $skippedMissingAnak++;
                    continue;
                }

                $dateYmd = (string) $rec['date_ymd'];
                $scheduleId = $this->resolveScheduleId(
                    $db,
                    $dateYmd,
                    $existingSchedulesByDate,
                    $scheduleIdByDate,
                    $createdSchedules,
                    $pickedScheduleFromMultiple,
                    $dryRun,
                    $now
                );

                if ($scheduleId <= 0) {
                    continue;
                }

                $resolved[] = [
                    'schedule_id' => $scheduleId,
                    'anak_id' => $anakId,
                ];
                $batchScheduleIds[$scheduleId] = true;
                $batchAnakIds[$anakId] = true;
            }

            if (count($resolved) === 0) {
                continue;
            }

            $batchScheduleIds = array_keys($batchScheduleIds);
            $batchAnakIds = array_keys($batchAnakIds);

            $existingAttendancePairs = [];
            $attendanceRows = $db->table('latihan_attendance')
                ->select('id, schedule_id, anak_id')
                ->whereIn('schedule_id', $batchScheduleIds)
                ->whereIn('anak_id', $batchAnakIds)
                ->get()
                ->getResultArray();
            foreach ($attendanceRows as $a) {
                $existingAttendancePairs[(int) $a['schedule_id'] . '|' . (int) $a['anak_id']] = true;
            }

            $existingStudentRowsMap = [];
            $studentRows = $db->table('schedule_students')
                ->select('id, schedule_id, anak_id, status')
                ->whereIn('schedule_id', $batchScheduleIds)
                ->whereIn('anak_id', $batchAnakIds)
                ->get()
                ->getResultArray();
            foreach ($studentRows as $s) {
                $existingStudentRowsMap[(int) $s['schedule_id'] . '|' . (int) $s['anak_id']] = $s;
            }

            $attendanceInserts = [];
            $studentInserts = [];
            $studentUpdates = [];

            foreach ($resolved as $r) {
                $pairKey = (int) $r['schedule_id'] . '|' . (int) $r['anak_id'];

                if (isset($existingAttendancePairs[$pairKey])) {
                    $skippedExistingAttendance++;
                } else {
                    $attendanceInserts[] = [
                        'schedule_id' => (int) $r['schedule_id'],
                        'anak_id' => (int) $r['anak_id'],
                        'status_kehadiran' => 'hadir',
                        'catatan' => 'Import XLS',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                if (!isset($existingStudentRowsMap[$pairKey])) {
                    $studentInserts[] = [
                        'schedule_id' => (int) $r['schedule_id'],
                        'anak_id' => (int) $r['anak_id'],
                        'status' => 'hadir',
                        'catatan' => 'Import XLS',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                } else {
                    $existingStatus = (string) ($existingStudentRowsMap[$pairKey]['status'] ?? '');
                    if (strtolower($existingStatus) !== 'hadir') {
                        $studentUpdates[] = [
                            'id' => (int) $existingStudentRowsMap[$pairKey]['id'],
                        ];
                    }
                }
            }

            if ($dryRun) {
                $insertedAttendance += count($attendanceInserts);
                $insertedScheduleStudents += count($studentInserts);
                $updatedScheduleStudents += count($studentUpdates);
                continue;
            }

            $db->transStart();
            try {
                if (count($attendanceInserts) > 0) {
                    $db->table('latihan_attendance')->insertBatch($attendanceInserts);
                    $insertedAttendance += count($attendanceInserts);
                }

                if (count($studentInserts) > 0) {
                    $db->table('schedule_students')->insertBatch($studentInserts);
                    $insertedScheduleStudents += count($studentInserts);
                }

                if (count($studentUpdates) > 0) {
                    foreach ($studentUpdates as $u) {
                        $db->table('schedule_students')->where('id', (int) $u['id'])->update([
                            'status' => 'hadir',
                            'catatan' => 'Import XLS',
                            'updated_at' => $now,
                        ]);
                    }
                    $updatedScheduleStudents += count($studentUpdates);
                }

                $db->transComplete();
            } catch (\Throwable $e) {
                $db->transRollback();
                CLI::error('Gagal saat proses batch offset=' . $offset . ': ' . $e->getMessage());
                return;
            }

            if ($db->transStatus() === false) {
                CLI::error('Transaksi batch gagal (offset=' . $offset . ').');
                return;
            }
        }

        CLI::newLine();
        CLI::write('Ringkasan:', 'green');
        CLI::write('- invalidDate: ' . $invalidDate);
        CLI::write('- invalidAnak: ' . $invalidAnak);
        CLI::write('- duplicateXls: ' . $duplicateXls);
        CLI::write('- skippedMissingAnak: ' . $skippedMissingAnak);
        CLI::write('- createdSchedules: ' . $createdSchedules);
        CLI::write('- pickedScheduleFromMultiple: ' . $pickedScheduleFromMultiple);
        CLI::write('- skippedExistingAttendance: ' . $skippedExistingAttendance);
        CLI::write('- insertedAttendance (latihan_attendance): ' . $insertedAttendance);
        CLI::write('- insertedScheduleStudents (schedule_students): ' . $insertedScheduleStudents);
        CLI::write('- updatedScheduleStudents: ' . $updatedScheduleStudents);
        CLI::write('Selesai.', 'green');
    }

    private function parseIndoDateToYmd(string $raw): ?string
    {
        $raw = trim($raw);
        if ($raw === '') {
            return null;
        }

        $raw = str_replace(['-', '.', '\\'], '/', $raw);
        $parts = explode('/', $raw);
        if (count($parts) !== 3) {
            $ts = strtotime($raw);
            if ($ts === false) {
                return null;
            }
            return date('Y-m-d', $ts);
        }

        $d = (int) trim($parts[0]);
        $m = (int) trim($parts[1]);
        $y = (int) trim($parts[2]);

        if ($y < 100) {
            $y += 2000;
        }

        if (!checkdate($m, $d, $y)) {
            return null;
        }

        return sprintf('%04d-%02d-%02d', $y, $m, $d);
    }

    private function writeSqlFile(string $path, array $records): void
    {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            throw new \RuntimeException('Folder tidak ditemukan: ' . $dir);
        }

        $fh = fopen($path, 'wb');
        if ($fh === false) {
            throw new \RuntimeException('Tidak bisa menulis file: ' . $path);
        }

        $write = static function ($fh, string $s): void {
            fwrite($fh, $s);
            fwrite($fh, PHP_EOL);
        };

        $write($fh, 'START TRANSACTION;');
        $write($fh, 'CREATE TEMPORARY TABLE tmp_riwayat_import (tanggal DATE NOT NULL, anak_id INT NOT NULL, PRIMARY KEY (tanggal, anak_id)) ENGINE=InnoDB;');

        $chunkSize = 1000;
        $values = [];
        $count = 0;
        foreach ($records as $r) {
            $tanggal = (string) $r['date_ymd'];
            $anakId = (int) $r['anak_id'];
            $values[] = "('" . $tanggal . "'," . $anakId . ')';
            $count++;
            if ($count % $chunkSize === 0) {
                $write($fh, 'INSERT IGNORE INTO tmp_riwayat_import (tanggal, anak_id) VALUES ' . implode(',', $values) . ';');
                $values = [];
            }
        }
        if (count($values) > 0) {
            $write($fh, 'INSERT IGNORE INTO tmp_riwayat_import (tanggal, anak_id) VALUES ' . implode(',', $values) . ';');
        }

        $write($fh, "INSERT INTO schedules (tanggal, jam_mulai, jam_selesai, materi, kapasitas, status, created_at, updated_at, jenis_latihan, status_latihan)
SELECT d.tanggal, '06:00:00', '18:00:00', 'Latihan Rutin', 50, 'selesai', NOW(), NOW(), 'group', 'selesai'
FROM (SELECT DISTINCT tanggal FROM tmp_riwayat_import) d
WHERE NOT EXISTS (
  SELECT 1 FROM schedules s
  WHERE s.tanggal = d.tanggal
    AND s.jam_mulai = '06:00:00'
    AND s.jam_selesai = '18:00:00'
    AND s.jenis_latihan = 'group'
);");

        $write($fh, "INSERT INTO latihan_attendance (schedule_id, anak_id, status_kehadiran, catatan, created_at, updated_at)
SELECT s.id, t.anak_id, 'hadir', 'Import XLS', NOW(), NOW()
FROM tmp_riwayat_import t
JOIN anak a ON a.id = t.anak_id
JOIN (
  SELECT tanggal, MIN(id) AS id
  FROM schedules
  WHERE jam_mulai = '06:00:00'
    AND jam_selesai = '18:00:00'
    AND jenis_latihan = 'group'
  GROUP BY tanggal
) s ON s.tanggal = t.tanggal
LEFT JOIN latihan_attendance la ON la.schedule_id = s.id AND la.anak_id = t.anak_id
WHERE la.id IS NULL;");

        $write($fh, "INSERT INTO schedule_students (schedule_id, anak_id, status, catatan, created_at, updated_at)
SELECT s.id, t.anak_id, 'hadir', 'Import XLS', NOW(), NOW()
FROM tmp_riwayat_import t
JOIN anak a ON a.id = t.anak_id
JOIN (
  SELECT tanggal, MIN(id) AS id
  FROM schedules
  WHERE jam_mulai = '06:00:00'
    AND jam_selesai = '18:00:00'
    AND jenis_latihan = 'group'
  GROUP BY tanggal
) s ON s.tanggal = t.tanggal
LEFT JOIN schedule_students ss ON ss.schedule_id = s.id AND ss.anak_id = t.anak_id
WHERE ss.id IS NULL;");

        $write($fh, 'COMMIT;');

        fclose($fh);
    }

    private function resolveScheduleId(
        $db,
        string $dateYmd,
        array &$existingSchedulesByDate,
        array &$scheduleIdByDate,
        int &$createdSchedules,
        int &$pickedScheduleFromMultiple,
        bool $dryRun,
        string $now
    ): int {
        if (isset($scheduleIdByDate[$dateYmd])) {
            return (int) $scheduleIdByDate[$dateYmd];
        }

        $candidates = $existingSchedulesByDate[$dateYmd] ?? [];

        if (count($candidates) === 0) {
            if ($dryRun) {
                $createdSchedules++;
                $fakeId = 900000000 + $createdSchedules;
                $scheduleIdByDate[$dateYmd] = $fakeId;
                $existingSchedulesByDate[$dateYmd] = [[
                    'id' => $fakeId,
                    'tanggal' => $dateYmd,
                    'jam_mulai' => '06:00:00',
                    'jam_selesai' => '18:00:00',
                    'jenis_latihan' => 'group',
                ]];
                return $fakeId;
            }

            $db->table('schedules')->insert([
                'tanggal' => $dateYmd,
                'jam_mulai' => '06:00:00',
                'jam_selesai' => '18:00:00',
                'materi' => 'Latihan Rutin',
                'kapasitas' => 50,
                'status' => 'selesai',
                'jenis_latihan' => 'group',
                'status_latihan' => 'selesai',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $newId = (int) $db->insertID();
            $createdSchedules++;
            $scheduleIdByDate[$dateYmd] = $newId;
            $existingSchedulesByDate[$dateYmd] = [[
                'id' => $newId,
                'tanggal' => $dateYmd,
                'jam_mulai' => '06:00:00',
                'jam_selesai' => '18:00:00',
                'jenis_latihan' => 'group',
            ]];
            return $newId;
        }

        if (count($candidates) === 1) {
            $scheduleIdByDate[$dateYmd] = (int) $candidates[0]['id'];
            return (int) $candidates[0]['id'];
        }

        $pickedScheduleFromMultiple++;
        foreach ($candidates as $c) {
            $jm = (string) ($c['jam_mulai'] ?? '');
            $js = (string) ($c['jam_selesai'] ?? '');
            $jl = (string) ($c['jenis_latihan'] ?? '');
            if ($jm === '06:00:00' && $js === '18:00:00' && strtolower($jl) === 'group') {
                $scheduleIdByDate[$dateYmd] = (int) $c['id'];
                return (int) $c['id'];
            }
        }

        $scheduleIdByDate[$dateYmd] = (int) $candidates[0]['id'];
        return (int) $candidates[0]['id'];
    }
}
