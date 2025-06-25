# Quiz Application

A production-ready advanced quiz web application built with CodeIgniter 4 and MySQL.

## Features

- **CMS Dashboard** for admins and teachers to manage quizzes, questions, users, and settings
- **Login System** for students using email (username) and password (set by admin)
- **Access Control**: Only logged-in students can access quizzes assigned to their class
- **Quiz Attempt Interface**:
  - One question displayed at a time
  - Numbered question palette with color indicators (grey for unattempted, green for answered)
  - Easy navigation between questions via palette
  - Navigation buttons: Previous, Save, Next, and Stop
  - Timer countdown with auto-submit when time expires
- **Question Management**:
  - Support for MCQs, Fill-in-the-Blank, and True/False (multiple types in one quiz)
  - Image support for questions and options
  - MathJax integration for mathematical equations
  - CKEditor for creating well-formatted questions
- **Result Display**:
  - Admin-configurable: show/hide results post-completion
  - Two result formats: Basic (Correct/Total) and Detailed Analysis
- **Question Import/Export**:
  - Via CSV format
  - Export function for backups and external editing

## Technology Stack

- **Backend**: PHP with CodeIgniter 4 framework, MySQL database
- **Frontend**: HTML, CSS, JavaScript, Bootstrap 5 (locally hosted)
- **Libraries**: MathJax for rendering mathematical expressions, CKEditor for rich content editing
- **Deployment**: Fully local setup (e.g., XAMPP), no reliance on external CDNs or APIs

## Installation

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Composer
- Web server (Apache/Nginx)

### Steps

1. **Clone the repository**

```bash
git clone https://github.com/yourusername/quiz-app.git
cd quiz-app
```

2. **Install dependencies**

```bash
composer install
```

3. **Set up the database**

- Create a new MySQL database
- Import the database schema from `quiz_app_schema.sql`
- Configure database connection in `app/Config/Database.php`

4. **Configure the application**

- Copy `env` to `.env` and update the configuration
- Set your base URL in `app/Config/App.php`

5. **Set proper permissions** (if on Linux/Unix)

```bash
chmod -R 777 writable/
```

6. **Start the application**

- If using XAMPP, move the project to `htdocs` directory
- Access the application at `http://localhost/quiz_app/`

## Default Login Credentials

- **Admin**: admin@example.com / admin123

## User Roles and Permissions

### Admin
- Full access to all features
- Manage users, classes, questions, quizzes
- View all results
- Configure system settings

### Teacher
- Create and manage questions
- Create and manage quizzes
- Assign quizzes to classes
- View results for their quizzes

### Student
- Take assigned quizzes
- View their results
- Track their progress

## Database Schema

The application uses the following main tables:

- `users` - User accounts (admin, teacher, student)
- `roles` - User roles
- `classes` - Classes/groups
- `student_class` - Student-class relationship
- `questions` - Quiz questions
- `question_options` - Options for multiple choice questions
- `fill_blank_answers` - Answers for fill-in-the-blank questions
- `quizzes` - Quiz information
- `quiz_questions` - Questions in a quiz
- `quiz_assignments` - Quiz assignments to classes
- `quiz_attempts` - Student quiz attempts
- `attempt_answers` - Answers in a quiz attempt
- `settings` - Application settings

## License

This project is licensed under the MIT License.

## Acknowledgements

- CodeIgniter 4 Framework
- Bootstrap 5
- MathJax
- CKEditor
- jQuery