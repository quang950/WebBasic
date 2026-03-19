<?php
// Database connection
error_reporting(E_ALL);
ini_set('display_errors', 0);

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'car_shop');

$conn = null;
$dbConnected = false;
$dbError = null;

if (class_exists('mysqli')) {
    mysqli_report(MYSQLI_REPORT_OFF);
    try {
        $tmpConn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($tmpConn && !$tmpConn->connect_error) {
            $tmpConn->set_charset("utf8mb4");
            $conn = $tmpConn;
            $dbConnected = true;
        } else {
            $dbError = $tmpConn ? $tmpConn->connect_error : 'Unknown DB connection error';
        }
    } catch (Throwable $e) {
        $dbError = $e->getMessage();
    }
} else {
    $dbError = 'PHP mysqli extension is not enabled';
}
?>