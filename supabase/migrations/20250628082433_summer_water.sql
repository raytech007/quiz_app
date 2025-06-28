-- Quiz App Database Schema
-- Compatible with MySQL/MariaDB for local servers (XAMPP, WAMP, MAMP)

-- Drop existing database if it exists (comment out in production)
DROP DATABASE IF EXISTS quiz_app;

-- Create database
CREATE DATABASE quiz_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database
USE quiz_app;

-- User Roles Table
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default roles
INSERT INTO roles (name, description) VALUES
('admin', 'Administrator with full access'),
('teacher', 'Teacher with quiz management access'),
('student', 'Student with quiz-taking access');

-- Users Table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    role_id INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    last_login DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT
);

-- Default users (password: admin123, teacher123, student123)
INSERT INTO users (email, password, first_name, last_name, role_id) VALUES
('admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', 1),
('teacher@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John', 'Teacher', 2),
('student1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Alice', 'Student', 3),
('student2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bob', 'Student', 3);

-- Classes/Groups Table
CREATE TABLE classes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_by INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
);

-- Sample class
INSERT INTO classes (name, description, created_by) VALUES
('Mathematics 101', 'Basic Mathematics Course', 2),
('Science 101', 'Introduction to Science', 2);

-- Student-Class Relationship (Many-to-Many)
CREATE TABLE student_class (
    student_id INT NOT NULL,
    class_id INT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (student_id, class_id),
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE
);

-- Assign students to classes
INSERT INTO student_class (student_id, class_id) VALUES
(3, 1), (4, 1), (3, 2), (4, 2);

-- Question Categories
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
);

-- Sample categories
INSERT INTO categories (name, description, created_by) VALUES
('Algebra', 'Algebraic equations and expressions', 2),
('Geometry', 'Geometric shapes and calculations', 2),
('Physics', 'Basic physics concepts', 2);

-- Question Types
CREATE TABLE question_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT
);

-- Insert default question types
INSERT INTO question_types (name, description) VALUES
('multiple_choice', 'Multiple choice question with single correct answer'),
('true_false', 'True or False question'),
('fill_blank', 'Fill in the blank question');

-- Questions Table
CREATE TABLE questions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT,
    question_type_id INT NOT NULL,
    content TEXT NOT NULL,
    explanation TEXT,
    difficulty_level ENUM('easy', 'medium', 'hard') DEFAULT 'medium',
    created_by INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (question_type_id) REFERENCES question_types(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
);

-- Sample questions
INSERT INTO questions (category_id, question_type_id, content, explanation, difficulty_level, created_by) VALUES
(1, 1, 'What is 2 + 2?', 'Basic addition problem', 'easy', 2),
(1, 2, 'Is 5 > 3?', 'Basic comparison', 'easy', 2),
(2, 3, 'A triangle has _____ sides.', 'Basic geometry fact', 'easy', 2),
(1, 1, 'What is the value of x in the equation 2x + 4 = 10?', 'Simple algebraic equation', 'medium', 2),
(3, 2, 'Does light travel faster than sound?', 'Basic physics concept', 'easy', 2);

-- Question Options (for multiple choice and true/false)
CREATE TABLE question_options (
    id INT PRIMARY KEY AUTO_INCREMENT,
    question_id INT NOT NULL,
    option_text TEXT NOT NULL,
    is_correct BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);

-- Sample options
INSERT INTO question_options (question_id, option_text, is_correct, sort_order) VALUES
-- Question 1: What is 2 + 2?
(1, '3', FALSE, 0),
(1, '4', TRUE, 1),
(1, '5', FALSE, 2),
(1, '6', FALSE, 3),
-- Question 2: Is 5 > 3?
(2, 'True', TRUE, 0),
(2, 'False', FALSE, 1),
-- Question 4: What is the value of x in 2x + 4 = 10?
(4, 'x = 2', FALSE, 0),
(4, 'x = 3', TRUE, 1),
(4, 'x = 4', FALSE, 2),
(4, 'x = 5', FALSE, 3),
-- Question 5: Does light travel faster than sound?
(5, 'True', TRUE, 0),
(5, 'False', FALSE, 1);

-- Fill in the Blank Answers
CREATE TABLE fill_blank_answers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    question_id INT NOT NULL,
    answer_text VARCHAR(255) NOT NULL,
    is_case_sensitive BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);

-- Sample fill blank answers
INSERT INTO fill_blank_answers (question_id, answer_text, is_case_sensitive) VALUES
(3, 'three', FALSE),
(3, '3', FALSE);

-- Quiz Table
CREATE TABLE quizzes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    time_limit INT DEFAULT 0, -- in minutes, 0 means no time limit
    pass_percentage DECIMAL(5,2) DEFAULT 60.00,
    is_randomized BOOLEAN DEFAULT FALSE,
    show_results BOOLEAN DEFAULT TRUE,
    show_answers BOOLEAN DEFAULT FALSE,
    max_attempts INT DEFAULT 1, -- 0 means unlimited
    created_by INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
);

-- Sample quizzes
INSERT INTO quizzes (title, description, time_limit, pass_percentage, show_results, show_answers, max_attempts, created_by) VALUES
('Basic Math Quiz', 'A simple quiz covering basic mathematical concepts', 30, 70.00, TRUE, TRUE, 2, 2),
('Science Fundamentals', 'Basic science concepts quiz', 45, 60.00, TRUE, FALSE, 1, 2);

-- Quiz Questions (Many-to-Many relationship)
CREATE TABLE quiz_questions (
    quiz_id INT NOT NULL,
    question_id INT NOT NULL,
    sort_order INT DEFAULT 0,
    points DECIMAL(5,2) DEFAULT 1.00,
    PRIMARY KEY (quiz_id, question_id),
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);

-- Add questions to quizzes
INSERT INTO quiz_questions (quiz_id, question_id, sort_order, points) VALUES
(1, 1, 0, 1.00),
(1, 2, 1, 1.00),
(1, 3, 2, 1.00),
(1, 4, 3, 2.00),
(2, 5, 0, 1.00),
(2, 3, 1, 1.00);

-- Quiz Assignments (to classes)
CREATE TABLE quiz_assignments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    quiz_id INT NOT NULL,
    class_id INT NOT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
);

-- Sample assignments
INSERT INTO quiz_assignments (quiz_id, class_id, start_time, end_time, created_by) VALUES
(1, 1, NOW(), DATE_ADD(NOW(), INTERVAL 7 DAY), 2),
(2, 2, NOW(), DATE_ADD(NOW(), INTERVAL 14 DAY), 2);

-- Quiz Attempts
CREATE TABLE quiz_attempts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    quiz_id INT NOT NULL,
    user_id INT NOT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME DEFAULT NULL,
    is_completed BOOLEAN DEFAULT FALSE,
    total_points DECIMAL(10,2) DEFAULT 0.00,
    score_percentage DECIMAL(5,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Quiz Attempt Answers
CREATE TABLE attempt_answers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    attempt_id INT NOT NULL,
    question_id INT NOT NULL,
    user_answer TEXT,
    is_correct BOOLEAN DEFAULT FALSE,
    points_earned DECIMAL(5,2) DEFAULT 0.00,
    answer_time DATETIME NOT NULL,
    FOREIGN KEY (attempt_id) REFERENCES quiz_attempts(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);

-- Settings Table
CREATE TABLE settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    description TEXT,
    is_public BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default settings
INSERT INTO settings (setting_key, setting_value, description, is_public) VALUES
('site_name', 'Quiz Application', 'Site name displayed in title and headers', TRUE),
('contact_email', 'admin@example.com', 'Contact email address', TRUE),
('results_default_view', 'detailed', 'Default view for quiz results (basic or detailed)', FALSE),
('enable_math_rendering', '1', 'Enable MathJax for mathematical expressions', FALSE),
('max_file_upload_size', '5', 'Maximum file upload size in MB', FALSE),
('session_timeout', '7200', 'Session timeout in seconds', FALSE);

-- Activity Logs Table for system activity
CREATE TABLE activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    activity_type VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Create indexes for performance optimization
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(role_id);
CREATE INDEX idx_users_active ON users(is_active);
CREATE INDEX idx_questions_category ON questions(category_id);
CREATE INDEX idx_questions_type ON questions(question_type_id);
CREATE INDEX idx_questions_active ON questions(is_active);
CREATE INDEX idx_quiz_attempts_user ON quiz_attempts(user_id);
CREATE INDEX idx_quiz_attempts_quiz ON quiz_attempts(quiz_id);
CREATE INDEX idx_quiz_attempts_completed ON quiz_attempts(is_completed);
CREATE INDEX idx_attempt_answers_attempt ON attempt_answers(attempt_id);
CREATE INDEX idx_quiz_assignments_dates ON quiz_assignments(start_time, end_time);
CREATE INDEX idx_quiz_assignments_class ON quiz_assignments(class_id);
CREATE INDEX idx_activity_logs_user ON activity_logs(user_id);
CREATE INDEX idx_activity_logs_type ON activity_logs(activity_type);
CREATE INDEX idx_activity_logs_date ON activity_logs(created_at);

-- Sample data for demonstration
-- Insert some sample quiz attempts for testing
INSERT INTO quiz_attempts (quiz_id, user_id, start_time, end_time, is_completed, total_points, score_percentage) VALUES
(1, 3, DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY), TRUE, 4.00, 80.00),
(1, 4, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY), TRUE, 3.00, 60.00);

-- Insert sample attempt answers
INSERT INTO attempt_answers (attempt_id, question_id, user_answer, is_correct, points_earned, answer_time) VALUES
-- Alice's attempt (attempt_id = 1)
(1, 1, '4', TRUE, 1.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(1, 2, 'True', TRUE, 1.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(1, 3, 'three', TRUE, 1.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(1, 4, '3', TRUE, 2.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
-- Bob's attempt (attempt_id = 2)
(2, 1, '4', TRUE, 1.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(2, 2, 'False', FALSE, 0.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(2, 3, 'three', TRUE, 1.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(2, 4, '2', FALSE, 0.00, DATE_SUB(NOW(), INTERVAL 1 DAY));

-- Insert sample activity logs
INSERT INTO activity_logs (user_id, activity_type, description, ip_address) VALUES
(1, 'login', 'Admin user logged in', '127.0.0.1'),
(2, 'login', 'Teacher user logged in', '127.0.0.1'),
(3, 'login', 'Student user logged in', '127.0.0.1'),
(2, 'quiz_create', 'Created quiz: Basic Math Quiz', '127.0.0.1'),
(3, 'quiz_start', 'Started quiz: Basic Math Quiz', '127.0.0.1'),
(3, 'quiz_submit', 'Submitted quiz: Basic Math Quiz', '127.0.0.1');