<?php
require_once "../includes/db.php";
require_once "../includes/functions.php";

if (!is_admin()) {
    redirect("../login.php");
}

// delete a car
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['delete_id'])) {
    $car_id = (int) $_POST['delete_id'];

    // don't delete a car that has bookings, it would break the history
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM bookings WHERE car_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $car_id);
    mysqli_stmt_execute($stmt);
    $total = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];

    if ($total > 0) {
        set_message("This car has bookings, so it can't be deleted. You can mark it as unavailable instead.", "error");
    } else {
        // remove the uploaded image file too
        $stmt = mysqli_prepare($conn, "SELECT image FROM cars WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $car_id);
        mysqli_stmt_execute($stmt);
        $car = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        if ($car && strpos($car['image'], "uploads/") === 0 && file_exists("../" . $car['image'])) {
            unlink("../" . $car['image']);
        }

        $stmt = mysqli_prepare($conn, "DELETE FROM cars WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $car_id);
        mysqli_stmt_execute($stmt);
        set_message("The car was deleted.");
    }
    redirect("cars.php");
}

$cars = mysqli_query($conn, "SELECT cars.*, (SELECT COUNT(*) FROM bookings WHERE bookings.car_id = cars.id) AS total_bookings
                             FROM cars ORDER BY id DESC");

$page_title = "Cars";
require_once "includes/admin-header.php";
?>

<div class="admin-top">
    <div>
        <h1>Cars</h1>
        <p class="muted"><?php echo mysqli_num_rows($cars); ?> car(s) in the garage</p>
    </div>
    <a href="car-form.php" class="btn btn-primary">+ Add a car</a>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Car</th>
                <th>Type</th>
                <th>Price / day</th>
                <th>Bookings</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($car = mysqli_fetch_assoc($cars)) { ?>
                <tr>
                    <td>
                        <div class="table-car">
                            <img src="../<?php echo e($car['image']); ?>" alt="">
                            <div>
                                <strong><?php echo e($car['brand'] . ' ' . $car['model']); ?></strong><br>
                                <span class="muted"><?php echo e($car['year']); ?></span>
                            </div>
                        </div>
                    </td>
                    <td><?php echo e($car['type']); ?></td>
                    <td><?php echo format_price($car['price_per_day']); ?></td>
                    <td><?php echo $car['total_bookings']; ?></td>
                    <td>
                        <?php if ($car['is_available']) { ?>
                            <span class="badge badge-available">Available</span>
                        <?php } else { ?>
                            <span class="badge badge-unavailable">Unavailable</span>
                        <?php } ?>
                    </td>
                    <td>
                        <div class="actions">
                            <a href="car-form.php?id=<?php echo $car['id']; ?>" class="btn btn-outline btn-small">Edit</a>
                            <form method="post">
                                <input type="hidden" name="delete_id" value="<?php echo $car['id']; ?>">
                                <button type="submit" class="btn btn-danger btn-small" data-confirm="Delete this car?">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php require_once "includes/admin-footer.php"; ?>
