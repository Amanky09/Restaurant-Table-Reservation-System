<?php
// ============================================
// PROCESS RESERVATION FORM
// ============================================
// This file receives the form data from index.php
// and saves it into the database.
// ============================================

// Include database connection
require_once 'db_config.php';

// Check if form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Get form data and sanitize it
    $customer_name    = trim((string)($_POST['customer_name'] ?? ''));
    $email            = trim((string)($_POST['email'] ?? ''));
    $phone            = trim((string)($_POST['phone'] ?? ''));
    $guests           = (int)($_POST['guests'] ?? 0);
    $reservation_date = (string)($_POST['reservation_date'] ?? '');
    $reservation_time = (string)($_POST['reservation_time'] ?? '');
    $special_request  = trim((string)($_POST['special_request'] ?? ''));

    if ($customer_name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[0-9+() -]{7,20}$/', $phone)
        || $guests < 1 || $guests > 20 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $reservation_date)
        || $reservation_date < date('Y-m-d') || !preg_match('/^\d{2}:\d{2}$/', $reservation_time)) {
        header('Location: index.php?status=error');
        exit();
    }

    // Insert data into the reservations table
    $stmt = $conn->prepare('INSERT INTO reservations (customer_name, email, phone, guests, reservation_date, reservation_time, special_request) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('sssisss', $customer_name, $email, $phone, $guests, $reservation_date, $reservation_time, $special_request);

    if ($stmt->execute()) {
        // Success — redirect back with success message
        header("Location: index.php?status=success");
        exit();
    } else {
        // Error — redirect back with error message
        header("Location: index.php?status=error");
        exit();
    }

} else {
    // If someone tries to access this file directly, send them to the form
    header("Location: index.php");
    exit();
}

// Close the database connection
$conn->close();
?>
