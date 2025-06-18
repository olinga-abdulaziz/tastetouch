<?php include('navbar.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Corporate Catering Services</title>
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

  <!-- Hero Section -->
  <section class="bg-dark text-white text-center py-5">
    <div class="container">
      <h1 class="display-5 fw-bold">Corporate Catering Services</h1>
      <p class="lead">Professional meals tailored to impress your team, clients, and partners.</p>
    </div>
  </section>

  <!-- Menu Highlights -->
  <section class="container py-5">
    <h2 class="text-center mb-4">🍴 Corporate Menu Selections</h2>
    <div class="row row-cols-1 row-cols-md-3 g-4">

      <!-- Breakfast -->
      <div class="col">
        <div class="card h-100">
          <img src="images/breakfast.jpg" class="card-img-top" alt="Breakfast">
          <div class="card-body">
            <h5 class="card-title">Corporate Breakfast</h5>
            <p class="card-text">Croissants, eggs, sausages, tea, and coffee.</p>
          </div>
        </div>
      </div>

      <!-- Lunch Options -->
      <div class="col">
        <div class="card h-100">
          <img src="images/lunch.jpg" class="card-img-top" alt="Lunch">
          <div class="card-body">
            <h5 class="card-title">Business Lunch</h5>
            <p class="card-text">Buffet with rice dishes, meat, vegetables, and drinks.</p>
          </div>
        </div>
      </div>

      <!-- Snacks & Drinks -->
      <div class="col">
        <div class="card h-100">
          <img src="images/snacks.jpg" class="card-img-top" alt="Snacks">
          <div class="card-body">
            <h5 class="card-title">Snacks & Beverages</h5>
            <p class="card-text">Cookies, sandwiches, fruit juice, water, and soda.</p>
          </div>
        </div>
      </div>

    </div>
  </section>

  <!-- Booking Form -->
  <section class="container py-5">
    <h2 class="text-center mb-4">📋 Book Corporate Catering</h2>
    <div class="row justify-content-center">
      <div class="col-md-10">
        <form action="includes/booking_process.php" method="POST" class="bg-white p-4 shadow rounded">
          <input type="hidden" name="service_type" value="Corporate Event Catering">
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
