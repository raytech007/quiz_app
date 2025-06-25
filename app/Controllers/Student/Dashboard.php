<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Student Dashboard',
            'active' => 'dashboard'
        ];

        // Get stats for the dashboard
        $db = \Config\Database::connect();
        $userId = session()->get('id');

        // Get student's classes
        $data['classes'] = $db->table('classes')
            ->select('classes.*')
            ->join('student_class', 'classes.id = student_class.class_id')
            ->where('student_class.student_id', $userId)
            ->where('classes.is_active', 1)
            ->get()
            ->getResultArray();

        // Get available quizzes for this student
        $data['availableQuizzes'] = $db->table('quiz_assignments')
            ->select('quiz_assignments.*, quizzes.title as quiz_title, classes.name as class_name')
            ->join('quizzes', 'quizzes.id = quiz_assignments.quiz_id')
            ->join('classes', 'classes.id = quiz_assignments.class_id')
            ->join('student_class', 'classes.id = student_class.class_id')
            ->where('student_class.student_id', $userId)
            ->where('quiz_assignments.start_time <=', date('Y-m-d H:i:s'))
            ->where('quiz_assignments.end_time >=', date('Y-m-d H:i:s'))
            ->where('quizzes.is_active', 1)
            ->get()
            ->getResultArray();

        // For each quiz, check if the student has any attempts and how many are allowed
        foreach ($data['availableQuizzes'] as $key => $quiz) {
            $attempts = $db->table('quiz_attempts')
                ->where('quiz_id', $quiz['quiz_id'])
                ->where('user_id', $userId)
                ->countAllResults();

            $maxAttempts = $db->table('quizzes')
                ->select('max_attempts')
                ->where('id', $quiz['quiz_id'])
                ->get()
                ->getRow()
                ->max_attempts;

            $data['availableQuizzes'][$key]['attempts'] = $attempts;
            $data['availableQuizzes'][$key]['max_attempts'] = $maxAttempts;
            $data['availableQuizzes'][$key]['can_attempt'] = ($maxAttempts == 0 || $attempts < $maxAttempts);
        }

        // Get recent quiz attempts by this student
        $data['recentAttempts'] = $db->table('quiz_attempts')
            ->select('quiz_attempts.*, quizzes.title as quiz_title')
            ->join('quizzes', 'quizzes.id = quiz_attempts.quiz_id')
            ->where('quiz_attempts.user_id', $userId)
            ->orderBy('quiz_attempts.start_time', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        // Get upcoming quizzes (not yet started)
        $data['upcomingQuizzes'] = $db->table('quiz_assignments')
            ->select('quiz_assignments.*, quizzes.title as quiz_title, classes.name as class_name')
            ->join('quizzes', 'quizzes.id = quiz_assignments.quiz_id')
            ->join('classes', 'classes.id = quiz_assignments.class_id')
            ->join('student_class', 'classes.id = student_class.class_id')
            ->where('student_class.student_id', $userId)
            ->where('quiz_assignments.start_time >', date('Y-m-d H:i:s'))
            ->where('quizzes.is_active', 1)
            ->orderBy('quiz_assignments.start_time', 'ASC')
            ->limit(5)
            ->get()
            ->getResultArray();

        return view('student/dashboard', $data);
    }
}
