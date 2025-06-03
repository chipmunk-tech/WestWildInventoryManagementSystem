-- Create database
CREATE DATABASE IF NOT EXISTS west_wild_ims;
USE west_wild_ims;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Items table
CREATE TABLE IF NOT EXISTS items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    category_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    unit VARCHAR(20) NOT NULL,
    supplier VARCHAR(100),
    min_stock_level INT DEFAULT 5,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
);

-- Stock issues table
CREATE TABLE IF NOT EXISTS stock_issues (
    id INT PRIMARY KEY AUTO_INCREMENT,
    item_id INT NOT NULL,
    quantity_issued INT NOT NULL,
    issued_to VARCHAR(100) NOT NULL,
    issued_by INT NOT NULL,
    issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expected_return_at TIMESTAMP NULL,
    returned_at TIMESTAMP NULL,
    return_notes TEXT,
    status ENUM('issued', 'returned', 'overdue') DEFAULT 'issued',
    notes TEXT,
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE RESTRICT,
    FOREIGN KEY (issued_by) REFERENCES users(id) ON DELETE RESTRICT
);

-- Stock movements table (for tracking all stock changes)
CREATE TABLE IF NOT EXISTS stock_movements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    item_id INT NOT NULL,
    movement_type ENUM('in', 'out', 'return') NOT NULL,
    quantity INT NOT NULL,
    reference_id INT,
    reference_type ENUM('issue', 'adjustment', 'initial', 'return') NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
);

-- Activity logs table
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
);

-- Settings table
CREATE TABLE IF NOT EXISTS settings (
    setting_key VARCHAR(50) PRIMARY KEY,
    setting_value TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password, full_name, email, role) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin@westwild.com', 'admin');

-- Insert default categories
INSERT INTO categories (name, description) VALUES
('Beverages', 'All beverage items including beers, water, and soft drinks'),
('Groceries & Snacks', 'Food items and snacks'),
('Pasta & Cereals', 'Pasta, spaghetti, and cereal products'),
('Dairy Products', 'Milk and dairy related items'),
('Tea & Coffee', 'Tea bags and coffee products'),
('Toiletries', 'Personal care and hygiene products'),
('Camping & Gear', 'Camping equipment and gear'),
('Vehicle Accessories', 'Vehicle related accessories and parts'),
('Electronics', 'Electronic devices including routers, modems, and accessories'); 