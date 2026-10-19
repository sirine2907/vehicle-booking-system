<?php
// start the session on every page
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// escape text before showing it in html (protects against XSS)
function e($text)
{
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

function redirect($page)
{
    header("Location: " . $page);
    exit;
}

function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

function is_admin()
{
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin';
}

// send the visitor to the login page if he is not logged in
function require_login($login_page = "login.php")
{
    if (!is_logged_in()) {
        set_message("Please login first.", "error");
        redirect($login_page);
    }
}

// save a message in the session to show it on the next page
function set_message($text, $type = "success")
{
    $_SESSION['message'] = $text;
    $_SESSION['message_type'] = $type;
}

function show_message()
{
    if (isset($_SESSION['message'])) {
        echo '<div class="alert alert-' . e($_SESSION['message_type']) . '">' . e($_SESSION['message']) . '</div>';
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
}

function format_price($price)
{
    return number_format($price, 2) . ' DT';
}

function format_date($date)
{
    return date("d M Y", strtotime($date));
}

// number of rental days between two dates (minimum 1 day)
function count_days($start_date, $end_date)
{
    $days = (strtotime($end_date) - strtotime($start_date)) / 86400;
    if ($days < 1) {
        $days = 1;
    }
    return (int) $days;
}

// check if a car is already booked between two dates
function car_is_booked($conn, $car_id, $start_date, $end_date)
{
    $sql = "SELECT COUNT(*) AS total FROM bookings
            WHERE car_id = ? AND status IN ('pending', 'approved')
            AND start_date < ? AND end_date > ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $car_id, $end_date, $start_date);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    return $row['total'] > 0;
}

// small html badge for the booking status
function status_badge($status)
{
    return '<span class="badge badge-' . e($status) . '">' . e(ucfirst($status)) . '</span>';
}
