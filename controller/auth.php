<?php
session_start();
require_once 'db.php'; // Make sure to include your database connection

// Function to handle login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Authentication successful
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['transport'] = $user['transport_company_name'];
        $_SESSION['role'] = $user['role'];
        header('Location: ../pages/dashboard.php');
        
    } else {
        // Invalid credentials
        echo "Invalid email or password.";
    }
}

// Function to handle registration
// Function to handle registration
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register_email']) && isset($_POST['register_password']) && isset($_POST['confirm_password']) && isset($_POST['transport_company'])) {
    $register_email = $_POST['register_email'];
    $register_password = $_POST['register_password'];
    $confirm_password = $_POST['confirm_password'];
    $transport_company_name = $_POST['transport_company']; // Get client info

    if ($register_password === $confirm_password) {
        $hashed_password = password_hash($register_password, PASSWORD_DEFAULT);

        // Prepare the SQL statement for inserting the user
        $stmt = $conn->prepare("INSERT INTO users (email, password, transport_company_name) VALUES (?, ?, ?)");
        if ($stmt->execute([$register_email, $hashed_password, $transport_company_name])) {
            echo "Registration successful!";
            exit;
        } else {
            echo "Error registering user.";
        }
    } else {
        echo "Passwords do not match.";
    }
}

// Forgot password feature (simple example, improve it with token system)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['forgot_email'])) {
    $forgot_email = $_POST['forgot_email'];

    // Check if email exists in the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$forgot_email]);
    $user = $stmt->fetch();

    if ($user) {
        // Here you would typically send a reset link to the user's email
        echo "Reset link sent to $forgot_email.";
    } else {
        echo "Email not found.";
    }
}


if (isset($_SESSION['user_id']) && isset($_SESSION['role']) ) {
    
$userId = $_SESSION['user_id'];

    // Check the user role
    if ($_SESSION['role'] === 'admin') {
        // Admin can see all bookings
        $query = "SELECT id, booking_date, booking_time, dock_number, transport_company_name, client_name, pallets_quantity FROM bookings"; 
        $stmt = $conn->prepare($query);
        $stmt->execute();
       
    } 
    // Client can only see their own bookings
    else if ($_SESSION['role'] === 'client') {
        $query = "SELECT id, booking_date, booking_time, dock_number, transport_company_name, client_name, pallets_quantity FROM bookings WHERE user_id = :user_id"; 
        $stmt = $conn->prepare($query);
        $stmt->execute([':user_id' => $userId]); // Using user_id from the session
       
    } 
    else {
        // If role is not recognized, return an error message
        echo json_encode(['error' => 'No bookings recorded']);
       
    }

    // Fetch all the bookings
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ob_end_clean();
    // Send the response as JSON
    header('Content-Type: application/json');
    echo json_encode(['bookings' => $bookings]);

} else {
    // Handle case where session is not set
    echo json_encode(['error' => 'User not authenticated']);
}
