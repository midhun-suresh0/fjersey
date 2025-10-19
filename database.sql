-- Create database
CREATE DATABASE IF NOT EXISTS fjersey_db;
USE fjersey_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (email)
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category VARCHAR(50) NOT NULL,
    team VARCHAR(100) NOT NULL,
    size VARCHAR(10) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    shipping_address TEXT NOT NULL,
    billing_address TEXT NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    size VARCHAR(10) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Cart items table (for persistent cart)
CREATE TABLE IF NOT EXISTS cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    size VARCHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Insert admin user
INSERT INTO users (username, email, password, role) VALUES 
('admin', 'admin@fjersey.com', '$2y$10$8MuRXOLkMeaUUZQbPsEYVeRq9jlXhNjenMTOf6F.11LMxTXJpd5Oi', 'admin');
-- Default password: admin123

-- Insert sample products
INSERT INTO products (name, description, price, category, team, size, stock, image) VALUES
('Manchester United Home Jersey 2023/24', 'Official Manchester United home jersey for the 2023/24 season.', 89.99, 'Premier League', 'Manchester United', 'M', 50, 'man_utd_home.jpg'),
('Barcelona Home Jersey 2023/24', 'Official FC Barcelona home jersey for the 2023/24 season.', 89.99, 'La Liga', 'Barcelona', 'L', 45, 'barcelona_home.jpg'),
('Real Madrid Away Jersey 2023/24', 'Official Real Madrid away jersey for the 2023/24 season.', 89.99, 'La Liga', 'Real Madrid', 'M', 40, 'real_madrid_away.jpg'),
('Liverpool Third Jersey 2023/24', 'Official Liverpool FC third jersey for the 2023/24 season.', 89.99, 'Premier League', 'Liverpool', 'S', 35, 'liverpool_third.jpg'),
('Bayern Munich Home Jersey 2023/24', 'Official Bayern Munich home jersey for the 2023/24 season.', 89.99, 'Bundesliga', 'Bayern Munich', 'XL', 30, 'bayern_home.jpg'),
('PSG Away Jersey 2023/24', 'Official Paris Saint-Germain away jersey for the 2023/24 season.', 89.99, 'Ligue 1', 'PSG', 'L', 25, 'psg_away.jpg'),
('Juventus Home Jersey 2023/24', 'Official Juventus home jersey for the 2023/24 season.', 89.99, 'Serie A', 'Juventus', 'M', 30, 'juventus_home.jpg'),
('Arsenal Third Jersey 2023/24', 'Official Arsenal third jersey for the 2023/24 season.', 89.99, 'Premier League', 'Arsenal', 'L', 20, 'arsenal_third.jpg');