<?php

namespace App\Models;

use CodeIgniter\Model;

class QuestionModel extends Model
{
    protected $table      = 'questions';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'category_id', 'question_type_id', 'content', 'explanation',
        'difficulty_level', 'created_by', 'is_active'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules    = [
        'question_type_id' => 'required|numeric',
        'content'          => 'required',
        'created_by'       => 'required|numeric',
    ];

    protected $validationMessages = [
        'question_type_id' => [
            'required' => 'Question type is required',
            'numeric'  => 'Invalid question type selected',
        ],
        'content' => [
            'required' => 'Question content is required',
        ],
        'created_by' => [
            'required' => 'Creator ID is required',
            'numeric'  => 'Invalid creator ID',
        ],
    ];

    protected $skipValidation     = false;
    protected $cleanValidationRules = true;

    /**
     * Get question with type information
     */
    public function getQuestionWithType($id)
    {
        $builder = $this->db->table('questions');
        $builder->select('questions.*, question_types.name as question_type, categories.name as category_name');
        $builder->join('question_types', 'question_types.id = questions.question_type_id');
        $builder->join('categories', 'categories.id = questions.category_id', 'left');
        $builder->where('questions.id', $id);

        return $builder->get()->getRowArray();
    }

    /**
     * Get questions created by a specific user
     */
    public function getQuestionsByUser($userId)
    {
        $builder = $this->db->table('questions');
        $builder->select('questions.*, question_types.name as question_type, categories.name as category_name');
        $builder->join('question_types', 'question_types.id = questions.question_type_id');
        $builder->join('categories', 'categories.id = questions.category_id', 'left');
        $builder->where('questions.created_by', $userId);
        $builder->orderBy('questions.created_at', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get all questions with type and category information
     */
    public function getAllQuestionsWithDetails()
    {
        $builder = $this->db->table('questions');
        $builder->select('questions.*, question_types.name as question_type, categories.name as category_name, users.first_name, users.last_name');
        $builder->join('question_types', 'question_types.id = questions.question_type_id');
        $builder->join('categories', 'categories.id = questions.category_id', 'left');
        $builder->join('users', 'users.id = questions.created_by');
        $builder->orderBy('questions.created_at', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get question options for multiple choice or true/false questions
     */
    public function getQuestionOptions($questionId)
    {
        $builder = $this->db->table('question_options');
        $builder->where('question_id', $questionId);
        $builder->orderBy('sort_order', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get fill-in-the-blank answers for a question
     */
    public function getFillBlankAnswers($questionId)
    {
        $builder = $this->db->table('fill_blank_answers');
        $builder->where('question_id', $questionId);

        return $builder->get()->getResultArray();
    }

    /**
     * Save question options for multiple choice or true/false questions
     */
    public function saveQuestionOptions($questionId, $options)
    {
        // Delete existing options first
        $this->db->table('question_options')->where('question_id', $questionId)->delete();

        // Insert new options
        foreach ($options as $index => $option) {
            $data = [
                'question_id' => $questionId,
                'option_text' => $option['text'],
                'is_correct' => $option['is_correct'] ?? false,
                'sort_order' => $index
            ];

            $this->db->table('question_options')->insert($data);
        }
    }

    /**
     * Save fill-in-the-blank answers for a question
     */
    public function saveFillBlankAnswers($questionId, $answers)
    {
        // Delete existing answers first
        $this->db->table('fill_blank_answers')->where('question_id', $questionId)->delete();

        // Insert new answers
        foreach ($answers as $answer) {
            $data = [
                'question_id' => $questionId,
                'answer_text' => $answer['text'],
                'is_case_sensitive' => $answer['is_case_sensitive'] ?? false
            ];

            $this->db->table('fill_blank_answers')->insert($data);
        }
    }

    /**
     * Get questions by category
     */
    public function getQuestionsByCategory($categoryId)
    {
        $builder = $this->db->table('questions');
        $builder->select('questions.*, question_types.name as question_type');
        $builder->join('question_types', 'question_types.id = questions.question_type_id');
        $builder->where('questions.category_id', $categoryId);
        $builder->orderBy('questions.created_at', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get questions by type
     */
    public function getQuestionsByType($typeId)
    {
        $builder = $this->db->table('questions');
        $builder->select('questions.*, categories.name as category_name');
        $builder->join('categories', 'categories.id = questions.category_id', 'left');
        $builder->where('questions.question_type_id', $typeId);
        $builder->orderBy('questions.created_at', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get questions for a specific quiz
     */
    public function getQuizQuestions($quizId)
    {
        $builder = $this->db->table('questions');
        $builder->select('questions.*, question_types.name as question_type, quiz_questions.sort_order, quiz_questions.points');
        $builder->join('quiz_questions', 'quiz_questions.question_id = questions.id');
        $builder->join('question_types', 'question_types.id = questions.question_type_id');
        $builder->where('quiz_questions.quiz_id', $quizId);
        $builder->orderBy('quiz_questions.sort_order', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get questions not in a specific quiz
     */
    public function getQuestionsNotInQuiz($quizId, $userId = null)
    {
        $subquery = $this->db->table('quiz_questions')
            ->select('question_id')
            ->where('quiz_id', $quizId)
            ->getCompiledSelect();

        $builder = $this->db->table('questions');
        $builder->select('questions.*, question_types.name as question_type, categories.name as category_name');
        $builder->join('question_types', 'question_types.id = questions.question_type_id');
        $builder->join('categories', 'categories.id = questions.category_id', 'left');
        $builder->where("questions.id NOT IN ($subquery)", null, false);
        $builder->where('questions.is_active', 1);

        // If userId is provided, only show questions created by this user
        if ($userId !== null) {
            $builder->where('questions.created_by', $userId);
        }

        $builder->orderBy('questions.created_at', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get question types
     */
    public function getQuestionTypes()
    {
        $builder = $this->db->table('question_types');
        return $builder->get()->getResultArray();
    }

    /**
     * Import questions from CSV
     */
    public function importFromCsv($data, $userId)
    {
        $this->db->transStart();

        foreach ($data as $row) {
            // Insert question
            $questionData = [
                'category_id' => $row['category_id'] ?? null,
                'question_type_id' => $row['question_type_id'],
                'content' => $row['content'],
                'explanation' => $row['explanation'] ?? null,
                'difficulty_level' => $row['difficulty_level'] ?? 'medium',
                'created_by' => $userId,
                'is_active' => 1
            ];

            $this->insert($questionData);
            $questionId = $this->insertID();

            // Handle options or answers based on question type
            if ($row['question_type_id'] == 1 || $row['question_type_id'] == 2) { // Multiple choice or True/False
                $this->saveQuestionOptions($questionId, $row['options']);
            } elseif ($row['question_type_id'] == 3) { // Fill in the blank
                $this->saveFillBlankAnswers($questionId, $row['answers']);
            }
        }

        $this->db->transComplete();

        return $this->db->transStatus();
    }
}
