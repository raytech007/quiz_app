<?php

namespace App\Models;

use CodeIgniter\Model;

class QuizAttemptModel extends Model
{
    protected $table      = 'quiz_attempts';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'quiz_id', 'user_id', 'start_time', 'end_time',
        'is_completed', 'total_points', 'score_percentage'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules    = [
        'quiz_id'  => 'required|numeric',
        'user_id'  => 'required|numeric',
        'start_time' => 'required',
    ];

    protected $validationMessages = [
        'quiz_id' => [
            'required' => 'Quiz ID is required',
            'numeric'  => 'Invalid quiz ID',
        ],
        'user_id' => [
            'required' => 'User ID is required',
            'numeric'  => 'Invalid user ID',
        ],
        'start_time' => [
            'required' => 'Start time is required',
        ],
    ];

    protected $skipValidation     = false;
    protected $cleanValidationRules = true;

    /**
     * Start a new quiz attempt
     */
    public function startAttempt($quizId, $userId)
    {
        $data = [
            'quiz_id' => $quizId,
            'user_id' => $userId,
            'start_time' => date('Y-m-d H:i:s'),
            'is_completed' => false,
            'total_points' => 0,
            'score_percentage' => 0,
        ];

        $this->insert($data);
        return $this->insertID();
    }

    /**
     * Get attempt details
     */
    public function getAttempt($attemptId)
    {
        $builder = $this->db->table('quiz_attempts');
        $builder->select('quiz_attempts.*, quizzes.title as quiz_title, quizzes.time_limit, quizzes.show_results, quizzes.show_answers, users.first_name, users.last_name');
        $builder->join('quizzes', 'quizzes.id = quiz_attempts.quiz_id');
        $builder->join('users', 'users.id = quiz_attempts.user_id');
        $builder->where('quiz_attempts.id', $attemptId);

        return $builder->get()->getRowArray();
    }

    /**
     * Get active attempt for a user on a quiz
     */
    public function getActiveAttempt($quizId, $userId)
    {
        $builder = $this->db->table('quiz_attempts');
        $builder->where('quiz_id', $quizId);
        $builder->where('user_id', $userId);
        $builder->where('is_completed', false);
        $builder->orderBy('start_time', 'DESC');
        $builder->limit(1);

        return $builder->get()->getRowArray();
    }

    /**
     * Get all answers for an attempt
     */
    public function getAttemptAnswers($attemptId)
    {
        $builder = $this->db->table('attempt_answers');
        $builder->select('attempt_answers.*, questions.content as question_content, questions.question_type_id');
        $builder->join('questions', 'questions.id = attempt_answers.question_id');
        $builder->where('attempt_answers.attempt_id', $attemptId);
        $builder->orderBy('attempt_answers.id', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Save an answer for a question in the attempt
     */
    public function saveAnswer($attemptId, $questionId, $answer)
    {
        // Check if an answer already exists for this question in this attempt
        $existing = $this->db->table('attempt_answers')
            ->where('attempt_id', $attemptId)
            ->where('question_id', $questionId)
            ->get()
            ->getRowArray();

        $data = [
            'user_answer' => $answer,
            'answer_time' => date('Y-m-d H:i:s')
        ];

        if ($existing) {
            // Update existing answer
            return $this->db->table('attempt_answers')
                ->where('id', $existing['id'])
                ->update($data);
        } else {
            // Insert new answer
            $data['attempt_id'] = $attemptId;
            $data['question_id'] = $questionId;
            $data['is_correct'] = false; // Will be evaluated when quiz is submitted
            $data['points_earned'] = 0;   // Will be calculated when quiz is submitted

            return $this->db->table('attempt_answers')->insert($data);
        }
    }

    /**
     * Get the quiz time remaining in seconds
     */
    public function getTimeRemaining($attemptId)
    {
        $attempt = $this->getAttempt($attemptId);

        if (!$attempt || $attempt['is_completed']) {
            return 0;
        }

        // If no time limit, return a large number
        if ($attempt['time_limit'] == 0) {
            return 86400; // 24 hours
        }

        $startTime = strtotime($attempt['start_time']);
        $timeLimit = $attempt['time_limit'] * 60; // Convert minutes to seconds
        $currentTime = time();
        $elapsed = $currentTime - $startTime;
        $remaining = $timeLimit - $elapsed;

        return max(0, $remaining);
    }

    /**
     * Submit and grade the quiz attempt
     */
    public function submitAttempt($attemptId)
    {
        $attempt = $this->find($attemptId);

        if (!$attempt || $attempt['is_completed']) {
            return false;
        }

        $this->db->transStart();

        // Get all answers for this attempt
        $answers = $this->getAttemptAnswers($attemptId);
        $totalPoints = 0;
        $earnedPoints = 0;

        // Grade each answer
        foreach ($answers as $answer) {
            $questionModel = new \App\Models\QuestionModel();
            $question = $questionModel->find($answer['question_id']);

            // Get the points for this question in this quiz
            $questionPoints = $this->db->table('quiz_questions')
                ->select('points')
                ->where('quiz_id', $attempt['quiz_id'])
                ->where('question_id', $answer['question_id'])
                ->get()
                ->getRowArray();

            $points = $questionPoints ? $questionPoints['points'] : 1.00;
            $totalPoints += $points;

            // Check if the answer is correct based on question type
            $isCorrect = false;
            $pointsEarned = 0;

            if ($question['question_type_id'] == 1) { // Multiple choice
                $correctOption = $this->db->table('question_options')
                    ->where('question_id', $question['id'])
                    ->where('is_correct', 1)
                    ->get()
                    ->getRowArray();

                if ($correctOption && $answer['user_answer'] == $correctOption['id']) {
                    $isCorrect = true;
                    $pointsEarned = $points;
                }
            } elseif ($question['question_type_id'] == 2) { // True/False
                $correctOption = $this->db->table('question_options')
                    ->where('question_id', $question['id'])
                    ->where('is_correct', 1)
                    ->get()
                    ->getRowArray();

                if ($correctOption && $answer['user_answer'] == $correctOption['id']) {
                    $isCorrect = true;
                    $pointsEarned = $points;
                }
            } elseif ($question['question_type_id'] == 3) { // Fill in the blank
                $correctAnswers = $this->db->table('fill_blank_answers')
                    ->where('question_id', $question['id'])
                    ->get()
                    ->getResultArray();

                foreach ($correctAnswers as $correctAnswer) {
                    $userAnswer = $answer['user_answer'];
                    $correctText = $correctAnswer['answer_text'];

                    if ($correctAnswer['is_case_sensitive']) {
                        // Case sensitive comparison
                        if ($userAnswer === $correctText) {
                            $isCorrect = true;
                            $pointsEarned = $points;
                            break;
                        }
                    } else {
                        // Case insensitive comparison
                        if (strtolower($userAnswer) === strtolower($correctText)) {
                            $isCorrect = true;
                            $pointsEarned = $points;
                            break;
                        }
                    }
                }
            }

            // Update the answer with the evaluation results
            $this->db->table('attempt_answers')
                ->where('id', $answer['id'])
                ->update([
                    'is_correct' => $isCorrect,
                    'points_earned' => $pointsEarned
                ]);

            $earnedPoints += $pointsEarned;
        }

        // Calculate score percentage
        $scorePercentage = ($totalPoints > 0) ? ($earnedPoints / $totalPoints) * 100 : 0;

        // Update the attempt record
        $this->update($attemptId, [
            'end_time' => date('Y-m-d H:i:s'),
            'is_completed' => true,
            'total_points' => $earnedPoints,
            'score_percentage' => $scorePercentage
        ]);

        $this->db->transComplete();

        return $this->db->transStatus();
    }

    /**
     * Auto-submit expired quiz attempts
     */
    public function autoSubmitExpiredAttempts()
    {
        $currentTime = date('Y-m-d H:i:s');

        // Get all active attempts
        $activeAttempts = $this->where('is_completed', false)->findAll();

        foreach ($activeAttempts as $attempt) {
            // Get the quiz time limit
            $quiz = $this->db->table('quizzes')
                ->select('time_limit')
                ->where('id', $attempt['quiz_id'])
                ->get()
                ->getRowArray();

            if (!$quiz || $quiz['time_limit'] == 0) {
                continue; // Skip if no time limit
            }

            $startTime = strtotime($attempt['start_time']);
            $timeLimit = $quiz['time_limit'] * 60; // Convert minutes to seconds
            $endTime = $startTime + $timeLimit;

            // If the attempt has expired, submit it
            if (time() > $endTime) {
                $this->submitAttempt($attempt['id']);
            }
        }
    }

    /**
     * Get all completed attempts for statistics
     */
    public function getCompletedAttempts()
    {
        return $this->where('is_completed', true)
                    ->orderBy('end_time', 'DESC')
                    ->findAll();
    }

    /**
     * Get the average score for a quiz
     */
    public function getAverageScore($quizId)
    {
        $builder = $this->db->table('quiz_attempts');
        $builder->selectAvg('score_percentage');
        $builder->where('quiz_id', $quizId);
        $builder->where('is_completed', true);

        $result = $builder->get()->getRowArray();
        return $result ? round($result['score_percentage'], 2) : 0;
    }
}
