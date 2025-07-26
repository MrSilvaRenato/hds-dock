<?php
session_start();
include 'db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['action']) && isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    $action = $data['action'];

    // Update booking logic
    if ($action === 'update' && isset($data['booking_id']) && isset($data['updatedDetails'])) {
        $details = explode(' ', $data['updatedDetails']);
        $updatedDate = $details[0];
        $updatedTime = $details[1];

        $query = "UPDATE bookings SET booking_date = :booking_date, booking_time = :booking_time WHERE id = :booking_id";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':booking_date' => $updatedDate,
            ':booking_time' => $updatedTime,
            ':booking_id' => $data['booking_id']
        ]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No rows updated']);
        }
    }

    // Delete booking logic
    if ($action === 'delete' && isset($data['booking_id'])) {
        $query = "DELETE FROM bookings WHERE id = :booking_id";
        $stmt = $conn->prepare($query);
        $stmt->execute([':booking_id' => $data['booking_id']]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No rows deleted']);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}