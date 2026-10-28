<?php require_once __DIR__ . '/auth.php'; require_admin(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Tavola — Admin Panel</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- ========== NAVIGATION ========== -->
    <nav class="navbar">
        <div class="nav-brand">
            <span class="brand-icon">🍽</span>
            <span class="brand-name">La Tavola</span>
        </div>
        <ul class="nav-links">
            <li><a href="index.php">Reserve</a></li>
            <li><a href="view_reservations.php">My Bookings</a></li>
            <li><a href="admin.php" class="active">Admin Panel</a></li>
            <li><form action="logout.php" method="post"><input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>"><button type="submit" class="btn btn-secondary">Sign out</button></form></li>
        </ul>
    </nav>

    <!-- ========== PAGE HEADER ========== -->
    <header class="page-header admin-header">
        <h1>Admin Panel</h1>
        <p>Manage all restaurant reservations</p>
    </header>

    <main class="container">

        <?php
        // Show action messages
        if (isset($_GET['msg'])) {
            if ($_GET['msg'] == 'deleted') {
                echo '<div class="alert alert-success">🗑 Reservation deleted successfully.</div>';
            } elseif ($_GET['msg'] == 'updated') {
                echo '<div class="alert alert-success">✅ Reservation status updated.</div>';
            }
        }
        ?>

        <!-- ========== FILTER BAR ========== -->
        <section class="form-section">
            <form action="admin.php" method="GET" class="search-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="filter_status">Filter by Status</label>
                        <select id="filter_status" name="filter_status">
                            <option value="All" <?php echo (isset($_GET['filter_status']) && $_GET['filter_status']=='All') ? 'selected' : ''; ?>>All</option>
                            <option value="Pending" <?php echo (isset($_GET['filter_status']) && $_GET['filter_status']=='Pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="Confirmed" <?php echo (isset($_GET['filter_status']) && $_GET['filter_status']=='Confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                            <option value="Cancelled" <?php echo (isset($_GET['filter_status']) && $_GET['filter_status']=='Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filter_date">Filter by Date</label>
                        <input type="date" id="filter_date" name="filter_date"
                               value="<?php echo h($_GET['filter_date'] ?? ''); ?>">
                    </div>
                    <div class="form-group btn-group">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="admin.php" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </section>

        <!-- ========== ALL RESERVATIONS TABLE ========== -->
        <?php
        require_once 'db_config.php';

        // Build query based on filters
        $status = (string)($_GET['filter_status'] ?? 'All');
        $date = (string)($_GET['filter_date'] ?? '');
        if (!in_array($status, ['All', 'Pending', 'Confirmed', 'Cancelled'], true)) $status = 'All';
        if ($date !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) $date = '';
        $stmt = $conn->prepare("SELECT * FROM reservations WHERE (? = 'All' OR status = ?) AND (? = '' OR reservation_date = ?) ORDER BY created_at DESC");
        $stmt->bind_param('ssss', $status, $status, $date, $date);
        $stmt->execute();
        $result = $stmt->get_result();

        // Count stats
        $total     = $conn->query("SELECT COUNT(*) as c FROM reservations")->fetch_assoc()['c'];
        $pending   = $conn->query("SELECT COUNT(*) as c FROM reservations WHERE status='Pending'")->fetch_assoc()['c'];
        $confirmed = $conn->query("SELECT COUNT(*) as c FROM reservations WHERE status='Confirmed'")->fetch_assoc()['c'];
        $cancelled = $conn->query("SELECT COUNT(*) as c FROM reservations WHERE status='Cancelled'")->fetch_assoc()['c'];
        ?>

        <!-- Stats Cards -->
        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total; ?></div>
                <div class="stat-label">Total</div>
            </div>
            <div class="stat-card stat-pending">
                <div class="stat-number"><?php echo $pending; ?></div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-card stat-confirmed">
                <div class="stat-number"><?php echo $confirmed; ?></div>
                <div class="stat-label">Confirmed</div>
            </div>
            <div class="stat-card stat-cancelled">
                <div class="stat-number"><?php echo $cancelled; ?></div>
                <div class="stat-label">Cancelled</div>
            </div>
        </section>

        <!-- Data Table -->
        <section class="table-section">
            <h2 class="section-title">All Reservations (<?php echo $result->num_rows; ?>)</h2>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Guests</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Request</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $status_class = '';
                                if ($row['status'] == 'Confirmed') $status_class = 'badge-success';
                                elseif ($row['status'] == 'Pending')   $status_class = 'badge-warning';
                                elseif ($row['status'] == 'Cancelled') $status_class = 'badge-danger';

                                $formatted_date = date('d M Y', strtotime($row['reservation_date']));
                                $formatted_time = date('h:i A', strtotime($row['reservation_time']));
                                $request_text = !empty($row['special_request']) ? htmlspecialchars($row['special_request']) : '—';

                                echo "<tr>
                                    <td>{$row['id']}</td>
                                    <td><strong>" . h($row['customer_name']) . "</strong></td>
                                    <td>" . h($row['email']) . "</td>
                                    <td>" . h($row['phone']) . "</td>
                                    <td>{$row['guests']}</td>
                                    <td>{$formatted_date}</td>
                                    <td>{$formatted_time}</td>
                                    <td class='request-cell'>{$request_text}</td>
                                    <td><span class='badge {$status_class}'>{$row['status']}</span></td>
                                    <td class='action-cell'>
                                        <div class='action-buttons'>";

                                // Status change buttons
                                if ($row['status'] != 'Confirmed') {
                                    echo "<form action='update_status.php' method='post'><input type='hidden' name='csrf' value='" . h(csrf_token()) . "'><input type='hidden' name='id' value='" . (int)$row['id'] . "'><input type='hidden' name='status' value='Confirmed'><button class='btn btn-small btn-success' title='Confirm'>✓</button></form>";
                                }
                                if ($row['status'] != 'Cancelled') {
                                    echo "<form action='update_status.php' method='post'><input type='hidden' name='csrf' value='" . h(csrf_token()) . "'><input type='hidden' name='id' value='" . (int)$row['id'] . "'><input type='hidden' name='status' value='Cancelled'><button class='btn btn-small btn-danger' title='Cancel'>✕</button></form>";
                                }

                                // Delete button
                                echo "<form action='delete_reservation.php' method='post' onsubmit=\"return confirm('Permanently delete this reservation?');\"><input type='hidden' name='csrf' value='" . h(csrf_token()) . "'><input type='hidden' name='id' value='" . (int)$row['id'] . "'><button class='btn btn-small btn-dark' title='Delete'>🗑</button></form>";

                                echo "      </div>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='10' class='text-center'>No reservations found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>

        <?php $conn->close(); ?>

    </main>

    <!-- ========== FOOTER ========== -->
    <footer class="footer">
        <p>© 2026 La Tavola Restaurant — Admin Panel</p>
    </footer>

</body>
</html>
