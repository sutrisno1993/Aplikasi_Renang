<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LogoModel;
use App\Libraries\R2Client;

class LogoController extends BaseController
{
    protected $logoModel;
    protected $r2Client;

    public function __construct()
    {
        $this->logoModel = new LogoModel();
        $this->r2Client = new R2Client();
    }

    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        $data = [
            'title' => 'Kelola Logo',
            'active' => 'logo',
            'nama' => session()->get('nama'),
            'logos' => $this->logoModel->orderBy('created_at', 'DESC')->findAll()
        ];

        return view('admin/logo/index', $data);
    }

    public function store()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        $file = $this->request->getFile('logo_file');
        $nama = $this->request->getPost('nama');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $ext = $file->getClientExtension();
            $newName = 'logo_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $r2Path = 'logo/' . $newName;

            $uploadedUrl = $this->r2Client->upload(
                $r2Path,
                file_get_contents($file->getTempName()),
                $file->getMimeType()
            );

            if ($uploadedUrl) {
                $this->logoModel->save([
                    'nama' => $nama,
                    'file_path' => $r2Path
                ]);
                session()->setFlashdata('success', 'Logo berhasil ditambahkan.');
            } else {
                session()->setFlashdata('error', 'Gagal mengunggah logo ke server cloud.');
            }
        } else {
            session()->setFlashdata('error', 'File tidak valid atau belum dipilih.');
        }

        return redirect()->to('admin/logo');
    }

    public function update($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        $logo = $this->logoModel->find($id);
        if (!$logo) {
            return redirect()->to('admin/logo')->with('error', 'Logo tidak ditemukan.');
        }

        $nama = $this->request->getPost('nama');
        $file = $this->request->getFile('logo_file');

        $updateData = [
            'id' => $id,
            'nama' => $nama
        ];

        // Jika ada file baru diunggah
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $ext = $file->getClientExtension();
            $newName = 'logo_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $r2Path = 'logo/' . $newName;

            $uploadedUrl = $this->r2Client->upload(
                $r2Path,
                file_get_contents($file->getTempName()),
                $file->getMimeType()
            );

            if ($uploadedUrl) {
                // Hapus file lama dari R2
                if (!empty($logo['file_path'])) {
                    $this->r2Client->delete($logo['file_path']);
                }
                
                $updateData['file_path'] = $r2Path;
            } else {
                return redirect()->to('admin/logo')->with('error', 'Gagal mengunggah gambar baru ke server cloud.');
            }
        }

        $this->logoModel->save($updateData);
        return redirect()->to('admin/logo')->with('success', 'Logo berhasil diperbarui.');
    }

    public function delete($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        $logo = $this->logoModel->find($id);
        if ($logo) {
            if (!empty($logo['file_path'])) {
                $this->r2Client->delete($logo['file_path']);
            }
            $this->logoModel->delete($id);
            session()->setFlashdata('success', 'Logo berhasil dihapus.');
        } else {
            session()->setFlashdata('error', 'Logo tidak ditemukan.');
        }

        return redirect()->to('admin/logo');
    }
}
