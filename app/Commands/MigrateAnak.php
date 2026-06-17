<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\R2Client;

class MigrateAnak extends BaseCommand
{
    protected $group       = 'R2';
    protected $name        = 'r2:migrate-anak';
    protected $description = 'Upload local anak photos to Cloudflare R2.';

    public function run(array $params)
    {
        $dir = 'C:\Users\sutrisnopc\Downloads\Terbaru\uploads\uploads\anak';
        
        if (!is_dir($dir)) {
            CLI::error("Directory not found: $dir");
            return;
        }

        $files = scandir($dir);
        $r2 = new R2Client();

        $count = 0;
        $failed = 0;

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $filePath = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_file($filePath)) {
                $key = 'anak/' . $file;
                
                // Get mime type
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $filePath);
                finfo_close($finfo);

                CLI::write("Uploading $file...");
                
                try {
                    $uploaded = $r2->upload($key, fopen($filePath, 'r'), $mime);
                    
                    if ($uploaded) {
                        CLI::write("Success: $key", 'green');
                        $count++;
                    } else {
                        CLI::error("Failed to get uploaded path for: $key");
                        $failed++;
                    }
                } catch (\Exception $e) {
                    CLI::error("Error uploading $key: " . $e->getMessage());
                    $failed++;
                }
            }
        }

        CLI::write("Done! Uploaded $count files. Failed: $failed", 'green');
    }
}
