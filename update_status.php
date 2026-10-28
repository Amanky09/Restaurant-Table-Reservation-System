<?php
// ============================================
// UPDATE RESERVATION STATUS
// ============================================

require_once __DIR__ . '/auth.php';
require_admin();
require_csrf();
require_once __DIR__ . '/db_config.php';

if (isset($_POST['id']) && isset($_POST['status'])) {

    $id     = (int)$_POST['id'];
    $status = (string)$_POST['status'];

    // Only allow valid statuses
    $allowed = ['Pending', 'Confirmed', 'Cancelled'];

    if ($id > 0 && in_array($status, $allowed, true)) {
        $stmt = $conn->prepare('UPDATE reservations SET status = ? WHERE id = ?');
        $stmt->bind_param('si', $status, $id);
        $stmt->execute();
    }

    $conn->close();
    header("Location: admin.php?msg=updated");
    exit();

} else {
    header("Location: admin.php");
    exit();
}
