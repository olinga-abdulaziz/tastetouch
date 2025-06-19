<?php
// Handle AJAX booking for all services
$successMsg = "";
$errorMsg = "";

// Database credentials
$servername = "";
$username = "root";
$password = "";
$dbname = "cateringdb";

// Only process if AJAX POST
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['service_type'])) {
    $conn = new mysqli($servername, $username, $password);
    if ($conn->connect_error) { exit("Error: Could not connect to database."); }
    $conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
    $conn->select_db($dbname);
    $conn->query("CREATE TABLE IF NOT EXISTS bookings (
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
    )");

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
        echo "Booked successfully";
    } else {
        echo "Error: Could not save booking.";
    }
    $conn->close();
    exit;
}

// Fetch services from database
$conn = new mysqli($servername, $username, $password, $dbname);
$services = [];
if (!$conn->connect_error) {
    $result = $conn->query("SELECT service_type, description, image_path FROM services");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $services[] = $row;
        }
        $result->free();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Catering Service Booking</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .card img {height: 200px; object-fit: cover;}
    .service-card {cursor: pointer; transition: transform 0.2s;}
    .service-card:hover {transform: scale(1.03);}
  </style>
</head>
<body class="bg-light">

<?php include('navbar.php'); ?>

<section class="bg-dark text-white text-center py-5 mb-4">
  <div class="container">
    <h1 class="display-4 fw-bold">🍽️ Welcome to Our Catering Services</h1>
    <p class="lead">From weddings to corporate events and birthdays, we serve delicious memories.</p>
    <a href="#services" class="btn btn-warning btn-lg mt-3">Explore Services</a>
  </div>
</section>

<div class="container py-5" id="services">
  <h2 class="text-center mb-4">📅 Book a Catering Service</h2>
  <div class="row row-cols-1 row-cols-md-3 g-4">
    <?php foreach ($services as $index => $service): ?>
      <div class="col">
        <div class="card service-card" data-bs-toggle="modal" data-bs-target="#serviceModal<?php echo $index; ?>">
          <img src="<?php echo htmlspecialchars($service['image_path']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($service['service_type']); ?>">
          <div class="card-body text-center">
            <h5 class="card-title"><?php echo htmlspecialchars($service['service_type']); ?></h5>
            <p class="card-text"><?php echo htmlspecialchars($service['description']); ?></p>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php foreach ($services as $index => $service): ?>
<div class="modal fade" id="serviceModal<?php echo $index; ?>" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form class="booking-form" data-service="<?php echo htmlspecialchars($service['service_type']); ?>" autocomplete="off">
        <div class="modal-header">
          <h5 class="modal-title"><?php echo htmlspecialchars($service['service_type']); ?> Booking</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body row g-3">
          <div class="col-12" id="serviceResponse<?php echo $index; ?>"></div>
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
            <textarea name="additional_info" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Submit Booking</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endforeach; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.booking-form').forEach(function(form) {
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    var serviceType = form.getAttribute('data-service');
    var formData = new FormData(form);
    formData.append('service_type', serviceType);

    // Find the response div for this modal
    var responseDiv = form.querySelector('[id^="serviceResponse"]');

    fetch('', { // same file
      method: 'POST',
      body: formData
    })
    .then(res => res.text())
    .then(data => {
      responseDiv.innerHTML = '<div class="alert alert-success text-center">' + data + '</div>';
      if (data.trim() === "Booked successfully") form.reset();
    })
    .catch(() => {
      responseDiv.innerHTML = '<div class="alert alert-danger text-center">Booking failed. Please try again.</div>';
    });
  });
});
</script>
</body>
</html>