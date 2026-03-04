-- IoTdelivers Database Structure
-- Created for E-commerce Website
-- Database: iotdelivers_db

-- Create Database
CREATE DATABASE IF NOT EXISTS iotdelivers_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE iotdelivers_db;

-- Users Table (for customers)
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    address TEXT,
    county VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Admins Table
CREATE TABLE IF NOT EXISTS admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role VARCHAR(50) DEFAULT 'admin',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories Table
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(150) UNIQUE NOT NULL,
    category_id INT NOT NULL,
    description LONGTEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255),
    stock_quantity INT DEFAULT 0,
    sku VARCHAR(100) UNIQUE,
    is_featured BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    user_id INT,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    delivery_address TEXT NOT NULL,
    county VARCHAR(50) NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL DEFAULT 'mpesa',
    order_status VARCHAR(50) DEFAULT 'Pending',
    mpesa_transaction_id VARCHAR(100),
    mpesa_receipt_number VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_order_number (order_number),
    INDEX idx_customer_email (customer_email),
    INDEX idx_order_status (order_status)
);

-- Order Items Table (many-to-many between Orders and Products)
CREATE TABLE IF NOT EXISTS order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(150) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Services Table (for CCTV Installation Services)
CREATE TABLE IF NOT EXISTS services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(150) UNIQUE NOT NULL,
    description LONGTEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Service Bookings Table
CREATE TABLE IF NOT EXISTS service_bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_number VARCHAR(50) UNIQUE NOT NULL,
    service_id INT NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    service_location TEXT NOT NULL,
    county VARCHAR(50) NOT NULL,
    preferred_date DATE,
    booking_status VARCHAR(50) DEFAULT 'Pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
);

-- Insert Sample Categories
INSERT INTO categories (name, slug, description, is_active) VALUES
('IoT Devices', 'iot-devices', 'Smart Internet of Things Devices for Home & Business', TRUE),
('Laptops', 'laptops', 'High Performance Laptops for Work & Gaming', TRUE),
('CCTV Cameras', 'cctv-cameras', 'Security CCTV Cameras & Surveillance Equipment', TRUE),
('Accessories', 'accessories', 'Tech Accessories & Equipment', TRUE);

-- Insert Sample Products
INSERT INTO products (name, slug, category_id, description, price, stock_quantity, sku, is_featured, is_active) VALUES
('Smart Home Hub', 'smart-home-hub', 1, 'Control your entire smart home with this advanced IoT hub. Features voice control, automation scheduling, and device management from anywhere.', 15999.00, 25, 'IOT001', TRUE, TRUE),
('IoT Smart Light Bulb', 'iot-smart-light-bulb', 1, 'WiFi-enabled smart bulb with 16 million color options. Control brightness, color, and scheduling via mobile app.', 2999.00, 50, 'IOT002', TRUE, TRUE),
('WiFi Video Doorbell', 'wifi-video-doorbell', 1, 'HD video doorbell with motion detection, two-way audio, and night vision. Get alerts on your phone.', 8999.00, 15, 'IOT003', TRUE, TRUE),

('Pro Laptop 15inch', 'pro-laptop-15inch', 2, 'Powerful 15-inch laptop with Intel i7, 16GB RAM, 512GB SSD. Perfect for professionals and content creators.', 89999.00, 10, 'LAP001', TRUE, TRUE),
('Budget Gaming Laptop', 'budget-gaming-laptop', 2, 'Entry-level gaming laptop with AMD Ryzen 5, 8GB RAM, GTX 1650. Great for gaming and multitasking.', 49999.00, 8, 'LAP002', TRUE, TRUE),
('Business Ultrabook', 'business-ultrabook', 2, 'Lightweight ultrabook with Intel i5, 8GB RAM, 256GB SSD. Perfect for business professionals.', 39999.00, 12, 'LAP003', FALSE, TRUE),

('HD CCTV Camera Outdoor', 'hd-cctv-camera-outdoor', 3, '2MP HD outdoor CCTV camera with night vision and weather-proof design. Perfect for property surveillance.', 4999.00, 30, 'CCTV001', TRUE, TRUE),
('4K CCTV Camera Pro', '4k-cctv-camera-pro', 3, '4K ultra HD CCTV camera with advanced motion detection and remote viewing. Enterprise-grade security.', 12999.00, 20, 'CCTV002', TRUE, TRUE),
('CCTV DVR 8 Channel', 'cctv-dvr-8-channel', 3, '8-channel DVR recording system with 2TB storage. Support up to 8 cameras with remote access.', 18999.00, 10, 'CCTV003', FALSE, TRUE);

-- Insert Sample Services
INSERT INTO services (name, slug, description, price, is_active) VALUES
('CCTV Installation Service', 'cctv-installation', 'Professional CCTV camera installation service for homes and businesses. Includes site survey, installation, and testing.', 5000.00, TRUE),
('Smart Home Setup', 'smart-home-setup', 'Complete smart home automation setup including device installation and configuration.', 8000.00, TRUE),
('Security System Integration', 'security-system-integration', 'Integrate CCTV, alarms, and access control systems into one unified security solution.', 12000.00, TRUE);

-- Insert Sample Admin User (password: admin123 - hashed with password_hash in PHP)
-- Password: admin123 (hashed using password_hash() in PHP)
INSERT INTO admins (username, email, password, full_name, role, is_active) VALUES
('james', 'james@iotdelivers.com', '$2y$10$YourHashedPasswordHere', 'James - CEO & Founder', 'admin', TRUE);

-- Note: To hash passwords in PHP, use:
-- password_hash('admin123', PASSWORD_BCRYPT)
-- This will generate a hashed password to replace in the INSERT statement above
