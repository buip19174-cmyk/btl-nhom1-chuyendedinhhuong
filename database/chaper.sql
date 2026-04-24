CREATE TABLE chapters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    story_id INT NOT NULL,
    chapter_number FLOAT NOT NULL, -- Dùng FLOAT để hỗ trợ chương phụ (VD: Chương 1.5)
    title VARCHAR(255),
    content LONGTEXT NOT NULL,
    view_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (story_id) REFERENCES stories(id) ON DELETE CASCADE,
    UNIQUE KEY unique_chapter (story_id, chapter_number)
);
