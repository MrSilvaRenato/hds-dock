<?php
session_start();
require_once 'db.php'; // adjust if needed

header('Content-Type: application/json');

// Ensure user is authenticated
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo json_encode(['error' => 'User not authenticated']);
    exit;
}

$start = $_GET['start'] ?? null;
$end = $_GET['end'] ?? null;

if (!$start || !$end) {
    echo json_encode(['error' => 'Invalid date range']);
    exit;
}

$userId = $_SESSION['user_id'];
$role = $_SESSION['role'];

try {
    if ($role === 'admin') {
        $stmt = $conn->prepare("SELECT id, booking_date, booking_time, transport_company_name FROM bookings WHERE booking_date BETWEEN :start AND :end");
        $stmt->execute([':start' => $start, ':end' => $end]);
    } else {
        $stmt = $conn->prepare("SELECT id, booking_date, booking_time, transport_company_name FROM bookings WHERE booking_date BETWEEN :start AND :end AND user_id = :user_id");
        $stmt->execute([':start' => $start, ':end' => $end, ':user_id' => $userId]);
    }

    $events = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $events[] = [
            'id' => $row['id'],
            'title' => $row['transport_company_name'],
            'start' => $row['booking_date'] . 'T' . $row['booking_time']
        ];
    }

    echo json_encode($events);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
