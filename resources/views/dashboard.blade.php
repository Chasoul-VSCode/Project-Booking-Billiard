<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard - Billiard Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        // Get CSRF token from meta tag
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Billiard Admin</a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col">
                <h2>Booking Management</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBookingModal">
                    Add New Booking
                </button>
            </div>
        </div>

        <!-- Table Status Cards -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Table 1</h5>
                    </div>
                    <div class="card-body" id="table1Status">
                        <div class="text-center">Loading...</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Table 2</h5>
                    </div>
                    <div class="card-body" id="table2Status">
                        <div class="text-center">Loading...</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Table 3</h5>
                    </div>
                    <div class="card-body" id="table3Status">
                        <div class="text-center">Loading...</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer Name</th>
                            <th>Table Number</th>
                            <th>Date</th>
                            <th>Start Time</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="bookingsTableBody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Booking Modal -->
    <div class="modal fade" id="addBookingModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addBookingForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Customer Name</label>
                            <input type="text" name="customer_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Table Number</label>
                            <select name="table_number" class="form-select" required>
                                <option value="">Select Table</option>
                                <option value="1">Table 1</option>
                                <option value="2">Table 2</option>
                                <option value="3">Table 3</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" name="booking_date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Start Time</label>
                            <input type="time" name="start_time" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Duration (hours)</label>
                            <input type="number" name="duration" class="form-control" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveBooking()">Save Booking</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Booking Modal -->
    <div class="modal fade" id="editBookingModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editBookingForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="booking_id" id="edit_booking_id">
                        <div class="mb-3">
                            <label class="form-label">Customer Name</label>
                            <input type="text" name="customer_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Table Number</label>
                            <select name="table_number" class="form-select" required>
                                <option value="1">Table 1</option>
                                <option value="2">Table 2</option>
                                <option value="3">Table 3</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" name="booking_date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Start Time</label>
                            <input type="time" name="start_time" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Duration (hours)</label>
                            <input type="number" name="duration" class="form-control" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="updateBooking()">Update Booking</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Function to calculate remaining time
        function calculateRemainingTime(startTime, duration) {
            const [hours, minutes] = startTime.split(':');
            const startDate = new Date();
            startDate.setHours(parseInt(hours), parseInt(minutes), 0);
            
            const endDate = new Date(startDate.getTime() + duration * 60 * 60 * 1000);
            const now = new Date();
            
            if (now > endDate) return 'Completed';
            
            const remainingMs = endDate - now;
            const remainingHours = Math.floor(remainingMs / (1000 * 60 * 60));
            const remainingMinutes = Math.floor((remainingMs % (1000 * 60 * 60)) / (1000 * 60));
            
            return `${remainingHours}h ${remainingMinutes}m remaining`;
        }

        // Function to update table status cards
        function updateTableStatus() {
            fetch('/bookings')
                .then(response => response.json())
                .then(data => {
                    if(data.status === 'success') {
                        // Initialize all tables as empty
                        const tableStatus = {
                            1: { status: 'empty' },
                            2: { status: 'empty' },
                            3: { status: 'empty' }
                        };
                        
                        // Update status for active bookings
                        data.data.forEach(booking => {
                            if (booking.status === 'active') {
                                const remainingTime = calculateRemainingTime(booking.start_time, booking.duration);
                                tableStatus[booking.table_number] = {
                                    status: 'occupied',
                                    customer: booking.customer_name,
                                    remainingTime: remainingTime
                                };
                            }
                        });
                        
                        // Update UI for each table
                        for (let i = 1; i <= 3; i++) {
                            const tableCard = document.getElementById(`table${i}Status`);
                            if (tableStatus[i].status === 'empty') {
                                tableCard.innerHTML = `
                                    <div class="text-center">
                                        <h6 class="text-muted">Available</h6>
                                        <p class="mb-0">Table is empty</p>
                                    </div>
                                `;
                            } else {
                                tableCard.innerHTML = `
                                    <div class="text-center">
                                        <h6 class="text-success">Occupied</h6>
                                        <p class="mb-0">Customer: ${tableStatus[i].customer}</p>
                                        <p class="mb-0">${tableStatus[i].remainingTime}</p>
                                    </div>
                                `;
                            }
                        }
                    }
                });
        }

        // Update table status every minute
        setInterval(updateTableStatus, 60000);
        
        // Load table status when page loads
        document.addEventListener('DOMContentLoaded', () => {
            updateTableStatus();
            loadBookings();
        });

        function loadBookings() {
            fetch('/bookings')
                .then(response => response.json())
                .then(data => {
                    if(data.status === 'success') {
                        const tbody = document.getElementById('bookingsTableBody');
                        tbody.innerHTML = '';
                        
                        data.data.forEach(booking => {
                            const statusClass = booking.status === 'active' ? 'bg-success' : 
                                              booking.status === 'completed' ? 'bg-info' : 'bg-secondary';
                            
                            tbody.innerHTML += `
                                <tr>
                                    <td>${booking.id}</td>
                                    <td>${booking.customer_name}</td>
                                    <td>Table ${booking.table_number}</td>
                                    <td>${booking.booking_date}</td>
                                    <td>${booking.start_time}</td>
                                    <td>${booking.duration} hours</td>
                                    <td><span class="badge ${statusClass}">${booking.status}</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" onclick="editBooking(${JSON.stringify(booking).replace(/"/g, '&quot;')})">Edit</button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteBooking(${booking.id})">Delete</button>
                                    </td>
                                </tr>
                            `;
                        });
                    }
                });
        }

        function saveBooking() {
            const form = document.getElementById('addBookingForm');
            const formData = new FormData(form);

            fetch('/bookings', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addBookingModal'));
                    modal.hide();
                    form.reset();
                    loadBookings();
                    updateTableStatus();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to save booking');
            });
        }

        function editBooking(booking) {
            const form = document.getElementById('editBookingForm');
            form.booking_id.value = booking.id;
            form.customer_name.value = booking.customer_name;
            form.table_number.value = booking.table_number;
            form.booking_date.value = booking.booking_date;
            form.start_time.value = booking.start_time;
            form.duration.value = booking.duration;

            const modal = new bootstrap.Modal(document.getElementById('editBookingModal'));
            modal.show();
        }

        function updateBooking() {
            const form = document.getElementById('editBookingForm');
            const formData = new FormData(form);
            const bookingId = formData.get('booking_id');

            fetch(`/bookings/${bookingId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editBookingModal'));
                    modal.hide();
                    loadBookings();
                    updateTableStatus();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to update booking');
            });
        }

        function deleteBooking(id) {
            if(confirm('Are you sure you want to delete this booking?')) {
                const formData = new FormData();
                formData.append('_method', 'DELETE');
                
                fetch(`/bookings/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if(data.status === 'success') {
                        loadBookings();
                        updateTableStatus();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to delete booking');
                });
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
