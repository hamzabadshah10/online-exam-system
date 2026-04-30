-- Database Schema for Secure Online Examination System
CREATE DATABASE IF NOT EXISTS online_exam_db;
USE online_exam_db;

-- Users table (Admins & Students)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student') DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Exams table
CREATE TABLE IF NOT EXISTS exams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    exam_date DATE NOT NULL,
    start_time TIME NOT NULL,
    duration_minutes INT NOT NULL,
    passing_marks DECIMAL(5,2) NOT NULL,
    total_marks DECIMAL(5,2) DEFAULT 0,
    status ENUM('pending', 'active', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Questions table (MCQs)
CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exam_id INT,
    question_text TEXT NOT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255) NOT NULL,
    option_d VARCHAR(255) NOT NULL,
    correct_option ENUM('A', 'B', 'C', 'D') NOT NULL,
    FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE
);

-- Enrollments table
CREATE TABLE IF NOT EXISTS enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exam_id INT,
    user_id INT,
    reg_number VARCHAR(50) NOT NULL,
    department VARCHAR(100),
    program VARCHAR(100),
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY (exam_id, user_id)
);

-- Results table
CREATE TABLE IF NOT EXISTS results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    enrollment_id INT,
    total_score DECIMAL(5,2) NOT NULL,
    total_correct INT NOT NULL,
    total_incorrect INT NOT NULL,
    status ENUM('Pass', 'Fail') NOT NULL,
    auto_submitted BOOLEAN DEFAULT FALSE,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    responses TEXT NULL,
    FOREIGN KEY (enrollment_id) REFERENCES enrollments(id) ON DELETE CASCADE
);

-- Insert Default Admin
-- Password is 'admin123'
INSERT IGNORE INTO users (name, email, password_hash, role) VALUES 
('System Admin', 'admin@example.com', '$2y$10$Wpw9eI99oG14k.y.Q5X.7eG2qKpwqI.IpmzP11iVq1GqO2w1u1p0S', 'admin');
