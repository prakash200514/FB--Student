Student Feedback System 🎓

A modern, web-based Academic Evaluation and Feedback System built with PHP and MySQL. This portal allows students to provide structured feedback on courses, faculty performance, and the Parent-Teacher Association (PTA).

🚀 Features

- **Dynamic Student Login**: Smart login system that categorizes students into UG (6 semesters) and PG (4 semesters).
- **Course Feedback**: Evaluation of curriculum content, structure, and learning outcomes.
- **Teacher Evaluation**: 14-point rating system for faculty teaching effectiveness and subject mastery.
- **PTA Feedback**: Specialized review section for Parent-Teacher Association activities and parent involvement.
- **Real-time Analytics**: Built-in statistics dashboard to view feedback trends (Integrated for Admin/Staff).
- **Responsive UI**: Premium glassmorphic design built with Bootstrap 5 and smooth CSS transitions.

🛠️ Tech Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript
- **Styling Framework**: Bootstrap 5
- **Icons**: FontAwesome 6

  ⚙️ Installation & Setup

1. **Clone the Project**:
   ```bash
   git clone https://github.com/prakash200514/FB--Student.git
   ```

2. **Move to Web Directory**:
   Copy the project folder to `C:\xampp\htdocs\` (XAMPP users).

3. **Database Setup**:
   - Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
   - Create a new database named `student_feedback_db`.
   - Import the `database.sql` file provided in the repository.

4. **Configuration**:
   - Open `config.php`.
   - Update the database credentials to match your local setup:
     ```php
     define('DB_SERVER', 'localhost');
     define('DB_USERNAME', 'root');
     define('DB_PASSWORD', 'your_password'); // Standard is empty in XAMPP
     define('DB_NAME', 'student_feedback_db');
     ```

5. **Run the Application**:
   - Start Apache and MySQL in your local server control panel.
   - Navigate to `http://localhost/student-feedback` in your browser.

📋 Prerequisites

- **Web Server**: XAMPP / WAMP / MAMP or any PHP-enabled server.
- **PHP Version**: 7.4 or higher.
- **Database**: MySQL/MariaDB.

📂 Project Structure

```text
├── admin/               # Administrative controls
├── assets/              # UI assets (CSS, Images)
├── config.php           # Database connection configuration
├── dashboard.php        # Main navigation hub for students
├── index.php            # Welcome & Login page
├── course_feedback.php   # Course evaluation module
├── teacher_feedback.php  # Faculty evaluation module
├── pta_feedback.php      # PTA review module
├── statistics.php       # Feedback analytics dashboard
└── database.sql         # SQL schema for the system
