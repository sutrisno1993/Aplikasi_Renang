<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ParentModel;
use App\Models\AnakModel;

class Parents extends BaseController
{
    protected $parentModel;
    protected $anakModel;

    public function __construct()
    {
        $this->parentModel = new ParentModel();
        $this->anakModel = new AnakModel();
    }

    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }
 
        // Get per_page limit from request or session
        $perPage = (int) ($this->request->getGet('per_page') ?? session()->get('per_page') ?? 10);
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }
        session()->set('per_page', $perPage);

        // Query untuk mengambil parent dan menggabungkan nama panggilan anak-anaknya beserta ID
        $builder = $this->parentModel
            ->select('parents.*, GROUP_CONCAT(CONCAT(anak.nama_panggilan, " (#", anak.id, ")") SEPARATOR ", ") as nama_anak')
            ->join('anak', 'anak.parent_id = parents.id', 'left')
            ->groupBy('parents.id')
            ->orderBy('parents.nama', 'ASC');
 
        $data = [
            'title' => 'Manajemen Orang Tua',
            'active' => 'parents',
            'parents' => $builder->paginate($perPage),
            'pager' => $this->parentModel->pager,
            'perPage' => $perPage
        ];
 
        return view('admin/parents/index', $data);
    }

    public function delete($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        // Cek apakah parent masih punya anak
        $anakCount = $this->anakModel->where('parent_id', $id)->countAllResults();
        if ($anakCount > 0) {
            return redirect()->to('admin/parents')->with('error', 'Tidak dapat menghapus orang tua yang masih memiliki data anak.');
        }

        $this->parentModel->delete($id);
        return redirect()->to('admin/parents')->with('success', 'Data orang tua berhasil dihapus.');
    }
}
