-- Real Estate CRM Database Schema

CREATE DATABASE IF NOT EXISTS real_estate_crm;
USE real_estate_crm;

-- Users Table (Admin & Sales Employees)
CREATE TABLE users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin', 'sales_employee') DEFAULT 'sales_employee',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX(email),
  INDEX(role)
);

-- Leads Table
CREATE TABLE leads (
  id INT PRIMARY KEY AUTO_INCREMENT,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  email VARCHAR(100),
  phone VARCHAR(20),
  stage ENUM('New', 'Contacted', 'Site Visit', 'Interested', 'Negotiation', 'Booked', 'Lost') DEFAULT 'New',
  assigned_to INT,
  source VARCHAR(100),
  budget DECIMAL(12, 2),
  preferred_property_type VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
  INDEX(stage),
  INDEX(assigned_to),
  INDEX(created_at)
);

-- Lead Notes & Follow-ups
CREATE TABLE lead_notes (
  id INT PRIMARY KEY AUTO_INCREMENT,
  lead_id INT NOT NULL,
  note TEXT NOT NULL,
  follow_up_date DATE,
  created_by INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
  FOREIGN KEY (created_by) REFERENCES users(id),
  INDEX(lead_id),
  INDEX(follow_up_date)
);

-- Projects Table
CREATE TABLE projects (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(150) NOT NULL,
  location VARCHAR(200),
  description TEXT,
  status ENUM('Active', 'Inactive') DEFAULT 'Active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX(status)
);

-- Buildings Table
CREATE TABLE buildings (
  id INT PRIMARY KEY AUTO_INCREMENT,
  project_id INT NOT NULL,
  name VARCHAR(100) NOT NULL,
  floor_count INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
  INDEX(project_id)
);

-- Units Table
CREATE TABLE units (
  id INT PRIMARY KEY AUTO_INCREMENT,
  building_id INT NOT NULL,
  project_id INT NOT NULL,
  unit_number VARCHAR(50) NOT NULL,
  type ENUM('1BHK', '2BHK', '3BHK', '4BHK', 'Commercial') NOT NULL,
  price DECIMAL(12, 2) NOT NULL,
  built_area DECIMAL(8, 2),
  status ENUM('Available', 'Booked', 'Unavailable') DEFAULT 'Available',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (building_id) REFERENCES buildings(id) ON DELETE CASCADE,
  FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
  UNIQUE(building_id, unit_number),
  INDEX(status),
  INDEX(type),
  INDEX(project_id)
);

-- Bookings Table (Prevent duplicate bookings with constraints)
CREATE TABLE bookings (
  id INT PRIMARY KEY AUTO_INCREMENT,
  lead_id INT NOT NULL,
  unit_id INT NOT NULL,
  booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  booking_status ENUM('Pending', 'Confirmed', 'Cancelled') DEFAULT 'Pending',
  payment_amount DECIMAL(12, 2),
  payment_date DATE,
  created_by INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
  FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE RESTRICT,
  FOREIGN KEY (created_by) REFERENCES users(id),
  UNIQUE KEY unique_confirmed_booking (unit_id, booking_status),
  INDEX(lead_id),
  INDEX(unit_id),
  INDEX(booking_status),
  INDEX(created_at)
);

-- Activity Log for audit trail
CREATE TABLE activity_log (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT,
  entity_type VARCHAR(50),
  entity_id INT,
  action VARCHAR(50),
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id),
  INDEX(user_id),
  INDEX(entity_type),
  INDEX(created_at)
);

-- Insert sample admin user (password: admin123)
INSERT INTO users (name, email, password, role) VALUES 
('Admin User', 'admin@crm.com', '$2y$10$Z8H5F4J7K3L2M1N0P9Q8R7S6T5U4V3W2X1Y0Z9Y8X7W6V5U4', 'admin'),
('John Sales', 'john@crm.com', '$2y$10$Z8H5F4J7K3L2M1N0P9Q8R7S6T5U4V3W2X1Y0Z9Y8X7W6V5U4', 'sales_employee'),
('Sarah Sales', 'sarah@crm.com', '$2y$10$Z8H5F4J7K3L2M1N0P9Q8R7S6T5U4V3W2X1Y0Z9Y8X7W6V5U4', 'sales_employee');

-- Sample project & properties
INSERT INTO projects (name, location, description, status) VALUES 
('Sunset Heights', 'Bangalore, India', 'Premium residential project', 'Active'),
('Tech Park Plaza', 'Bangalore, India', 'Commercial & retail space', 'Active');

INSERT INTO buildings (project_id, name, floor_count) VALUES 
(1, 'Tower A', 15),
(1, 'Tower B', 12),
(2, 'Commercial Block', 10);

INSERT INTO units (building_id, project_id, unit_number, type, price, built_area, status) VALUES 
(1, 1, 'A-101', '2BHK', 7500000, 1200, 'Available'),
(1, 1, 'A-102', '2BHK', 7500000, 1200, 'Available'),
(1, 1, 'A-103', '3BHK', 9500000, 1600, 'Available'),
(2, 1, 'B-101', '1BHK', 5500000, 850, 'Available'),
(2, 1, 'B-102', '2BHK', 7500000, 1200, 'Booked'),
(3, 2, 'C-101', 'Commercial', 15000000, 5000, 'Available');
