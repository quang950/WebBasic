<?php
// Database connection
error_reporting(E_ALL);
ini_set('display_errors', 0);

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'car_shop');

$conn = null;
$pdo = null;
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
        
        // Init PDO theo rule.md
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        
    } catch (Throwable $e) {
        $dbError = $e->getMessage();
    }
} else {
    $dbError = 'PHP mysqli extension is not enabled';
}
?>