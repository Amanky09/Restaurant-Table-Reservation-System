<?php
// ============================================
// DELETE A RESERVATION (Admin only)
// ============================================

require_once __DIR__ . '/auth.php';
require_admin();
require_csrf();
require_once __DIR__ . '/db_config.php';

if (isset($_POST['id'])) {

    $id = (int)$_POST['id'];

    if ($id > 0) {
        $stmt = $conn->prepare('DELETE FROM reservations WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
    }

    $conn->close();
    header("Location: admin.php?msg=deleted");
    exit();

} else {
    header("Location: admin.php");
    exit();
}
