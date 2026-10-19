<?php
// database settings (change them to match your computer)
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "drivenow";

// connect to mysql
try {
    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    mysqli_set_charset($conn, "utf8mb4");
} catch (Exception $e) {
    die("Could not connect to the database. Please check the settings in includes/db.php");
}
