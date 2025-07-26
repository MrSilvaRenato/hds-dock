<?php
require_once 'controller/db.php';

try {
    $stmt = $conn->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "<h2>📦 Tables in Database:</h2><ul>";
    foreach ($tables as $table) {
        echo "<li>$table</li>";
    }
    echo "</ul>";

    echo "<h3>✅ Database connection successful!</h3>";
} catch (PDOException $e) {
    echo "<h3>❌ Database connection failed:</h3>" . $e->getMessage();
}