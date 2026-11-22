-- Contact Manager database schema (MySQL 8 / MariaDB 10.4+)
-- Run:  mysql -u root -p < database/schema.sql
-- Then create the admin account with:  php scripts/create_admin.php admin

CREATE DATABASE IF NOT EXISTS contact_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE contact_manager;

CREATE TABLE IF NOT EXISTS users (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,          -- output of password_hash(), never plain text
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS contacts (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(150) NOT NULL UNIQUE,
    phone      VARCHAR(20)  NOT NULL,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Sample data so the list isn't empty on first run.
INSERT INTO contacts (name, email, phone) VALUES
    ('abdullah fahad',   'abbdul@gmail.com',   '+966 50 123 4567'),
    ('omar Alshammari','omar@gmail.com',   '055-987-6543'),
    ('ahmed Hassan',    'lina@gmail.com',   '+966 54 222 3344');
