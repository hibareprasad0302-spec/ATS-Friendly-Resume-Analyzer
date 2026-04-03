CREATE DATABASE IF NOT EXISTS ats_resume_analyzer
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE ats_resume_analyzer;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB;

-- Skills master table (admin-curated skill dictionary)
CREATE TABLE IF NOT EXISTS skills_master (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    skill_name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL DEFAULT 'general',
    aliases JSON DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_skill (skill_name),
    INDEX idx_category (category)
) ENGINE=InnoDB;

-- Resume analysis reports
CREATE TABLE IF NOT EXISTS resume_reports (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED DEFAULT NULL,

    -- Input data
    original_filename VARCHAR(255) NOT NULL,
    stored_filename VARCHAR(255) NOT NULL,
    file_type ENUM('pdf', 'docx') NOT NULL,
    job_description TEXT NOT NULL,
    job_role VARCHAR(150) DEFAULT NULL,

    -- Extracted text
    extracted_text MEDIUMTEXT DEFAULT NULL,

    -- Scores (100 total)
    score_keyword DECIMAL(5,2) NOT NULL DEFAULT 0,
    score_skills DECIMAL(5,2) NOT NULL DEFAULT 0,
    score_sections DECIMAL(5,2) NOT NULL DEFAULT 0,
    score_projects DECIMAL(5,2) NOT NULL DEFAULT 0,
    score_experience DECIMAL(5,2) NOT NULL DEFAULT 0,
    score_education DECIMAL(5,2) NOT NULL DEFAULT 0,
    score_formatting DECIMAL(5,2) NOT NULL DEFAULT 0,
    total_score DECIMAL(5,2) NOT NULL DEFAULT 0,

    -- Detailed results (JSON)
    matched_keywords JSON DEFAULT NULL,
    missing_keywords JSON DEFAULT NULL,
    matched_skills JSON DEFAULT NULL,
    missing_skills JSON DEFAULT NULL,
    detected_sections JSON DEFAULT NULL,
    missing_sections JSON DEFAULT NULL,
    suggestions JSON DEFAULT NULL,
    full_report JSON DEFAULT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_created (created_at),
    INDEX idx_total_score (total_score)
) ENGINE=InnoDB;
