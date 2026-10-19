<?php
require_once "../includes/db.php";
require_once "../includes/functions.php";

if (!is_admin()) {
    redirect("../login.php");
}

// approve or reject a booking
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $booking_id = (int) $_POST['booking_id'];
    $new_status = $_POST['status'];

    if (in_array($new_status, ["approved", "rejected"])) {
        $stmt = mysqli_prepare($conn, "UPDATE bookings SET status = ? WHERE id = ? AND status = 'pending'");
        mysqli_stmt_bind_param($stmt, "si", $new_status, $booking_id);
        mysqli_stmt_execute($stmt);
        set_message("Booking #" . $booking_id . " was " . $new_status . ".");
    }
    redirect("bookings.php");
}

// filter by status with the tabs
$statuses = ["pending", "approved", "rejected", "cancelled"];
$filter = isset($_GET['status']) && in_array($_GET['status'], $statuses) ? $_GET['status'] : "";

$sql = "SELECT bookings.*, users.name, users.email, cars.brand, cars.model
        FROM bookings
        JOIN users ON users.id = bookings.user_id
        JOIN cars ON cars.id = bookings.car_id";

if ($filter != "") {
    $sql .= " WHERE bookings.status = ?";
}
$sql .= " ORDER BY bookings.start_date DESC";

$stmt = mysqli_prepare($conn, $sql);
if ($filter != "") {
    mysqli_stmt_bind_param($stmt, "s", $filter);
}
mysqli_stmt_execute($stmt);
$bookings = mysqli_stmt_get_result($stmt);

$page_title = "Bookings";
require_once "includes/admin-header.php";
?>

<div class="admin-top">
    <div>
        <h1>Bookings</h1>
        <p class="muted">Approve or reject the booking requests of your clients.</p>
    </div>
</div>

<div class="tabs">
    <a href="bookings.php" class="<?php if ($filter == "") echo "active"; ?>">All</a>
    <?php foreach ($statuses as $status) { ?>
        <a href="bookings.php?status=<?php echo $status; ?>" class="<?php if ($filter == $status) echo "active"; ?>"><?php echo ucfirst($status); ?></a>
    <?php } ?>
</div>

<?php if (mysqli_num_rows($bookings) == 0) { ?>
    <div class="empty-box">
        <h3>No bookings here</h3>
    </div>
<?php } else { ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Client</th>
                    <th>Car</th>
                    <th>Dates</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($booking = mysqli_fetch_assoc($bookings)) { ?>
                    <tr>
                        <td><?php echo $booking['id']; ?></td>
                        <td>
                            <strong><?php echo e($booking['name']); ?></strong><br>
                            <span class="muted"><?php echo e($booking['email']); ?></span>
                        </td>
                        <td><?php echo e($booking['brand'] . ' ' . $booking['model']); ?></td>
                        <td>
                            <?php echo format_date($booking['start_date']); ?> &rarr; <?php echo format_date($booking['end_date']); ?><br>
                            <span class="muted"><?php echo count_days($booking['start_date'], $booking['end_date']); ?> day(s)</span>
                        </td>
                        <td><?php echo format_price($booking['total_price']); ?></td>
                        <td><?php echo status_badge($booking['status']); ?></td>
                        <td>
                            <?php if ($booking['status'] == "pending") { ?>
                                <div class="actions">
                                    <form method="post">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="btn btn-success btn-small">Approve</button>
                                    </form>
                                    <form method="post">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="btn btn-danger btn-small" data-confirm="Reject this booking?">Reject</button>
                                    </form>
                                </div>
                            <?php } else { ?>
                                <span class="muted">-</span>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php } ?>

<?php require_once "includes/admin-footer.php"; ?>
