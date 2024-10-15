<?php
// db.php
$host = 'localhost'; // Your database host
$db = 'warehouse_db'; // Your database name
$user = 'root'; // Your database username
$pass = ''; // Your database password
$charset = 'utf8mb4'; // Ensure this variable is defined

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Establishing the connection and assigning it to the $conn variable
    $conn = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
