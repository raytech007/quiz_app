<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'email', 'password', 'first_name', 'last_name', 'role_id',
        'is_active', 'last_login'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules    = [
        'email'     => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password'  => 'required|min_length[6]',
        'first_name' => 'required|min_length[2]',
        'last_name' => 'required|min_length[2]',
        'role_id'   => 'required|numeric',
    ];

    protected $validationMessages = [
        'email' => [
            'required' => 'Email is required',
            'valid_email' => 'Please enter a valid email address',
            'is_unique' => 'Email already exists in the system',
        ],
        'password' => [
            'required' => 'Password is required',
            'min_length' => 'Password must be at least 6 characters long',
        ],
        'first_name' => [
            'required' => 'First name is required',
            'min_length' => 'First name must be at least 2 characters long',
        ],
        'last_name' => [
            'required' => 'Last name is required',
            'min_length' => 'Last name must be at least 2 characters long',
        ],
        'role_id' => [
            'required' => 'Role is required',
            'numeric' => 'Invalid role selected',
        ],
    ];

    protected $skipValidation     = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['hashPassword'];
    protected $beforeUpdate   = ['hashPassword'];

    /**
     * Hash the password before storing in the database
     */
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }

        return $data;
    }

    /**
     * Get user with role information
     */
    public function getUserWithRole($id)
    {
        $builder = $this->db->table('users');
        $builder->select('users.*, roles.name as role_name');
        $builder->join('roles', 'roles.id = users.role_id');
        $builder->where('users.id', $id);

        return $builder->get()->getRowArray();
    }

    /**
     * Get all users with role information
     */
    public function getUsersWithRoles()
    {
        $builder = $this->db->table('users');
        $builder->select('users.*, roles.name as role_name');
        $builder->join('roles', 'roles.id = users.role_id');

        return $builder->get()->getResultArray();
    }

    /**
     * Get all users with a specific role
     */
    public function getUsersByRole($roleName)
    {
        $builder = $this->db->table('users');
        $builder->select('users.*');
        $builder->join('roles', 'roles.id = users.role_id');
        $builder->where('roles.name', $roleName);

        return $builder->get()->getResultArray();
    }

    /**
     * Get students in a specific class
     */
    public function getStudentsInClass($classId)
    {
        $builder = $this->db->table('users');
        $builder->select('users.*');
        $builder->join('student_class', 'users.id = student_class.student_id');
        $builder->join('roles', 'roles.id = users.role_id');
        $builder->where('student_class.class_id', $classId);
        $builder->where('roles.name', 'student');

        return $builder->get()->getResultArray();
    }

    /**
     * Get students not in a specific class
     */
    public function getStudentsNotInClass($classId)
    {
        $subquery = $this->db->table('student_class')
            ->select('student_id')
            ->where('class_id', $classId)
            ->getCompiledSelect();

        $builder = $this->db->table('users');
        $builder->select('users.*');
        $builder->join('roles', 'roles.id = users.role_id');
        $builder->where('roles.name', 'student');
        $builder->where("users.id NOT IN ($subquery)", null, false);

        return $builder->get()->getResultArray();
    }
}
