<?php
// Start session
session_start();

// Check if admin is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: admin-login.php');
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

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = filter_var($_GET['delete_id'], FILTER_VALIDATE_INT);
    if ($delete_id) {
        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$delete_id]);
            $success = "Customer deleted successfully.";
        } catch (PDOException $e) {
            $error = "Error deleting customer: " . $e->getMessage();
        }
    } else {
        $error = "Invalid customer ID.";
    }
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
    <title>Manage Customers - Catering Services</title>
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
        .btn-danger {
            color: #212529;
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
            <a href="customer.php" class="active"><i class="fas fa-users me-2"></i> Manage Customers</a>
            <a href="services.php"><i class="fas fa-utensils me-2"></i> Manage Services</a>
            <a href="bookings.php"><i class="fas fa-calendar-check me-2"></i> View Bookings</a>
            <a href="reports.php"><i class="fas fa-chart-bar me-2"></i> Generate Reports</a>
            <a href="adminlogin.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
        </nav>
    </div>
    <div class="content">
        <section id="customers">
            <h2 class="mb-4">Manage Customers</h2>
            <?php if (isset($success)): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            <!-- <form action="customer.php" method="POST" class="mb-3">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="customerName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="customerName" name="customerName" required>
                    </div>
                    <div class="col-md-4">
                        <label for="customerEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="customerEmail" name="customerEmail" required>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Add Customer</button>
                    </div>
                </div>
            </form> -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($customers)): ?>
                            <tr>
                                <td colspan="5" class="text-center">No customers found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($customers as $customer): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($customer['id']); ?></td>
                                    <td><?php echo htmlspecialchars($customer['username']); ?></td>
                                    <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                    <td>
                                        <a href="customer.php?delete_id=<?php echo htmlspecialchars($customer['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this customer?');">Delete</a>
                                    </td>
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