<?php
require_once "includes/db.php";
require_once "includes/functions.php";

require_login();

$car_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmt = mysqli_prepare($conn, "SELECT * FROM cars WHERE id = ? AND is_available = 1");
mysqli_stmt_bind_param($stmt, "i", $car_id);
mysqli_stmt_execute($stmt);
$car = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$car) {
    set_message("This car does not exist.", "error");
    redirect("cars.php");
}

$today = date("Y-m-d");
$errors = [];
$start_date = "";
$end_date = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // check the dates
    if ($start_date == "" || $end_date == "") {
        $errors[] = "Please choose both dates.";
    } elseif ($start_date < $today) {
        $errors[] = "Pick-up date can't be in the past.";
    } elseif ($end_date <= $start_date) {
        $errors[] = "Return date must be after the pick-up date.";
    } elseif (car_is_booked($conn, $car_id, $start_date, $end_date)) {
        $errors[] = "Sorry, this car is already booked for some of these dates. Please choose other dates.";
    }

    // save the booking
    if (count($errors) == 0) {
        $days = count_days($start_date, $end_date);
        $total_price = $days * $car['price_per_day'];
        $user_id = $_SESSION['user_id'];

        $stmt = mysqli_prepare($conn, "INSERT INTO bookings (user_id, car_id, start_date, end_date, total_price) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iissd", $user_id, $car_id, $start_date, $end_date, $total_price);
        mysqli_stmt_execute($stmt);

        set_message("Your booking was sent! We will confirm it soon.");
        redirect("my-bookings.php");
    }
}

// dates already taken for this car
$stmt = mysqli_prepare($conn, "SELECT start_date, end_date FROM bookings
                               WHERE car_id = ? AND status IN ('pending', 'approved') AND end_date >= ?
                               ORDER BY start_date");
mysqli_stmt_bind_param($stmt, "is", $car_id, $today);
mysqli_stmt_execute($stmt);
$booked = mysqli_stmt_get_result($stmt);

$page_title = "Book " . $car['brand'] . " " . $car['model'];
require_once "includes/header.php";
?>

<section class="page-header">
    <div class="container">
        <a href="car.php?id=<?php echo $car['id']; ?>">&larr; Back to car details</a>
        <h1 style="margin-top: 14px;">Book your car</h1>
    </div>
</section>

<section class="container details-grid">
    <div class="box">
        <h3>Choose your dates</h3>

        <?php foreach ($errors as $error) { ?>
            <div class="alert alert-error"><?php echo e($error); ?></div>
        <?php } ?>

        <form method="post" id="bookingForm" data-price="<?php echo e($car['price_per_day']); ?>">
            <div class="form-row">
                <div class="form-group">
                    <label for="start_date">Pick-up date</label>
                    <input type="date" name="start_date" id="start_date" min="<?php echo $today; ?>" value="<?php echo e($start_date); ?>" required>
                </div>
                <div class="form-group">
                    <label for="end_date">Return date</label>
                    <input type="date" name="end_date" id="end_date" min="<?php echo $today; ?>" value="<?php echo e($end_date); ?>" required>
                </div>
            </div>

            <div class="summary-row"><span class="muted">Price per day</span><span><?php echo format_price($car['price_per_day']); ?></span></div>
            <div class="summary-row"><span class="muted">Rental days</span><span id="totalDays">-</span></div>
            <div class="summary-row total"><span>Total</span><span id="totalPrice">-</span></div>

            <button type="submit" class="btn btn-primary btn-block" style="margin-top: 20px;">Confirm booking</button>
            <p class="muted" style="margin-top: 12px; font-size: 14px;">You pay at the agency when you pick up the car.</p>
        </form>
    </div>

    <aside>
        <div class="box">
            <div class="details-image" style="padding: 20px; margin-bottom: 18px;">
                <img src="<?php echo e($car['image']); ?>" alt="<?php echo e($car['brand'] . ' ' . $car['model']); ?>">
            </div>
            <h3><?php echo e($car['brand'] . ' ' . $car['model']); ?></h3>
            <div class="car-specs">
                <span class="chip"><?php echo e($car['year']); ?></span>
                <span class="chip"><?php echo e($car['seats']); ?> seats</span>
                <span class="chip"><?php echo e($car['transmission']); ?></span>
                <span class="chip"><?php echo e($car['fuel']); ?></span>
            </div>
        </div>

        <div class="box">
            <h3>Not available on</h3>
            <?php if (mysqli_num_rows($booked) == 0) { ?>
                <p class="muted">No bookings yet, all dates are free.</p>
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
