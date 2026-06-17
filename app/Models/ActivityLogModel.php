<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table = 'activity_logs';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'user_type', 'action', 'description', 'ip_address', 'user_agent'];
    protected $useTimestamps = false;

    public function addLog($action, $description = null)
    {
        $session = session();
        return $this->insert([
            'user_id' => $session->get('id'),
            'user_type' => 'admin', // Default for now
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->getIPAddress(),
            'user_agent' => request()->getUserAgent()->getAgentString(),
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
