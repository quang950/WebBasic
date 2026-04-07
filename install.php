<?php
/**
 * Installation Script - Initialize Database and Seed Data
 */

// MySQL connection without selecting database
$servername = "localhost";
$username = "root";
$password = "";

$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read and execute SQL file
$sqlFile = __DIR__ . '/DataBase/car_shop.sql';
if (!file_exists($sqlFile)) {
    die("SQL file not found: $sqlFile");
}

// Disable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

$sql = file_get_contents($sqlFile);
$queries = array_filter(array_map('trim', explode(';', $sql)), fn($q) => !empty($q));

$installed = 0;
$errors = [];

foreach ($queries as $query) {
    if (trim($query)) {
        if (!$conn->query($query)) {
            $errors[] = "Query failed: " . $conn->error . "\nQuery: " . substr($query, 0, 100);
        } else {
            $installed++;
        }
    }
}

// Re-enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// Output result
if (count($errors) === 0) {
    echo json_encode([
        'success' => true,
        'message' => "✅ Database setup complete. Installed $installed SQL statements successfully.",
        'errors' => []
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        'success' => false,
        'message' => "Database setup completed with errors. Installed $installed statements.",
        'errors' => $errors
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>
