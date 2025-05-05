-- Add shipping columns to orders table
ALTER TABLE orders 
ADD COLUMN shipping_method ENUM('sicepat', 'jne') NOT NULL AFTER payment_method,
ADD COLUMN shipping_fee DECIMAL(10, 2) NOT NULL DEFAULT 0.00 AFTER shipping_method,
ADD COLUMN service_fee DECIMAL(10, 2) NOT NULL DEFAULT 5000.00 AFTER shipping_fee;

-- Add size column to order_details table
ALTER TABLE order_details
ADD COLUMN size VARCHAR(10) DEFAULT NULL AFTER quantity;

-- Add phone number to users table
ALTER TABLE users
ADD COLUMN phone_number VARCHAR(20) DEFAULT NULL AFTER email;
