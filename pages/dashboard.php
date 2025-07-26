<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php'); // Redirect to login if not logged in
    exit();
    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="../style/style.css">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Dashboard - Home Delivery Booking</title>
</head>
<body>
<header>
    <div class="header">
        <h1>Dashboard</h1>
        <p class="description">
            <?php 
                // Check if the user role is 'admin'
                if ($_SESSION['role'] === 'admin') {
                    echo "Welcome to your bookings dashboard (you are logged in as admin) " . $_SESSION['transport'] . "!";
                } else {
                    echo "Welcome to your bookings dashboard " . $_SESSION['transport'] . "!";
                }
            ?>
        </p>
    </div>
    <button id="logout">Logout</button>
</header>
  
</div>
<div class="exportBTN">
<div>
    <a href="../controller/export_csv.php" class="btn btnExport btn-success">
        ⬇️ Export Today’s Bookings (CSV)
    </a>
</div></div>
<div class="container">

  <div id="calendar"></div>

 <div class="pagination-controls">
    <button id="prevDay">← Previous Day</button>
    <button id="nextDay">Next Day →</button>
</div>


 <div class="bookings-list container">
  <div class="row" id="bookingList">
    <!-- Cards will go here -->
  </div>
</div>


    <div id="bookingModal" style="display:none;" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span> <!-- Close button -->

        <h2>Book a Timeslot</h2>
        <form id="bookingForm">
            <input type="hidden" id="bookingDate" name="booking_date" required>
            <input type="hidden" id="bookingId" name="booking_id">
                        <div class="form-group">
    <label for="dockSelect">Select Dock:</label>
    <select id="dockSelect" name="dock_number" required>
        <option value="Select">Select a dock</option> <!-- Default non-selectable option -->
        <option value="24">Dock 24</option>
        <option value="25">Dock 25</option>
        <option value="26">Dock 26</option>
        <option value="27">Dock 27</option>
    </select>
</div>
            <div class="form-group">
                <label for="bookingTime">Select Time:</label>
                <select id="bookingTime" name="booking_time" required>
                    <option value="">Select a time</option>
                </select>
            </div>

            <div class="form-group">
                <label for="transportCompany">Transport Company Name:</label>
                <input type="text" id="transportCompany" name="transport_company" required>
            </div>
            <div class="form-group">
                <label for="palletsQuantity">Pallets Quantity:</label>
                <input type="number" id="palletsQuantity" name="pallets_quantity" required>
            </div>
            <div class="form-group">
                <label for="truckType">Truck Type:</label>
                <select id="truckType" name="truck_type" required>
                    <option value="Rigid Truck">Rigid-Truck</option>
                    <option value="B-double">B-double</option>
                    <option value="Single Trailer">Single Trailer</option>
                    <option value="Van">Refrigerated Van</option>
                </select>
            </div>
            <div class="form-group">
                <label for="contactName">Contact Name:</label>
                <input type="text" id="contactName" name="contact_name" required>
            </div>
            <div class="form-group">
                <label for="contactNumber">Contact Number:</label>
                <input type="text" id="contactNumber" name="contact_number" required>
            </div>
            <div class="form-group">
                <label for="clientName">Client Name:</label>
                <select id="clientName" name="client_name" required>
                    <option value="Austral Fisheries">Austral Fisheries</option>
                    <option value="BE Campbell">BE Campbell</option>
                    <option value="BP CocaCola, Asahi, Frucore">BP CocaCola, Asahi, Frucore</option>
                    <option value="Sc3sixty">Sc3sixty</option>
                    <option value="Slade Fisheries">Slade Fisheries</option>
                    <option value="SureGood Foods">SureGood Foods</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <button type="submit" class="btn" id="addBookingButton">New booking</button>
            <br><br>
            <button type="submit" id="updateBookingButton" class="btn">Update booking</button>
        </form>
    </div>
</div>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
    <script src="../js/app.js"></script>
</body>
</html>

