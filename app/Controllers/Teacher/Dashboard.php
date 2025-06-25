<?php

namespace App\Controllers\Teacher;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Teacher Dashboard',
            'active' => 'dashboard'
        ];

        // Get stats for the dashboard
        $db = \Config\Database::connect();
        $userId = session()->get('id');

        // Count classes, quizzes, and questions created by this teacher
        $data['totalClasses'] = $db->table('classes')
            ->where('created_by', $userId)
            ->countAllResults();

        $data['totalQuizzes'] = $db->table('quizzes')
            ->where('created_by', $userId)
            ->countAllResults();

        $data['totalQuestions'] = $db->table('questions')
            ->where('created_by', $userId)
            ->countAllResults();

        // Get teacher's classes
        $data['classes'] = $db->table('classes')
            ->where('created_by', $userId)
            ->get()
            ->getResultArray();

        // Get recent quiz attempts for quizzes created by this teacher
        $data['recentAttempts'] = $db->table('quiz_attempts')
            ->select('quiz_attempts.*, quizzes.title as quiz_title, users.first_name, users.last_name')
            ->join('quizzes', 'quizzes.id = quiz_attempts.quiz_id')
            ->join('users', 'users.id = quiz_attempts.user_id')
            ->where('quizzes.created_by', $userId)
            ->orderBy('quiz_attempts.start_time', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        // Get upcoming quiz assignments
        $data['upcomingAssignments'] = $db->table('quiz_assignments')
            ->select('quiz_assignments.*, quizzes.title as quiz_title, classes.name as class_name')
            ->join('quizzes', 'quizzes.id = quiz_assignments.quiz_id')
            ->join('classes', 'classes.id = quiz_assignments.class_id')
            ->where('quizzes.created_by', $userId)
            ->where('quiz_assignments.end_time >', date('Y-m-d H:i:s'))
            ->orderBy('quiz_assignments.start_time', 'ASC')
            ->limit(5)
            ->get()
            ->getResultArray();

        return view('teacher/dashboard', $data);
    }
}
