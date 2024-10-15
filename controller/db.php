<?php
// db.php
$host = 'uyu7j8yohcwo35j3.cbetxkdyhwsb.us-east-1.rds.amazonaws.com'; // Your database host
$db = 'm3rmvhjdnig3k8bj'; // Your database name
$user = 'w2c6b1ecngwol0yg'; // Your database username
$pass = 'sslzlob5862zrfiv'; // Your database passwords
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
