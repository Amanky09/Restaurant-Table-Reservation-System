<?php require_once __DIR__ . '/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Tavola — My Bookings</title>
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
            <li><a href="view_reservations.php" class="active">My Bookings</a></li>
            <li><a href="admin.php">Admin Panel</a></li>
        </ul>
    </nav>

    <!-- ========== PAGE HEADER ========== -->
    <header class="page-header">
        <h1>My Bookings</h1>
        <p>Search your reservations by email or phone number</p>
    </header>

    <!-- ========== SEARCH FORM ========== -->
    <main class="container">

        <section class="form-section">
            <form action="view_reservations.php" method="GET" class="search-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="search">Email or Phone</label>
                        <input type="text" id="search" name="search"
                               placeholder="Enter your email or phone..."
                               value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                               required>
                    </div>
                    <div class="form-group btn-group">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </div>
            </form>
        </section>

        <!-- ========== RESULTS TABLE ========== -->
        <?php
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            require_once 'db_config.php';

            $search = trim((string)$_GET['search']);
            $stmt = $conn->prepare('SELECT * FROM reservations WHERE email = ? OR phone = ? ORDER BY reservation_date DESC, reservation_time DESC');
            $stmt->bind_param('ss', $search, $search);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo '<section class="table-section">';
                echo '<h2 class="section-title">Your Reservations (' . $result->num_rows . ' found)</h2>';
                echo '<div class="table-wrapper">';
                echo '<table class="data-table">';
                echo '<thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Guests</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                      </thead>';
                echo '<tbody>';

                $count = 1;
                while ($row = $result->fetch_assoc()) {
                    // Color the status badge
                    $status_class = '';
                    if ($row['status'] == 'Confirmed') $status_class = 'badge-success';
                    elseif ($row['status'] == 'Pending')   $status_class = 'badge-warning';
                    elseif ($row['status'] == 'Cancelled') $status_class = 'badge-danger';

                    $formatted_date = date('d M Y', strtotime($row['reservation_date']));
                    $formatted_time = date('h:i A', strtotime($row['reservation_time']));

                    echo "<tr>
                            <td>{$count}</td>
                            <td>" . h($row['customer_name']) . "</td>
                            <td>{$formatted_date}</td>
                            <td>{$formatted_time}</td>
                            <td>{$row['guests']}</td>
                            <td><span class='badge {$status_class}'>{$row['status']}</span></td>
                            <td>";

                    // Only show cancel button if not already cancelled
                    if ($row['status'] != 'Cancelled') {
                        echo "<form action='cancel_reservation.php' method='post' onsubmit=\"return confirm('Cancel this reservation?');\"><input type='hidden' name='csrf' value='" . h(csrf_token()) . "'><input type='hidden' name='id' value='" . (int)$row['id'] . "'><input type='hidden' name='search' value='" . h($search) . "'><button class='btn btn-small btn-danger'>Cancel</button></form>";
                    } else {
                        echo "<span class='text-muted'>—</span>";
                    }

                    echo "  </td>
                          </tr>";
                    $count++;
                }

                echo '</tbody></table></div></section>';
            } else {
                echo '<div class="alert alert-error">No reservations found for "' . htmlspecialchars($search) . '"</div>';
            }

            $conn->close();
        }
        ?>
    </main>

    <!-- ========== FOOTER ========== -->
    <footer class="footer">
        <p>© 2026 La Tavola Restaurant. All rights reserved.</p>
    </footer>

</body>
</html>
