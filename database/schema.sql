-- National County Sports System - Database Schema
-- Run this file to initialize the database

CREATE DATABASE IF NOT EXISTS national_county_sports_system;
USE national_county_sports_system;

-- Drop existing tables in reverse dependency order (children first)
DROP TABLE IF EXISTS match_goals;
DROP TABLE IF EXISTS gallery_photos;
DROP TABLE IF EXISTS match_squad_players;
DROP TABLE IF EXISTS match_report_cards;
DROP TABLE IF EXISTS match_reports;
DROP TABLE IF EXISTS matches;
DROP TABLE IF EXISTS documents;
DROP TABLE IF EXISTS activity_logs;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS approval_workflow;
DROP TABLE IF EXISTS players;
DROP TABLE IF EXISTS sports_disciplines;
DROP TABLE IF EXISTS counties;
DROP TABLE IF EXISTS users;

-- Users table (all roles)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('super_admin', 'county_coordinator', 'association_admin', 'match_commissioner') NOT NULL,
    county_id INT NULL,
    association_id INT NULL,
    phone VARCHAR(20),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- County groupings
CREATE TABLE counties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    group_label ENUM('A', 'B', 'C', 'D') NOT NULL,
    code VARCHAR(10) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sports disciplines
CREATE TABLE sports_disciplines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    association_name VARCHAR(100) NOT NULL,
    association_code VARCHAR(10) UNIQUE NOT NULL,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Players registration
CREATE TABLE players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nir_number VARCHAR(20) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    nationality VARCHAR(100) NOT NULL DEFAULT 'Liberian',
    year_of_nscm VARCHAR(4) NOT NULL,
    age TINYINT UNSIGNED NOT NULL DEFAULT 0,
    city VARCHAR(100) NOT NULL DEFAULT '',
    last_club VARCHAR(200) NOT NULL DEFAULT '',
    current_club VARCHAR(200) NOT NULL DEFAULT '',
    county_id INT NOT NULL,
    primary_position VARCHAR(50) NOT NULL,
    emergency_contact_name VARCHAR(100) NOT NULL,
    emergency_contact_phone VARCHAR(20) NOT NULL,
    emergency_contact_relation VARCHAR(50) NOT NULL,
    medical_fitness_status ENUM('fit', 'unfit', 'pending_review') DEFAULT 'pending_review',
    medical_notes TEXT,
    photo_path VARCHAR(255),
    sport_discipline_id INT NOT NULL,
    registered_by INT NOT NULL,
    status ENUM('draft', 'submitted', 'approved', 'rejected') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (county_id) REFERENCES counties(id) ON DELETE RESTRICT,
    FOREIGN KEY (sport_discipline_id) REFERENCES sports_disciplines(id) ON DELETE RESTRICT,
    FOREIGN KEY (registered_by) REFERENCES users(id) ON DELETE RESTRICT
);

-- Approval workflow
CREATE TABLE approval_workflow (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_id INT NOT NULL,
    action ENUM('submit', 'approve', 'reject', 'return_for_revision') NOT NULL,
    action_by INT NOT NULL,
    role_at_time ENUM('county_coordinator', 'association_admin', 'super_admin') NOT NULL,
    comments TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    FOREIGN KEY (action_by) REFERENCES users(id) ON DELETE RESTRICT
);

-- Notifications
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'success', 'warning', 'danger') DEFAULT 'info',
    is_read TINYINT(1) DEFAULT 0,
    link VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Activity logs
CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Documents uploaded by admin
CREATE TABLE documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(100) NOT NULL,
    file_size INT NOT NULL,
    uploaded_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE RESTRICT
);

-- Match fixtures and results
CREATE TABLE matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sport_discipline_id INT NOT NULL,
    home_county_id INT NOT NULL,
    away_county_id INT NOT NULL,
    home_score INT DEFAULT NULL,
    away_score INT DEFAULT NULL,
    match_date DATETIME NOT NULL,
    status ENUM('scheduled','live','completed') DEFAULT 'scheduled',
    timer_kickoff DATETIME DEFAULT NULL,
    timer_offset INT NOT NULL DEFAULT 0,
    group_label ENUM('A','B','C','D') NOT NULL,
    round VARCHAR(50) DEFAULT 'Group Stage',
    notes TEXT,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sport_discipline_id) REFERENCES sports_disciplines(id) ON DELETE RESTRICT,
    FOREIGN KEY (home_county_id) REFERENCES counties(id) ON DELETE RESTRICT,
    FOREIGN KEY (away_county_id) REFERENCES counties(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
);

-- Indexes for performance
CREATE INDEX idx_players_county ON players(county_id);
CREATE INDEX idx_players_sport ON players(sport_discipline_id);
CREATE INDEX idx_players_status ON players(status);
CREATE INDEX idx_approval_player ON approval_workflow(player_id);
CREATE INDEX idx_notifications_user ON notifications(user_id);
CREATE INDEX idx_logs_user ON activity_logs(user_id);
CREATE INDEX idx_documents_uploaded ON documents(uploaded_by);
CREATE INDEX idx_matches_sport ON matches(sport_discipline_id);
CREATE INDEX idx_matches_group ON matches(group_label);
CREATE INDEX idx_matches_status ON matches(status);

-- Match commissioner reports (yellow/red cards, notes)
CREATE TABLE match_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    match_id INT NOT NULL,
    commissioner_id INT NOT NULL,
    home_yellow_cards INT DEFAULT 0,
    home_red_cards INT DEFAULT 0,
    away_yellow_cards INT DEFAULT 0,
    away_red_cards INT DEFAULT 0,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
    FOREIGN KEY (commissioner_id) REFERENCES users(id) ON DELETE RESTRICT
);

-- Migration: add match_commissioner role to existing installations
ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'county_coordinator', 'association_admin', 'match_commissioner') NOT NULL;

-- Match report cards (individual player card incidents)
CREATE TABLE match_report_cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    report_id INT NOT NULL,
    team ENUM('home', 'away') NOT NULL,
    card_type ENUM('yellow', 'red') NOT NULL,
    jersey_number INT NOT NULL,
    player_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (report_id) REFERENCES match_reports(id) ON DELETE CASCADE
);

-- Match squad players (starting XI and substitutes)
CREATE TABLE match_squad_players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    report_id INT NOT NULL,
    team ENUM('home', 'away') NOT NULL,
    player_type ENUM('starting', 'substitute') NOT NULL,
    jersey_number INT NOT NULL,
    player_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (report_id) REFERENCES match_reports(id) ON DELETE CASCADE
);

-- Contact messages
CREATE TABLE contact_messages (
    id INT(11) NOT NULL AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Match goals (goal scorers)
CREATE TABLE match_goals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    match_id INT NOT NULL,
    team ENUM('home','away') NOT NULL,
    player_name VARCHAR(100) NOT NULL,
    jersey_number INT NOT NULL,
    minute INT DEFAULT NULL,
    goal_type ENUM('normal','penalty','own_goal') DEFAULT 'normal',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Gallery photos
CREATE TABLE gallery_photos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_slug VARCHAR(100) NOT NULL,
    category_title VARCHAR(200) NOT NULL,
    photo_path VARCHAR(255) NOT NULL,
    caption VARCHAR(255) DEFAULT '',
    sort_order INT DEFAULT 0,
    uploaded_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Media videos
CREATE TABLE videos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    video_type ENUM('file','url') NOT NULL DEFAULT 'file',
    video_path VARCHAR(255) NULL,
    embed_url VARCHAR(500) NULL,
    description TEXT NULL,
    thumbnail_path VARCHAR(255) NULL,
    sort_order INT DEFAULT 0,
    uploaded_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
