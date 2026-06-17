<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JenisLesModel;

class JenisLes extends BaseController
{
    protected $jenisLesModel;
    
    public function __construct()
    {
        $this->jenisLesModel = new JenisLesModel();
    }
    
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }
        
        $data = [
            'title' => 'Kelola Jenis Les',
            'nama' => session()->get('nama'),
            'jenisLes' => $this->jenisLesModel->findAll()
        ];
        
        return view('admin/jenis_les', $data);
    }
    
    public function create()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }
        
        $data = [
            'title' => 'Tambah Jenis Les',
            'nama' => session()->get('nama')
        ];
        
        return view('admin/jenis_les_create', $data);
    }
    
    public function store()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }
        
        // Validasi input
        $rules = [
            'nama_les' => 'required|min_length[3]|max_length[100]',
            'harga' => 'required|numeric',
            'earn_owner' => 'required|numeric',
            'earn_coach' => 'required|numeric',
            'keterangan' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Simpan data
        $data = [
            'nama_les' => $this->request->getPost('nama_les'),
            'harga' => $this->request->getPost('harga'),
            'earn_owner' => $this->request->getPost('earn_owner'),
            'earn_coach' => $this->request->getPost('earn_coach'),
            'keterangan' => $this->request->getPost('keterangan')
        ];
        
        $this->jenisLesModel->insert($data);
        
        session()->setFlashdata('success', 'Jenis Les berhasil ditambahkan');
        return redirect()->to('admin/jenis-les');
    }
    
    public function edit($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }
        
        $jenisLes = $this->jenisLesModel->find($id);
        
        if (!$jenisLes) {
            session()->setFlashdata('error', 'Jenis Les tidak ditemukan');
            return redirect()->to('admin/jenis-les');
        }
        
        $data = [
            'title' => 'Edit Jenis Les',
            'nama' => session()->get('nama'),
            'jenisLes' => $jenisLes
        ];
        
        return view('admin/jenis_les_edit', $data);
    }
    
    public function update($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }
        
        // Validasi input
        $rules = [
            'nama_les' => 'required|min_length[3]|max_length[100]',
            'harga' => 'required|numeric',
            'earn_owner' => 'required|numeric',
            'earn_coach' => 'required|numeric',
            'keterangan' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Update data
        $data = [
            'nama_les' => $this->request->getPost('nama_les'),
            'harga' => $this->request->getPost('harga'),
            'earn_owner' => $this->request->getPost('earn_owner'),
            'earn_coach' => $this->request->getPost('earn_coach'),
            'keterangan' => $this->request->getPost('keterangan')
        ];
        
        $this->jenisLesModel->update($id, $data);
        
        session()->setFlashdata('success', 'Jenis Les berhasil diperbarui');
        return redirect()->to('admin/jenis-les');
    }
    
    public function delete($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }
        
        $jenisLes = $this->jenisLesModel->find($id);
        
        if (!$jenisLes) {
            session()->setFlashdata('error', 'Jenis Les tidak ditemukan');
            return redirect()->to('admin/jenis-les');
        }
        
        $this->jenisLesModel->delete($id);
        
        session()->setFlashdata('success', 'Jenis Les berhasil dihapus');
        return redirect()->to('admin/jenis-les');
    }
}