<?php
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

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];
    $upload_dir = "Uploads/";
    
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    if ($action == "create" || $action == "update") {
        $service_type = $manage_type == "home" ? $_POST['home_service_type'] : $_POST['service_type'];
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
            } else {
                $stmt = $conn->prepare("UPDATE services SET service_type = ?, description = ?, image_path = ? WHERE id = ?");
                $stmt->execute([$service_type, $description, $image_path, $_POST['id']]);
            }
            // Redirect to clear form
            header("Location: services.php?manage_type=$manage_type" . ($manage_type == "home" ? "&home_service_type=" . urlencode($home_service_type) : ""));
            exit();
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
    <title>Manage Catering Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .service-image {
            max-width: 100px;
            max-height: 100px;
            object-fit: cover;
        }
        body {
            background-color: #212529;
        }
        #homeServiceType {
            display: none;
        }
    </style>
</head>
<body class="text-light">
    <div class="container mt-5">
        <h2 class="text-warning">Manage Catering Services</h2>
        <p class="mb-3"><a href="admindashboard.php" class="text-light">View AdminDashboard</a> | <a href="customer.php" class="text-light">Manage Customers</a></p>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- Management Type Selection -->
        <div class="mb-4">
            <form method="GET">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label class="form-label">Manage</label>
                    </div>
                    <div class="col-auto">
                        <select class="form-select bg-dark text-light border-warning" name="manage_type" id="manageType" onchange="this.form.submit()">
                            <option value="individual" <?php echo $manage_type == "individual" ? 'selected' : ''; ?>>Individual Services</option>
                            <option value="home" <?php echo $manage_type == "home" ? 'selected' : ''; ?>>Home Page Services</option>
                        </select>
                    </div>
                    <div class="col-auto" id="homeServiceType">
                        <label class="form-label">Service Type</label>
                        <select class="form-select bg-dark text-light border-warning" name="home_service_type" onchange="this.form.submit()">
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
        <div class="card mb-4 bg-dark text-light border-warning">
            <div class="card-body">
                <h5 class="card-title text-warning"><?php echo $title; ?></h5>
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
                            <input type="text" class="form-control bg-dark text-light border-warning" value="<?php echo ucfirst($home_service_type); ?>" readonly>
                        <?php else: ?>
                            <select class="form-select bg-dark text-light border-warning" name="service_type" required>
                                <option value="home" <?php echo $service_type == 'home' ? 'selected' : ''; ?>>Home</option>
                                <option value="wedding" <?php echo $service_type == 'wedding' ? 'selected' : ''; ?>>Wedding</option>
                                <option value="birthday" <?php echo $service_type == 'birthday' ? 'selected' : ''; ?>>Birthday</option>
                                <option value="corporate" <?php echo $service_type == 'corporate' ? 'selected' : ''; ?>>Corporate</option>
                            </select>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control bg-dark text-light border-warning" name="description" rows="4"><?php echo htmlspecialchars($description); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <input type="file" class="form-control bg-dark text-light border-warning" name="image" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-warning">Save Service</button>
                    <a href="services.php?manage_type=<?php echo $manage_type; ?><?php echo $manage_type == 'home' && $home_service_type ? '&home_service_type=' . urlencode($home_service_type) : ''; ?>" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>

        <!-- Services Table -->
        <table class="table table-striped bg-dark text-light border-warning">
            <thead>
                <tr class="text-warning">
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

    <script>
        document.getElementById('manageType').addEventListener('change', function() {
            document.getElementById('homeServiceType').style.display = this.value === 'home' ? 'block' : 'none';
        });
        // Set initial visibility based on manage_type
        document.getElementById('homeServiceType').style.display = '<?php echo $manage_type === 'home' ? 'block' : 'none'; ?>';
    </script>
</body>
</html>