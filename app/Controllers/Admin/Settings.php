<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingModel;
use App\Models\ActivityLogModel;

class Settings extends BaseController
{
    protected $settingModel;
    protected $logModel;

    public function __construct()
    {
        $this->settingModel = new SettingModel();
        $this->logModel = new ActivityLogModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Pengaturan Sistem',
            'active' => 'settings',
            'settings' => $this->settingModel->findAll(),
            'logs' => $this->logModel->select('activity_logs.*, admin.nama as admin_nama')
                ->join('admin', 'admin.id = activity_logs.user_id', 'left')
                ->orderBy('created_at', 'DESC')
                ->limit(100)
                ->get()->getResultArray()
        ];

        return view('admin/settings/index', $data);
    }

    public function update()
    {
        $settings = $this->request->getPost('settings');
        
        if ($settings) {
            foreach ($settings as $key => $value) {
                $this->settingModel->updateSetting($key, $value);
            }
            
            $this->logModel->addLog('Update Settings', 'Memperbarui konfigurasi sistem.');
            return redirect()->to('admin/settings')->with('success', 'Pengaturan berhasil diperbarui.');
        }

        return redirect()->back()->with('error', 'Gagal memperbarui pengaturan.');
    }

    /**
     * Sinkronisasi Ulang Semua Sisa Pertemuan Siswa
     * Berguna jika ada perubahan rumus atau data yang tidak sinkron
     */
    public function syncAll()
    {
        $anakModel = new \App\Models\AnakModel();
        $semuaAnak = $anakModel->findAll();
        
        $count = 0;
        foreach ($semuaAnak as $a) {
            $anakModel->recalculateSisaPertemuan($a['id']);
            $count++;
        }

        $this->logModel->addLog('Sync All', "Melakukan sinkronisasi ulang sisa pertemuan untuk {$count} siswa.");
        
        return redirect()->to('admin/settings')->with('success', "Berhasil mensinkronisasi {$count} data siswa.");
    }
}
