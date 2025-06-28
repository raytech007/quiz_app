<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Users extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $data = [
            'title' => 'User Management',
            'active' => 'users',
            'users' => $this->userModel->getUsersWithRoles()
        ];

        return view('admin/users/index', $data);
    }

    public function add()
    {
        $data = [
            'title' => 'Add User',
            'active' => 'users',
            'roles' => $this->db->table('roles')->get()->getResultArray()
        ];

        return view('admin/users/add', $data);
    }

    public function create()
    {
        $rules = [
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'first_name' => 'required|min_length[2]',
            'last_name' => 'required|min_length[2]',
            'role_id' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'role_id' => $this->request->getPost('role_id'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];

        if ($this->userModel->insert($data)) {
            $this->logActivity(session()->get('id'), 'user_create', 'Created user: ' . $data['email']);
            return redirect()->to('/admin/users')->with('message', 'User created successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create user');
        }
    }

    public function edit($id)
    {
        $user = $this->userModel->getUserWithRole($id);

        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found');
        }

        $data = [
            'title' => 'Edit User',
            'active' => 'users',
            'user' => $user,
            'roles' => $this->db->table('roles')->get()->getResultArray()
        ];

        return view('admin/users/edit', $data);
    }

    public function update($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found');
        }

        $rules = [
            'email' => "required|valid_email|is_unique[users.email,id,{$id}]",
            'first_name' => 'required|min_length[2]',
            'last_name' => 'required|min_length[2]',
            'role_id' => 'required|numeric'
        ];

        // Only validate password if it's provided
        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[6]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'email' => $this->request->getPost('email'),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'role_id' => $this->request->getPost('role_id'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];

        // Only update password if provided
        if ($this->request->getPost('password')) {
            $data['password'] = $this->request->getPost('password');
        }

        if ($this->userModel->update($id, $data)) {
            $this->logActivity(session()->get('id'), 'user_update', 'Updated user: ' . $data['email']);
            return redirect()->to('/admin/users')->with('message', 'User updated successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update user');
        }
    }

    public function delete($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found');
        }

        // Prevent deletion of the current user
        if ($id == session()->get('id')) {
            return redirect()->to('/admin/users')->with('error', 'You cannot delete your own account');
        }

        if ($this->userModel->delete($id)) {
            $this->logActivity(session()->get('id'), 'user_delete', 'Deleted user: ' . $user['email']);
            return redirect()->to('/admin/users')->with('message', 'User deleted successfully');
        } else {
            return redirect()->to('/admin/users')->with('error', 'Failed to delete user');
        }
    }
}