<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Classes extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $builder = $this->db->table('classes');
        $builder->select('classes.*, users.first_name, users.last_name');
        $builder->join('users', 'users.id = classes.created_by');
        $builder->orderBy('classes.created_at', 'DESC');
        $classes = $builder->get()->getResultArray();

        $data = [
            'title' => 'Class Management',
            'active' => 'classes',
            'classes' => $classes
        ];

        return view('admin/classes/index', $data);
    }

    public function add()
    {
        $data = [
            'title' => 'Add Class',
            'active' => 'classes'
        ];

        return view('admin/classes/add', $data);
    }

    public function create()
    {
        $rules = [
            'name' => 'required|min_length[3]',
            'description' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'created_by' => session()->get('id'),
            'is_active' => 1
        ];

        if ($this->db->table('classes')->insert($data)) {
            $this->logActivity(session()->get('id'), 'class_create', 'Created class: ' . $data['name']);
            return redirect()->to('/admin/classes')->with('message', 'Class created successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create class');
        }
    }

    public function edit($id)
    {
        $class = $this->db->table('classes')->where('id', $id)->get()->getRowArray();

        if (!$class) {
            return redirect()->to('/admin/classes')->with('error', 'Class not found');
        }

        $data = [
            'title' => 'Edit Class',
            'active' => 'classes',
            'class' => $class
        ];

        return view('admin/classes/edit', $data);
    }

    public function update($id)
    {
        $class = $this->db->table('classes')->where('id', $id)->get()->getRowArray();

        if (!$class) {
            return redirect()->to('/admin/classes')->with('error', 'Class not found');
        }

        $rules = [
            'name' => 'required|min_length[3]',
            'description' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];

        if ($this->db->table('classes')->where('id', $id)->update($data)) {
            $this->logActivity(session()->get('id'), 'class_update', 'Updated class: ' . $data['name']);
            return redirect()->to('/admin/classes')->with('message', 'Class updated successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update class');
        }
    }

    public function delete($id)
    {
        $class = $this->db->table('classes')->where('id', $id)->get()->getRowArray();

        if (!$class) {
            return redirect()->to('/admin/classes')->with('error', 'Class not found');
        }

        // Check if class has students
        $studentCount = $this->db->table('student_class')->where('class_id', $id)->countAllResults();

        if ($studentCount > 0) {
            return redirect()->to('/admin/classes')->with('error', 'Cannot delete class with enrolled students');
        }

        if ($this->db->table('classes')->where('id', $id)->delete()) {
            $this->logActivity(session()->get('id'), 'class_delete', 'Deleted class: ' . $class['name']);
            return redirect()->to('/admin/classes')->with('message', 'Class deleted successfully');
        } else {
            return redirect()->to('/admin/classes')->with('error', 'Failed to delete class');
        }
    }

    public function students($id)
    {
        $class = $this->db->table('classes')->where('id', $id)->get()->getRowArray();

        if (!$class) {
            return redirect()->to('/admin/classes')->with('error', 'Class not found');
        }

        $students = $this->userModel->getStudentsInClass($id);
        $availableStudents = $this->userModel->getStudentsNotInClass($id);

        $data = [
            'title' => 'Class Students',
            'active' => 'classes',
            'class' => $class,
            'students' => $students,
            'availableStudents' => $availableStudents
        ];

        return view('admin/classes/students', $data);
    }

    public function addStudent($classId)
    {
        $studentId = $this->request->getPost('student_id');

        if (!$studentId) {
            return redirect()->back()->with('error', 'Please select a student');
        }

        // Check if student is already in class
        $exists = $this->db->table('student_class')
            ->where('student_id', $studentId)
            ->where('class_id', $classId)
            ->countAllResults();

        if ($exists > 0) {
            return redirect()->back()->with('error', 'Student is already in this class');
        }

        $data = [
            'student_id' => $studentId,
            'class_id' => $classId
        ];

        if ($this->db->table('student_class')->insert($data)) {
            $student = $this->userModel->find($studentId);
            $class = $this->db->table('classes')->where('id', $classId)->get()->getRowArray();
            
            $this->logActivity(session()->get('id'), 'student_assign', 
                "Assigned student {$student['first_name']} {$student['last_name']} to class {$class['name']}");
            
            return redirect()->back()->with('message', 'Student added to class successfully');
        } else {
            return redirect()->back()->with('error', 'Failed to add student to class');
        }
    }

    public function removeStudent($classId, $studentId)
    {
        $deleted = $this->db->table('student_class')
            ->where('student_id', $studentId)
            ->where('class_id', $classId)
            ->delete();

        if ($deleted) {
            $student = $this->userModel->find($studentId);
            $class = $this->db->table('classes')->where('id', $classId)->get()->getRowArray();
            
            $this->logActivity(session()->get('id'), 'student_remove', 
                "Removed student {$student['first_name']} {$student['last_name']} from class {$class['name']}");
            
            return redirect()->back()->with('message', 'Student removed from class successfully');
        } else {
            return redirect()->back()->with('error', 'Failed to remove student from class');
        }
    }
}