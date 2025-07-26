<?php
require 'db.php'; // Ensure this connects using JAWSDB_MARIA_URL

$sqlUsers = file_get_contents('users.sql');
$sqlBookings = file_get_contents('bookings.sql');

try {
    $conn->exec($sqlUsers);
    $conn->exec($sqlBookings);
    echo "✅ Database tables and data imported successfully!";
} catch (PDOException $e) {
    echo "❌ Error importing SQL: " . $e->getMessage();
}
