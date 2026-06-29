<?php

/**
 * Database Configuration
 * hotel_crs
 */

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'hotel_crs');
define('DB_CHARSET', 'utf8mb4');

$conn = mysqli_connect(
    DB_HOST,
    DB_USER,
    DB_PASS,
    DB_NAME
);

if (!$conn) {

    error_log(mysqli_connect_error());

    die("Database Connection Failed");
}

mysqli_set_charset(
    $conn,
    DB_CHARSET
);