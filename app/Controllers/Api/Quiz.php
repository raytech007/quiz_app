<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\QuizAttemptModel;
use CodeIgniter\API\ResponseTrait;

class Quiz extends BaseController
{
    use ResponseTrait;

    protected $attemptModel;

    public function __construct()
    {
        $this->attemptModel = new QuizAttemptModel();
    }

    /**
     * AJAX endpoint to save an answer
     */
    public function saveAnswer()
    {
        if (!$this->request->isAJAX()) {
            return $this->fail('Invalid request method', 400);
        }

        $attemptId = $this->request->getPost('attempt_id');
        $questionId = $this->request->getPost('question_id');
        $answer = $this->request->getPost('answer');

        $userId = session()->get('id');
        $attempt = $this->attemptModel->find($attemptId);

        // Validate the attempt belongs to this user
        if (!$attempt || $attempt['user_id'] != $userId || $attempt['is_completed']) {
            return $this->respond(['success' => false, 'message' => 'Invalid attempt']);
        }

        // Save the answer
        $success = $this->attemptModel->saveAnswer($attemptId, $questionId, $answer);

        return $this->respond(['success' => $success]);
    }

    /**
     * AJAX endpoint to get the remaining time for a quiz attempt
     */
    public function getRemainingTime($attemptId)
    {
        if (!$this->request->isAJAX()) {
            return $this->fail('Invalid request method', 400);
        }

        $userId = session()->get('id');
        $attempt = $this->attemptModel->find($attemptId);

        // Validate the attempt belongs to this user
        if (!$attempt || $attempt['user_id'] != $userId) {
            return $this->respond(['success' => false, 'message' => 'Invalid attempt', 'timeRemaining' => null]);
        }

        // If attempt is completed, return 0
        if ($attempt['is_completed']) {
            return $this->respond(['success' => true, 'timeRemaining' => 0]);
        }

        // Get the time remaining
        $timeRemaining = $this->attemptModel->getTimeRemaining($attemptId);

        return $this->respond(['success' => true, 'timeRemaining' => $timeRemaining]);
    }
}
