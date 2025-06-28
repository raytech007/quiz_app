<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\QuizAttemptModel;
use App\Models\QuizModel;

class Results extends BaseController
{
    protected $attemptModel;
    protected $quizModel;

    public function __construct()
    {
        $this->attemptModel = new QuizAttemptModel();
        $this->quizModel = new QuizModel();
    }

    public function index()
    {
        $builder = $this->db->table('quiz_attempts');
        $builder->select('quiz_attempts.*, quizzes.title as quiz_title, users.first_name, users.last_name');
        $builder->join('quizzes', 'quizzes.id = quiz_attempts.quiz_id');
        $builder->join('users', 'users.id = quiz_attempts.user_id');
        $builder->orderBy('quiz_attempts.start_time', 'DESC');
        $attempts = $builder->get()->getResultArray();

        $data = [
            'title' => 'Quiz Results',
            'active' => 'results',
            'attempts' => $attempts
        ];

        return view('admin/results/index', $data);
    }

    public function view($id)
    {
        $attempt = $this->attemptModel->getAttempt($id);

        if (!$attempt) {
            return redirect()->to('/admin/results')->with('error', 'Result not found');
        }

        $quiz = $this->quizModel->find($attempt['quiz_id']);
        $answers = $this->attemptModel->getAttemptAnswers($id);

        // Enhance answers with question details
        foreach ($answers as $key => $answer) {
            $question = $this->db->table('questions')
                ->select('questions.*, question_types.name as question_type')
                ->join('question_types', 'question_types.id = questions.question_type_id')
                ->where('questions.id', $answer['question_id'])
                ->get()->getRowArray();
            
            $answers[$key]['question'] = $question;

            // Get correct answer based on question type
            if ($question['question_type_id'] == 1 || $question['question_type_id'] == 2) {
                $options = $this->db->table('question_options')
                    ->where('question_id', $question['id'])
                    ->orderBy('sort_order', 'ASC')
                    ->get()->getResultArray();
                
                $answers[$key]['options'] = $options;

                foreach ($options as $option) {
                    if ($option['is_correct']) {
                        $answers[$key]['correct_answer'] = $option;
                        break;
                    }
                }
            } elseif ($question['question_type_id'] == 3) {
                $fillAnswers = $this->db->table('fill_blank_answers')
                    ->where('question_id', $question['id'])
                    ->get()->getResultArray();
                
                $answers[$key]['correct_answers'] = $fillAnswers;
            }
        }

        $data = [
            'title' => 'Quiz Result Details',
            'active' => 'results',
            'attempt' => $attempt,
            'quiz' => $quiz,
            'answers' => $answers
        ];

        return view('admin/results/view', $data);
    }

    public function byQuiz($quizId)
    {
        $quiz = $this->quizModel->find($quizId);

        if (!$quiz) {
            return redirect()->to('/admin/results')->with('error', 'Quiz not found');
        }

        $attempts = $this->quizModel->getQuizAttempts($quizId);

        $data = [
            'title' => 'Quiz Results: ' . $quiz['title'],
            'active' => 'results',
            'quiz' => $quiz,
            'attempts' => $attempts
        ];

        return view('admin/results/by_quiz', $data);
    }

    public function byClass($classId)
    {
        $class = $this->db->table('classes')->where('id', $classId)->get()->getRowArray();

        if (!$class) {
            return redirect()->to('/admin/results')->with('error', 'Class not found');
        }

        $builder = $this->db->table('quiz_attempts');
        $builder->select('quiz_attempts.*, quizzes.title as quiz_title, users.first_name, users.last_name');
        $builder->join('quizzes', 'quizzes.id = quiz_attempts.quiz_id');
        $builder->join('users', 'users.id = quiz_attempts.user_id');
        $builder->join('student_class', 'student_class.student_id = users.id');
        $builder->where('student_class.class_id', $classId);
        $builder->orderBy('quiz_attempts.start_time', 'DESC');
        $attempts = $builder->get()->getResultArray();

        $data = [
            'title' => 'Class Results: ' . $class['name'],
            'active' => 'results',
            'class' => $class,
            'attempts' => $attempts
        ];

        return view('admin/results/by_class', $data);
    }

    public function byStudent($studentId)
    {
        $student = $this->db->table('users')->where('id', $studentId)->get()->getRowArray();

        if (!$student) {
            return redirect()->to('/admin/results')->with('error', 'Student not found');
        }

        $attempts = $this->quizModel->getStudentAttempts($studentId);

        $data = [
            'title' => 'Student Results: ' . $student['first_name'] . ' ' . $student['last_name'],
            'active' => 'results',
            'student' => $student,
            'attempts' => $attempts
        ];

        return view('admin/results/by_student', $data);
    }

    public function export($attemptId)
    {
        $attempt = $this->attemptModel->getAttempt($attemptId);

        if (!$attempt) {
            return redirect()->to('/admin/results')->with('error', 'Result not found');
        }

        $answers = $this->attemptModel->getAttemptAnswers($attemptId);

        $filename = 'quiz_result_' . $attemptId . '_' . date('Y-m-d_H-i-s') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // CSV Header
        fputcsv($output, [
            'Question', 'User Answer', 'Correct Answer', 'Is Correct', 'Points Earned'
        ]);

        foreach ($answers as $answer) {
            $question = $this->db->table('questions')->where('id', $answer['question_id'])->get()->getRowArray();
            
            $correctAnswer = '';
            if ($question['question_type_id'] == 1 || $question['question_type_id'] == 2) {
                $correct = $this->db->table('question_options')
                    ->where('question_id', $question['id'])
                    ->where('is_correct', 1)
                    ->get()->getRowArray();
                
                if ($correct) {
                    $correctAnswer = $correct['option_text'];
                }
            } elseif ($question['question_type_id'] == 3) {
                $correctAnswers = $this->db->table('fill_blank_answers')
                    ->where('question_id', $question['id'])
                    ->get()->getResultArray();
                
                $correctAnswer = implode(', ', array_column($correctAnswers, 'answer_text'));
            }

            fputcsv($output, [
                strip_tags($question['content']),
                $answer['user_answer'],
                $correctAnswer,
                $answer['is_correct'] ? 'Yes' : 'No',
                $answer['points_earned']
            ]);
        }

        fclose($output);
        exit;
    }
}