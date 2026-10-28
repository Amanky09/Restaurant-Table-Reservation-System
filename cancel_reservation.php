<?php
// ============================================
// CANCEL A RESERVATION
// ============================================

require_once __DIR__ . '/auth.php';
require_csrf();
require_once __DIR__ . '/db_config.php';

if (isset($_POST['id'], $_POST['search'])) {
    $id = (int)$_POST['id'];
    $search = trim((string)$_POST['search']);
    if ($id <= 0 || $search === '') {
        http_response_code(400);
        exit('Invalid reservation');
    }

    // Update the status to "Cancelled"
    $stmt = $conn->prepare("UPDATE reservations SET status = 'Cancelled' WHERE id = ? AND (email = ? OR phone = ?)");
    $stmt->bind_param('iss', $id, $search, $search);
    $stmt->execute();
    $conn->close();

    // Redirect back to the bookings page
    header("Location: view_reservations.php?search=" . urlencode($search));
    exit();

} else {
    header("Location: view_reservations.php");
    exit();
}
