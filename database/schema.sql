-- DriveNow database
-- import this file in phpMyAdmin or with: mysql -u root -p < database/schema.sql

CREATE DATABASE IF NOT EXISTS drivenow CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE drivenow;

DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS messages;
DROP TABLE IF EXISTS cars;
DROP TABLE IF EXISTS users;

-- users table (customers and admins)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(30),
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- cars table
CREATE TABLE cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    brand VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    year INT NOT NULL,
    type VARCHAR(30) NOT NULL,
    transmission VARCHAR(20) NOT NULL,
    seats INT NOT NULL,
    fuel VARCHAR(20) NOT NULL,
    price_per_day DECIMAL(8,2) NOT NULL,
    image VARCHAR(255),
    description TEXT,
    is_available TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- bookings table
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    car_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (car_id) REFERENCES cars(id)
);

-- messages from the contact form
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    subject VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- sample data --------------------------------------------------

-- admin password: admin123 / user password: user123
INSERT INTO users (name, email, phone, password, role) VALUES
('Admin', 'admin@drivenow.com', '+216 20 000 000', '$2y$10$dqF8P.oAWlE7f0YhtUIgKuZFUx6yyEAC3WKTkb.J2AdFspoDH4A4K', 'admin'),
('Sara Ben Ali', 'sara@example.com', '+216 22 123 456', '$2y$10$y1tgylQ3ihOT358q22KRWOA6YPebrFsfbNYCAjbc6RZbfKl6ISPB2', 'user'),
('Mehdi Trabelsi', 'mehdi@example.com', '+216 55 987 654', '$2y$10$y1tgylQ3ihOT358q22KRWOA6YPebrFsfbNYCAjbc6RZbfKl6ISPB2', 'user');

INSERT INTO cars (brand, model, year, type, transmission, seats, fuel, price_per_day, image, description) VALUES
('Toyota', 'Corolla', 2023, 'Sedan', 'Automatic', 5, 'Hybrid', 45.00, 'images/cars/sedan-white.svg', 'A comfortable and very economical sedan. Perfect for city trips and long drives on the highway.'),
('Volkswagen', 'Golf', 2022, 'Hatchback', 'Manual', 5, 'Petrol', 40.00, 'images/cars/hatchback-blue.svg', 'Compact, fun to drive and easy to park. A great choice for couples or small families.'),
('Kia', 'Sportage', 2024, 'SUV', 'Automatic', 5, 'Diesel', 70.00, 'images/cars/suv-green.svg', 'Spacious SUV with a big trunk, ideal for family holidays and road trips.'),
('Hyundai', 'Tucson', 2023, 'SUV', 'Automatic', 5, 'Hybrid', 75.00, 'images/cars/suv-grey.svg', 'Modern hybrid SUV with low fuel consumption and lots of comfort features.'),
('BMW', '3 Series', 2023, 'Sedan', 'Automatic', 5, 'Diesel', 95.00, 'images/cars/sedan-black.svg', 'Premium sedan for business trips or when you want to travel in style.'),
('Ford', 'Mustang', 2022, 'Sports', 'Automatic', 4, 'Petrol', 140.00, 'images/cars/sports-red.svg', 'Powerful sports car with a V8 engine. Make your weekend unforgettable.'),
('Renault', 'Clio', 2023, 'Hatchback', 'Manual', 5, 'Petrol', 32.00, 'images/cars/hatchback-orange.svg', 'Our cheapest car. Small, reliable and perfect for getting around town.'),
('Mercedes', 'Vito', 2022, 'Van', 'Manual', 8, 'Diesel', 90.00, 'images/cars/van-white.svg', 'Large van with 8 seats. Great for groups, airport transfers and events.'),
('Tesla', 'Model 3', 2024, 'Sedan', 'Automatic', 5, 'Electric', 110.00, 'images/cars/sedan-blue.svg', '100% electric sedan with autopilot and a range of more than 500 km.');

INSERT INTO bookings (user_id, car_id, start_date, end_date, total_price, status) VALUES
(2, 3, '2026-11-02', '2026-11-06', 280.00, 'approved'),
(2, 6, '2026-12-20', '2026-12-22', 280.00, 'pending'),
(2, 7, '2026-09-10', '2026-09-15', 160.00, 'approved'),
(3, 1, '2026-11-10', '2026-11-14', 180.00, 'pending'),
(3, 5, '2026-10-25', '2026-10-28', 285.00, 'approved'),
(3, 2, '2026-08-01', '2026-08-03', 80.00, 'rejected');

INSERT INTO messages (name, email, subject, message) VALUES
('Youssef Gharbi', 'youssef@example.com', 'Airport pickup', 'Hello, can I pick up the car directly at Tunis-Carthage airport? Thanks.'),
('Amira Saidi', 'amira@example.com', 'Long term rental', 'Do you have a discount if I rent a car for a full month?');
