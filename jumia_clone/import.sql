-- Create database
CREATE DATABASE IF NOT EXISTS jumia_clone_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE jumia_clone_db;

-- users table
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  is_verified TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- products table
CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sku VARCHAR(50) DEFAULT NULL,
  title VARCHAR(255) NOT NULL,
  price DECIMAL(12,2) NOT NULL,
  img VARCHAR(255) DEFAULT '',
  stock INT DEFAULT 100,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- cart items (temporary cart per user)
CREATE TABLE IF NOT EXISTS cart_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  product_id INT NOT NULL,
  qty INT NOT NULL DEFAULT 1,
  added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY user_product (user_id, product_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  amount DECIMAL(12,2) NOT NULL,
  status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT,
  title VARCHAR(255) NOT NULL,
  price DECIMAL(12,2) NOT NULL,
  qty INT NOT NULL,
  subtotal DECIMAL(12,2) GENERATED ALWAYS AS (price * qty) STORED,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);


-- email verification tokens
CREATE TABLE IF NOT EXISTS email_verifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  token VARCHAR(128) NOT NULL,
  expires_at DATETIME NOT NULL,
  used TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- password reset tokens
CREATE TABLE IF NOT EXISTS password_resets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  token VARCHAR(128) NOT NULL,
  expires_at DATETIME NOT NULL,
  used TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


-- Insert sample products


INSERT INTO products (sku, title, price, img, stock) VALUES
('PH-XYZ', 'Smartphone XYZ', 109795.00, 'assets/image/smartphone.jpg', 20),
('TV-55', 'LED TV 55\"', 439000.00, 'assets/image/LED TV 55.jpg', 10),
('BL-4IN1', 'Blender 4-in-1', 24890.00, 'assets/image/Blender.jpg', 30),
('SNK-M', 'Sneakers (Men)', 33700.00, 'assets/image/Sneakers1.jpg', 25),
('SNK-M', 'Sneakers (Men)', 33700.00, 'assets/image/Sneakers2.jpg', 35),
('SNK-M', 'Sneakers (Men)', 33700.00, 'assets/image/Sneakers3.jpg', 40),
('SNK-M', 'Sneakers (Men)', 33700.00, 'assets/image/Sneakers4.jpg', 50),
('SNK-M', 'Sneakers (Men)', 33700.00, 'assets/image/Sneakers5.jpg', 100),
('SNK-M', 'Sneakers (Men)', 33700.00, 'assets/image/Sneakers6.jpg', 80),
('SNK-M', 'Sneakers (Men)', 33700.00, 'assets/image/Sneakers7.jpg', 200),
('PB-20K', 'Power Bank 20000mAh', 6908.00, 'assets/image/Power Bank.jpg', 50),
('LP-11', 'Laptop 11\" (Student)', 140999.00, 'assets/image/Laptop 11.jpg', 8);


