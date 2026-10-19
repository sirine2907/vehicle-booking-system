<?php
require_once "includes/db.php";
require_once "includes/functions.php";

// get the car from the id in the url
$car_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmt = mysqli_prepare($conn, "SELECT * FROM cars WHERE id = ? AND is_available = 1");
mysqli_stmt_bind_param($stmt, "i", $car_id);
mysqli_stmt_execute($stmt);
$car = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$car) {
    set_message("This car does not exist.", "error");
    redirect("cars.php");
}

// upcoming dates where the car is already booked
$today = date("Y-m-d");
$stmt = mysqli_prepare($conn, "SELECT start_date, end_date FROM bookings
                               WHERE car_id = ? AND status IN ('pending', 'approved') AND end_date >= ?
                               ORDER BY start_date");
mysqli_stmt_bind_param($stmt, "is", $car_id, $today);
mysqli_stmt_execute($stmt);
$booked = mysqli_stmt_get_result($stmt);

$page_title = $car['brand'] . " " . $car['model'];
require_once "includes/header.php";
?>

<section class="page-header">
    <div class="container">
        <a href="cars.php">&larr; Back to cars</a>
    </div>
</section>

<section class="container details-grid">
    <div>
        <div class="details-image">
            <img src="<?php echo e($car['image']); ?>" alt="<?php echo e($car['brand'] . ' ' . $car['model']); ?>">
        </div>

        <div class="specs-grid">
            <div class="spec"><span>Year</span><strong><?php echo e($car['year']); ?></strong></div>
            <div class="spec"><span>Type</span><strong><?php echo e($car['type']); ?></strong></div>
            <div class="spec"><span>Seats</span><strong><?php echo e($car['seats']); ?></strong></div>
            <div class="spec"><span>Gearbox</span><strong><?php echo e($car['transmission']); ?></strong></div>
            <div class="spec"><span>Fuel</span><strong><?php echo e($car['fuel']); ?></strong></div>
            <div class="spec"><span>Mileage</span><strong>Unlimited</strong></div>
        </div>

        <div class="box">
            <h3>About this car</h3>
            <p class="muted"><?php echo nl2br(e($car['description'])); ?></p>
        </div>
    </div>

    <aside>
        <div class="box">
            <h1><?php echo e($car['brand'] . ' ' . $car['model']); ?></h1>
            <p class="muted"><?php echo e($car['year'] . ' · ' . $car['type']); ?></p>
            <p class="price" style="margin: 20px 0;"><?php echo format_price($car['price_per_day']); ?> <small>/ day</small></p>
            <a href="book.php?id=<?php echo $car['id']; ?>" class="btn btn-primary btn-block">Book this car</a>
            <?php if (!is_logged_in()) { ?>
                <p class="muted text-center" style="margin-top: 12px; font-size: 14px;">You need an account to book.</p>
            <?php } ?>
        </div>

        <div class="box">
            <h3>Already booked</h3>
            <?php if (mysqli_num_rows($booked) == 0) { ?>
                <p class="muted">This car is free for all upcoming dates.</p>
            <?php } else { ?>
                <ul class="booked-list">
                    <?php while ($row = mysqli_fetch_assoc($booked)) { ?>
                        <li><?php echo format_date($row['start_date']); ?> &rarr; <?php echo format_date($row['end_date']); ?></li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </div>
    </aside>
</section>

<?php require_once "includes/footer.php"; ?>
