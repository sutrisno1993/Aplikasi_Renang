<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function index()
    {
        // Check if already logged in
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/admin/dashboard');
        }
        
        return view('admin/login');
    }
    
    public function login()
    {
        $email = $this->request->getPost('email'); // Ubah dari username ke email
        $password = $this->request->getPost('password');
        
        // Validate login credentials
        $userModel = new UserModel();
        $user = $userModel->getUserByEmail($email); // Gunakan method baru
        
        if ($user && password_verify($password, $user['password'])) {
            // Set session data. Admin dan boss boleh login bersamaan.

            $sessionData = [
                'id' => $user['id'],
                'nama' => $user['nama'],
                'email' => $user['email'], 
                'role' => $user['role'] ?? 'admin',
                'isLoggedIn' => true
            ];
            
            session()->set($sessionData);
            return redirect()->to('/admin/dashboard');
        } else {
            session()->setFlashdata('error', 'Email atau password salah');
            return redirect()->to('/auth')->withInput();
        }
    }
    
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth');
    }
}