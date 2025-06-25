<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        // Check if already logged in
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    public function attemptLogin()
    {
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $userModel->where('email', $email)->first();

        if ($user && password_verify($password, $user['password'])) {
            if (!$user['is_active']) {
                return redirect()->back()->withInput()->with('error', 'Your account is deactivated. Please contact the administrator.');
            }

            // Get the role name
            $db = \Config\Database::connect();
            $role = $db->table('roles')->where('id', $user['role_id'])->get()->getRow();

            // Set session data
            $sessionData = [
                'id' => $user['id'],
                'email' => $user['email'],
                'firstName' => $user['first_name'],
                'lastName' => $user['last_name'],
                'role' => $role->name,
                'isLoggedIn' => true
            ];

            session()->set($sessionData);

            // Update last login timestamp
            $userModel->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);

            // Log the login activity
            $this->logActivity($user['id'], 'login', 'User logged in');

            // Redirect based on role
            if ($role->name === 'admin') {
                return redirect()->to('/admin');
            } elseif ($role->name === 'teacher') {
                return redirect()->to('/teacher');
            } else {
                return redirect()->to('/student');
            }
        }

        return redirect()->back()->withInput()->with('error', 'Invalid login credentials');
    }

    public function logout()
    {
        // Log the logout activity if user was logged in
        if (session()->get('isLoggedIn')) {
            $this->logActivity(session()->get('id'), 'logout', 'User logged out');
        }

        // Destroy the session
        session()->destroy();

        return redirect()->to('/login')->with('message', 'You have been logged out successfully');
    }

    private function logActivity($userId, $activityType, $description)
    {
        $db = \Config\Database::connect();

        $data = [
            'user_id' => $userId,
            'activity_type' => $activityType,
            'description' => $description,
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString()
        ];

        $db->table('activity_logs')->insert($data);
    }
}
