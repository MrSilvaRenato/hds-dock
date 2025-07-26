// document.addEventListener('DOMContentLoaded', function() {
//     var calendarEl = document.getElementById('calendar');
//     var calendar = new FullCalendar.Calendar(calendarEl, {
//         initialView: 'dayGridMonth',
//         validRange: {
//             start: new Date() // Set the start date to today
//         },
       
//         events: '/path/to/your/event-source.php' // Endpoint to fetch booked events
//     });
//     calendar.render();
// });


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

let currentDate = new Date();
let bookingsGrouped = {};

function changeDate(offset) {
    currentDate.setDate(currentDate.getDate() + offset);
    const dateStr = currentDate.toISOString().split('T')[0];
    document.getElementById('currentDateLabel').innerText = dateStr;
    renderCardsForDate(dateStr, bookingsGrouped);
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
            const calendarEl = document.getElementById('calendar');
            const bookingList = document.getElementById('bookingList');
            bookingList.innerHTML = '';

            const bookings = data.bookings || [];

            const events = bookings.map(booking => ({
                title: `${booking.client_name} (${booking.pallets_quantity} PL)`,
                start: `${booking.booking_date}T${booking.booking_time}`,
                allDay: false,
                extendedProps: {
                    ...booking
                }
            }));

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: events,
              dateClick: function(info) {
    const selectedDate = info.dateStr;

    // Show booking modal
    const modal = document.getElementById("bookingModal");
    const bookingDateInput = document.getElementById("bookingDate");
    if (modal && bookingDateInput) {
        modal.style.display = "block";
        bookingDateInput.value = selectedDate;
    }

    // Switch to Add mode
    document.getElementById('addBookingButton').style.display = 'block';
    document.getElementById('updateBookingButton').style.display = 'none';

    // Also update cards below
    renderCardsForDate(selectedDate, groupBookingsByDate(bookings));
}
            });

            calendar.render();

            // Show today’s bookings on initial load
            const today = new Date().toISOString().split('T')[0];
            renderCardsForDate(today, groupBookingsByDate(bookings));

            setupPagination(today, groupBookingsByDate(bookings));
        })
        .catch(error => console.error('Error fetching bookings:', error));
}

function renderCardsForDate(date, bookingsByDate) {
    const bookingList = document.getElementById('bookingList');
    bookingList.innerHTML = '';

    const bookings = bookingsByDate[date] || [];

    if (bookings.length === 0) {
        bookingList.innerHTML = `<p>No bookings found for ${date}.</p>`;
        return;
    }

    bookings.forEach(booking => {
        const card = document.createElement('div');
        card.className = 'booking-card';
        card.innerHTML = `
            <div class="card-header">
                <strong>${booking.client_name}</strong> – ${booking.booking_date} at ${booking.booking_time.substring(0, 5)}
            </div>
            <div class="card-body">
                <p><strong>Dock:</strong> ${booking.dock_number}</p>
                <p><strong>Transport:</strong> ${booking.transport_company_name}</p>
                <p><strong>Pallets:</strong> ${booking.pallets_quantity}</p>
                <button class="update-btn" data-id="${booking.id}" data-booking='${JSON.stringify(booking)}'>Update</button>
                <button class="cancel-btn" data-id="${booking.id}" data-booking='${JSON.stringify(booking)}'>Cancel</button>
            </div>
        `;
        bookingList.appendChild(card);
    });
}

function groupBookingsByDate(bookings) {
    const grouped = {};
    bookings.forEach(b => {
        const date = b.booking_date;
        if (!grouped[date]) grouped[date] = [];
        grouped[date].push(b);
    });
    return grouped;
}

function setupPagination(currentDate, bookingsByDate) {
    const prevBtn = document.getElementById('prevDay');
    const nextBtn = document.getElementById('nextDay');
    let selectedDate = new Date(currentDate);

    function updateView() {
        const formatted = selectedDate.toISOString().split('T')[0];
        renderCardsForDate(formatted, bookingsByDate);
    }

    prevBtn.addEventListener('click', () => {
        selectedDate.setDate(selectedDate.getDate() - 1);
        updateView();
    });

    nextBtn.addEventListener('click', () => {
        selectedDate.setDate(selectedDate.getDate() + 1);
        updateView();
    });
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