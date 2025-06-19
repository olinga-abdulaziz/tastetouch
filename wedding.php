
<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database credentials
$servername = "";
$username = "root";
$password = "";
$dbname = "cateringdb";
$successMsg = "";
$errorMsg = "";

// Initialize debug log
$logFile = 'debug.log';
file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] Starting wedding.php\n", FILE_APPEND);

// Connect to MySQL server
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    $error = "Connection failed: " . $conn->connect_error;
    file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] $error\n", FILE_APPEND);
    die($error);
}
file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] Connected to MySQL server\n", FILE_APPEND);

// Create database if not exists
if (!$conn->query("CREATE DATABASE IF NOT EXISTS $dbname")) {
    $error = "Database creation failed: " . $conn->error;
    file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] $error\n", FILE_APPEND);
    die($error);
}
file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] Database $dbname ensured\n", FILE_APPEND);

// Select the database
$conn->select_db($dbname);

// Create bookings table if not exists
$tableSql = "CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_type VARCHAR(255) NOT NULL,
    client_name VARCHAR(255) NOT NULL,
    client_email VARCHAR(255) NOT NULL,
    client_phone VARCHAR(20) NOT NULL,
    event_date DATE NOT NULL,
    venue VARCHAR(255) NOT NULL,
    people_count INT NOT NULL,
    additional_info TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if (!$conn->query($tableSql)) {
    $error = "Table creation failed: " . $conn->error;
    file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] $error\n", FILE_APPEND);
    die($error);
}
file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] Bookings table ensured\n", FILE_APPEND);

// Check if total_amount column exists, if not, add it
$columnCheck = $conn->query("SHOW COLUMNS FROM bookings LIKE 'total_amount'");
if ($columnCheck->num_rows == 0) {
    file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] total_amount column not found, attempting to add\n", FILE_APPEND);
    $alterSql = "ALTER TABLE bookings ADD total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00";
    if (!$conn->query($alterSql)) {
        $error = "Failed to add total_amount column: " . $conn->error;
        file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] $error\n", FILE_APPEND);
        die($error);
    }
    file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] total_amount column added successfully\n", FILE_APPEND);
} else {
    file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] total_amount column already exists\n", FILE_APPEND);
}

// Fetch wedding services from database
$services = [];
$result = $conn->query("SELECT service_type, description, image_path FROM services WHERE service_type = 'Wedding'");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
    $result->free();
} else {
    file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] Failed to fetch services: " . $conn->error . "\n", FILE_APPEND);
}

// Insert booking data if form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $service_type = $conn->real_escape_string($_POST['service_type']);
    $client_name = $conn->real_escape_string($_POST['client_name']);
    $client_email = $conn->real_escape_string($_POST['client_email']);
    $client_phone = $conn->real_escape_string($_POST['client_phone']);
    $event_date = $conn->real_escape_string($_POST['event_date']);
    $venue = $conn->real_escape_string($_POST['venue']);
    $people_count = (int)$_POST['people_count'];
    $additional_info = $conn->real_escape_string($_POST['additional_info']);

    // Calculate total amount
    if ($people_count <= 4) {
        $total_amount = 10000.00; // Base price for 1-4 people
    } elseif ($people_count <= 9) {
        $total_amount = 12500.00 + ($people_count - 5) * 500.00; // Ksh12,500 + Ksh500 per person above 4
    } elseif ($people_count <= 20) {
        $total_amount = 20000.00; // Flat rate for 10-20 people
    } else {
        $total_amount = 20000.00 + ($people_count - 20) * 300.00; // Ksh20,000 + Ksh300 per person above 20
    }

    $insertSql = "INSERT INTO bookings (service_type, client_name, client_email, client_phone, event_date, venue, people_count, total_amount, additional_info)
                  VALUES ('$service_type', '$client_name', '$client_email', '$client_phone', '$event_date', '$venue', $people_count, $total_amount, '$additional_info')";

    if ($conn->query($insertSql)) {
        $successMsg = "Booking submitted successfully! Total Amount: Ksh" . number_format($total_amount, 2);
        file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] Booking inserted successfully\n", FILE_APPEND);
    } else {
        $errorMsg = "Error: " . $conn->error;
        file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] Booking insertion failed: " . $conn->error . "\n", FILE_APPEND);
    }
}

$conn->close();
?>

<?php include('navbar.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Wedding Catering Services</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .card img {
      height: 200px;
      object-fit: cover;
    }
    .service-card {
      cursor: pointer;
      transition: transform 0.2s;
    }
    .service-card:hover {
      transform: scale(1.03);
    }
    .total-amount {
      font-size: 1.1em;
      font-weight: bold;
      margin-top: 10px;
      text-align: center;
    }
  </style>
</head>
<body class="bg-light">

  <!-- Hero Section -->
  <section class="bg-dark text-white text-center py-5">
    <div class="container">
      <h1 class="display-5 fw-bold">Wedding Catering Services</h1>
      <p class="lead">Exquisite dishes, elegant presentation, and unforgettable taste for your big day.</p>
    </div>
  </section>

  <!-- Food Categories -->
  <section class="container py-5">
    <h2 class="text-center mb-4">🍽️ Our Wedding Menu Highlights</h2>
    <div class="row row-cols-1 row-cols-md-3 g-4">
      <?php foreach ($services as $service): ?>
        <div class="col">
          <div class="card h-100">
            <img src="<?php echo htmlspecialchars($service['image_path']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($service['service_type']); ?>">
            <div class="card-body">
              <h5 class="card-title"><?php echo htmlspecialchars($service['service_type']); ?></h5>
              <p class="card-text"><?php echo htmlspecialchars($service['description']); ?></p>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- Booking Form -->
  <section class="container py-5">
    <h2 class="text-center mb-4">📋 Book Wedding Catering</h2>
    <?php if ($successMsg): ?>
      <div class="alert alert-success text-center"><?php echo $successMsg; ?></div>
    <?php elseif ($errorMsg): ?>
      <div class="alert alert-danger text-center"><?php echo $errorMsg; ?></div>
    <?php endif; ?>
    <div class="row justify-content-center">
      <div class="col-md-10">
        <form action="" method="POST" class="bg-white p-4 shadow rounded">
          <input type="hidden" name="service_type" value="Wedding Catering">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Full Name</label>
              <input type="text" name="client_name" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" name="client_email" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone</label>
              <input type="tel" name="client_phone" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Event Date</label>
              <input type="date" name="event_date" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Venue</label>
              <input type="text" name="venue" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Number of People</label>
              <input type="number" name="people_count" class="form-control" min="1" required oninput="calculateTotal(this)">
            </div>
            <div class="col-12">
              <label class="form-label">Additional Info</label>
              <textarea name="additional_info" class="form-control" rows="3"></textarea>
            </div>
            <div class="col-12 total-amount" id="totalAmount">Total Amount: Ksh0.00</div>
          </div>
          <div class="mt-4 text-center">
            <button type="submit" class="btn btn-primary px-5">Submit Booking</button>
          </div>
        </form>
      </div>
    </div>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function calculateTotal(input) {
      const people = parseInt(input.value);
      let total = 0;
      if (people > 0) {
        if (people <= 4) {
          total = 10000;
        } else if (people <= 9) {
          total = 12500 + (people - 5) * 500;
        } else if (people <= 20) {
          total = 20000;
        } else {
          total = 20000 + (people - 20) * 300;
        }
      }
      document.getElementById('totalAmount').textContent = `Total Amount: Ksh${total.toLocaleString('en-KE', { minimumFractionDigits: 2 })}`;
    }
  </script>
</body>
</html>
