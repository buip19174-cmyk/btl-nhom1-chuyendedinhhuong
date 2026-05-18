-- Thêm cột coins vào bảng users (nếu chưa có)
ALTER TABLE users ADD COLUMN IF NOT EXISTS coins INT DEFAULT 0;

-- Bảng lưu lịch sử chương đã mua
CREATE TABLE IF NOT EXISTS purchased_chapters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    chapter_id INT NOT NULL,
    coins_spent INT NOT NULL DEFAULT 3,
    purchased_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_purchase (user_id, chapter_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (chapter_id) REFERENCES chapters(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Bảng lịch sử nạp coin
CREATE TABLE IF NOT EXISTS coin_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount INT NOT NULL,          -- số coin
    vnd_amount INT NOT NULL,      -- số tiền VND
    type ENUM('topup','spend') NOT NULL,
    note VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
