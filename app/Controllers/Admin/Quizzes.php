<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\QuizModel;
use App\Models\QuestionModel;

class Quizzes extends BaseController
{
    protected $quizModel;
    protected $questionModel;

    public function __construct()
    {
        $this->quizModel = new QuizModel();
        $this->questionModel = new QuestionModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Quiz Management',
            'active' => 'quizzes',
            'quizzes' => $this->quizModel->getAllQuizzesWithCreator()
        ];

        return view('admin/quizzes/index', $data);
    }

    public function add()
    {
        $data = [
            'title' => 'Add Quiz',
            'active' => 'quizzes'
        ];

        return view('admin/quizzes/add', $data);
    }

    public function create()
    {
        $rules = [
            'title' => 'required|min_length[3]',
            'description' => 'permit_empty',
            'time_limit' => 'permit_empty|numeric',
            'pass_percentage' => 'permit_empty|numeric|greater_than[0]|less_than_equal_to[100]',
            'max_attempts' => 'permit_empty|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'time_limit' => $this->request->getPost('time_limit') ?: 0,
            'pass_percentage' => $this->request->getPost('pass_percentage') ?: 60.00,
            'is_randomized' => $this->request->getPost('is_randomized') ? 1 : 0,
            'show_results' => $this->request->getPost('show_results') ? 1 : 0,
            'show_answers' => $this->request->getPost('show_answers') ? 1 : 0,
            'max_attempts' => $this->request->getPost('max_attempts') ?: 1,
            'created_by' => session()->get('id'),
            'is_active' => 1
        ];

        if ($this->quizModel->insert($data)) {
            $this->logActivity(session()->get('id'), 'quiz_create', 'Created quiz: ' . $data['title']);
            return redirect()->to('/admin/quizzes')->with('message', 'Quiz created successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create quiz');
        }
    }

    public function edit($id)
    {
        $quiz = $this->quizModel->find($id);

        if (!$quiz) {
            return redirect()->to('/admin/quizzes')->with('error', 'Quiz not found');
        }

        $data = [
            'title' => 'Edit Quiz',
            'active' => 'quizzes',
            'quiz' => $quiz
        ];

        return view('admin/quizzes/edit', $data);
    }

    public function update($id)
    {
        $quiz = $this->quizModel->find($id);

        if (!$quiz) {
            return redirect()->to('/admin/quizzes')->with('error', 'Quiz not found');
        }

        $rules = [
            'title' => 'required|min_length[3]',
            'description' => 'permit_empty',
            'time_limit' => 'permit_empty|numeric',
            'pass_percentage' => 'permit_empty|numeric|greater_than[0]|less_than_equal_to[100]',
            'max_attempts' => 'permit_empty|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'time_limit' => $this->request->getPost('time_limit') ?: 0,
            'pass_percentage' => $this->request->getPost('pass_percentage') ?: 60.00,
            'is_randomized' => $this->request->getPost('is_randomized') ? 1 : 0,
            'show_results' => $this->request->getPost('show_results') ? 1 : 0,
            'show_answers' => $this->request->getPost('show_answers') ? 1 : 0,
            'max_attempts' => $this->request->getPost('max_attempts') ?: 1,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];

        if ($this->quizModel->update($id, $data)) {
            $this->logActivity(session()->get('id'), 'quiz_update', 'Updated quiz: ' . $data['title']);
            return redirect()->to('/admin/quizzes')->with('message', 'Quiz updated successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update quiz');
        }
    }

    public function delete($id)
    {
        $quiz = $this->quizModel->find($id);

        if (!$quiz) {
            return redirect()->to('/admin/quizzes')->with('error', 'Quiz not found');
        }

        // Check if quiz has attempts
        $attemptCount = $this->db->table('quiz_attempts')->where('quiz_id', $id)->countAllResults();

        if ($attemptCount > 0) {
            return redirect()->to('/admin/quizzes')->with('error', 'Cannot delete quiz with existing attempts');
        }

        if ($this->quizModel->delete($id)) {
            $this->logActivity(session()->get('id'), 'quiz_delete', 'Deleted quiz: ' . $quiz['title']);
            return redirect()->to('/admin/quizzes')->with('message', 'Quiz deleted successfully');
        } else {
            return redirect()->to('/admin/quizzes')->with('error', 'Failed to delete quiz');
        }
    }

    public function questions($id)
    {
        $quiz = $this->quizModel->getQuizWithCreator($id);

        if (!$quiz) {
            return redirect()->to('/admin/quizzes')->with('error', 'Quiz not found');
        }

        $quizQuestions = $this->questionModel->getQuizQuestions($id);
        $availableQuestions = $this->questionModel->getQuestionsNotInQuiz($id);

        $data = [
            'title' => 'Quiz Questions',
            'active' => 'quizzes',
            'quiz' => $quiz,
            'quizQuestions' => $quizQuestions,
            'availableQuestions' => $availableQuestions
        ];

        return view('admin/quizzes/questions', $data);
    }

    public function addQuestion($quizId)
    {
        $questionId = $this->request->getPost('question_id');
        $points = $this->request->getPost('points') ?: 1.00;

        if (!$questionId) {
            return redirect()->back()->with('error', 'Please select a question');
        }

        if ($this->quizModel->addQuestion($quizId, $questionId, $points)) {
            $question = $this->questionModel->find($questionId);
            $this->logActivity(session()->get('id'), 'quiz_question_add', 
                "Added question to quiz: {$question['content']}");
            
            return redirect()->back()->with('message', 'Question added to quiz successfully');
        } else {
            return redirect()->back()->with('error', 'Failed to add question to quiz or question already exists');
        }
    }

    public function removeQuestion($quizId, $questionId)
    {
        if ($this->quizModel->removeQuestion($quizId, $questionId)) {
            $question = $this->questionModel->find($questionId);
            $this->logActivity(session()->get('id'), 'quiz_question_remove', 
                "Removed question from quiz: {$question['content']}");
            
            return redirect()->back()->with('message', 'Question removed from quiz successfully');
        } else {
            return redirect()->back()->with('error', 'Failed to remove question from quiz');
        }
    }
}