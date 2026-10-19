<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

if (!isset($page_title)) {
    $page_title = "Car Rental";
}

// used to highlight the current link in the menu
$current_page = basename($_SERVER['PHP_SELF']);

function nav_link($page, $label, $current_page)
{
    $class = ($page == $current_page) ? 'active' : '';
    echo '<a href="' . $page . '" class="' . $class . '">' . $label . '</a>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($page_title); ?> | DriveNow</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="navbar">
    <div class="container nav-inner">
        <a href="index.php" class="logo">Drive<span>Now</span></a>

        <button class="menu-toggle" id="menuToggle" aria-label="Open menu">
            <span></span><span></span><span></span>
        </button>

        <nav class="nav-links" id="navLinks">
            <?php nav_link("index.php", "Home", $current_page); ?>
            <?php nav_link("cars.php", "Cars", $current_page); ?>
            <?php nav_link("about.php", "About", $current_page); ?>
            <?php nav_link("contact.php", "Contact", $current_page); ?>

            <?php if (is_logged_in()) { ?>
                <?php nav_link("my-bookings.php", "My Bookings", $current_page); ?>
                <?php nav_link("profile.php", "Profile", $current_page); ?>
                <?php if (is_admin()) { ?>
                    <a href="admin/index.php">Admin</a>
                <?php } ?>
                <a href="logout.php" class="btn btn-outline btn-small">Logout</a>
            <?php } else { ?>
                <?php nav_link("login.php", "Login", $current_page); ?>
                <a href="register.php" class="btn btn-primary btn-small">Sign up</a>
            <?php } ?>
        </nav>
    </div>
</header>

<main>
    <div class="container message-box">
        <?php show_message(); ?>
    </div>
