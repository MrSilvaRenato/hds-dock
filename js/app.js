document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        validRange: {
            start: new Date() // Set the start date to today
        },
        dateClick: function(info) {

            document.getElementById('bookingDate').value = info.dateStr; // Set booking date
            document.getElementById('bookingModal').style.display = 'block'; // Show modal
            document.getElementById('addBookingButton').style.display = 'block';
            document.getElementById('updateBookingButton').style.display = 'none';
        },
        events: '/path/to/your/event-source.php' // Endpoint to fetch booked events
    });
    calendar.render();
});


document.getElementById('logout').addEventListener('click', function() {
    window.location.href = '../controller/logout.php'; // Create a logout.php to destroy session
});


// Function to close the modal
function closeModal() {
    document.getElementById('bookingModal').style.display = 'none';
}

// Adding an event listener to close the modal when the user clicks outside it
window.addEventListener('click', function(event) {
    if (event.target === document.getElementById('bookingModal')) {
        closeModal();
    }
});



//handling add new booking and update booking
document.addEventListener('DOMContentLoaded', function() {
    const bookingForm = document.getElementById('bookingForm');
    const addBookingButton = document.getElementById('addBookingButton');
    const updateBookingButton = document.getElementById('updateBookingButton');

    if (bookingForm) {
        addBookingButton.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent default submission
            submitBookingForm('add');
        });

        updateBookingButton.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent default submission
            submitBookingForm('update');
        });
    }
});

// Function to submit the booking form
function submitBookingForm(action) {
    const bookingForm = document.getElementById('bookingForm');
    const formData = new FormData(bookingForm);

    // Append action type to the form data
    formData.append('action', action);

    fetch('../controller/functions.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message); // Show success message
            window.location.reload();
            closeModal(); // Close the modal
            bookingForm.reset(); // Reset the form fields
        } else {
            alert(data.message); // Show error message
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while submitting the form.');
    });
}



// Function to generate timeslots
function generateTimeSlots() {
    const timeslotSelect = document.getElementById('bookingTime');
    const startHour = 6;  // 6:00 AM
    const endHour = 14;   // 2:00 PM
    const interval = 45;  // 45 minutes

    // Clear previous options if any
    timeslotSelect.innerHTML = 'Select a time';

    // Initialize the first time
    let currentHour = startHour;
    let currentMinute = 0;

    while (currentHour < endHour || (currentHour === endHour && currentMinute <= 30)) {
        // Format minutes and hours
        let formattedHour = currentHour < 10 ? '0' + currentHour : currentHour;
        let formattedMinute = currentMinute < 10 ? '0' + currentMinute : currentMinute;
        let time = `${formattedHour}:${formattedMinute}`;

        // Create an option element
        const option = document.createElement('option');
        option.value = time;
        option.textContent = time;
        timeslotSelect.appendChild(option);

        // Update the time by adding the interval
        currentMinute += interval;
        if (currentMinute >= 60) {
            currentMinute -= 60;
            currentHour++;
        }
    }
}

// Function to handle dock number and date selection and disable already booked times
function handleDockAndDateSelection() {
    const dockNumber = document.getElementById('dockSelect').value;  // Selected dock number
    const bookingDate = document.getElementById('bookingDate').value;  // Date selected on calendar
    const timeslotSelect = document.getElementById('bookingTime');

    if (dockNumber && bookingDate) {
        // Regenerate the timeslots each time the dock or date is changed
        generateTimeSlots();

        // Send an AJAX request to the server
        fetch('../controller/functions.php?' + new URLSearchParams({
            'dock_number': dockNumber,
            'booking_date': bookingDate
        }))
        .then(response => response.json())  // Parse JSON response
        .then(data => {
            console.log(data); // Log the response to check its structure

            // Enable all options first before applying the booked times
            for (const option of timeslotSelect.options) {
                option.disabled = false;  // Ensure all options are enabled first
                option.style.backgroundColor = '';  // Reset background color
                option.textContent = option.value;  // Reset text content to original time
            }

            // Loop through each booked time and disable it in the timeslot dropdown
            data.forEach(bookedTime => {
                // Check if the dock_number matches
                if (bookedTime.dock_number == dockNumber) {
                    const time = bookedTime.booking_time.substring(0, 5);  // Extract the booking_time and format it

                    for (const option of timeslotSelect.options) {
                        if (option.value === time) {
                            option.disabled = true;  // Disable the option
                            option.style.backgroundColor = 'rgba(255, 99, 71, 0.3)';  // Soft red with transparency
                            option.textContent += ' - Slot taken';  // Append message if not already added
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error:', error));
    }
}

// Attach the event listener to the dock selection
document.getElementById('dockSelect').addEventListener('change', handleDockAndDateSelection);

// Call the function to initially generate timeslots when the page loads
generateTimeSlots();


document.addEventListener('DOMContentLoaded', () => {
    fetchBookings(); // Fetch and display bookings on page load
});
// Example of creating a table for bookings
function fetchBookings() {
    fetch('../controller/auth.php') 
        .then(response => response.json())
        .then(data => {
            const bookingList = document.getElementById('bookingList');
            bookingList.innerHTML = ''; 

            if (data.bookings && data.bookings.length === 0) {
                bookingList.innerHTML = '<p>A booking has not been made.</p>';
            } else if (data.bookings) {
                let tableHTML = `
                    <table class="booking-table">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Dock N*</th>
                                <th>Transport</th>
                                <th>Client</th>
                                <th>Pallets</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                data.bookings.forEach(booking => {
                    const formattedTime = booking.booking_time.substring(0, 5);
                    tableHTML += `
                        <tr>
                         <td>
                                <button class="update-btn" data-id="${booking.id}" data-booking='${JSON.stringify(booking)}'>Update</button>
                                <button class="cancel-btn" data-id="${booking.id}" data-booking='${JSON.stringify(booking)}'>Cancel</button>
                            </td>
                            <td>${booking.booking_date}</td>
                            <td>${formattedTime}</td>
                            <td>${booking.dock_number}</td>
                            <td>${booking.transport_company_name}</td>
                            <td>${booking.client_name}</td>
                            <td>${booking.pallets_quantity}</td>

                        </tr>
                    ` 
                    console.log(booking ); });

                tableHTML += '</tbody></table>';
                bookingList.innerHTML = tableHTML;
            } else {
                bookingList.innerHTML = '<p>Please, make a booking.</p>';
            }
        })
        .catch(error => console.error('Error fetching bookings:', error));
}



// document.addEventListener('click', function(e) {
//     // Handle Update Booking button click
//     if (e.target && e.target.classList.contains('update-btn')) {
//         const bookingId = e.target.getAttribute('data-id'); // Get booking ID
//         const bookingDetails = JSON.parse(e.target.getAttribute('data-booking')); // Get booking details

//         // Populate form fields or handle the update logic
//         // Example: show a modal with booking details to edit
//         console.log('Update booking:', bookingId, bookingDetails);
//         // You can now populate a modal or form with these details and send an update request
//     }

//     // Handle Cancel Booking button click
//     if (e.target && e.target.classList.contains('cancel-btn')) {
//         const bookingId = e.target.getAttribute('data-id'); // Get booking ID
//         console.log('Cancel booking:', bookingId);
        
//         // Confirm delete action
//         if (confirm('Are you sure you want to cancel this booking?')) {
//             // Make an API call to delete the booking
//             fetch('../controller/cancel_update.php', {
//                 method: 'POST',
//                 headers: {
//                     'Content-Type': 'application/json'
//                 },
//                 body: JSON.stringify({ booking_id: bookingId, action: 'delete' })
//             })
//             .then(response => response.json())
//             .then(data => {
//                 if (data.success) {
//                     alert('Booking cancelled successfully');
//                     fetchBookings(); // Refresh the bookings list
//                 } else {
//                     alert('Error cancelling booking: ' + data.message);
//                 }
//             })
//             .catch(error => console.error('Error:', error));
//         }
//     }
// });



document.addEventListener('click', function(e) {
    // Handle Update Booking button click
    if (e.target && e.target.classList.contains('update-btn')) {
        const bookingId = e.target.getAttribute('data-id'); // Get booking ID

        // Call the function to open the modal with booking details
        openUpdateModal(bookingId);
       document.getElementById('addBookingButton').style.display = 'none';
    }

    // Handle Cancel Booking button click
    if (e.target && e.target.classList.contains('cancel-btn')) {
        const bookingId = e.target.getAttribute('data-id'); // Get booking ID
        console.log('Cancel booking:', bookingId);
        
        // Confirm delete action
        if (confirm('Are you sure you want to cancel this booking?')) {
            // Make an API call to delete the booking
            fetch('../controller/cancel_update.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ booking_id: bookingId, action: 'delete' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Booking cancelled successfully');
                    fetchBookings(); // Refresh the bookings list
                } else {
                    alert('Error cancelling booking: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }
});


// Function to open the modal and populate it with booking data for update
function openUpdateModal(bookingId) {
    // Send an AJAX request to fetch the booking details
    fetch('../controller/getBookingDetails.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ booking_id: bookingId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Populate the form fields with the booking details
            document.getElementById('bookingDate').value = data.booking_date;
            document.getElementById('bookingTime').value = data.booking_time;
            document.getElementById('dockSelect').value = data.dock_number;
            document.getElementById('transportCompany').value = data.transport_company;
            document.getElementById('palletsQuantity').value = data.pallets_quantity;
            document.getElementById('truckType').value = data.truck_type;
            document.getElementById('contactName').value = data.contact_name;
            document.getElementById('contactNumber').value = data.contact_number;
            document.getElementById('clientName').value = data.client_name;

            // Set the booking ID as a hidden input (if needed for updates)
            document.getElementById('bookingId').value = bookingId;

            // Show the modal
            document.getElementById('bookingModal').style.display = 'block';
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while fetching booking details.');
    });
}

// Ensure you have the correct selector for your update buttons
document.querySelectorAll('.update-btn').forEach(button => {
    button.addEventListener('click', function() {
        const bookingId = this.getAttribute('data-id');
        openUpdateModal(bookingId);
    });
});