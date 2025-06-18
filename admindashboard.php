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

// Fetch customers
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
            <a href="admin-dashboard.php" class="active"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
            <a href="customer.php"><i class="fas fa-users me-2"></i> Manage Customers</a>
            <a href="services.html"><i class="fas fa-utensils me-2"></i> Manage Services</a>
            <a href="bookings.html"><i class="fas fa-calendar-check me-2"></i> View Bookings</a>
            <a href="reports.html"><i class="fas fa-chart-bar me-2"></i> Generate Reports</a>
            <a href="admin-login.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
        </nav>
    </div>
    <div class="content">
        <section id="dashboard">
            <h2 class="mb-4">Dashboard Overview</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="card p-3 mb-4">
                        <h5>Total Bookings</h5>
                        <h3>120</h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-3 mb-4">
                        <h5>Active Customers</h5>
                        <h3>85</h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-3 mb-4">
                        <h5>Revenue (This Month)</h5>
                        <h3>$12,500</h3>
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
                                <td colspan="4" class="text-center">No customers found.</td>
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
</body>
</html>