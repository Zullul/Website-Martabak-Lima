-- Database Schema for Martabak Lima Website
-- Create database
CREATE DATABASE IF NOT EXISTS `martabak_lima` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `martabak_lima`;

-- Admin table for login management
CREATE TABLE `admin` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `username` varchar(50) NOT NULL UNIQUE,
    `password` varchar(255) NOT NULL,
    `email` varchar(100) NOT NULL,
    `full_name` varchar(100) NOT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `is_active` tinyint(1) DEFAULT 1,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Toppings table
CREATE TABLE `toppings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `price` decimal(10,2) NOT NULL DEFAULT 0.00,
    `category` enum('protein','cheese','extras','sweet') NOT NULL,
    `description` text,
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Menu packages table
CREATE TABLE `menu_packages` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(150) NOT NULL,
    `category` enum('asin','manis') NOT NULL,
    `price` decimal(10,2) NOT NULL,
    `description` text,
    `image` varchar(255),
    `ingredients` text,
    `is_signature` tinyint(1) DEFAULT 0,
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Orders table
CREATE TABLE `orders` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `order_number` varchar(20) NOT NULL UNIQUE,
    `customer_name` varchar(100),
    `customer_phone` varchar(20),
    `customer_address` text,
    `items` text NOT NULL, -- JSON format of ordered items
    `total_amount` decimal(10,2) NOT NULL,
    `status` enum('pending','confirmed','preparing','ready','delivered','cancelled') DEFAULT 'pending',
    `notes` text,
    `order_date` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin user (password: admin123)
INSERT INTO `admin` (`username`, `password`, `email`, `full_name`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@martabaklima.com', 'Administrator');

-- Insert default toppings
INSERT INTO `toppings` (`name`, `price`, `category`, `description`) VALUES
('Daging Sapi', 8000.00, 'protein', 'Daging sapi pilihan yang sudah dibumbui'),
('Ikan Tuna Asap', 10000.00, 'protein', 'Ikan tuna asap kaya gizi'),
('Ayam Shredded', 6000.00, 'protein', 'Ayam suwir dengan bumbu special'),
('Telur Bebek', 3000.00, 'protein', 'Telur bebek segar'),
('Keju Mozarella', 5000.00, 'cheese', 'Keju mozarella premium'),
('Keju Kraft', 3000.00, 'cheese', 'Keju kraft original'),
('Daun Bawang', 1000.00, 'extras', 'Daun bawang segar'),
('Cabe Rawit', 500.00, 'extras', 'Cabe rawit untuk yang suka pedas'),
('Coklat', 2000.00, 'sweet', 'Coklat premium untuk martabak manis'),
('Keju', 3000.00, 'sweet', 'Keju untuk martabak manis'),
('Kacang', 1500.00, 'sweet', 'Kacang tanah pilihan'),
('Susu Kental Manis', 1000.00, 'sweet', 'Susu kental manis original');

-- Insert default menu packages
INSERT INTO `menu_packages` (`name`, `category`, `price`, `description`, `image`, `ingredients`, `is_signature`) VALUES
('Martabak Sapi Mozarella Telur Bebek', 'asin', 31500.00, 'Signature dish dengan daging sapi, keju mozarella, dan telur bebek', 'Martabak_Mozarella.jpg', 'Daging Sapi, Keju Mozarella, Telur Bebek', 1),
('Martabak Ikan Tuna Asap', 'asin', 25000.00, 'Martabak dengan ikan tuna asap yang kaya gizi', 'Martabak_tuna.jpg', 'Ikan Tuna Asap, Telur', 0),
('Martabak Ayam Klasik', 'asin', 21000.00, 'Martabak asin klasik dengan ayam shredded', 'Martabak_ayam.jpg', 'Ayam Shredded, Telur, Daun Bawang', 0),
('Martabak Coklat Keju', 'manis', 17000.00, 'Perpaduan sempurna coklat dan keju dalam martabak manis', 'Martabak_coklatkeju.jpg', 'Coklat, Keju, Susu Kental Manis', 0),
('Martabak Pandan Susu', 'manis', 15000.00, 'Martabak manis dengan aroma pandan dan susu', 'Martabak_pandansusu.jpg', 'Pandan, Susu Kental Manis', 0),
('Martabak Kacang Original', 'manis', 13500.00, 'Martabak manis tradisional dengan kacang tanah', 'Martabak_kacangoriginal.jpg', 'Kacang Tanah, Gula', 0);

-- Create indexes for better performance
CREATE INDEX idx_menu_category ON menu_packages(category);
CREATE INDEX idx_menu_active ON menu_packages(is_active);
CREATE INDEX idx_topping_category ON toppings(category);
CREATE INDEX idx_topping_active ON toppings(is_active);
CREATE INDEX idx_order_status ON orders(status);
CREATE INDEX idx_order_date ON orders(order_date);