<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\QuestionModel;
use App\Models\CategoryModel;

class Questions extends BaseController
{
    protected $questionModel;
    protected $categoryModel;

    public function __construct()
    {
        $this->questionModel = new QuestionModel();
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Question Management',
            'active' => 'questions',
            'questions' => $this->questionModel->getAllQuestionsWithDetails()
        ];

        return view('admin/questions/index', $data);
    }

    public function add()
    {
        $data = [
            'title' => 'Add Question',
            'active' => 'questions',
            'categories' => $this->categoryModel->findAll(),
            'questionTypes' => $this->questionModel->getQuestionTypes()
        ];

        return view('admin/questions/add', $data);
    }

    public function create()
    {
        $rules = [
            'question_type_id' => 'required|numeric',
            'content' => 'required',
            'category_id' => 'permit_empty|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $questionData = [
            'category_id' => $this->request->getPost('category_id') ?: null,
            'question_type_id' => $this->request->getPost('question_type_id'),
            'content' => $this->request->getPost('content'),
            'explanation' => $this->request->getPost('explanation'),
            'difficulty_level' => $this->request->getPost('difficulty_level'),
            'created_by' => session()->get('id'),
            'is_active' => 1
        ];

        $this->db->transStart();

        $questionId = $this->questionModel->insert($questionData);

        if ($questionId) {
            $questionType = $this->request->getPost('question_type_id');

            if ($questionType == 1 || $questionType == 2) { // Multiple choice or True/False
                $options = $this->request->getPost('options');
                if ($options) {
                    $this->questionModel->saveQuestionOptions($questionId, $options);
                }
            } elseif ($questionType == 3) { // Fill in the blank
                $answers = $this->request->getPost('answers');
                if ($answers) {
                    $this->questionModel->saveFillBlankAnswers($questionId, $answers);
                }
            }
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Failed to create question');
        }

        $this->logActivity(session()->get('id'), 'question_create', 'Created question: ' . $questionData['content']);

        return redirect()->to('/admin/questions')->with('message', 'Question created successfully');
    }

    public function edit($id)
    {
        $question = $this->questionModel->getQuestionWithType($id);

        if (!$question) {
            return redirect()->to('/admin/questions')->with('error', 'Question not found');
        }

        $data = [
            'title' => 'Edit Question',
            'active' => 'questions',
            'question' => $question,
            'categories' => $this->categoryModel->findAll(),
            'questionTypes' => $this->questionModel->getQuestionTypes(),
            'options' => $this->questionModel->getQuestionOptions($id),
            'fillAnswers' => $this->questionModel->getFillBlankAnswers($id)
        ];

        return view('admin/questions/edit', $data);
    }

    public function update($id)
    {
        $question = $this->questionModel->find($id);

        if (!$question) {
            return redirect()->to('/admin/questions')->with('error', 'Question not found');
        }

        $rules = [
            'question_type_id' => 'required|numeric',
            'content' => 'required',
            'category_id' => 'permit_empty|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $questionData = [
            'category_id' => $this->request->getPost('category_id') ?: null,
            'question_type_id' => $this->request->getPost('question_type_id'),
            'content' => $this->request->getPost('content'),
            'explanation' => $this->request->getPost('explanation'),
            'difficulty_level' => $this->request->getPost('difficulty_level')
        ];

        $this->db->transStart();

        $this->questionModel->update($id, $questionData);

        $questionType = $this->request->getPost('question_type_id');

        if ($questionType == 1 || $questionType == 2) { // Multiple choice or True/False
            $options = $this->request->getPost('options');
            if ($options) {
                $this->questionModel->saveQuestionOptions($id, $options);
            }
        } elseif ($questionType == 3) { // Fill in the blank
            $answers = $this->request->getPost('answers');
            if ($answers) {
                $this->questionModel->saveFillBlankAnswers($id, $answers);
            }
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Failed to update question');
        }

        $this->logActivity(session()->get('id'), 'question_update', 'Updated question: ' . $questionData['content']);

        return redirect()->to('/admin/questions')->with('message', 'Question updated successfully');
    }

    public function delete($id)
    {
        $question = $this->questionModel->find($id);

        if (!$question) {
            return redirect()->to('/admin/questions')->with('error', 'Question not found');
        }

        // Check if question is used in any quiz
        $isUsed = $this->db->table('quiz_questions')->where('question_id', $id)->countAllResults();

        if ($isUsed > 0) {
            return redirect()->to('/admin/questions')->with('error', 'Cannot delete question as it is used in quizzes');
        }

        $this->questionModel->delete($id);

        $this->logActivity(session()->get('id'), 'question_delete', 'Deleted question: ' . $question['content']);

        return redirect()->to('/admin/questions')->with('message', 'Question deleted successfully');
    }

    public function import()
    {
        $data = [
            'title' => 'Import Questions',
            'active' => 'questions',
            'categories' => $this->categoryModel->findAll()
        ];

        return view('admin/questions/import', $data);
    }

    public function importCsv()
    {
        $file = $this->request->getFile('csv_file');

        if (!$file->isValid()) {
            return redirect()->back()->with('error', 'Please select a valid CSV file');
        }

        if ($file->getExtension() !== 'csv') {
            return redirect()->back()->with('error', 'Please upload a CSV file');
        }

        $csvData = array_map('str_getcsv', file($file->getTempName()));
        $header = array_shift($csvData);

        $importData = [];
        $errors = [];

        foreach ($csvData as $rowIndex => $row) {
            if (count($row) < 3) {
                $errors[] = "Row " . ($rowIndex + 2) . ": Insufficient data";
                continue;
            }

            $questionData = [
                'content' => $row[0],
                'question_type_id' => $row[1],
                'category_id' => !empty($row[2]) ? $row[2] : null,
                'explanation' => !empty($row[3]) ? $row[3] : null,
                'difficulty_level' => !empty($row[4]) ? $row[4] : 'medium'
            ];

            // Handle options/answers based on question type
            if ($row[1] == 1 || $row[1] == 2) { // Multiple choice or True/False
                $options = [];
                for ($i = 5; $i < count($row); $i += 2) {
                    if (!empty($row[$i])) {
                        $options[] = [
                            'text' => $row[$i],
                            'is_correct' => !empty($row[$i + 1]) && $row[$i + 1] == '1'
                        ];
                    }
                }
                $questionData['options'] = $options;
            } elseif ($row[1] == 3) { // Fill in the blank
                $answers = [];
                for ($i = 5; $i < count($row); $i += 2) {
                    if (!empty($row[$i])) {
                        $answers[] = [
                            'text' => $row[$i],
                            'is_case_sensitive' => !empty($row[$i + 1]) && $row[$i + 1] == '1'
                        ];
                    }
                }
                $questionData['answers'] = $answers;
            }

            $importData[] = $questionData;
        }

        if (!empty($errors)) {
            return redirect()->back()->with('error', 'Import failed: ' . implode(', ', $errors));
        }

        $success = $this->questionModel->importFromCsv($importData, session()->get('id'));

        if ($success) {
            $this->logActivity(session()->get('id'), 'questions_import', 'Imported ' . count($importData) . ' questions from CSV');
            return redirect()->to('/admin/questions')->with('message', count($importData) . ' questions imported successfully');
        } else {
            return redirect()->back()->with('error', 'Failed to import questions');
        }
    }

    public function export()
    {
        $questions = $this->questionModel->getAllQuestionsWithDetails();

        $filename = 'questions_export_' . date('Y-m-d_H-i-s') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // CSV Header
        fputcsv($output, [
            'Content', 'Type ID', 'Category ID', 'Explanation', 'Difficulty',
            'Option 1', 'Is Correct 1', 'Option 2', 'Is Correct 2',
            'Option 3', 'Is Correct 3', 'Option 4', 'Is Correct 4'
        ]);

        foreach ($questions as $question) {
            $row = [
                $question['content'],
                $question['question_type_id'],
                $question['category_id'],
                $question['explanation'],
                $question['difficulty_level']
            ];

            // Add options/answers
            if ($question['question_type_id'] == 1 || $question['question_type_id'] == 2) {
                $options = $this->questionModel->getQuestionOptions($question['id']);
                for ($i = 0; $i < 4; $i++) {
                    if (isset($options[$i])) {
                        $row[] = $options[$i]['option_text'];
                        $row[] = $options[$i]['is_correct'] ? '1' : '0';
                    } else {
                        $row[] = '';
                        $row[] = '';
                    }
                }
            } elseif ($question['question_type_id'] == 3) {
                $answers = $this->questionModel->getFillBlankAnswers($question['id']);
                for ($i = 0; $i < 4; $i++) {
                    if (isset($answers[$i])) {
                        $row[] = $answers[$i]['answer_text'];
                        $row[] = $answers[$i]['is_case_sensitive'] ? '1' : '0';
                    } else {
                        $row[] = '';
                        $row[] = '';
                    }
                }
            } else {
                for ($i = 0; $i < 8; $i++) {
                    $row[] = '';
                }
            }

            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    public function getOptions($questionId)
    {
        $options = $this->questionModel->getQuestionOptions($questionId);
        return $this->response->setJSON($options);
    }
}