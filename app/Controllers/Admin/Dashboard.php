<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Admin Dashboard',
            'active' => 'dashboard'
        ];

        // Get stats for the dashboard
        $db = \Config\Database::connect();

        // Count users by role
        $data['totalAdmins'] = $db->table('users')
            ->join('roles', 'roles.id = users.role_id')
            ->where('roles.name', 'admin')
            ->countAllResults();

        $data['totalTeachers'] = $db->table('users')
            ->join('roles', 'roles.id = users.role_id')
            ->where('roles.name', 'teacher')
            ->countAllResults();

        $data['totalStudents'] = $db->table('users')
            ->join('roles', 'roles.id = users.role_id')
            ->where('roles.name', 'student')
            ->countAllResults();

        // Count classes, quizzes, and questions
        $data['totalClasses'] = $db->table('classes')->countAllResults();
        $data['totalQuizzes'] = $db->table('quizzes')->countAllResults();
        $data['totalQuestions'] = $db->table('questions')->countAllResults();

        // Get recent quiz attempts
        $data['recentAttempts'] = $db->table('quiz_attempts')
            ->select('quiz_attempts.*, quizzes.title as quiz_title, users.first_name, users.last_name')
            ->join('quizzes', 'quizzes.id = quiz_attempts.quiz_id')
            ->join('users', 'users.id = quiz_attempts.user_id')
            ->orderBy('quiz_attempts.start_time', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        // Get recent user activities
        $data['recentActivities'] = $db->table('activity_logs')
            ->select('activity_logs.*, users.first_name, users.last_name')
            ->join('users', 'users.id = activity_logs.user_id', 'left')
            ->orderBy('activity_logs.created_at', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        return view('admin/dashboard', $data);
    }
}
