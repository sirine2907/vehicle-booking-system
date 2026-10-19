<?php
$page_title = "About us";
require_once "includes/header.php";

// some real numbers from the database
$total_cars = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM cars"))['total'];
$total_clients = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'user'"))['total'];
$total_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM bookings WHERE status = 'approved'"))['total'];
?>

<section class="page-header">
    <div class="container">
        <h1>About DriveNow</h1>
        <p class="muted">A small local company with one simple goal: make car rental easy.</p>
    </div>
</section>

<section class="container about-grid">
    <div class="about-text">
        <h2 style="margin-bottom: 16px;">We started with <span class="accent">three cars</span> and a lot of motivation.</h2>
        <p>DriveNow was created in Tunis by a group of friends who were tired of long phone calls, hidden fees and old cars when renting a vehicle.</p>
        <p>Today we offer a growing list of city cars, SUVs, vans and even a few sports cars. Every car is cleaned and checked after each rental.</p>
        <p>You can book online in a few minutes, follow your booking from your account and cancel for free before the pick-up day.</p>
        <a href="cars.php" class="btn btn-primary" style="margin-top: 10px;">See our cars</a>
    </div>

    <div class="stats-grid">
        <div class="stat"><strong><?php echo $total_cars; ?></strong><span>Cars in our garage</span></div>
        <div class="stat"><strong><?php echo $total_clients; ?></strong><span>Registered clients</span></div>
        <div class="stat"><strong><?php echo $total_bookings; ?></strong><span>Confirmed trips</span></div>
        <div class="stat"><strong>4.8</strong><span>Average rating</span></div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-title text-center">
            <h2>Our values</h2>
        </div>
        <div class="features-grid">
            <div class="feature">
                <h3>Honest</h3>
                <p>No hidden fees. The total price is shown before you confirm your booking.</p>
            </div>
            <div class="feature">
                <h3>Reliable</h3>
                <p>Our cars are recent and maintained regularly by professional mechanics.</p>
            </div>
            <div class="feature">
                <h3>Friendly</h3>
                <p>Our team answers your questions by phone, email or directly at the agency.</p>
            </div>
        </div>
    </div>
</section>

<?php require_once "includes/footer.php"; ?>
