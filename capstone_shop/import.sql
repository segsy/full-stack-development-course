CREATE DATABASE IF NOT EXISTS capstone_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE capstone_shop;

-- users
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  is_verified TINYINT(1) DEFAULT 0,
  role VARCHAR(50) DEFAULT 'customer',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- products
CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sku VARCHAR(50),
  title VARCHAR(255) NOT NULL,
  description TEXT,
  price DECIMAL(12,2) NOT NULL,
  img VARCHAR(255),
  stock INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- cart_items (persistent per user)
CREATE TABLE cart_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  product_id INT NOT NULL,
  qty INT NOT NULL DEFAULT 1,
  added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_user_product (user_id, product_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- orders & order_items
CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  total DECIMAL(12,2) NOT NULL,
  status VARCHAR(50) DEFAULT 'pending',
  shipping_address TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT,
  title VARCHAR(255),
  price DECIMAL(12,2),
  qty INT,
  subtotal DECIMAL(12,2),
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- email verification tokens
CREATE TABLE email_verifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  token VARCHAR(128) NOT NULL,
  expires_at DATETIME NOT NULL,
  used TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- password reset tokens
CREATE TABLE password_resets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  token VARCHAR(128) NOT NULL,
  expires_at DATETIME NOT NULL,
  used TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample products
INSERT INTO products (sku, title,description, price, img, stock) VALUES
('Sneakers', 'Sneakers (Men)','Comfortable sneakers.', 109795.00, 'assets/image/_1.jpg', 20),
('Sneakers', 'Sneakers (Men)','Comfortable sneakers.', 109795.00, 'assets/image/_2.jpg', 20),
('BL-4IN1', 'Sneakers (Men)','Comfortable sneakers.', 24890.00, 'assets/image/_3.jpg', 30),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_4.jpg', 25),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_5.jpg', 35),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_6.jpg', 40),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_7.jpg', 50),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_8.jpg', 100),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_9.jpg', 80),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_10.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_11.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_12.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_13.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_14.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_15.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_16.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_17.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_18.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_19.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_20.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_21.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_22.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_23.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_24.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_25.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_26.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_27.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_28.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_29.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_30.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_31.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_32.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_33.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_34.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_35.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_36.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_37.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_38.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_39.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_40.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_41.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_42.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_43.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_44.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_45.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_46.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_47.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_48.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_49.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_50.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_51.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_52.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_53.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_54.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_55.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_56.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_57.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_58.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_59.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_60.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_61.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_62.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_63.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_64.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_65.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_66.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_67.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_68.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_69.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_70.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_71.jpg', 200),
('SNK-M', 'Sneakers (Men)','Comfortable sneakers.', 33700.00, 'assets/image/_72.jpg', 200);