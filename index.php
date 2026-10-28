<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Tavola — Reserve Your Table</title>
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
            <li><a href="index.php" class="active">Reserve</a></li>
            <li><a href="view_reservations.php">My Bookings</a></li>
            <li><a href="admin.php">Admin Panel</a></li>
        </ul>
    </nav>

    <!-- ========== HERO SECTION ========== -->
    <header class="hero">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1>Reserve Your Table</h1>
            <p>Experience fine dining at its best. Book your special evening with us.</p>
        </div>
    </header>

    <!-- ========== RESERVATION FORM ========== -->
    <main class="container">

        <?php
        // Show success or error messages
        if (isset($_GET['status'])) {
            if ($_GET['status'] == 'success') {
                echo '<div class="alert alert-success">
                        ✅ Reservation booked successfully! We look forward to seeing you.
                      </div>';
            } elseif ($_GET['status'] == 'error') {
                echo '<div class="alert alert-error">
                        ❌ Something went wrong. Please try again.
                      </div>';
            }
        }
        ?>

        <section class="form-section">
            <h2 class="section-title">Booking Details</h2>
            <p class="section-subtitle">Fill in the form below to reserve your table</p>

            <form action="process_reservation.php" method="POST" class="reservation-form">

                <!-- Row 1: Name & Email -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="customer_name">Full Name</label>
                        <input type="text" id="customer_name" name="customer_name"
                               placeholder="e.g. Aman Singh" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email"
                               placeholder="e.g. aman@email.com" required>
                    </div>
                </div>

                <!-- Row 2: Phone & Guests -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone"
                               placeholder="e.g. +91 98765 43210" required>
                    </div>
                    <div class="form-group">
                        <label for="guests">Number of Guests</label>
                        <select id="guests" name="guests" required>
                            <option value="">— Select —</option>
                            <option value="1">1 Person</option>
                            <option value="2">2 People</option>
                            <option value="3">3 People</option>
                            <option value="4">4 People</option>
                            <option value="5">5 People</option>
                            <option value="6">6 People</option>
                            <option value="7">7 People</option>
                            <option value="8">8 People</option>
                            <option value="9">9 People</option>
                            <option value="10">10 People</option>
                        </select>
                    </div>
                </div>

                <!-- Row 3: Date & Time -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="reservation_date">Date</label>
                        <input type="date" id="reservation_date" name="reservation_date" required>
                    </div>
                    <div class="form-group">
                        <label for="reservation_time">Time</label>
                        <input type="time" id="reservation_time" name="reservation_time" required>
                    </div>
                </div>

                <!-- Row 4: Special Request -->
                <div class="form-group full-width">
                    <label for="special_request">Special Requests (optional)</label>
                    <textarea id="special_request" name="special_request" rows="4"
                              placeholder="e.g. Window seat, birthday cake, vegetarian menu..."></textarea>
                </div>

                <!-- Submit Button -->
                <div class="form-group full-width">
                    <button type="submit" class="btn btn-primary">Book My Table</button>
                </div>

            </form>
        </section>
    </main>

    <!-- ========== FOOTER ========== -->
    <footer class="footer">
        <p>© 2026 La Tavola Restaurant. All rights reserved.</p>
    </footer>

    <!-- Set minimum date to today -->
    <script>
        document.getElementById('reservation_date').min = new Date().toISOString().split('T')[0];
    </script>

</body>
</html>
