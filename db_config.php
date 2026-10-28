<?php
// ============================================
// DATABASE CONFIGURATION
// ============================================
// Change these values to match your setup
// ============================================

$host = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$database = getenv('DB_NAME') ?: 'restaurant_db';

// Create connection
$conn = new mysqli($host, $username, $password);
$conn->set_charset('utf8mb4');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = 'CREATE DATABASE IF NOT EXISTS `' . str_replace('`', '``', $database) . '`';
$conn->query($sql);

// Select the database
$conn->select_db($database);

// Create the reservations table if it doesn't exist
$table_sql = "CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    guests INT NOT NULL,
    reservation_date DATE NOT NULL,
    reservation_time TIME NOT NULL,
    special_request TEXT,
    status ENUM('Pending', 'Confirmed', 'Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($table_sql)) {
    die("Table creation failed: " . $conn->error);
}
?>
