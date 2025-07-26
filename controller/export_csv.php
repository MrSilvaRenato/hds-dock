<?php
ob_start();
require_once 'db.php';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="bookings_' . date('Y-m-d') . '.csv"');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');
fputcsv($output, ['ID', 'Date', 'Time', 'Dock', 'Client', 'Transport', 'Pallets', 'Truck Type', 'Contact Name', 'Contact Number']);

$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT * FROM bookings WHERE booking_date = ?");
$stmt->execute([$today]);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, [
        $row['id'],
        $row['booking_date'],
        $row['booking_time'],
        $row['dock_number'],
        $row['client_name'],
        $row['transport_company_name'],
        $row['pallets_quantity'],
        $row['truck_type'],
        $row['contact_name'],
        $row['contact_number']
    ]);
}
fclose($output);
ob_end_flush();
exit;
