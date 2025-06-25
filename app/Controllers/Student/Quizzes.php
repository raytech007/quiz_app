<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\QuizModel;
use App\Models\QuestionModel;
use App\Models\QuizAttemptModel;

class Quizzes extends BaseController
{
    protected $quizModel;
    protected $questionModel;
    protected $attemptModel;

    public function __construct()
    {
        $this->quizModel = new QuizModel();
        $this->questionModel = new QuestionModel();
        $this->attemptModel = new QuizAttemptModel();
    }

    /**
     * Display all available quizzes for the student
     */
    public function index()
    {
        $userId = session()->get('id');

        $data = [
            'title' => 'Available Quizzes',
            'active' => 'quizzes',
            'availableQuizzes' => $this->quizModel->getAvailableQuizzesForStudent($userId)
        ];

        // For each quiz, check if the student has any attempts and how many are allowed
        foreach ($data['availableQuizzes'] as $key => $quiz) {
            $attempts = $this->db->table('quiz_attempts')
                ->where('quiz_id', $quiz['quiz_id'])
                ->where('user_id', $userId)
                ->countAllResults();

            $data['availableQuizzes'][$key]['attempts'] = $attempts;
            $data['availableQuizzes'][$key]['can_attempt'] = ($quiz['max_attempts'] == 0 || $attempts < $quiz['max_attempts']);
        }

        return view('student/quizzes/index', $data);
    }

    /**
     * View quiz details before starting
     */
    public function view($quizId)
    {
        $userId = session()->get('id');

        // Check if the quiz is available to this student
        if (!$this->quizModel->canStudentAttemptQuiz($userId, $quizId)) {
            return redirect()->to('/student/quizzes')->with('error', 'This quiz is not available to you.');
        }

        $quiz = $this->quizModel->getQuizWithCreator($quizId);
        if (!$quiz) {
            return redirect()->to('/student/quizzes')->with('error', 'Quiz not found.');
        }

        // Get number of questions
        $questionCount = $this->quizModel->getQuestionCount($quizId);

        // Get student's previous attempts on this quiz
        $attempts = $this->attemptModel->where('quiz_id', $quizId)
            ->where('user_id', $userId)
            ->orderBy('start_time', 'DESC')
            ->findAll();

        // Check if the student can attempt the quiz
        $canAttempt = $this->quizModel->canStudentAttemptQuiz($userId, $quizId);

        // Check if there's an active attempt
        $activeAttempt = $this->attemptModel->getActiveAttempt($quizId, $userId);

        $data = [
            'title' => 'Quiz Details',
            'active' => 'quizzes',
            'quiz' => $quiz,
            'questionCount' => $questionCount,
            'attempts' => $attempts,
            'canAttempt' => $canAttempt,
            'activeAttempt' => $activeAttempt
        ];

        return view('student/quizzes/view', $data);
    }

    /**
     * Start or continue a quiz attempt
     */
    public function take($quizId)
    {
        $userId = session()->get('id');

        // Check if the quiz is available to this student
        if (!$this->quizModel->canStudentAttemptQuiz($userId, $quizId)) {
            return redirect()->to('/student/quizzes')->with('error', 'This quiz is not available to you.');
        }

        $quiz = $this->quizModel->getQuizWithCreator($quizId);
        if (!$quiz) {
            return redirect()->to('/student/quizzes')->with('error', 'Quiz not found.');
        }

        // Check if there's an active attempt
        $activeAttempt = $this->attemptModel->getActiveAttempt($quizId, $userId);

        if (!$activeAttempt) {
            // Start a new attempt
            $attemptId = $this->attemptModel->startAttempt($quizId, $userId);
            $activeAttempt = $this->attemptModel->find($attemptId);

            // Log the action
            $this->logActivity($userId, 'quiz_start', "Started quiz: {$quiz['title']}");
        }

        // Get quiz questions
        $questions = $this->questionModel->getQuizQuestions($quizId);

        // Randomize questions if the quiz is set to randomize
        if ($quiz['is_randomized']) {
            shuffle($questions);
        }

        // Get answers for this attempt
        $answers = $this->attemptModel->getAttemptAnswers($activeAttempt['id']);

        // Create a lookup array for easier access to answers
        $answerLookup = [];
        foreach ($answers as $answer) {
            $answerLookup[$answer['question_id']] = $answer;
        }

        // Get the time remaining
        $timeRemaining = $this->attemptModel->getTimeRemaining($activeAttempt['id']);

        // If time has expired, auto-submit the quiz
        if ($quiz['time_limit'] > 0 && $timeRemaining <= 0) {
            $this->attemptModel->submitAttempt($activeAttempt['id']);
            return redirect()->to('/student/results/view/' . $activeAttempt['id'])
                ->with('message', 'Your quiz time has expired. The quiz has been automatically submitted.');
        }

        $data = [
            'title' => 'Take Quiz: ' . $quiz['title'],
            'active' => 'quizzes',
            'quiz' => $quiz,
            'attempt' => $activeAttempt,
            'questions' => $questions,
            'answers' => $answerLookup,
            'timeRemaining' => $timeRemaining,
            'scripts' => ['quiz-timer', 'quiz-interface']
        ];

        return view('student/quizzes/take', $data);
    }

    /**
     * AJAX endpoint to save an answer
     */
    public function saveAnswer()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $attemptId = $this->request->getPost('attempt_id');
        $questionId = $this->request->getPost('question_id');
        $answer = $this->request->getPost('answer');

        $userId = session()->get('id');
        $attempt = $this->attemptModel->find($attemptId);

        // Validate the attempt belongs to this user
        if (!$attempt || $attempt['user_id'] != $userId || $attempt['is_completed']) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid attempt']);
        }

        // Save the answer
        $success = $this->attemptModel->saveAnswer($attemptId, $questionId, $answer);

        return $this->response->setJSON(['success' => $success]);
    }

    /**
     * Submit the quiz
     */
    public function submit()
    {
        $attemptId = $this->request->getPost('attempt_id');
        $userId = session()->get('id');

        $attempt = $this->attemptModel->find($attemptId);

        // Validate the attempt belongs to this user
        if (!$attempt || $attempt['user_id'] != $userId || $attempt['is_completed']) {
            return redirect()->to('/student/quizzes')->with('error', 'Invalid attempt');
        }

        // Submit and grade the attempt
        $this->attemptModel->submitAttempt($attemptId);

        // Log the action
        $quiz = $this->quizModel->find($attempt['quiz_id']);
        $this->logActivity($userId, 'quiz_submit', "Submitted quiz: {$quiz['title']}");

        return redirect()->to('/student/results/view/' . $attemptId)
            ->with('message', 'Your quiz has been submitted successfully.');
    }
}
