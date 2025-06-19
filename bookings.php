<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cateringdb";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create bookings table if not exists, with status column
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
        status ENUM('pending', 'declined') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

// Initialize form variables
$action = "create";
$booking_id = "";
$service_type = "";
$client_name = "";
$client_email = "";
$client_phone = "";
$event_date = "";
$venue = "";
$people_count = "";
$additional_info = "";
$status = "pending";
$title = "Add New Booking";
$error = "";
$filter_service = isset($_GET['service']) ? $_GET['service'] : "";

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];
    
    if ($action == "create" || $action == "update") {
        $service_type = $_POST['service_type'];
        $client_name = $_POST['client_name'];
        $client_email = $_POST['client_email'];
        $client_phone = $_POST['client_phone'];
        $event_date = $_POST['event_date'];
        $venue = $_POST['venue'];
        $people_count = $_POST['people_count'];
        $additional_info = $_POST['additional_info'];
        $status = $_POST['status'];
        
        if ($action == "create") {
            $stmt = $conn->prepare("INSERT INTO bookings (service_type, client_name, client_email, client_phone, event_date, venue, people_count, additional_info, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$service_type, $client_name, $client_email, $client_phone, $event_date, $venue, $people_count, $additional_info, $status]);
        } else {
            $stmt = $conn->prepare("UPDATE bookings SET service_type = ?, client_name = ?, client_email = ?, client_phone = ?, event_date = ?, venue = ?, people_count = ?, additional_info = ?, status = ? WHERE id = ?");
            $stmt->execute([$service_type, $client_name, $client_email, $client_phone, $event_date, $venue, $people_count, $additional_info, $status, $_POST['id']]);
        }
        // Redirect to clear form
        header("Location: bookings.php" . ($filter_service ? "?service=$filter_service" : ""));
        exit();
    } elseif ($action == "delete") {
        $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        header("Location: bookings.php" . ($filter_service ? "?service=$filter_service" : ""));
        exit();
    } elseif ($action == "decline") {
        $stmt = $conn->prepare("UPDATE bookings SET status = 'declined' WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        header("Location: bookings.php" . ($filter_service ? "?service=$filter_service" : ""));
        exit();
    } elseif ($action == "edit") {
        // Populate form for editing
        $stmt = $conn->prepare("SELECT * FROM bookings WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($booking) {
            $action = "update";
            $booking_id = $booking['id'];
            $service_type = $booking['service_type'];
            $client_name = $booking['client_name'];
            $client_email = $booking['client_email'];
            $client_phone = $booking['client_phone'];
            $event_date = $booking['event_date'];
            $venue = $booking['venue'];
            $people_count = $booking['people_count'];
            $additional_info = $booking['additional_info'];
            $status = $booking['status'];
            $title = "Edit Booking";
        }
    }
}

// Fetch all services for filter and form dropdown
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
    <title>Manage Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #212529;
        }
        .table .status-declined {
            color: #dc3545;
        }
        .table .status-pending {
            color: #ffc107;
        }
    </style>
</head>
<body class="text-light">
    <div class="container mt-5">
        <h2 class="text-warning">Manage Bookings</h2>
        <p class="mb-3"><a href="admindashboard.php" class="text-light">View AdminDashboard</a> | <a href="services.php" class="text-light">Manage Services</a></p>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- Filter by Service Type -->
        <div class="mb-4">
            <form method="GET">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label class="form-label">Filter by Service</label>
                    </div>
                    <div class="col-auto">
                        <select class="form-select bg-dark text-light border-warning" name="service" onchange="this.form.submit()">
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

        <!-- Create/Update Form -->
        <div class="card mb-4 bg-dark text-light border-warning">
            <div class="card-body">
                <h5 class="card-title text-warning"><?php echo $title; ?></h5>
                <form method="POST">
                    <input type="hidden" name="action" value="<?php echo $action; ?>">
                    <input type="hidden" name="id" value="<?php echo $booking_id; ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Service Type</label>
                            <select class="form-select bg-dark text-light border-warning" name="service_type" required>
                                <?php foreach ($services as $service): ?>
                                    <option value="<?php echo $service['service_type']; ?>" <?php echo $service_type == $service['service_type'] ? 'selected' : ''; ?>>
                                        <?php echo ucfirst($service['service_type']); ?> Catering
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Client Name</label>
                            <input type="text" class="form-control bg-dark text-light border-warning" name="client_name" value="<?php echo htmlspecialchars($client_name); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control bg-dark text-light border-warning" name="client_email" value="<?php echo htmlspecialchars($client_email); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="tel" class="form-control bg-dark text-light border-warning" name="client_phone" value="<?php echo htmlspecialchars($client_phone); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Event Date</label>
                            <input type="date" class="form-control bg-dark text-light border-warning" name="event_date" value="<?php echo $event_date; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Venue</label>
                            <input type="text" class="form-control bg-dark text-light border-warning" name="venue" value="<?php echo htmlspecialchars($venue); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Number of People</label>
                            <input type="number" class="form-control bg-dark text-light border-warning" name="people_count" value="<?php echo $people_count; ?>" min="1" required>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Additional Info</label>
                            <textarea class="form-control bg-dark text-light border-warning" name="additional_info" rows="3"><?php echo htmlspecialchars($additional_info); ?></textarea>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-warning">Save Booking</button>
                        <a href="bookings.php<?php echo $filter_service ? '?service=' . urlencode($filter_service) : ''; ?>" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bookings Table -->
        <table class="table table-striped bg-dark text-light border-warning">
            <thead>
                <tr class="text-warning">
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
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="edit">
                                <input type="hidden" name="id" value="<?php echo $booking['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-warning">Edit</button>
                            </form>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $booking['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this booking?')">Delete</button>
                            </form>
                            
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>