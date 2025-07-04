<?php
// Start session
session_start();

// Check if admin is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: adminlogin.php');
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cateringdb";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create bookings table if not exists, without status column
    $sql = "CREATE TABLE IF NOT EXISTS bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        service_type VARCHAR(100) NOT NULL,
        client_name VARCHAR(100) NOT NULL,
        client_email VARCHAR(100) NOT NULL,
        client_phone VARCHAR(20) NOT NULL,
        event_date DATE NOT NULL,
        venue VARCHAR(255) NOT NULL,
        people_count INT NOT NULL,
        additional_info TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

// Initialize variables
$error = "";
$success = "";
$filter_service = isset($_GET['service']) ? $_GET['service'] : "";

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];
    
    if ($action == "update") {
        $service_type = $_POST['service_type'];
        $client_name = $_POST['client_name'];
        $client_email = $_POST['client_email'];
        $client_phone = $_POST['client_phone'];
        $event_date = $_POST['event_date'];
        $venue = $_POST['venue'];
        $people_count = $_POST['people_count'];
        $additional_info = $_POST['additional_info'];
        
        try {
            $stmt = $conn->prepare("UPDATE bookings SET service_type = ?, client_name = ?, client_email = ?, client_phone = ?, event_date = ?, venue = ?, people_count = ?, additional_info = ? WHERE id = ?");
            $stmt->execute([$service_type, $client_name, $client_email, $client_phone, $event_date, $venue, $people_count, $additional_info, $_POST['id']]);
            $success = "Booking updated successfully.";
        } catch(PDOException $e) {
            $error = "Error updating booking: " . $e->getMessage();
        }
        // Redirect to maintain filter
        header("Location: bookings.php" . ($filter_service ? "?service=$filter_service" : ""));
        exit();
    } elseif ($action == "delete") {
        try {
            $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $success = "Booking deleted successfully.";
        } catch(PDOException $e) {
            $error = "Error deleting booking: " . $e->getMessage();
        }
        header("Location: bookings.php" . ($filter_service ? "?service=$filter_service" : ""));
        exit();
    }
}

// Fetch all services for filter dropdown
$stmt = $conn->query("SELECT DISTINCT service_type FROM services");
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch bookings (filtered by service type if selected)
$query = "SELECT * FROM bookings ORDER BY created_at DESC";
if ($filter_service) {
    $query = "SELECT * FROM bookings WHERE service_type = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute([$filter_service]);
} else {
    $stmt = $conn->query($query);
}
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - Catering Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #212529;
            color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background: #343a40;
            padding-top: 1rem;
            position: fixed;
            width: 250px;
        }
        .sidebar a {
            color: #f8f9fa;
            padding: 0.75rem 1rem;
            display: block;
            text-decoration: none;
            font-size: 1.1rem;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #ffc107;
            color: #212529;
            border-radius: 0.5rem;
        }
        .content {
            margin-left: 250px;
            padding: 2rem;
            background-color: #2c3034;
        }
        .table {
            background-color: #343a40;
            color: #f8f9fa;
        }
        .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: #495057;
        }
        .table th, .table td {
            border-color: #495057;
        }
        .btn-primary, .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #212529;
        }
        .btn-primary:hover, .btn-warning:hover {
            background-color: #ffca2c;
            border-color: #ffca2c;
        }
        .btn-danger {
            color: #212529;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .form-control, .form-select {
            background-color: #495057;
            color: #f8f9fa;
            border-color: #6c757d;
        }
        .form-control:focus, .form-select:focus {
            background-color: #495057;
            color: #f8f9fa;
            border-color: #ffc107;
            box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
        }
        .alert-success {
            background-color: #495057;
            color: #ffc107;
            border-color: #ffc107;
        }
        .alert-danger {
            background-color: #495057;
            color: #ffc107;
            border-color: #ffc107;
        }
        .modal-content {
            background-color: #343a40;
            color: #f8f9fa;
            border-color: #495057;
        }
        .modal-header, .modal-footer {
            border-color: #495057;
        }
        .form-control[readonly] {
            background-color: #495057;
            opacity: 1;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
            }
            .content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="text-center mb-4">
            <h4 class="text-white mt-2">Catering Admin</h4>
        </div>
        <nav>
            <a href="admindashboard.php"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
            <a href="customer.php"><i class="fas fa-users me-2"></i> Manage Customers</a>
            <a href="services.php"><i class="fas fa-utensils me-2"></i> Manage Services</a>
            <a href="bookings.php" class="active"><i class="fas fa-calendar-check me-2"></i> View Bookings</a>
            <a href="reports.php"><i class="fas fa-chart-bar me-2"></i> Generate Reports</a>
            <a href="adminlogin.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
        </nav>
    </div>
    <div class="content">
        <section id="bookings">
            <h2 class="mb-4">Manage Bookings</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <!-- Filter by Service Type -->
            <div class="mb-4">
                <form method="GET">
                    <div class="row g-3 align-items-center">
                        <div class="col-auto">
                            <label class="form-label">Filter by Service</label>
                        </div>
                        <div class="col-auto">
                            <select class="form-select" name="service" onchange="this.form.submit()">
                                <option value="">All Services</option>
                                <?php foreach ($services as $service): ?>
                                    <option value="<?php echo $service['service_type']; ?>" <?php echo $filter_service == $service['service_type'] ? 'selected' : ''; ?>>
                                        <?php echo ucfirst($service['service_type']); ?> Catering
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Bookings Table -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Service Type</th>
                            <th>Client Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Event Date</th>
                            <th>Venue</th>
                            <th>People</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($bookings)): ?>
                            <tr>
                                <td colspan="9" class="text-center">No bookings found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td><?php echo $booking['id']; ?></td>
                                    <td><?php echo ucfirst($booking['service_type']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['client_name']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['client_email']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['client_phone']); ?></td>
                                    <td><?php echo $booking['event_date']; ?></td>
                                    <td><?php echo htmlspecialchars($booking['venue']); ?></td>
                                    <td><?php echo $booking['people_count']; ?></td>
                                    <td>
                                        <!-- Edit Button triggers modal -->
                                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editBookingModal<?php echo $booking['id']; ?>">Edit</button>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $booking['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this booking?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <!-- Edit Modal for each booking -->
                                <div class="modal fade" id="editBookingModal<?php echo $booking['id']; ?>" tabindex="-1" aria-labelledby="editBookingModalLabel<?php echo $booking['id']; ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editBookingModalLabel<?php echo $booking['id']; ?>">Edit Booking</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="POST">
                                                    <input type="hidden" name="action" value="update">
                                                    <input type="hidden" name="id" value="<?php echo $booking['id']; ?>">
                                                    <input type="hidden" name="service_type" value="<?php echo htmlspecialchars($booking['service_type']); ?>">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label">Service Type</label>
                                                            <input type="text" class="form-control" value="<?php echo ucfirst(htmlspecialchars($booking['service_type'])); ?> Catering" readonly>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Client Name</label>
                                                            <input type="text" class="form-control" name="client_name" value="<?php echo htmlspecialchars($booking['client_name']); ?>" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Email</label>
                                                            <input type="email" class="form-control" name="client_email" value="<?php echo htmlspecialchars($booking['client_email']); ?>" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Phone</label>
                                                            <input type="tel" class="form-control" name="client_phone" value="<?php echo htmlspecialchars($booking['client_phone']); ?>" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Event Date</label>
                                                            <input type="date" class="form-control" name="event_date" value="<?php echo $booking['event_date']; ?>" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Venue</label>
                                                            <input type="text" class="form-control" name="venue" value="<?php echo htmlspecialchars($booking['venue']); ?>" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Number of People</label>
                                                            <input type="number" class="form-control" name="people_count" value="<?php echo $booking['people_count']; ?>" min="1" required>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label">Additional Info</label>
                                                            <textarea class="form-control" name="additional_info" rows="3"><?php echo htmlspecialchars($booking['additional_info']); ?></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="mt-3">
                                                        <button type="submit" class="btn btn-warning">Save Changes</button>
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>