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
    type ENUM('shoes', 'jersey', 'socks', 'finger_tape', 'knee_pad', 'headband', 'racket') NOT NULL,
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

-- Create admin table
CREATE TABLE admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin
INSERT INTO admin (username, password) VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- password: password

-- Insert Football Products
-- Football Shoes (Nike)
INSERT INTO products (category, name, type, brand, size, price, stock) VALUES
('sepakbola', 'Nike Mercurial Superfly', 'shoes', 'Nike', '32', 2000000.00, 10),
('sepakbola', 'Nike Mercurial Superfly', 'shoes', 'Nike', '33', 2000000.00, 10),
('sepakbola', 'Nike Mercurial Superfly', 'shoes', 'Nike', '34', 2000000.00, 10),
('sepakbola', 'Nike Mercurial Superfly', 'shoes', 'Nike', '35', 2000000.00, 10),
('sepakbola', 'Nike Mercurial Superfly', 'shoes', 'Nike', '36', 2000000.00, 10),
('sepakbola', 'Nike Mercurial Superfly', 'shoes', 'Nike', '37', 2000000.00, 10),
('sepakbola', 'Nike Mercurial Superfly', 'shoes', 'Nike', '38', 2000000.00, 10),
('sepakbola', 'Nike Mercurial Superfly', 'shoes', 'Nike', '39', 2000000.00, 10),
('sepakbola', 'Nike Mercurial Superfly', 'shoes', 'Nike', '40', 2000000.00, 10),
('sepakbola', 'Nike Mercurial Superfly', 'shoes', 'Nike', '41', 2000000.00, 10),
('sepakbola', 'Nike Mercurial Superfly', 'shoes', 'Nike', '42', 2000000.00, 10),
('sepakbola', 'Nike Mercurial Superfly', 'shoes', 'Nike', '43', 2000000.00, 10),
('sepakbola', 'Nike Mercurial Superfly', 'shoes', 'Nike', '44', 2000000.00, 10),
('sepakbola', 'Nike Mercurial Superfly', 'shoes', 'Nike', '45', 2000000.00, 10);

-- Football Shoes (Adidas)
INSERT INTO products (category, name, type, brand, size, price, stock) VALUES
('sepakbola', 'Adidas Predator Edge', 'shoes', 'Adidas', '32', 2000000.00, 10),
('sepakbola', 'Adidas Predator Edge', 'shoes', 'Adidas', '33', 2000000.00, 10),
-- ... (repeat for sizes 34-45)

-- Football Shoes (Puma)
INSERT INTO products (category, name, type, brand, size, price, stock) VALUES
('sepakbola', 'Puma Ultra', 'shoes', 'Puma', '32', 2000000.00, 10),
('sepakbola', 'Puma Ultra', 'shoes', 'Puma', '33', 2000000.00, 10),
-- ... (repeat for sizes 34-45)

-- Football Shoes (Specs)
INSERT INTO products (category, name, type, brand, size, price, stock) VALUES
('sepakbola', 'Specs Accelerator', 'shoes', 'Specs', '32', 2000000.00, 10),
('sepakbola', 'Specs Accelerator', 'shoes', 'Specs', '33', 2000000.00, 10),
-- ... (repeat for sizes 34-45)

-- Football Shoes (Ortusheight)
INSERT INTO products (category, name, type, brand, size, price, stock) VALUES
('sepakbola', 'Ortusheight Velocity', 'shoes', 'Ortusheight', '32', 2000000.00, 10),
('sepakbola', 'Ortusheight Velocity', 'shoes', 'Ortusheight', '33', 2000000.00, 10),
-- ... (repeat for sizes 34-45)

-- Football Jerseys
INSERT INTO products (category, name, type, brand, size, price, stock) VALUES
-- Indonesia
('sepakbola', 'Indonesia Home Jersey', 'jersey', NULL, 'S', 1000000.00, 10),
('sepakbola', 'Indonesia Home Jersey', 'jersey', NULL, 'M', 1000000.00, 10),
('sepakbola', 'Indonesia Home Jersey', 'jersey', NULL, 'L', 1000000.00, 10),
('sepakbola', 'Indonesia Home Jersey', 'jersey', NULL, 'XL', 1000000.00, 10),
('sepakbola', 'Indonesia Away Jersey', 'jersey', NULL, 'S', 1000000.00, 10),
('sepakbola', 'Indonesia Away Jersey', 'jersey', NULL, 'M', 1000000.00, 10),
('sepakbola', 'Indonesia Away Jersey', 'jersey', NULL, 'L', 1000000.00, 10),
('sepakbola', 'Indonesia Away Jersey', 'jersey', NULL, 'XL', 1000000.00, 10),

-- Manchester United
('sepakbola', 'Manchester United Home Jersey', 'jersey', NULL, 'S', 1000000.00, 10),
-- ... (repeat for M, L, XL and Away Jersey)

-- AC Milan
('sepakbola', 'AC Milan Home Jersey', 'jersey', NULL, 'S', 1000000.00, 10),
-- ... (repeat for M, L, XL and Away Jersey)

-- Real Madrid
('sepakbola', 'Real Madrid Home Jersey', 'jersey', NULL, 'S', 1000000.00, 10),
-- ... (repeat for M, L, XL and Away Jersey)

-- Barcelona
('sepakbola', 'Barcelona Home Jersey', 'jersey', NULL, 'S', 1000000.00, 10),
-- ... (repeat for M, L, XL and Away Jersey)

-- AL-NASR
('sepakbola', 'AL-NASR Home Jersey', 'jersey', NULL, 'S', 1000000.00, 10),
-- ... (repeat for M, L, XL and Away Jersey)

-- PSG
('sepakbola', 'PSG Home Jersey', 'jersey', NULL, 'S', 1000000.00, 10),
-- ... (repeat for M, L, XL and Away Jersey)

-- Liverpool
('sepakbola', 'Liverpool Home Jersey', 'jersey', NULL, 'S', 1000000.00, 10),
-- ... (repeat for M, L, XL and Away Jersey)

-- Persib
('sepakbola', 'Persib Home Jersey', 'jersey', NULL, 'S', 1000000.00, 10),
-- ... (repeat for M, L, XL and Away Jersey)

-- Persija
('sepakbola', 'Persija Home Jersey', 'jersey', NULL, 'S', 1000000.00, 10),
-- ... (repeat for M, L, XL and Away Jersey)

-- Football Socks
INSERT INTO products (category, name, type, price, stock) VALUES
('sepakbola', 'Pro Football Socks 1', 'socks', 150000.00, 10),
('sepakbola', 'Pro Football Socks 2', 'socks', 150000.00, 10),
('sepakbola', 'Pro Football Socks 3', 'socks', 150000.00, 10),
('sepakbola', 'Pro Football Socks 4', 'socks', 150000.00, 10),
('sepakbola', 'Pro Football Socks 5', 'socks', 150000.00, 10),
('sepakbola', 'Pro Football Socks 6', 'socks', 150000.00, 10),
('sepakbola', 'Pro Football Socks 7', 'socks', 150000.00, 10),
('sepakbola', 'Pro Football Socks 8', 'socks', 150000.00, 10),
('sepakbola', 'Pro Football Socks 9', 'socks', 150000.00, 10),
('sepakbola', 'Pro Football Socks 10', 'socks', 150000.00, 10);

-- Futsal Products
-- Futsal Jerseys
INSERT INTO products (category, name, type, brand, size, price, stock) VALUES
-- Timnas Indonesia
('futsal', 'Timnas Indonesia Futsal Jersey', 'jersey', NULL, 'S', 750000.00, 10),
('futsal', 'Timnas Indonesia Futsal Jersey', 'jersey', NULL, 'M', 750000.00, 10),
('futsal', 'Timnas Indonesia Futsal Jersey', 'jersey', NULL, 'L', 750000.00, 10),
('futsal', 'Timnas Indonesia Futsal Jersey', 'jersey', NULL, 'XL', 750000.00, 10),

-- Bintang Timur Surabaya
('futsal', 'Bintang Timur Surabaya Jersey', 'jersey', NULL, 'S', 750000.00, 10),
-- ... (repeat for M, L, XL)

-- Blacksteel Manokwari
('futsal', 'Blacksteel Manokwari Jersey', 'jersey', NULL, 'S', 750000.00, 10),
-- ... (repeat for M, L, XL)

-- Vamos Mataram
('futsal', 'Vamos Mataram Jersey', 'jersey', NULL, 'S', 750000.00, 10),
-- ... (repeat for M, L, XL)

-- Pendekar United
('futsal', 'Pendekar United Jersey', 'jersey', NULL, 'S', 750000.00, 10),
-- ... (repeat for M, L, XL)

-- Futsal Shoes
INSERT INTO products (category, name, type, brand, size, price, stock) VALUES
-- Nike Futsal
('futsal', 'Nike Lunar Gato', 'shoes', 'Nike', '32', 1000000.00, 10),
-- ... (repeat for sizes 33-45)

-- Adidas Futsal
('futsal', 'Adidas Top Sala', 'shoes', 'Adidas', '32', 1000000.00, 10),
-- ... (repeat for sizes 33-45)

-- Specs Futsal
('futsal', 'Specs Meta', 'shoes', 'Specs', '32', 1000000.00, 10),
-- ... (repeat for sizes 33-45)

-- Ortusheight Futsal
('futsal', 'Ortusheight Quantum', 'shoes', 'Ortusheight', '32', 1000000.00, 10),
-- ... (repeat for sizes 33-45)

-- Futsal Accessories
INSERT INTO products (category, name, type, price, stock) VALUES
-- Finger Tape
('futsal', 'Pro Finger Tape 1', 'finger_tape', 50000.00, 10),
('futsal', 'Pro Finger Tape 2', 'finger_tape', 50000.00, 10),
('futsal', 'Pro Finger Tape 3', 'finger_tape', 50000.00, 10),
('futsal', 'Pro Finger Tape 4', 'finger_tape', 50000.00, 10),
('futsal', 'Pro Finger Tape 5', 'finger_tape', 50000.00, 10),
('futsal', 'Pro Finger Tape 6', 'finger_tape', 50000.00, 10),
('futsal', 'Pro Finger Tape 7', 'finger_tape', 50000.00, 10),
('futsal', 'Pro Finger Tape 8', 'finger_tape', 50000.00, 10),
('futsal', 'Pro Finger Tape 9', 'finger_tape', 50000.00, 10),
('futsal', 'Pro Finger Tape 10', 'finger_tape', 50000.00, 10),

-- Knee Pad
('futsal', 'Pro Knee Pad 1', 'knee_pad', 200000.00, 10),
('futsal', 'Pro Knee Pad 2', 'knee_pad', 200000.00, 10),
('futsal', 'Pro Knee Pad 3', 'knee_pad', 200000.00, 10),
('futsal', 'Pro Knee Pad 4', 'knee_pad', 200000.00, 10),
('futsal', 'Pro Knee Pad 5', 'knee_pad', 200000.00, 10);

-- Running Products
-- Running Jerseys
INSERT INTO products (category, name, type, size, price, stock) VALUES
('running', 'Pro Running Jersey 1', 'jersey', 'S', 500000.00, 10),
('running', 'Pro Running Jersey 1', 'jersey', 'M', 500000.00, 10),
('running', 'Pro Running Jersey 1', 'jersey', 'L', 500000.00, 10),
('running', 'Pro Running Jersey 1', 'jersey', 'XL', 500000.00, 10),
-- ... (repeat for jerseys 2-20)

-- Running Shoes
INSERT INTO products (category, name, type, brand, size, price, stock) VALUES
-- Nike Running
('running', 'Nike Air Zoom', 'shoes', 'Nike', '32', 1000000.00, 10),
-- ... (repeat for sizes 33-45)

-- Adidas Running
('running', 'Adidas Ultraboost', 'shoes', 'Adidas', '32', 1000000.00, 10),
-- ... (repeat for sizes 33-45)

-- Hoka Running
('running', 'Hoka Bondi', 'shoes', 'Hoka', '32', 1000000.00, 10),
-- ... (repeat for sizes 33-45)

-- Badminton Products
-- Badminton Jerseys
INSERT INTO products (category, name, type, brand, size, price, stock) VALUES
-- Indonesia
('bulutangkis', 'Indonesia Badminton Jersey', 'jersey', NULL, 'S', 500000.00, 10),
-- ... (repeat for M, L, XL)

-- Malaysia
('bulutangkis', 'Malaysia Badminton Jersey', 'jersey', NULL, 'S', 500000.00, 10),
-- ... (repeat for other countries and sizes)

-- Badminton Shoes
INSERT INTO products (category, name, type, brand, size, price, stock) VALUES
('bulutangkis', 'Pro Badminton Shoes 1', 'shoes', NULL, '32', 1500000.00, 10),
-- ... (repeat for all sizes and 20 variations)

-- Badminton Headbands
INSERT INTO products (category, name, type, price, stock) VALUES
('bulutangkis', 'Pro Headband 1', 'headband', 100000.00, 10),
-- ... (repeat for headbands 2-10)

-- Badminton Rackets
INSERT INTO products (category, name, type, brand, price, stock) VALUES
('bulutangkis', 'Yonex Astrox 88D Pro', 'racket', 'Yonex', 2500000.00, 10),
('bulutangkis', 'Yonex Astrox 99 Pro', 'racket', 'Yonex', 2800000.00, 10),
('bulutangkis', 'Li-Ning Air Force F97', 'racket', 'Li-Ning', 2300000.00, 10),
('bulutangkis', 'Li-Ning Turbo Charging 75', 'racket', 'Li-Ning', 2400000.00, 10),
('bulutangkis', 'Victor Thruster K F', 'racket', 'Victor', 2600000.00, 10),
('bulutangkis', 'Victor Jetspeed S 12', 'racket', 'Victor', 2700000.00, 10),
('bulutangkis', 'Fleet Power Strike', 'racket', 'Fleet', 2200000.00, 10),
('bulutangkis', 'Fleet Light Speed', 'racket', 'Fleet', 2100000.00, 10),
('bulutangkis', 'Apacs Lethal 10', 'racket', 'Apacs', 1900000.00, 10),
('bulutangkis', 'Apacs Dual Power & Speed', 'racket', 'Apacs', 2000000.00, 10);
