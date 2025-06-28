<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table      = 'categories';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'name', 'description', 'created_by'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules    = [
        'name'       => 'required|min_length[3]|max_length[100]',
        'created_by' => 'required|numeric'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Category name is required',
            'min_length' => 'Category name must be at least 3 characters long',
            'max_length' => 'Category name cannot exceed 100 characters'
        ],
        'created_by' => [
            'required' => 'Creator ID is required',
            'numeric'  => 'Invalid creator ID'
        ]
    ];

    protected $skipValidation     = false;
    protected $cleanValidationRules = true;

    /**
     * Get all categories with creator information
     */
    public function getAllCategoriesWithCreator()
    {
        $builder = $this->db->table('categories');
        $builder->select('categories.*, users.first_name, users.last_name');
        $builder->join('users', 'users.id = categories.created_by');
        $builder->orderBy('categories.created_at', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get categories created by a specific user
     */
    public function getCategoriesByCreator($userId)
    {
        return $this->where('created_by', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get category with question count
     */
    public function getCategoryWithQuestionCount($id)
    {
        $builder = $this->db->table('categories');
        $builder->select('categories.*, COUNT(questions.id) as question_count');
        $builder->join('questions', 'questions.category_id = categories.id', 'left');
        $builder->where('categories.id', $id);
        $builder->groupBy('categories.id');

        return $builder->get()->getRowArray();
    }
}