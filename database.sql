-- Create Database
CREATE DATABASE IF NOT EXISTS blog_app;
USE blog_app;

-- User Table
CREATE TABLE IF NOT EXISTS user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Blog Post Table
CREATE TABLE IF NOT EXISTS blogPost (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
);

-- Insert Sample User (password: admin123)
INSERT INTO user (username, email, password, role) VALUES 
('admin', 'admin@blogspace.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert Sample Blog Posts
INSERT INTO blogPost (user_id, title, content) VALUES 
(1, 'Getting Started with Web Development', '# Welcome to Web Development\n\nWeb development is an exciting journey. In this post, we will explore the fundamentals.\n\n## What You Will Learn\n\n- HTML basics\n- CSS styling\n- JavaScript interactivity\n- Backend with PHP\n\nLet us begin this amazing journey together!'),
(1, '10 Tips for Better Code', '# Writing Better Code\n\nHere are 10 essential tips:\n\n1. Write clean, readable code\n2. Use meaningful variable names\n3. Comment your code\n4. Follow coding standards\n5. Test your code regularly\n\nHappy coding!');