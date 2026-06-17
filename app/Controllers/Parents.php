<?php

namespace App\Controllers;

use App\Libraries\R2Client;

class Parents extends BaseController
{
    protected $anakModel;
    protected $jenisLesModel;
    protected $r2;
    
    public function __construct()
    {
        // Load model yang diperlukan
        $this->anakModel = new \App\Models\AnakModel();
        $this->jenisLesModel = new \App\Models\JenisLesModel();
        $this->r2 = new R2Client();
    }
    
    public function tambahAnakForm()
    {
        // Cek apakah parent sudah login
        if (!session()->get('parent_isLoggedIn')) {
            return redirect()->to('/parent/login');
        }
        
        // Data untuk view
        $data = [
            'title' => 'Tambah Data Anak',
            'jenis_les' => $this->jenisLesModel->findAll()
        ];
        
        return view('parent/tambahanak', $data);
    }
    
    public function tambahAnak()
    {
        // Cek apakah parent sudah login
        if (!session()->get('parent_isLoggedIn')) {
            return redirect()->to('/parent/login');
        }
        
        // Ambil parent_id dari session
        $parent_id = session()->get('parent_id');
        
        // Validasi input
        $rules = [
            'nama' => 'required|min_length[3]',
            'nama_panggilan' => 'permit_empty|min_length[2]',
            'asal_sekolah' => 'permit_empty',
            'tanggal_lahir' => 'required|valid_date',
            'jenis_kelamin' => 'required|in_list[Laki-laki,Perempuan]',
            'jenis_les_id' => 'required|numeric',
            'riwayat_penyakit' => 'permit_empty',
            'foto' => 'permit_empty|uploaded[foto]|is_image[foto]|max_size[foto,2048]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Siapkan data anak
        $dataAnak = [
            'nama' => $this->request->getPost('nama'),
            'nama_panggilan' => $this->request->getPost('nama_panggilan'),
            'asal_sekolah' => $this->request->getPost('asal_sekolah'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'jenis_les_id' => $this->request->getPost('jenis_les_id'),
            'riwayat_penyakit' => $this->request->getPost('riwayat_penyakit'),
            'parent_id' => $parent_id,
            'status' => 'non-aktif',
            'sisa_pertemuan' => 0
        ];
        
        // Handle foto upload
        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $key = 'anak/' . $foto->getRandomName();
            $uploadedPath = $this->r2->upload($key, fopen($foto->getTempName(), 'r'), $foto->getMimeType());
            if ($uploadedPath) {
                $dataAnak['foto'] = $key;
            }
        }
        
        // Simpan data anak
        try {
            $this->anakModel->insert($dataAnak);
            $anak_id = $this->anakModel->insertID();
            
            session()->setFlashdata('success', 'Data anak berhasil ditambahkan');
            return redirect()->to('/parent/dashboard');
        } catch (\Exception $e) {
            log_message('error', 'Error saat menyimpan data anak: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data anak: ' . $e->getMessage());
        }
    }
    
    public function bayar()
    {
        $pembayaranModel = new \App\Models\PembayaranModel();
        $anakModel = new \App\Models\AnakModel();
        
        $anak_id = $this->request->getPost('anak_id');
        $parent_id = session()->get('parent_id');
        
        // Debug: Cek session
        log_message('debug', 'All session data: ' . print_r(session()->get(), true));
        log_message('debug', 'Parent ID from session in bayar: ' . $parent_id);
        
        if (!$parent_id) {
            log_message('error', 'Parent ID is NULL - User might not be properly logged in');
            return redirect()->to('/auth/parent')->with('error', 'Silakan login kembali');
        }
        
        // Ambil data anak untuk memastikan parent_id
        $anak = $anakModel->find($anak_id);
        
        $data = [
            'anak_id' => $anak_id,
            'parent_id' => $parent_id,
            'tanggal' => date('Y-m-d H:i:s'),
            'jumlah_pertemuan' => $this->request->getPost('jumlah_pertemuan'),
            'total' => $this->request->getPost('total'),
            'metode_pembayaran' => $this->request->getPost('metode_pembayaran'), // Ambil dari form
            'status' => 'pending'
        ];
        
        // Debug: Cek data sebelum insert
        log_message('debug', 'Payment data before insert in bayar: ' . print_r($data, true));
        
        $pembayaranModel->insert($data);
        
        return redirect()->back()->with('success', 'Pembayaran berhasil dibuat');
    }
    
    public function editAnak($id = null)
    {
        // Cek apakah parent sudah login
        if (!session()->get('parent_isLoggedIn')) {
            return redirect()->to('/parent/login');
        }
        
        // Jika tidak ada ID, redirect ke dashboard
        if (!$id) {
            return redirect()->to('/parent/dashboard')->with('error', 'ID anak tidak ditemukan');
        }
        
        // Ambil parent_id dari session
        $parent_id = session()->get('parent_id');
        
        // Ambil data anak
        $anak = $this->anakModel->find($id);
        
        // Cek apakah anak ditemukan dan milik parent yang sedang login
        if (!$anak || $anak['parent_id'] != $parent_id) {
            return redirect()->to('/parent/dashboard')->with('error', 'Data anak tidak ditemukan');
        }
        
        // Data untuk view
        $data = [
            'title' => 'Edit Data Anak',
            'anak' => $anak,
            'jenis_les' => $this->jenisLesModel->findAll()
        ];
        
        return view('parent/edit_anak', $data);
    }
    
    public function updateAnak($id = null)
    {
        // Cek apakah parent sudah login
        if (!session()->get('parent_isLoggedIn')) {
            return redirect()->to('/parent/login');
        }
        
        // Jika tidak ada ID dari parameter URL, coba ambil dari form
        if (!$id) {
            $id = $this->request->getPost('id');
        }
        
        // Jika masih tidak ada ID, redirect ke dashboard
        if (!$id) {
            return redirect()->to('/parent/dashboard')->with('error', 'ID anak tidak ditemukan');
        }
        
        // Ambil parent_id dari session
        $parent_id = session()->get('parent_id');
        
        // Ambil data anak
        $anak = $this->anakModel->find($id);
        
        // Cek apakah anak ditemukan dan milik parent yang sedang login
        if (!$anak || $anak['parent_id'] != $parent_id) {
            return redirect()->to('/parent/dashboard')->with('error', 'Data anak tidak ditemukan');
        }
        
        // Validasi input
        $rules = [
            'nama' => 'required|min_length[3]',
            'nama_panggilan' => 'permit_empty|min_length[2]',
            'asal_sekolah' => 'permit_empty',
            'tanggal_lahir' => 'required|valid_date',
            'jenis_kelamin' => 'required|in_list[Laki-laki,Perempuan]',
            'jenis_les_id' => 'required|numeric',
            'riwayat_penyakit' => 'permit_empty',
            'foto' => 'permit_empty|is_image[foto]|max_size[foto,2048]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $newJenisLesId = (int) $this->request->getPost('jenis_les_id');
        $currentJenisLesId = (int) $anak['jenis_les_id'];

        if ($newJenisLesId !== $currentJenisLesId) {
            $transferCheck = $this->anakModel->validateTransferJenisLes((int) $id, $newJenisLesId, $currentJenisLesId);
            if (!$transferCheck['allowed']) {
                return redirect()->back()->withInput()->with('error', $transferCheck['message']);
            }
        }

        $dataAnak = [
            'nama' => $this->request->getPost('nama'),
            'nama_panggilan' => $this->request->getPost('nama_panggilan'),
            'asal_sekolah' => $this->request->getPost('asal_sekolah'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'jenis_les_id' => $newJenisLesId,
            'riwayat_penyakit' => $this->request->getPost('riwayat_penyakit'),
        ];
        
        // Handle foto upload
        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            // Delete old file if exists
            if (!empty($anak['foto'])) {
                $this->r2->delete($anak['foto']);
            }
            
            // Upload to Cloudflare R2
             $key = 'anak/' . $foto->getRandomName();
             $uploadedPath = $this->r2->upload($key, fopen($foto->getTempName(), 'r'), $foto->getMimeType());
             if ($uploadedPath) {
                 $dataAnak['foto'] = $key;
             }
        }
        
        // Update data anak
        try {
            if (!$this->anakModel->update($id, $dataAnak)) {
                throw new \Exception('Gagal memperbarui data anak: ' . implode(', ', $this->anakModel->errors()));
            }
            
            // Set flash message dan redirect
            session()->setFlashdata('success', 'Data anak berhasil diperbarui');
            return redirect()->to('/parent/dashboard');
        } catch (\Exception $e) {
            // Log error
            log_message('error', 'Error saat memperbarui data anak: ' . $e->getMessage());
            
            // Redirect dengan pesan error
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data anak: ' . $e->getMessage());
        }
    }
}
