<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Catering Service Booking</title>
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
  <section class="bg-dark text-white text-center py-5 mb-4">
  <div class="container">
    <h1 class="display-4 fw-bold">🍽️ Welcome to Our Catering Services</h1>
    <p class="lead">From weddings to corporate events and birthdays, we serve delicious memories.</p>
    <a href="#wedding" class="btn btn-warning btn-lg mt-3">Explore Services</a>
  </div>
</section>

  <div class="container py-5">
    <h2 class="text-center mb-4">📅 Book a Catering Service</h2>
    <div class="row row-cols-1 row-cols-md-3 g-4">

      <!-- Wedding Catering Card -->
      <div class="col" id="wedding">
        <div class="card service-card" data-bs-toggle="modal" data-bs-target="#weddingModal">
          <img src="images/wedding.jpg" class="card-img-top" alt="Wedding Catering">
          <div class="card-body">
            <h5 class="card-title">Wedding Catering</h5>
            <p class="card-text">Perfect menus for your special day.</p>
          </div>
        </div>
      </div>

      <!-- Corporate Catering Card -->
      <div class="col" id="corporate">
        <div class="card service-card" data-bs-toggle="modal" data-bs-target="#corporateModal">
          <img src="images/corporate.jpg" class="card-img-top" alt="Corporate Catering">
          <div class="card-body">
            <h5 class="card-title">Corporate Event Catering</h5>
            <p class="card-text">Delicious food for your business events.</p>
          </div>
        </div>
      </div>

      <!-- Birthday Catering Card -->
      <div class="col" id="birthday">
        <div class="card service-card" data-bs-toggle="modal" data-bs-target="#birthdayModal">
          <img src="images/birthday.jpg" class="card-img-top" alt="Birthday Catering">
          <div class="card-body">
            <h5 class="card-title">Birthday Party Catering</h5>
            <p class="card-text">Celebrate with delicious meals for guests.</p>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- Wedding Modal -->
  <div class="modal fade" id="weddingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form action="includes/booking_process.php" method="POST">
          <input type="hidden" name="service_type" value="Wedding Catering">
          <div class="modal-header">
            <h5 class="modal-title">Book Wedding Catering</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body row g-3">
            <div class="col-md-6"><label class="form-label">Full Name</label><input type="text" name="client_name" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="client_email" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Phone</label><input type="tel" name="client_phone" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Event Date</label><input type="date" name="event_date" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Venue</label><input type="text" name="venue" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Number of People</label><input type="number" name="people_count" class="form-control" min="1" required></div>
            <div class="col-12"><label class="form-label">Additional Info</label><textarea name="additional_info" class="form-control" rows="3"></textarea></div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Submit Booking</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Corporate Modal -->
  <div class="modal fade" id="corporateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form action="includes/booking_process.php" method="POST">
          <input type="hidden" name="service_type" value="Corporate Event Catering">
          <div class="modal-header">
            <h5 class="modal-title">Book Corporate Catering</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body row g-3">
            <div class="col-md-6"><label class="form-label">Full Name</label><input type="text" name="client_name" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="client_email" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Phone</label><input type="tel" name="client_phone" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Event Date</label><input type="date" name="event_date" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Venue</label><input type="text" name="venue" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Number of People</label><input type="number" name="people_count" class="form-control" min="1" required></div>
            <div class="col-12"><label class="form-label">Additional Info</label><textarea name="additional_info" class="form-control" rows="3"></textarea></div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Submit Booking</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Birthday Modal -->
  <div class="modal fade" id="birthdayModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form action="includes/booking_process.php" method="POST">
          <input type="hidden" name="service_type" value="Birthday Party Catering">
          <div class="modal-header">
            <h5 class="modal-title">Book Birthday Catering</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body row g-3">
            <div class="col-md-6"><label class="form-label">Full Name</label><input type="text" name="client_name" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="client_email" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Phone</label><input type="tel" name="client_phone" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Event Date</label><input type="date" name="event_date" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Venue</label><input type="text" name="venue" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Number of People</label><input type="number" name="people_count" class="form-control" min="1" required></div>
            <div class="col-12"><label class="form-label">Additional Info</label><textarea name="additional_info" class="form-control" rows="3"></textarea></div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Submit Booking</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
