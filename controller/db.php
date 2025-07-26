<?php
$url = parse_url(getenv("JAWSDB_MARIA_URL"));

$host = $url["host"];
$user = $url["user"];
$pass = $url["pass"];
$db   = substr($url["path"], 1);
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $conn = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

$pdo = $conn; // 👈 Add this line to fix the undefined variable issue