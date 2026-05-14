-- Migration: New Roles & Custom Permissions
-- Run this in phpMyAdmin against the `pwd` database

-- Custom roles created by county officers
CREATE TABLE IF NOT EXISTS custom_roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    county_id INT NOT NULL,
    role_name VARCHAR(100) NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Permissions belonging to a custom role (one row per permission)
-- Allowed permission values: approve_health, approve_county, view_reports, manage_officers, manage_hospitals
CREATE TABLE IF NOT EXISTS role_permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    permission VARCHAR(100) NOT NULL,
    FOREIGN KEY (role_id) REFERENCES custom_roles(id) ON DELETE CASCADE
);

-- Links officials to custom roles
CREATE TABLE IF NOT EXISTS officer_custom_roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    official_id INT NOT NULL,
    role_id INT NOT NULL,
    assigned_by INT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (official_id) REFERENCES officials(id),
    FOREIGN KEY (role_id) REFERENCES custom_roles(id) ON DELETE CASCADE
);
