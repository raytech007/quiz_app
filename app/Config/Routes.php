<?php

namespace Config;

$routes = Services::routes();

if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

// Authentication Routes
$routes->get('/', 'Auth::login');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attemptLogin');
$routes->get('logout', 'Auth::logout');

// Dashboard Routes
$routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);

// Admin Routes
$routes->group('admin', ['filter' => 'admin'], function($routes) {
    $routes->get('/', 'Admin\Dashboard::index');
    
    // User Management
    $routes->get('users', 'Admin\Users::index');
    $routes->get('users/add', 'Admin\Users::add');
    $routes->post('users/create', 'Admin\Users::create');
    $routes->get('users/edit/(:num)', 'Admin\Users::edit/$1');
    $routes->post('users/update/(:num)', 'Admin\Users::update/$1');
    $routes->get('users/delete/(:num)', 'Admin\Users::delete/$1');
    
    // Class Management
    $routes->get('classes', 'Admin\Classes::index');
    $routes->get('classes/add', 'Admin\Classes::add');
    $routes->post('classes/create', 'Admin\Classes::create');
    $routes->get('classes/edit/(:num)', 'Admin\Classes::edit/$1');
    $routes->post('classes/update/(:num)', 'Admin\Classes::update/$1');
    $routes->get('classes/delete/(:num)', 'Admin\Classes::delete/$1');
    $routes->get('classes/students/(:num)', 'Admin\Classes::students/$1');
    $routes->post('classes/addstudent/(:num)', 'Admin\Classes::addStudent/$1');
    $routes->get('classes/removestudent/(:num)/(:num)', 'Admin\Classes::removeStudent/$1/$2');
    
    // Category Management
    $routes->get('categories', 'Admin\Categories::index');
    $routes->get('categories/add', 'Admin\Categories::add');
    $routes->post('categories/create', 'Admin\Categories::create');
    $routes->get('categories/edit/(:num)', 'Admin\Categories::edit/$1');
    $routes->post('categories/update/(:num)', 'Admin\Categories::update/$1');
    $routes->get('categories/delete/(:num)', 'Admin\Categories::delete/$1');
    
    // Question Management
    $routes->get('questions', 'Admin\Questions::index');
    $routes->get('questions/add', 'Admin\Questions::add');
    $routes->post('questions/create', 'Admin\Questions::create');
    $routes->get('questions/edit/(:num)', 'Admin\Questions::edit/$1');
    $routes->post('questions/update/(:num)', 'Admin\Questions::update/$1');
    $routes->get('questions/delete/(:num)', 'Admin\Questions::delete/$1');
    $routes->get('questions/import', 'Admin\Questions::import');
    $routes->post('questions/importcsv', 'Admin\Questions::importCsv');
    $routes->get('questions/export', 'Admin\Questions::export');
    $routes->get('questions/get-options/(:num)', 'Admin\Questions::getOptions/$1');
    
    // Quiz Management
    $routes->get('quizzes', 'Admin\Quizzes::index');
    $routes->get('quizzes/add', 'Admin\Quizzes::add');
    $routes->post('quizzes/create', 'Admin\Quizzes::create');
    $routes->get('quizzes/edit/(:num)', 'Admin\Quizzes::edit/$1');
    $routes->post('quizzes/update/(:num)', 'Admin\Quizzes::update/$1');
    $routes->get('quizzes/delete/(:num)', 'Admin\Quizzes::delete/$1');
    $routes->get('quizzes/questions/(:num)', 'Admin\Quizzes::questions/$1');
    $routes->post('quizzes/addquestion/(:num)', 'Admin\Quizzes::addQuestion/$1');
    $routes->get('quizzes/removequestion/(:num)/(:num)', 'Admin\Quizzes::removeQuestion/$1/$2');
    
    // Quiz Assignment
    $routes->get('assignments', 'Admin\Assignments::index');
    $routes->get('assignments/add', 'Admin\Assignments::add');
    $routes->post('assignments/create', 'Admin\Assignments::create');
    $routes->get('assignments/edit/(:num)', 'Admin\Assignments::edit/$1');
    $routes->post('assignments/update/(:num)', 'Admin\Assignments::update/$1');
    $routes->get('assignments/delete/(:num)', 'Admin\Assignments::delete/$1');
    
    // Results and Reports
    $routes->get('results', 'Admin\Results::index');
    $routes->get('results/view/(:num)', 'Admin\Results::view/$1');
    $routes->get('results/byquiz/(:num)', 'Admin\Results::byQuiz/$1');
    $routes->get('results/byclass/(:num)', 'Admin\Results::byClass/$1');
    $routes->get('results/bystudent/(:num)', 'Admin\Results::byStudent/$1');
    $routes->get('results/export/(:num)', 'Admin\Results::export/$1');
    
    // Settings
    $routes->get('settings', 'Admin\Settings::index');
    $routes->post('settings/update', 'Admin\Settings::update');
});

// Teacher Routes
$routes->group('teacher', ['filter' => 'teacher'], function($routes) {
    $routes->get('/', 'Teacher\Dashboard::index');
    
    // Limited Class Management
    $routes->get('classes', 'Teacher\Classes::index');
    $routes->get('classes/students/(:num)', 'Teacher\Classes::students/$1');
    
    // Question Management
    $routes->get('questions', 'Teacher\Questions::index');
    $routes->get('questions/add', 'Teacher\Questions::add');
    $routes->post('questions/create', 'Teacher\Questions::create');
    $routes->get('questions/edit/(:num)', 'Teacher\Questions::edit/$1');
    $routes->post('questions/update/(:num)', 'Teacher\Questions::update/$1');
    $routes->get('questions/delete/(:num)', 'Teacher\Questions::delete/$1');
    $routes->get('questions/import', 'Teacher\Questions::import');
    $routes->post('questions/importcsv', 'Teacher\Questions::importCsv');
    $routes->get('questions/export', 'Teacher\Questions::export');
    $routes->get('questions/get-options/(:num)', 'Teacher\Questions::getOptions/$1');
    
    // Quiz Management
    $routes->get('quizzes', 'Teacher\Quizzes::index');
    $routes->get('quizzes/add', 'Teacher\Quizzes::add');
    $routes->post('quizzes/create', 'Teacher\Quizzes::create');
    $routes->get('quizzes/edit/(:num)', 'Teacher\Quizzes::edit/$1');
    $routes->post('quizzes/update/(:num)', 'Teacher\Quizzes::update/$1');
    $routes->get('quizzes/delete/(:num)', 'Teacher\Quizzes::delete/$1');
    $routes->get('quizzes/questions/(:num)', 'Teacher\Quizzes::questions/$1');
    $routes->post('quizzes/addquestion/(:num)', 'Teacher\Quizzes::addQuestion/$1');
    $routes->get('quizzes/removequestion/(:num)/(:num)', 'Teacher\Quizzes::removeQuestion/$1/$2');
    
    // Quiz Assignment
    $routes->get('assignments', 'Teacher\Assignments::index');
    $routes->get('assignments/add', 'Teacher\Assignments::add');
    $routes->post('assignments/create', 'Teacher\Assignments::create');
    $routes->get('assignments/edit/(:num)', 'Teacher\Assignments::edit/$1');
    $routes->post('assignments/update/(:num)', 'Teacher\Assignments::update/$1');
    $routes->get('assignments/delete/(:num)', 'Teacher\Assignments::delete/$1');
    
    // Results and Reports
    $routes->get('results', 'Teacher\Results::index');
    $routes->get('results/view/(:num)', 'Teacher\Results::view/$1');
    $routes->get('results/byquiz/(:num)', 'Teacher\Results::byQuiz/$1');
    $routes->get('results/byclass/(:num)', 'Teacher\Results::byClass/$1');
    $routes->get('results/bystudent/(:num)', 'Teacher\Results::byStudent/$1');
    $routes->get('results/export/(:num)', 'Teacher\Results::export/$1');
});

// Student Routes
$routes->group('student', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Student\Dashboard::index');
    $routes->get('quizzes', 'Student\Quizzes::index');
    $routes->get('quizzes/view/(:num)', 'Student\Quizzes::view/$1');
    $routes->get('quizzes/take/(:num)', 'Student\Quizzes::take/$1');
    $routes->post('quizzes/save', 'Student\Quizzes::saveAnswer');
    $routes->post('quizzes/submit', 'Student\Quizzes::submit');
    $routes->get('results', 'Student\Results::index');
    $routes->get('results/view/(:num)', 'Student\Results::view/$1');
});

// API Routes for AJAX calls
$routes->group('api', ['filter' => 'auth'], function($routes) {
    $routes->post('quiz/save-answer', 'Api\Quiz::saveAnswer');
    $routes->get('quiz/get-time/(:num)', 'Api\Quiz::getRemainingTime/$1');
});

if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}