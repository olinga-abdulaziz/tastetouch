<?php
// Database credentials
$servername = "";
$username = "root";
$password = "";
$dbname = "tasteful_touch";
$successMsg = "";
$errorMsg = "";

// Connect to MySQL server
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
if (!$conn->query("CREATE DATABASE IF NOT EXISTS $dbname")) {
    die("Database creation failed: " . $conn->error);
}

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
    die("Table creation failed: " . $conn->error);
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

    $insertSql = "INSERT INTO bookings (service_type, client_name, client_email, client_phone, event_date, venue, people_count, additional_info)
                  VALUES ('$service_type', '$client_name', '$client_email', '$client_phone', '$event_date', '$venue', $people_count, '$additional_info')";

    if ($conn->query($insertSql)) {
        $successMsg = "Booked successfully";
    } else {
        $errorMsg = "Error: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Wedding Catering Booking</title>
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
  </style>
</head>
<body class="bg-light">

  <?php include('navbar.php'); ?>

  <!-- Hero Section -->
  <section class="bg-dark text-white text-center py-5">
    <div class="container">
      <h1 class="display-5 fw-bold">Wedding Catering Services</h1>
      <p class="lead">Exquisite dishes, elegant presentation, and unforgettable taste for your big day.</p>
    </div>
  </section>

  <!-- Menu Highlights -->
  <section class="container py-5">
    <h2 class="text-center mb-4">🍽️ Our Wedding Menu Highlights</h2>
    <div class="row row-cols-1 row-cols-md-3 g-4">

      <!-- Appetizers -->
      <div class="col">
        <div class="card h-100">
          <img src="images/appetizers.jpg" class="card-img-top" alt="Appetizers">
          <div class="card-body">
            <h5 class="card-title">Appetizers</h5>
            <p class="card-text">Mini samosas, spring rolls, meatballs, and more.</p>
          </div>
        </div>
      </div>

      <!-- Main Course -->
      <div class="col">
        <div class="card h-100">
          <img src="images/main-course.jpg" class="card-img-top" alt="Main Course">
          <div class="card-body">
            <h5 class="card-title">Main Courses</h5>
            <p class="card-text">Pilau, biryani, roasted chicken, ugali, vegetables, and beef stew.</p>
          </div>
        </div>
      </div>

      <!-- Desserts -->
      <div class="col">
        <div class="card h-100">
          <img src="images/desserts.jpg" class="card-img-top" alt="Desserts">
          <div class="card-body">
            <h5 class="card-title">Desserts</h5>
            <p class="card-text">Fruit platters, cupcakes, chocolate fountains, and traditional sweets.</p>
          </div>
        </div>
      </div>

      <!-- Drinks -->
      <div class="col">
        <div class="card h-100">
          <img src="images/drinks.jpg" class="card-img-top" alt="Drinks">
          <div class="card-body">
            <h5 class="card-title">Drinks</h5>
            <p class="card-text">Fresh juices, soft drinks, mocktails, and water stations.</p>
          </div>
        </div>
      </div>

      <!-- Cakes -->
      <div class="col">
        <div class="card h-100">
          <img src="images/cakes.jpg" class="card-img-top" alt="Cakes">
          <div class="card-body">
            <h5 class="card-title">Cakes</h5>
            <p class="card-text">Wedding cakes for your special day.</p>
          </div>
        </div>
      </div>

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
              <input type="number" name="people_count" class="form-control" min="1" required>
            </div>
            <div class="col-12">
              <label class="form-label">Additional Info</label>
              <textarea name="additional_info" class="form-control" rows="3"></textarea>
            </div>
          </div>
          <div class="mt-4 text-center">
            <button type="submit" class="btn btn-primary px-5">Submit Booking</button>
          </div>
        </form>
      </div>
    </div>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
