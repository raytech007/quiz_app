<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\QuizModel;

class Assignments extends BaseController
{
    protected $quizModel;

    public function __construct()
    {
        $this->quizModel = new QuizModel();
    }

    public function index()
    {
        $builder = $this->db->table('quiz_assignments');
        $builder->select('quiz_assignments.*, quizzes.title as quiz_title, classes.name as class_name, users.first_name, users.last_name');
        $builder->join('quizzes', 'quizzes.id = quiz_assignments.quiz_id');
        $builder->join('classes', 'classes.id = quiz_assignments.class_id');
        $builder->join('users', 'users.id = quiz_assignments.created_by');
        $builder->orderBy('quiz_assignments.start_time', 'DESC');
        $assignments = $builder->get()->getResultArray();

        $data = [
            'title' => 'Quiz Assignments',
            'active' => 'assignments',
            'assignments' => $assignments
        ];

        return view('admin/assignments/index', $data);
    }

    public function add()
    {
        $quizzes = $this->db->table('quizzes')
            ->select('quizzes.*, users.first_name, users.last_name')
            ->join('users', 'users.id = quizzes.created_by')
            ->where('quizzes.is_active', 1)
            ->get()->getResultArray();

        $classes = $this->db->table('classes')
            ->where('is_active', 1)
            ->get()->getResultArray();

        $data = [
            'title' => 'Add Assignment',
            'active' => 'assignments',
            'quizzes' => $quizzes,
            'classes' => $classes
        ];

        return view('admin/assignments/add', $data);
    }

    public function create()
    {
        $rules = [
            'quiz_id' => 'required|numeric',
            'class_id' => 'required|numeric',
            'start_time' => 'required',
            'end_time' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $startTime = $this->request->getPost('start_time');
        $endTime = $this->request->getPost('end_time');

        // Validate that end time is after start time
        if (strtotime($endTime) <= strtotime($startTime)) {
            return redirect()->back()->withInput()->with('error', 'End time must be after start time');
        }

        $data = [
            'quiz_id' => $this->request->getPost('quiz_id'),
            'class_id' => $this->request->getPost('class_id'),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'created_by' => session()->get('id')
        ];

        if ($this->db->table('quiz_assignments')->insert($data)) {
            $quiz = $this->db->table('quizzes')->where('id', $data['quiz_id'])->get()->getRowArray();
            $class = $this->db->table('classes')->where('id', $data['class_id'])->get()->getRowArray();
            
            $this->logActivity(session()->get('id'), 'assignment_create', 
                "Assigned quiz '{$quiz['title']}' to class '{$class['name']}'");
            
            return redirect()->to('/admin/assignments')->with('message', 'Assignment created successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create assignment');
        }
    }

    public function edit($id)
    {
        $assignment = $this->db->table('quiz_assignments')->where('id', $id)->get()->getRowArray();

        if (!$assignment) {
            return redirect()->to('/admin/assignments')->with('error', 'Assignment not found');
        }

        $quizzes = $this->db->table('quizzes')
            ->select('quizzes.*, users.first_name, users.last_name')
            ->join('users', 'users.id = quizzes.created_by')
            ->where('quizzes.is_active', 1)
            ->get()->getResultArray();

        $classes = $this->db->table('classes')
            ->where('is_active', 1)
            ->get()->getResultArray();

        $data = [
            'title' => 'Edit Assignment',
            'active' => 'assignments',
            'assignment' => $assignment,
            'quizzes' => $quizzes,
            'classes' => $classes
        ];

        return view('admin/assignments/edit', $data);
    }

    public function update($id)
    {
        $assignment = $this->db->table('quiz_assignments')->where('id', $id)->get()->getRowArray();

        if (!$assignment) {
            return redirect()->to('/admin/assignments')->with('error', 'Assignment not found');
        }

        $rules = [
            'quiz_id' => 'required|numeric',
            'class_id' => 'required|numeric',
            'start_time' => 'required',
            'end_time' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $startTime = $this->request->getPost('start_time');
        $endTime = $this->request->getPost('end_time');

        // Validate that end time is after start time
        if (strtotime($endTime) <= strtotime($startTime)) {
            return redirect()->back()->withInput()->with('error', 'End time must be after start time');
        }

        $data = [
            'quiz_id' => $this->request->getPost('quiz_id'),
            'class_id' => $this->request->getPost('class_id'),
            'start_time' => $startTime,
            'end_time' => $endTime
        ];

        if ($this->db->table('quiz_assignments')->where('id', $id)->update($data)) {
            $quiz = $this->db->table('quizzes')->where('id', $data['quiz_id'])->get()->getRowArray();
            $class = $this->db->table('classes')->where('id', $data['class_id'])->get()->getRowArray();
            
            $this->logActivity(session()->get('id'), 'assignment_update', 
                "Updated assignment: quiz '{$quiz['title']}' to class '{$class['name']}'");
            
            return redirect()->to('/admin/assignments')->with('message', 'Assignment updated successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update assignment');
        }
    }

    public function delete($id)
    {
        $assignment = $this->db->table('quiz_assignments')->where('id', $id)->get()->getRowArray();

        if (!$assignment) {
            return redirect()->to('/admin/assignments')->with('error', 'Assignment not found');
        }

        if ($this->db->table('quiz_assignments')->where('id', $id)->delete()) {
            $this->logActivity(session()->get('id'), 'assignment_delete', 'Deleted quiz assignment');
            return redirect()->to('/admin/assignments')->with('message', 'Assignment deleted successfully');
        } else {
            return redirect()->to('/admin/assignments')->with('error', 'Failed to delete assignment');
        }
    }
}