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
  - Navigation buttons: Previous, Save, Next, and Submit
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

- **Backend**: PHP 7.4+ with CodeIgniter 4 framework
- **Database**: MySQL with MySQLi driver
- **Frontend**: HTML, CSS, JavaScript, Bootstrap 5 (locally hosted)
- **Libraries**: MathJax for rendering mathematical expressions, CKEditor for rich content editing
- **Deployment**: Fully local setup (e.g., XAMPP), no reliance on external CDNs or APIs

## Installation

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Composer
- Web server (Apache/Nginx) - XAMPP, WAMP, or MAMP recommended

### Steps

1. **Clone or download the project**
   - Extract the project files to your web server directory (e.g., `htdocs` for XAMPP)

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Set up the database**
   - Create a new MySQL database named `quiz_app`
   - Import the database schema from `quiz_app_schema.sql`
   - Update database configuration in `app/Config/Database.php` if needed

4. **Configure the application**
   - Copy `env` to `.env` and update the configuration
   - Set your base URL in `app/Config/App.php` (e.g., `http://localhost/quiz_app/`)

5. **Set proper permissions** (if on Linux/Unix)
   ```bash
   chmod -R 777 writable/
   ```

6. **Download required libraries**
   - Download CodeIgniter 4.6.1 from: https://api.github.com/repos/codeigniter4/framework/zipball/v4.6.1
   - Download CKEditor 4.22.1 from: https://download.cksource.com/CKEditor/CKEditor/CKEditor%204.22.1/ckeditor_4.22.1_full.zip
   - Download MathJax from: https://github.com/mathjax/MathJax/archive/master.zip
   - Extract and place them in the appropriate directories

7. **Start the application**
   - Access the application at `http://localhost/quiz_app/`

## Default Login Credentials

- **Admin**: admin@example.com / admin123
- **Teacher**: teacher@example.com / teacher123
- **Student**: student1@example.com / student123

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

The application uses MySQL with the following main tables:

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

## Key Features Implementation

### Quiz Taking Interface
- **Single Question Display**: Shows one question at a time for focused attention
- **Question Palette**: Visual navigation with color-coded status indicators
- **Auto-Save**: Answers are automatically saved via AJAX
- **Timer**: Real-time countdown with auto-submission when time expires
- **Progress Tracking**: Visual indicators show answered vs unanswered questions

### Question Types
- **Multiple Choice**: Radio button selection with multiple options
- **True/False**: Simple binary choice questions
- **Fill in the Blank**: Text input with multiple acceptable answers

### Content Management
- **Rich Text Editor**: CKEditor integration for formatted content
- **Mathematical Expressions**: MathJax support for equations
- **Import/Export**: CSV-based bulk operations
- **Image Support**: Upload and display images in questions

### Security Features
- **Role-based Access Control**: Different permissions for admin, teacher, student
- **CSRF Protection**: Built-in security against cross-site request forgery
- **Input Validation**: Server-side validation for all user inputs
- **Session Management**: Secure session handling with timeout

## License

This project is licensed under the MIT License.

## Support

For local server setup (XAMPP, WAMP, MAMP), ensure:
- PHP 7.4+ is enabled
- MySQL service is running
- mod_rewrite is enabled for Apache
- All required PHP extensions are installed

## Acknowledgements

- CodeIgniter 4 Framework
- Bootstrap 5
- MathJax
- CKEditor
- jQuery