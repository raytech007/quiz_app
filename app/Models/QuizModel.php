<?php

namespace App\Models;

use CodeIgniter\Model;

class QuizModel extends Model
{
    protected $table      = 'quizzes';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'title', 'description', 'time_limit', 'pass_percentage',
        'is_randomized', 'show_results', 'show_answers', 'max_attempts',
        'created_by', 'is_active'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules    = [
        'title'           => 'required|min_length[3]',
        'created_by'      => 'required|numeric',
        'time_limit'      => 'permit_empty|numeric',
        'pass_percentage' => 'permit_empty|numeric',
        'max_attempts'    => 'permit_empty|numeric',
    ];

    protected $validationMessages = [
        'title' => [
            'required' => 'Quiz title is required',
            'min_length' => 'Quiz title must be at least 3 characters long',
        ],
        'created_by' => [
            'required' => 'Creator ID is required',
            'numeric'  => 'Invalid creator ID',
        ],
        'time_limit' => [
            'numeric' => 'Time limit must be a number',
        ],
        'pass_percentage' => [
            'numeric' => 'Pass percentage must be a number',
        ],
        'max_attempts' => [
            'numeric' => 'Maximum attempts must be a number',
        ],
    ];

    protected $skipValidation     = false;
    protected $cleanValidationRules = true;

    /**
     * Get quiz with creator information
     */
    public function getQuizWithCreator($id)
    {
        $builder = $this->db->table('quizzes');
        $builder->select('quizzes.*, users.first_name, users.last_name');
        $builder->join('users', 'users.id = quizzes.created_by');
        $builder->where('quizzes.id', $id);

        return $builder->get()->getRowArray();
    }

    /**
     * Get all quizzes with creator information
     */
    public function getAllQuizzesWithCreator()
    {
        $builder = $this->db->table('quizzes');
        $builder->select('quizzes.*, users.first_name, users.last_name');
        $builder->join('users', 'users.id = quizzes.created_by');
        $builder->orderBy('quizzes.created_at', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get quizzes created by a specific user
     */
    public function getQuizzesByCreator($userId)
    {
        $builder = $this->db->table('quizzes');
        $builder->select('quizzes.*');
        $builder->where('quizzes.created_by', $userId);
        $builder->orderBy('quizzes.created_at', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get question count for a quiz
     */
    public function getQuestionCount($quizId)
    {
        $builder = $this->db->table('quiz_questions');
        $builder->where('quiz_id', $quizId);

        return $builder->countAllResults();
    }

    /**
     * Add a question to a quiz
     */
    public function addQuestion($quizId, $questionId, $points = 1.00, $sortOrder = null)
    {
        // Check if question is already in the quiz
        $existing = $this->db->table('quiz_questions')
            ->where('quiz_id', $quizId)
            ->where('question_id', $questionId)
            ->countAllResults();

        if ($existing > 0) {
            return false; // Question already exists in this quiz
        }

        // If sort_order is not provided, get the highest current sort_order and increment
        if ($sortOrder === null) {
            $maxOrder = $this->db->table('quiz_questions')
                ->selectMax('sort_order')
                ->where('quiz_id', $quizId)
                ->get()
                ->getRowArray();

            $sortOrder = (empty($maxOrder) || $maxOrder['sort_order'] === null) ? 0 : $maxOrder['sort_order'] + 1;
        }

        // Add the question
        $data = [
            'quiz_id' => $quizId,
            'question_id' => $questionId,
            'points' => $points,
            'sort_order' => $sortOrder
        ];

        return $this->db->table('quiz_questions')->insert($data);
    }

    /**
     * Remove a question from a quiz
     */
    public function removeQuestion($quizId, $questionId)
    {
        return $this->db->table('quiz_questions')
            ->where('quiz_id', $quizId)
            ->where('question_id', $questionId)
            ->delete();
    }

    /**
     * Update question sort order within a quiz
     */
    public function updateQuestionOrder($quizId, $questionId, $newOrder)
    {
        return $this->db->table('quiz_questions')
            ->where('quiz_id', $quizId)
            ->where('question_id', $questionId)
            ->update(['sort_order' => $newOrder]);
    }

    /**
     * Update question points within a quiz
     */
    public function updateQuestionPoints($quizId, $questionId, $points)
    {
        return $this->db->table('quiz_questions')
            ->where('quiz_id', $quizId)
            ->where('question_id', $questionId)
            ->update(['points' => $points]);
    }

    /**
     * Get assigned classes for a quiz
     */
    public function getAssignedClasses($quizId)
    {
        $builder = $this->db->table('quiz_assignments');
        $builder->select('quiz_assignments.*, classes.name as class_name');
        $builder->join('classes', 'classes.id = quiz_assignments.class_id');
        $builder->where('quiz_assignments.quiz_id', $quizId);
        $builder->orderBy('quiz_assignments.start_time', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get available quizzes for a student
     */
    public function getAvailableQuizzesForStudent($studentId)
    {
        $currentTime = date('Y-m-d H:i:s');

        $builder = $this->db->table('quiz_assignments');
        $builder->select('quiz_assignments.*, quizzes.title as quiz_title, quizzes.time_limit, quizzes.max_attempts, classes.name as class_name');
        $builder->join('quizzes', 'quizzes.id = quiz_assignments.quiz_id');
        $builder->join('classes', 'classes.id = quiz_assignments.class_id');
        $builder->join('student_class', 'student_class.class_id = classes.id');
        $builder->where('student_class.student_id', $studentId);
        $builder->where('quiz_assignments.start_time <=', $currentTime);
        $builder->where('quiz_assignments.end_time >=', $currentTime);
        $builder->where('quizzes.is_active', 1);
        $builder->orderBy('quiz_assignments.end_time', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Check if a student can attempt a quiz
     */
    public function canStudentAttemptQuiz($studentId, $quizId)
    {
        $currentTime = date('Y-m-d H:i:s');

        // Check if the quiz is available to the student
        $assignment = $this->db->table('quiz_assignments')
            ->select('quiz_assignments.*, quizzes.max_attempts')
            ->join('quizzes', 'quizzes.id = quiz_assignments.quiz_id')
            ->join('classes', 'classes.id = quiz_assignments.class_id')
            ->join('student_class', 'student_class.class_id = classes.id')
            ->where('student_class.student_id', $studentId)
            ->where('quiz_assignments.quiz_id', $quizId)
            ->where('quiz_assignments.start_time <=', $currentTime)
            ->where('quiz_assignments.end_time >=', $currentTime)
            ->where('quizzes.is_active', 1)
            ->get()
            ->getRowArray();

        if (empty($assignment)) {
            return false; // Quiz not available to this student
        }

        // Check if the student has reached the maximum number of attempts
        $attempts = $this->db->table('quiz_attempts')
            ->where('user_id', $studentId)
            ->where('quiz_id', $quizId)
            ->countAllResults();

        if ($assignment['max_attempts'] == 0) {
            return true; // Unlimited attempts
        }

        return $attempts < $assignment['max_attempts'];
    }

    /**
     * Get all attempts for a quiz
     */
    public function getQuizAttempts($quizId)
    {
        $builder = $this->db->table('quiz_attempts');
        $builder->select('quiz_attempts.*, users.first_name, users.last_name');
        $builder->join('users', 'users.id = quiz_attempts.user_id');
        $builder->where('quiz_attempts.quiz_id', $quizId);
        $builder->orderBy('quiz_attempts.start_time', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get all attempts by a student
     */
    public function getStudentAttempts($studentId)
    {
        $builder = $this->db->table('quiz_attempts');
        $builder->select('quiz_attempts.*, quizzes.title as quiz_title');
        $builder->join('quizzes', 'quizzes.id = quiz_attempts.quiz_id');
        $builder->where('quiz_attempts.user_id', $studentId);
        $builder->orderBy('quiz_attempts.start_time', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get total points for a quiz
     */
    public function getTotalPoints($quizId)
    {
        $builder = $this->db->table('quiz_questions');
        $builder->selectSum('points');
        $builder->where('quiz_id', $quizId);

        $result = $builder->get()->getRowArray();
        return $result['points'] ?? 0;
    }
}
