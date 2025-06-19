<?php
// Start session
session_start();

// Check if admin is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: adminlogin.php');
    exit;
}

// Database connection
$host = 'localhost';
$dbname = 'cateringdb';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Fetch total bookings
try {
    $stmt = $pdo->query("SELECT COUNT(*) AS total_bookings FROM bookings");
    $total_bookings = $stmt->fetch(PDO::FETCH_ASSOC)['total_bookings'];
} catch (PDOException $e) {
    $total_bookings = 0;
    error_log("Error fetching bookings: " . $e->getMessage());
}

// Fetch total customers
try {
    $stmt = $pdo->query("SELECT COUNT(*) AS total_customers FROM users");
    $total_customers = $stmt->fetch(PDO::FETCH_ASSOC)['total_customers'];
} catch (PDOException $e) {
    $total_customers = 0;
    error_log("Error fetching customers: " . $e->getMessage());
}

// Calculate total revenue (KSH 1000 per person)
try {
    $stmt = $pdo->query("SELECT SUM(people_count) AS total_people FROM bookings");
    $total_people = $stmt->fetch(PDO::FETCH_ASSOC)['total_people'] ?: 0;
    $price_per_person = 1000; // KSH per person
    $total_revenue = $total_people * $price_per_person;
} catch (PDOException $e) {
    $total_revenue = 0;
    error_log("Error calculating revenue: " . $e->getMessage());
}

// Fetch customers for table
try {
    $stmt = $pdo->query("SELECT id, username, email FROM users");
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching customers: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Catering Services</title>
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
        .card {
            background-color: #343a40;
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            color: #f8f9fa;
        }
        .card-icon {
            font-size: 2rem;
            color: #ffc107;
        }
        .card-title {
            font-size: 1.25rem;
            color: #ffc107;
        }
        .card-text {
            font-size: 2rem;
            font-weight: bold;
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
        .btn-primary {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #212529;
        }
        .btn-primary:hover {
            background-color: #ffca2c;
            border-color: #ffca2c;
        }
        .alert-danger {
            background-color: #495057;
            color: #ffc107;
            border-color: #ffc107;
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
            <a href="admindashboard.php" class="active"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
            <a href="customer.php"><i class="fas fa-users me-2"></i> Manage Customers</a>
            <a href="services.php"><i class="fas fa-utensils me-2"></i> Manage Services</a>
            <a href="bookings.php"><i class="fas fa-calendar-check me-2"></i> View Bookings</a>
            <a href="reports.php"><i class="fas fa-chart-bar me-2"></i> Generate Reports</a>
            <a href="adminlogout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
        </nav>
    </div>
    <div class="content">
        <section id="dashboard">
            <h2 class="mb-4">Dashboard Overview</h2>
            <div class="row g-4">
                <!-- Total Bookings Card -->
                <div class="col-md-4">
                    <div class="card p-3">
                        <div class="card-body d-flex align-items-center">
                            <i class="fas fa-calendar-check card-icon me-3"></i>
                            <div>
                                <h4 class="card-title">Total Bookings</h4>
                                <p class="card-text"><?php echo htmlspecialchars($total_bookings); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Total Customers Card -->
                <div class="col-md-4">
                    <div class="card p-3">
                        <div class="card-body d-flex align-items-center">
                            <i class="fas fa-users card-icon me-3"></i>
                            <div>
                                <h4 class="card-title">Total Customers</h4>
                                <p class="card-text"><?php echo htmlspecialchars($total_customers); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Total Revenue Card -->
                <div class="col-md-4">
                    <div class="card p-3">
                        <div class="card-body d-flex align-items-center">
                            <i class="fas fa-money-bill-wave card-icon me-3"></i>
                            <div>
                                <h4 class="card-title">Total Revenue</h4>
                                <p class="card-text">KSH <?php echo number_format($total_revenue, 0, '.', ','); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="customers" class="mt-5">
            <h2 class="mb-4">Customers</h2>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($customers)): ?>
                            <tr>
                                <td colspan="3" class="text-center">No customers found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($customers as $customer): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($customer['id']); ?></td>
                                    <td><?php echo htmlspecialchars($customer['username']); ?></td>
                                    <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                </tr>
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