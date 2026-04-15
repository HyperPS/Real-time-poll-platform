-- Real-Time Live Poll Platform - Database Schema
-- Auto-loaded by Docker on first run

USE polling_system;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    is_active TINYINT(1) DEFAULT 1,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX email_idx (email),
    INDEX role_idx (role)
);

-- Polls table
CREATE TABLE IF NOT EXISTS polls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question VARCHAR(500) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX status_idx (status)
);

-- Poll options table
CREATE TABLE IF NOT EXISTS poll_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    poll_id INT NOT NULL,
    option_text VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (poll_id) REFERENCES polls(id) ON DELETE CASCADE,
    INDEX poll_id_idx (poll_id)
);

-- Votes table
CREATE TABLE IF NOT EXISTS votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    poll_id INT NOT NULL,
    option_id INT NOT NULL,
    user_id INT DEFAULT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    voted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (poll_id) REFERENCES polls(id) ON DELETE CASCADE,
    FOREIGN KEY (option_id) REFERENCES poll_options(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX ip_address_idx (ip_address),
    INDEX poll_option_idx (poll_id, ip_address, is_active),
    INDEX user_idx (user_id)
);

-- Vote history table (audit trail)
CREATE TABLE IF NOT EXISTS vote_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    poll_id INT NOT NULL,
    option_id INT,
    ip_address VARCHAR(45) NOT NULL,
    user_id INT DEFAULT NULL,
    action_type ENUM('vote', 'release', 'revote') NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    details JSON,
    FOREIGN KEY (poll_id) REFERENCES polls(id) ON DELETE CASCADE,
    INDEX action_type_idx (action_type),
    INDEX ip_address_idx (ip_address),
    INDEX timestamp_idx (timestamp)
);

-- Activity logs table (forensic web logging)
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    device_type VARCHAR(50),
    browser VARCHAR(100),
    os_platform VARCHAR(100),
    referrer VARCHAR(500),
    request_uri VARCHAR(500),
    request_method VARCHAR(10),
    session_id VARCHAR(255),
    extra_data JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX user_idx (user_id),
    INDEX action_idx (action),
    INDEX ip_idx (ip_address),
    INDEX created_idx (created_at)
);

-- Sessions table
CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id INT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    payload LONGTEXT NOT NULL,
    last_activity INT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Default admin (password: admin123)
INSERT INTO users (name, email, password, role) VALUES 
('Admin User', 'admin@polling.test', '$2y$10$YSMTyj2Ur4vQVTS34t4o6eGxJraURk9rD1tFTjuqrIES7ks48wpDe', 'admin');

-- Default user (password: user123)
INSERT INTO users (name, email, password, role) VALUES 
('Test User', 'user@polling.test', '$2y$10$rHeEUjGweOgpkEglUXWnJeYHd8RfG9xybL7Kh8jxx1PVTtmf55lkK', 'user');

-- Sample polls
INSERT INTO polls (question, status, created_by) VALUES
('What is your favorite programming language?', 'active', 1),
('Which framework do you prefer for web development?', 'active', 1),
('Best database for small to medium projects?', 'active', 1);

-- Poll options
INSERT INTO poll_options (poll_id, option_text) VALUES
(1, 'PHP'),
(1, 'Python'),
(1, 'JavaScript'),
(1, 'Java'),
(2, 'Laravel'),
(2, 'React'),
(2, 'Vue.js'),
(2, 'Angular'),
(3, 'MySQL'),
(3, 'PostgreSQL'),
(3, 'SQLite'),
(3, 'MongoDB');
