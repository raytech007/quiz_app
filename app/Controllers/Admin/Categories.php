<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;

class Categories extends BaseController
{
    protected $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Category Management',
            'active' => 'categories',
            'categories' => $this->categoryModel->getAllCategoriesWithCreator()
        ];

        return view('admin/categories/index', $data);
    }

    public function add()
    {
        $data = [
            'title' => 'Add Category',
            'active' => 'categories'
        ];

        return view('admin/categories/add', $data);
    }

    public function create()
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'description' => 'permit_empty|max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'created_by' => session()->get('id')
        ];

        if ($this->categoryModel->insert($data)) {
            $this->logActivity(session()->get('id'), 'category_create', 'Created category: ' . $data['name']);
            return redirect()->to('/admin/categories')->with('message', 'Category created successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create category');
        }
    }

    public function edit($id)
    {
        $category = $this->categoryModel->find($id);

        if (!$category) {
            return redirect()->to('/admin/categories')->with('error', 'Category not found');
        }

        $data = [
            'title' => 'Edit Category',
            'active' => 'categories',
            'category' => $category
        ];

        return view('admin/categories/edit', $data);
    }

    public function update($id)
    {
        $category = $this->categoryModel->find($id);

        if (!$category) {
            return redirect()->to('/admin/categories')->with('error', 'Category not found');
        }

        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'description' => 'permit_empty|max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description')
        ];

        if ($this->categoryModel->update($id, $data)) {
            $this->logActivity(session()->get('id'), 'category_update', 'Updated category: ' . $data['name']);
            return redirect()->to('/admin/categories')->with('message', 'Category updated successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update category');
        }
    }

    public function delete($id)
    {
        $category = $this->categoryModel->find($id);

        if (!$category) {
            return redirect()->to('/admin/categories')->with('error', 'Category not found');
        }

        // Check if category is used by any questions
        $questionsCount = $this->db->table('questions')->where('category_id', $id)->countAllResults();

        if ($questionsCount > 0) {
            return redirect()->to('/admin/categories')->with('error', 'Cannot delete category as it is used by questions');
        }

        if ($this->categoryModel->delete($id)) {
            $this->logActivity(session()->get('id'), 'category_delete', 'Deleted category: ' . $category['name']);
            return redirect()->to('/admin/categories')->with('message', 'Category deleted successfully');
        } else {
            return redirect()->to('/admin/categories')->with('error', 'Failed to delete category');
        }
    }
}