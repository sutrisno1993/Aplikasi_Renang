<?php

namespace App\Controllers;

use App\Models\CoachModel;

class CoachAuth extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Login Pelatih'
        ];
        return view('coach/login', $data);
    }

    public function register()
    {
        $data = [
            'title' => 'Register Pelatih'
        ];
        return view('coach/register', $data);
    }

    public function save()
    {
        // Validasi input
        $rules = [
            'nama' => 'required|min_length[3]',
            'email' => 'required|valid_email|is_unique[coach.email]',
            'telepon' => 'required|numeric',
            'alamat' => 'required',
            'keahlian' => 'required',
            'pengalaman' => 'required|numeric',
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]'
        ];

        if ($this->validate($rules)) {
            $coachModel = new CoachModel();
            
            $data = [
                'nama' => $this->request->getVar('nama'),
                'email' => $this->request->getVar('email'),
                'telepon' => $this->request->getVar('telepon'),
                'alamat' => $this->request->getVar('alamat'),
                'keahlian' => $this->request->getVar('keahlian'),
                'pengalaman' => $this->request->getVar('pengalaman'),
                'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT)
            ];
            
            $coachModel->save($data);
            
            session()->setFlashdata('success', 'Registrasi berhasil. Silakan login.');
            return redirect()->to('/coach/login');
        } else {
            $data = [
                'title' => 'Register Pelatih',
                'validation' => $this->validator
            ];
            return view('coach/register', $data);
        }
    }

    public function login()
    {
        $session = session();
        $coachModel = new CoachModel();
        
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');
        
        $coach = $coachModel->where('email', $email)->first();
        
        if ($coach) {
            $pass = $coach['password'];
            $authenticatePassword = password_verify($password, $pass);
            
            if ($authenticatePassword) {
                $ses_data = [
                    'coach_id' => $coach['id'],
                    'coach_nama' => $coach['nama'],
                    'coach_email' => $coach['email'],
                    'coach_role' => $coach['role'],
                    'coach_isLoggedIn' => TRUE
                ];
                
                $session->set($ses_data);
                return redirect()->to('/coach/dashboard');
            } else {
                $session->setFlashdata('msg', 'Password salah');
                return redirect()->to('/coach/login');
            }
        } else {
            $session->setFlashdata('msg', 'Email tidak ditemukan');
            return redirect()->to('/coach/login');
        }
    }

    public function dashboard()
    {
        if (!session()->get('coach_isLoggedIn')) {
            return redirect()->to('/coach/login');
        }
        
        $data = [
            'title' => 'Dashboard Pelatih — ' . session()->get('coach_nama')
        ];
        
        return view('coach/dashboard', $data);
    }

    public function logout()
    {
        $session = session();
        $session->remove(['coach_id', 'coach_nama', 'coach_email', 'coach_role', 'coach_isLoggedIn']);
        return redirect()->to('/coach/login');
    }
}