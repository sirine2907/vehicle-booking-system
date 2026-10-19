<?php
require_once "includes/db.php";
require_once "includes/functions.php";

require_login();

$user_id = $_SESSION['user_id'];
$today = date("Y-m-d");

// all bookings of the logged user with the car info
$sql = "SELECT bookings.*, cars.brand, cars.model, cars.image
        FROM bookings
        JOIN cars ON cars.id = bookings.car_id
        WHERE bookings.user_id = ?
        ORDER BY bookings.start_date DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$bookings = mysqli_stmt_get_result($stmt);

$page_title = "My bookings";
require_once "includes/header.php";
?>

<section class="page-header">
    <div class="container">
        <h1>My bookings</h1>
        <p class="muted">Here you can follow your bookings and cancel them before the pick-up day.</p>
    </div>
</section>

<section class="container">
    <?php if (mysqli_num_rows($bookings) == 0) { ?>
        <div class="empty-box">
            <h3>You don't have any booking yet</h3>
            <p class="muted">Find a car you like and book it in a few clicks.</p>
            <a href="cars.php" class="btn btn-primary">Browse cars</a>
        </div>
    <?php } else { ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Car</th>
                        <th>Pick-up</th>
                        <th>Return</th>
                        <th>Days</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($booking = mysqli_fetch_assoc($bookings)) { ?>
                        <tr>
                            <td>
                                <div class="table-car">
                                    <img src="<?php echo e($booking['image']); ?>" alt="">
                                    <strong><?php echo e($booking['brand'] . ' ' . $booking['model']); ?></strong>
                                </div>
                            </td>
                            <td><?php echo format_date($booking['start_date']); ?></td>
                            <td><?php echo format_date($booking['end_date']); ?></td>
                            <td><?php echo count_days($booking['start_date'], $booking['end_date']); ?></td>
                            <td><?php echo format_price($booking['total_price']); ?></td>
                            <td><?php echo status_badge($booking['status']); ?></td>
                            <td>
                                <?php
                                // user can cancel only if the booking is still active and not started
                                $can_cancel = in_array($booking['status'], ['pending', 'approved']) && $booking['start_date'] > $today;
                                if ($can_cancel) { ?>
                                    <form method="post" action="cancel-booking.php">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-small" data-confirm="Do you really want to cancel this booking?">Cancel</button>
                                    </form>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>
</section>

<?php require_once "includes/footer.php"; ?>
