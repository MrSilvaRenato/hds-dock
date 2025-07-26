<?php

session_start();
include 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    handleBookingSubmission();
}

function handleBookingSubmission() {
    global $conn; // Use the global connection variable
    ob_start();
    $response = array(); // Initialize the response array

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $action = $_POST['action'] ?? 'add'; // Get action type (add/update)
        $bookingId = $_POST['booking_id'] ?? null;

        // Collect form data safely
        $transport_company_name = $_POST['transport_company'] ?? null;
        $palletsQuantity = (int)($_POST['pallets_quantity'] ?? 0);
        $truckType = $_POST['truck_type'] ?? null;
        $contactName = $_POST['contact_name'] ?? null;
        $contactNumber = $_POST['contact_number'] ?? null;
        $clientName = $_POST['client_name'] ?? null;
        $bookingTime = $_POST['booking_time'] ?? null;
        $dockNumber = (int)($_POST['dock_number'] ?? 0);
        $bookingDate = $_POST['booking_date'] ?? null;

        $user_id = $_SESSION['user_id'] ?? null; 
        $missingFields = [];

        if (empty($transport_company_name)) {
            $missingFields[] = 'Transport Company Name';
        }
        if (empty($bookingTime)) {
            $missingFields[] = 'Booking Time';
        }
        if (empty($dockNumber)) {
            $missingFields[] = 'Dock Number';
        }
        if (empty($user_id)) {
            $missingFields[] = 'User ID';
        }

        if (!empty($missingFields)) {
            $response['success'] = false;
            $response['message'] = "Please fill in all required fields: " . implode(', ', $missingFields);
        } else {
            // Check if the transport company name exists in the users table
            $checkQuery = "SELECT * FROM users WHERE transport_company_name = :transport_company_name";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->execute([':transport_company_name' => $transport_company_name]);

            if ($checkStmt->rowCount() === 0) {
                $response['success'] = false;
                $response['message'] = "Transport company name does not exist.";
            } else {
                try {
                    if ($action === 'add') {
                        // Insert new booking
                        $sql = "INSERT INTO bookings (user_id, booking_time, booking_date, transport_company_name, pallets_quantity, truck_type, contact_name, contact_number, client_name, dock_number) 
                                VALUES (:user_id, :booking_time, :booking_date, :transport_company_name, :pallets_quantity, :truck_type, :contact_name, :contact_number, :client_name, :dock_number)";

                        $stmt = $conn->prepare($sql);
                        $stmt->execute([
                            ':user_id' => $user_id,
                            ':booking_time' => $bookingTime,
                            ':booking_date' => $bookingDate,
                            ':transport_company_name' => $transport_company_name,
                            ':pallets_quantity' => $palletsQuantity,
                            ':truck_type' => $truckType,
                            ':contact_name' => $contactName,
                            ':contact_number' => $contactNumber,
                            ':client_name' => $clientName,
                            ':dock_number' => $dockNumber,
                        ]);

                        $response['success'] = true;
                        $response['message'] = "Booking successful!";
                    } elseif ($action === 'update' && !empty($bookingId)) {
                        // Update existing booking
                        $sql = "UPDATE bookings SET 
                                booking_time = :booking_time,
                                booking_date = :booking_date,
                                transport_company_name = :transport_company_name,
                                pallets_quantity = :pallets_quantity,
                                truck_type = :truck_type,
                                contact_name = :contact_name,
                                contact_number = :contact_number,
                                client_name = :client_name,
                                dock_number = :dock_number
                                WHERE id = :booking_id";

                        $stmt = $conn->prepare($sql);
                        $stmt->execute([
                            ':booking_time' => $bookingTime,
                            ':booking_date' => $bookingDate,
                            ':transport_company_name' => $transport_company_name,
                            ':pallets_quantity' => $palletsQuantity,
                            ':truck_type' => $truckType,
                            ':contact_name' => $contactName,
                            ':contact_number' => $contactNumber,
                            ':client_name' => $clientName,
                            ':dock_number' => $dockNumber,
                            ':booking_id' => $bookingId,
                        ]);

                        $response['success'] = true;
                        $response['message'] = "Booking updated successfully!";
                    } else {
                        $response['success'] = false;
                        $response['message'] = "Invalid action.";
                    }
                } catch (PDOException $e) {
                    $response['success'] = false;
                    $response['message'] = "Error: " . $e->getMessage();
                }
            }
        }
    }

    echo json_encode($response); // Send response back as JSON
    ob_end_flush();
}





//select time fetch and disable
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    handleBookingCheck(); // Call the function to check bookings
}
function handleBookingCheck() {
    
    global $conn;
    $dock_number = $_GET['dock_number'] ?? null;
    $booking_date = $_GET['booking_date'] ?? null;

    if ($dock_number && $booking_date) {
        $query = "SELECT booking_time, dock_number FROM bookings WHERE dock_number = :dock_number AND booking_date = :booking_date";
        $stmt = $conn->prepare($query);

        if ($stmt->execute([
            ':dock_number' => $dock_number,
            ':booking_date' => $booking_date
        ])) {
            $booked_times = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Check if any times were booked
            if (empty($booked_times)) {
                echo json_encode(['error' => 'empty']);
            } else {
                
                echo json_encode($booked_times);
            }
        } else {
            echo json_encode(['error' => 'Failed to execute query']);
        }
    } else {
        echo json_encode(['error' => 'Dock number or booking date not provided']);
    }
}