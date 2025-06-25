<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        // Redirect based on user role
        $role = session()->get('role');

        if ($role === 'admin') {
            return redirect()->to('/admin');
        } elseif ($role === 'teacher') {
            return redirect()->to('/teacher');
        } else {
            return redirect()->to('/student');
        }
    }
}
