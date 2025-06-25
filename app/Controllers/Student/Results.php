<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\QuizAttemptModel;
use App\Models\QuizModel;
use App\Models\QuestionModel;

class Results extends BaseController
{
    protected $attemptModel;
    protected $quizModel;
    protected $questionModel;

    public function __construct()
    {
        $this->attemptModel = new QuizAttemptModel();
        $this->quizModel = new QuizModel();
        $this->questionModel = new QuestionModel();
    }

    /**
     * Display all quiz results for the student
     */
    public function index()
    {
        $userId = session()->get('id');

        $data = [
            'title' => 'My Quiz Results',
            'active' => 'results',
            'attempts' => $this->quizModel->getStudentAttempts($userId)
        ];

        return view('student/results/index', $data);
    }

    /**
     * View detailed results for a specific quiz attempt
     */
    public function view($attemptId)
    {
        $userId = session()->get('id');
        $attempt = $this->attemptModel->getAttempt($attemptId);

        // Validate the attempt belongs to this user
        if (!$attempt || $attempt['user_id'] != $userId) {
            return redirect()->to('/student/results')->with('error', 'Result not found or access denied.');
        }

        // Get quiz details
        $quiz = $this->quizModel->find($attempt['quiz_id']);

        // Check if results are viewable (either quiz shows results or user is admin)
        if (!$quiz['show_results'] && session()->get('role') !== 'admin') {
            $data = [
                'title' => 'Quiz Results',
                'active' => 'results',
                'attempt' => $attempt,
                'quiz' => $quiz,
                'resultsHidden' => true
            ];

            return view('student/results/hidden', $data);
        }

        // Get attempt answers
        $answers = $this->attemptModel->getAttemptAnswers($attemptId);

        // Enhance answers with question details
        foreach ($answers as $key => $answer) {
            // Get question details
            $question = $this->questionModel->getQuestionWithType($answer['question_id']);
            $answers[$key]['question'] = $question;

            // Get correct answer based on question type
            if ($question['question_type_id'] == 1 || $question['question_type_id'] == 2) { // Multiple choice or True/False
                $options = $this->questionModel->getQuestionOptions($question['id']);
                $answers[$key]['options'] = $options;

                foreach ($options as $option) {
                    if ($option['is_correct']) {
                        $answers[$key]['correct_answer'] = $option;
                        break;
                    }
                }
            } elseif ($question['question_type_id'] == 3) { // Fill in the blank
                $fillAnswers = $this->questionModel->getFillBlankAnswers($question['id']);
                $answers[$key]['correct_answers'] = $fillAnswers;
            }
        }

        $data = [
            'title' => 'Quiz Results',
            'active' => 'results',
            'attempt' => $attempt,
            'quiz' => $quiz,
            'answers' => $answers,
            'showAnswers' => $quiz['show_answers']
        ];

        return view('student/results/view', $data);
    }
}
