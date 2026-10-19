<?php
require_once "includes/functions.php";

// remove everything from the session
$_SESSION = [];
session_destroy();

header("Location: index.php");
exit;
