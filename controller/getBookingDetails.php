<?php
session_start();
include 'db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['booking_id']) && isset($_SESSION['user_id'])) {
    $bookingId = $data['booking_id'];

    // Query to fetch the booking details
    $query = "SELECT * FROM bookings WHERE id = :booking_id";
    $stmt = $conn->prepare($query);
    $stmt->execute([':booking_id' => $bookingId]);

    // Check if the booking exists
    if ($stmt->rowCount() > 0) {
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        // Send the booking details back as JSON
        echo json_encode([
            'success' => true,
            'booking_date' => $booking['booking_date'],
            'booking_time' => $booking['booking_time'],
            'dock_number' => $booking['dock_number'],
            'transport_company' => $booking['transport_company_name'],
            'pallets_quantity' => $booking['pallets_quantity'],
            'truck_type' => $booking['truck_type'],
            'contact_name' => $booking['contact_name'],
            'contact_number' => $booking['contact_number'],
            'client_name' => $booking['client_name']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Booking not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}