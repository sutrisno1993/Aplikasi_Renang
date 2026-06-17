<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table = 'settings';
    protected $primaryKey = 'id';
    protected $allowedFields = ['key', 'value', 'description'];
    protected $useTimestamps = true;

    public function getSetting($key)
    {
        return $this->where('key', $key)->first();
    }

    public function updateSetting($key, $value)
    {
        return $this->where('key', $key)->set(['value' => $value])->update();
    }
}
