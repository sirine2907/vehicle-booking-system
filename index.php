<?php
$page_title = "Home";
require_once "includes/header.php";

// latest 6 available cars for the "featured" section
$featured = mysqli_query($conn, "SELECT * FROM cars WHERE is_available = 1 ORDER BY id DESC LIMIT 6");

// values for the search form
$brands = mysqli_query($conn, "SELECT DISTINCT brand FROM cars WHERE is_available = 1 ORDER BY brand");
$types = mysqli_query($conn, "SELECT DISTINCT type FROM cars WHERE is_available = 1 ORDER BY type");

// small numbers for the hero
$total_cars = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM cars WHERE is_available = 1"))['total'];
$total_clients = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'user'"))['total'];
?>

<section class="hero">
    <div class="container hero-grid">
        <div>
            <span class="hero-tag">Car rental in Tunisia</span>
            <h1>Find the right car for <span class="accent">every trip.</span></h1>
            <p>Choose from our cars, pick your dates and book online in less than two minutes. Simple prices, no surprises.</p>
            <div class="hero-buttons">
                <a href="cars.php" class="btn btn-primary">Browse cars</a>
                <a href="about.php" class="btn btn-outline">How it works</a>
            </div>
            <div class="hero-stats">
                <div><strong><?php echo $total_cars; ?>+</strong><span>Cars ready</span></div>
                <div><strong><?php echo $total_clients; ?>+</strong><span>Happy clients</span></div>
                <div><strong>24/7</strong><span>Support</span></div>
            </div>
        </div>
        <div class="hero-image">
            <img src="images/cars/sports-yellow.svg" alt="Yellow sports car">
        </div>
    </div>
</section>

<section class="search-box">
    <div class="container">
        <form action="cars.php" method="get" class="search-form">
            <div class="form-group">
                <label for="brand">Brand</label>
                <select name="brand" id="brand">
                    <option value="">All brands</option>
                    <?php while ($row = mysqli_fetch_assoc($brands)) { ?>
                        <option value="<?php echo e($row['brand']); ?>"><?php echo e($row['brand']); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="type">Car type</label>
                <select name="type" id="type">
                    <option value="">All types</option>
                    <?php while ($row = mysqli_fetch_assoc($types)) { ?>
                        <option value="<?php echo e($row['type']); ?>"><?php echo e($row['type']); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="max_price">Max price per day (DT)</label>
                <input type="number" name="max_price" id="max_price" min="0" placeholder="e.g. 80">
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-title section-head">
            <div>
                <h2>Popular cars</h2>
                <p>Our most recent cars, cleaned and checked before every rental.</p>
            </div>
            <a href="cars.php" class="btn btn-outline">See all cars</a>
        </div>

        <div class="cars-grid">
            <?php while ($car = mysqli_fetch_assoc($featured)) { ?>
                <?php include "includes/car-card.php"; ?>
            <?php } ?>
        </div>
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <div class="section-title text-center">
            <h2>Why rent with DriveNow?</h2>
            <p>We keep things simple so you can focus on your trip.</p>
        </div>
        <div class="features-grid">
            <div class="feature">
                <div class="feature-icon">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <h3>Clear prices</h3>
                <p>The price you see is the price you pay. Insurance and unlimited km are included.</p>
            </div>
            <div class="feature">
                <div class="feature-icon">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <h3>Safe cars</h3>
                <p>Every car is checked and cleaned after each rental by our team.</p>
            </div>
            <div class="feature">
                <div class="feature-icon">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                </div>
                <h3>Free cancellation</h3>
                <p>Plans changed? Cancel your booking from your account before the pick-up day.</p>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-title text-center">
            <h2>Book in 3 easy steps</h2>
        </div>
        <div class="steps">
            <div class="step">
                <div class="step-number">01</div>
                <h3>Choose a car</h3>
                <p>Filter by brand, type or price and find the car you like.</p>
            </div>
            <div class="step">
                <div class="step-number">02</div>
                <h3>Pick your dates</h3>
                <p>Select pick-up and return dates and see the total price right away.</p>
            </div>
            <div class="step">
                <div class="step-number">03</div>
                <h3>Drive away</h3>
                <p>We confirm your booking and your car is ready on the day.</p>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="cta">
            <div>
                <h2>Ready for your next trip?</h2>
                <p>Create a free account and book your first car today.</p>
            </div>
            <a href="<?php echo is_logged_in() ? 'cars.php' : 'register.php'; ?>" class="btn">Get started</a>
        </div>
    </div>
</section>

<?php require_once "includes/footer.php"; ?>
