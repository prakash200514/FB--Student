-- Database Name: student_feedback_db

CREATE DATABASE IF NOT EXISTS student_feedback_db;
USE student_feedback_db;

-- Table for Admin
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin (user: admin, pass: admin123)
INSERT IGNORE INTO admins (username, password) VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Table for Course Feedback
CREATE TABLE IF NOT EXISTS feedback_course (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(100) NOT NULL,
    register_number VARCHAR(50) NOT NULL,
    semester VARCHAR(20) NOT NULL,
    feedback_data TEXT NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_submission (register_number, semester)
);

-- Table for Teacher Feedback
CREATE TABLE IF NOT EXISTS feedback_teacher (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(100) NOT NULL,
    register_number VARCHAR(50) NOT NULL,
    semester VARCHAR(20) NOT NULL,
    teacher_name VARCHAR(100) NOT NULL,
    subject_code VARCHAR(50),
    q1 INT, q2 INT, q3 INT, q4 INT, q5 INT,
    q6 INT, q7 INT, q8 INT, q9 INT, q10 INT,
    q11 INT, q12 INT, q13 INT, q14 INT,
    suggestions TEXT,
    total_score FLOAT,
    percentage FLOAT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);



-- Table for PTA Feedback
CREATE TABLE IF NOT EXISTS feedback_pta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(100) NOT NULL,
    register_number VARCHAR(50) NOT NULL,
    q1 VARCHAR(255),
    q2 VARCHAR(255),
    q3 VARCHAR(255),
    graduation_plan VARCHAR(255),
    opinion_rating INT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
