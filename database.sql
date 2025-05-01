-- Create database
CREATE DATABASE IF NOT EXISTS sports_shop;
USE sports_shop;

-- Create users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    address TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create products table
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category ENUM('sepakbola', 'futsal', 'running', 'bulutangkis') NOT NULL,
    name VARCHAR(255) NOT NULL,
    type ENUM('shoes', 'jersey', 'socks', 'finger_tape', 'knee_pad', 'headband') NOT NULL,
    brand VARCHAR(100),
    size VARCHAR(10),
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create orders table
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    total_amount DECIMAL(10, 2) NOT NULL,
    shipping_address TEXT NOT NULL,
    payment_method ENUM('BCA', 'BRI', 'DANA') NOT NULL,
    status ENUM('pending', 'confirmed', 'shipped', 'delivered') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Create order_details table
CREATE TABLE order_details (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Insert Football Products
-- Football Shoes
INSERT INTO products (category, name, type, brand, size, price, stock) VALUES
-- Nike Football Shoes
('sepakbola', 'Nike Mercurial', 'shoes', 'Nike', '32', 2000000.00, 10),
('sepakbola', 'Nike Mercurial', 'shoes', 'Nike', '33', 2000000.00, 10),
-- ... (repeat for sizes 34-45)

-- Adidas Football Shoes
('sepakbola', 'Adidas Predator', 'shoes', 'Adidas', '32', 2000000.00, 10),
('sepakbola', 'Adidas Predator', 'shoes', 'Adidas', '33', 2000000.00, 10),
-- ... (repeat for sizes 34-45)

-- Puma Football Shoes
('sepakbola', 'Puma Future', 'shoes', 'Puma', '32', 2000000.00, 10),
-- ... (repeat for all sizes and other brands)

-- Football Jerseys
-- Indonesia
('sepakbola', 'Indonesia Home Jersey', 'jersey', NULL, 'S', 1000000.00, 10),
('sepakbola', 'Indonesia Home Jersey', 'jersey', NULL, 'M', 1000000.00, 10),
('sepakbola', 'Indonesia Home Jersey', 'jersey', NULL, 'L', 1000000.00, 10),
('sepakbola', 'Indonesia Home Jersey', 'jersey', NULL, 'XL', 1000000.00, 10),
('sepakbola', 'Indonesia Away Jersey', 'jersey', NULL, 'S', 1000000.00, 10),
-- ... (repeat for other sizes and teams)

-- Futsal Products
-- Futsal Jerseys
('futsal', 'Indonesia Futsal Jersey', 'jersey', NULL, 'S', 750000.00, 10),
('futsal', 'Indonesia Futsal Jersey', 'jersey', NULL, 'M', 750000.00, 10),
-- ... (repeat for other sizes and teams)

-- Futsal Shoes
('futsal', 'Nike Futsal Pro', 'shoes', 'Nike', '32', 1000000.00, 10),
('futsal', 'Nike Futsal Pro', 'shoes', 'Nike', '33', 1000000.00, 10),
-- ... (repeat for all sizes and brands)

-- Running Products
-- Running Jerseys
('running', 'Pro Running Jersey 1', 'jersey', NULL, 'S', 500000.00, 10),
('running', 'Pro Running Jersey 1', 'jersey', NULL, 'M', 500000.00, 10),
-- ... (repeat for all sizes)

-- Running Shoes
('running', 'Nike Air Zoom', 'shoes', 'Nike', '32', 1000000.00, 10),
('running', 'Nike Air Zoom', 'shoes', 'Nike', '33', 1000000.00, 10),
-- ... (repeat for all sizes and brands)

-- Badminton Products
-- Badminton Jerseys
('bulutangkis', 'Indonesia Badminton Jersey', 'jersey', NULL, 'S', 500000.00, 10),
('bulutangkis', 'Indonesia Badminton Jersey', 'jersey', NULL, 'M', 500000.00, 10),
-- ... (repeat for all sizes and countries)

-- Badminton Shoes
('bulutangkis', 'Pro Badminton Shoes', 'shoes', NULL, '32', 1500000.00, 10),
('bulutangkis', 'Pro Badminton Shoes', 'shoes', NULL, '33', 1500000.00, 10);
-- ... (repeat for all sizes)

-- Create admin table
CREATE TABLE admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin
INSERT INTO admin (username, password) VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- password: password
