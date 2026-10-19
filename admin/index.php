<?php
$page_title = "Dashboard";
require_once "includes/admin-header.php";

// small helper to get one number from the database
function count_rows($conn, $sql)
{
    $row = mysqli_fetch_assoc(mysqli_query($conn, $sql));
    return $row['total'];
}

$total_cars = count_rows($conn, "SELECT COUNT(*) AS total FROM cars");
$total_users = count_rows($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'user'");
$total_bookings = count_rows($conn, "SELECT COUNT(*) AS total FROM bookings");
$pending_bookings = count_rows($conn, "SELECT COUNT(*) AS total FROM bookings WHERE status = 'pending'");
$total_messages = count_rows($conn, "SELECT COUNT(*) AS total FROM messages");
$revenue = count_rows($conn, "SELECT COALESCE(SUM(total_price), 0) AS total FROM bookings WHERE status = 'approved'");

// last 5 bookings
$latest = mysqli_query($conn, "SELECT bookings.*, users.name, cars.brand, cars.model
                               FROM bookings
                               JOIN users ON users.id = bookings.user_id
                               JOIN cars ON cars.id = bookings.car_id
                               ORDER BY bookings.created_at DESC, bookings.id DESC
                               LIMIT 5");
?>

<div class="admin-top">
    <div>
        <h1>Dashboard</h1>
        <p class="muted">Hello <?php echo e($_SESSION['user_name']); ?>, here is what's happening today.</p>
    </div>
    <a href="car-form.php" class="btn btn-primary">+ Add a car</a>
</div>

<div class="dashboard-cards">
    <div class="dash-card highlight">
        <span>Revenue (approved)</span>
        <strong><?php echo format_price($revenue); ?></strong>
    </div>
    <div class="dash-card">
        <span>Pending bookings</span>
        <strong><?php echo $pending_bookings; ?></strong>
    </div>
    <div class="dash-card">
        <span>Total bookings</span>
        <strong><?php echo $total_bookings; ?></strong>
    </div>
    <div class="dash-card">
        <span>Cars</span>
        <strong><?php echo $total_cars; ?></strong>
    </div>
    <div class="dash-card">
        <span>Clients</span>
        <strong><?php echo $total_users; ?></strong>
    </div>
    <div class="dash-card">
        <span>Messages</span>
        <strong><?php echo $total_messages; ?></strong>
    </div>
</div>

<div class="section-head" style="margin-bottom: 16px;">
    <h2>Latest bookings</h2>
    <a href="bookings.php">See all &rarr;</a>
</div>

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
            </tr>
        </thead>
        <tbody>
            <?php while ($booking = mysqli_fetch_assoc($latest)) { ?>
                <tr>
                    <td><?php echo $booking['id']; ?></td>
                    <td><?php echo e($booking['name']); ?></td>
                    <td><?php echo e($booking['brand'] . ' ' . $booking['model']); ?></td>
                    <td><?php echo format_date($booking['start_date']); ?> &rarr; <?php echo format_date($booking['end_date']); ?></td>
                    <td><?php echo format_price($booking['total_price']); ?></td>
                    <td><?php echo status_badge($booking['status']); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php require_once "includes/admin-footer.php"; ?>
