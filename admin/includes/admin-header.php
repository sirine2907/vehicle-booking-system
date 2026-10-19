<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

// only admins can see these pages
if (!is_admin()) {
    set_message("You need an admin account to open this page.", "error");
    redirect("../login.php");
}

if (!isset($page_title)) {
    $page_title = "Admin";
}

$current_page = basename($_SERVER['PHP_SELF']);

function admin_link($page, $label, $current_page)
{
    // car-form.php is part of the cars section
    $active = ($page == $current_page || ($page == "cars.php" && $current_page == "car-form.php"));
    echo '<a href="' . $page . '" class="' . ($active ? 'active' : '') . '">' . $label . '</a>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($page_title); ?> | DriveNow Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="admin-layout">
    <aside class="sidebar">
        <a href="index.php" class="logo">Drive<span>Now</span></a>
        <p class="sidebar-label">Admin panel</p>

        <nav>
            <?php admin_link("index.php", "Dashboard", $current_page); ?>
            <?php admin_link("cars.php", "Cars", $current_page); ?>
            <?php admin_link("bookings.php", "Bookings", $current_page); ?>
            <?php admin_link("users.php", "Users", $current_page); ?>
            <?php admin_link("messages.php", "Messages", $current_page); ?>
        </nav>

        <nav class="sidebar-bottom">
            <a href="../index.php">View website</a>
            <a href="../logout.php">Logout</a>
        </nav>
    </aside>

    <main class="admin-main">
        <?php show_message(); ?>
