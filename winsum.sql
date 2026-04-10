-- MASTER DATABASE SETUP FOR WINSUM HOME DECOR
-- Database: winsumhome decor

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+07:00";

-- 1. Xóa các bảng cũ để tránh xung đột
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS order_shipping_details;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS product_variants;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

-- 2. Bảng Người dùng
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Bảng Danh mục
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    parent_id INT DEFAULT 0
);

-- 4. Bảng Sản phẩm
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    sku VARCHAR(50) UNIQUE,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- 4.1 Bảng Ảnh sản phẩm
CREATE TABLE product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    is_main BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- 5. Bảng Biến thể sản phẩm (Màu sắc, Giá, Kho)
CREATE TABLE product_variants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    color_name VARCHAR(50),
    hex_code VARCHAR(7),
    price DECIMAL(15, 2) NOT NULL,
    stock INT DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- 6. Bảng Đơn hàng
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(15, 2) NOT NULL,
    status ENUM('Pending', 'Processing', 'Confirmed', 'Completed', 'Cancelled') DEFAULT 'Pending',
    payment_method ENUM('COD', 'Bank Transfer') DEFAULT 'COD',
    shipping_fee DECIMAL(15, 2) DEFAULT 30000.00,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- 7. Bảng Chi tiết giao hàng
CREATE TABLE order_shipping_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    customer_address TEXT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- 8. Bảng Sản phẩm trong đơn hàng
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name_at_purchase VARCHAR(255),
    variant_id INT,
    quantity INT NOT NULL,
    price DECIMAL(15, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- 9. Bảng Đánh giá sản phẩm
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- DỮ LIỆU MẪU (DÀNH CHO WINSUM HOME)

-- Tài khoản Admin (Pass: admin123) và User Test (Pass: 123456)
INSERT INTO users (id, username, email, password) VALUES 
(9, 'admin', 'admin@winsumhome.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
(1, 'demo_user', 'user@gmail.com', '$2y$10$8v/2fLnxrL9hG/5.o9v1A.c8vG5rS/Y6i5eA4kO3A2mK8.iA5A6eG');

-- Danh mục decor
INSERT INTO categories (id, name) VALUES 
(1, 'Đèn Trang Trí'),
(2, 'Kệ Gỗ Trang Trí'),
(3, 'Đồ Decor Cao Cấp'),
(4, 'Nội Thất Tối Giản');

-- Sản phẩm decor
INSERT INTO products (id, category_id, sku, name, description) VALUES 
(1, 1, 'LMP-MOD-01', 'Đèn Bàn Hiện Đại', 'Mẫu đèn bàn cao cấp phong cách Bắc Âu, ánh sáng dịu nhẹ.'),
(2, 1, 'LMP-BRS-02', 'Đèn Thả Brass Luxury', 'Thiết kế tinh xảo từ đồng nguyên khối, tạo điểm nhấn cho phòng ăn.'),
(3, 2, 'SHF-WOD-03', 'Kệ Gỗ Tổ Ong', 'Bộ 3 kệ lục giác treo tường gỗ thông tự nhiên.'),
(4, 3, 'VAS-CER-04', 'Bình Hoa Gốm Thủ Công', 'Gốm men hỏa biến, nghệ thuật trang trí đẳng cấp.'),
(5, 1, 'LMP-MIN-05', 'Đèn Đứng Minimalist', 'Chiều cao 1.6m, thân kim loại sơn tĩnh điện đen mờ.');

-- Ảnh sản phẩm
INSERT INTO product_images (product_id, image_url, is_main) VALUES 
(1, 'modern_table_lamp.png', TRUE),
(1, 'modern_table_lamp_2.png', FALSE),
(2, 'pendant_light_brass.png', TRUE),
(3, 'wooden_decorative_shelf_set.png', TRUE),
(4, 'wall_sconce_modern.png', TRUE),
(5, 'luxury_floor_lamp.png', TRUE);

-- Biến thể (Màu sắc & Giá)
INSERT INTO product_variants (product_id, color_name, hex_code, price, stock) VALUES 
(1, 'Vàng Kim', '#D4AF37', 1200000, 15),
(1, 'Bạc Chrome', '#C0C0C0', 1150000, 10),
(2, 'Đồng Đỏ', '#B87333', 2850000, 5),
(3, 'Gỗ Thông', '#E3C58F', 850000, 20),
(4, 'Xanh Ngọc', '#00A86B', 450000, 30),
(5, 'Đen Nhám', '#222222', 1950000, 8);

-- Đánh giá mẫu
INSERT INTO reviews (product_id, user_id, rating, comment) VALUES 
(1, 1, 5, 'Sản phẩm đẹp tuyệt vời, rất đáng đồng tiền.'),
(2, 1, 4, 'Đèn rất sáng và sang trọng, nhưng lắp đặt hơi khó.'),
(3, 1, 5, 'Kệ rất chắc chắn, shop tư vấn nhiệt tình.');
