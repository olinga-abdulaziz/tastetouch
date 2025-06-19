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
    
    // Create services table if not exists
    $sql = "CREATE TABLE IF NOT EXISTS services (
        id INT AUTO_INCREMENT PRIMARY KEY,
        service_type VARCHAR(50) NOT NULL,
        description TEXT,
        image_path VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

// Initialize form variables
$action = "create";
$service_id = "";
$service_type = "";
$description = "";
$manage_type = isset($_POST['manage_type']) ? $_POST['manage_type'] : "individual";
$home_service_type = isset($_POST['home_service_type']) ? $_POST['home_service_type'] : "";
$title = "Add New Service";
$error = "";
$success = "";

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];
    $upload_dir = "Uploads/";
    
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    if ($action == "create" || $action == "update") {
        $service_type = $manage_type == "home" ? $_POST['home_service_type'] : $_POST['service_type'];
        
        // Validate service type
        if ($manage_type == "home" && empty($service_type)) {
            $error = "Please select a service type for Home Page Services.";
        } else {
            $description = $_POST['description'];
            $image_path = "";
            
            // Handle image upload
            if (!empty($_FILES['image']['name'])) {
                $image_name = time() . '_' . basename($_FILES['image']['name']);
                $image_path = $upload_dir . $image_name;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
                    // Image uploaded successfully
                } else {
                    $error = "Failed to upload image.";
                }
            } elseif ($action == "update" && empty($_FILES['image']['name'])) {
                // Keep existing image if no new image uploaded
                $stmt = $conn->prepare("SELECT image_path FROM services WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                $image_path = $stmt->fetchColumn();
            }
            
            if (!$error) {
                if ($action == "create") {
                    $stmt = $conn->prepare("INSERT INTO services (service_type, description, image_path) VALUES (?, ?, ?)");
                    $stmt->execute([$service_type, $description, $image_path]);
                    $success = "Service added successfully.";
                } else {
                    $stmt = $conn->prepare("UPDATE services SET service_type = ?, description = ?, image_path = ? WHERE id = ?");
                    $stmt->execute([$service_type, $description, $image_path, $_POST['id']]);
                    $success = "Service updated successfully.";
                }
                // Redirect to clear form
                header("Location: services.php?manage_type=$manage_type" . ($manage_type == "home" ? "&home_service_type=" . urlencode($home_service_type) : ""));
                exit();
            }
        }
    } elseif ($action == "delete") {
        // Delete image file
        $stmt = $conn->prepare("SELECT image_path FROM services WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $image_path = $stmt->fetchColumn();
        if ($image_path && file_exists($image_path)) {
            unlink($image_path);
        }
        
        $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $success = "Service deleted successfully.";
        header("Location: services.php?manage_type=$manage_type" . ($manage_type == "home" ? "&home_service_type=" . urlencode($home_service_type) : ""));
        exit();
    } elseif ($action == "edit") {
        // Populate form for editing
        $stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $service = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($service) {
            $action = "update";
            $service_id = $service['id'];
            $service_type = $service['service_type'];
            $description = $service['description'];
            $title = "Edit Service";
            $manage_type = isset($_POST['manage_type']) ? $_POST['manage_type'] : "individual";
            $home_service_type = $manage_type == "home" ? $service['service_type'] : "";
        }
    }
}

// Handle GET parameters for filtering
$manage_type = isset($_GET['manage_type']) ? $_GET['manage_type'] : "individual";
$home_service_type = isset($_GET['home_service_type']) ? $_GET['home_service_type'] : "";

// Fetch services based on manage_type
if ($manage_type == "home" && $home_service_type) {
    $stmt = $conn->prepare("SELECT * FROM services WHERE service_type = ?");
    $stmt->execute([$home_service_type]);
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $stmt = $conn->query("SELECT * FROM services");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services - Catering Services</title>
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
        .service-image {
            max-width: 100px;
            max-height: 100px;
            object-fit: cover;
        }
        #homeServiceType {
            display: none;
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
            <a href="services.php" class="active"><i class="fas fa-utensils me-2"></i> Manage Services</a>
            <a href="bookings.php"><i class="fas fa-calendar-check me-2"></i> View Bookings</a>
            <a href="reports.php"><i class="fas fa-chart-bar me-2"></i> Generate Reports</a>
            <a href="adminlogin.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
        </nav>
    </div>
    <div class="content">
        <section id="services">
            <h2 class="mb-4">Manage Catering Services</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <!-- Management Type Selection -->
            <div class="mb-4">
                <form method="GET">
                    <div class="row g-3 align-items-center">
                        <div class="col-auto">
                            <label class="form-label">Manage</label>
                        </div>
                        <div class="col-auto">
                            <select class="form-select" name="manage_type" id="manageType" onchange="this.form.submit()">
                                <option value="individual" <?php echo $manage_type == "individual" ? 'selected' : ''; ?>>Individual Services</option>
                                <option value="home" <?php echo $manage_type == "home" ? 'selected' : ''; ?>>Home Page Services</option>
                            </select>
                        </div>
                        <div class="col-auto" id="homeServiceType">
                            <label class="form-label">Service Type</label>
                            <select class="form-select" name="home_service_type" onchange="this.form.submit()">
                                <option value="">Select Service</option>
                                <option value="home" <?php echo $home_service_type == 'home' ? 'selected' : ''; ?>>Home</option>
                                <option value="wedding" <?php echo $home_service_type == 'wedding' ? 'selected' : ''; ?>>Wedding</option>
                                <option value="birthday" <?php echo $home_service_type == 'birthday' ? 'selected' : ''; ?>>Birthday</option>
                                <option value="corporate" <?php echo $home_service_type == 'corporate' ? 'selected' : ''; ?>>Corporate</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Create/Update Form -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $title; ?></h5>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="<?php echo $action; ?>">
                        <input type="hidden" name="id" value="<?php echo $service_id; ?>">
                        <input type="hidden" name="manage_type" value="<?php echo $manage_type; ?>">
                        <?php if ($manage_type == "home"): ?>
                            <input type="hidden" name="home_service_type" value="<?php echo $home_service_type; ?>">
                        <?php endif; ?>
                        <div class="mb-3">
                            <label class="form-label">Service Type</label>
                            <?php if ($manage_type == "home"): ?>
                                <input type="text" class="form-control" value="<?php echo ucfirst($home_service_type); ?>" readonly>
                            <?php else: ?>
                                <select class="form-select" name="service_type" required>
                                    <option value="home" <?php echo $service_type == 'home' ? 'selected' : ''; ?>>Home</option>
                                    <option value="wedding" <?php echo $service_type == 'wedding' ? 'selected' : ''; ?>>Wedding</option>
                                    <option value="birthday" <?php echo $service_type == 'birthday' ? 'selected' : ''; ?>>Birthday</option>
                                    <option value="corporate" <?php echo $service_type == 'corporate' ? 'selected' : ''; ?>>Corporate</option>
                                </select>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="4"><?php echo htmlspecialchars($description); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-warning">Save Service</button>
                        <a href="services.php?manage_type=<?php echo $manage_type; ?><?php echo $manage_type == 'home' && $home_service_type ? '&home_service_type=' . urlencode($home_service_type) : ''; ?>" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>

            <!-- Services Table -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Service Type</th>
                            <th>Description</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $service): ?>
                            <tr>
                                <td><?php echo $service['id']; ?></td>
                                <td><?php echo ucfirst($service['service_type']); ?></td>
                                <td><?php echo htmlspecialchars($service['description']); ?></td>
                                <td>
                                    <?php if ($service['image_path']): ?>
                                        <img src="<?php echo $service['image_path']; ?>" class="service-image">
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="edit">
                                        <input type="hidden" name="id" value="<?php echo $service['id']; ?>">
                                        <input type="hidden" name="manage_type" value="<?php echo $manage_type; ?>">
                                        <?php if ($manage_type == "home"): ?>
                                            <input type="hidden" name="home_service_type" value="<?php echo $home_service_type; ?>">
                                        <?php endif; ?>
                                        <button type="submit" class="btn btn-sm btn-warning">Edit</button>
                                    </form>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $service['id']; ?>">
                                        <input type="hidden" name="manage_type" value="<?php echo $manage_type; ?>">
                                        <?php if ($manage_type == "home"): ?>
                                            <input type="hidden" name="home_service_type" value="<?php echo $home_service_type; ?>">
                                        <?php endif; ?>
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this service?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        document.getElementById('manageType').addEventListener('change', function() {
            document.getElementById('homeServiceType').style.display = this.value === 'home' ? 'block' : 'none';
        });
        // Set initial visibility based on manage_type
        document.getElementById('homeServiceType').style.display = '<?php echo $manage_type === 'home' ? 'block' : 'none'; ?>';
    </script>
</body>
</html>