<?php
require_once "includes/db.php";
require_once "includes/functions.php";

require_login();

// only accept POST requests
if ($_SERVER['REQUEST_METHOD'] != "POST") {
    redirect("my-bookings.php");
}

$booking_id = (int) $_POST['booking_id'];
$user_id = $_SESSION['user_id'];
$today = date("Y-m-d");

// the user can only cancel his own bookings that did not start yet
$sql = "UPDATE bookings SET status = 'cancelled'
        WHERE id = ? AND user_id = ? AND status IN ('pending', 'approved') AND start_date > ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iis", $booking_id, $user_id, $today);
mysqli_stmt_execute($stmt);

if (mysqli_stmt_affected_rows($stmt) > 0) {
    set_message("Your booking was cancelled.");
} else {
    set_message("This booking can't be cancelled.", "error");
}

redirect("my-bookings.php");
