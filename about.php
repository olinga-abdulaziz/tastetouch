<!-- filepath: d:\xampp\htdocs\projects\Tasteful Touch\tastetouch\about.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Tasteful Touch</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Include Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- About Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <h1 class="mb-4">About Tasteful Touch</h1>
                    <p>
                        At Tasteful Touch, we specialize in providing exceptional catering services for all occasions. 
                        Whether you're planning a wedding, corporate event, birthday party, or any special gathering, 
                        our team is dedicated to delivering a memorable culinary experience.
                    </p>
                    <p>
                        Our chefs use only the freshest ingredients to craft delicious dishes that will leave your guests impressed. 
                        From elegant appetizers to decadent desserts, we tailor our menu to suit your preferences and needs.
                    </p>
                </div>
                <div class="col-lg-6">
                    <img src="images/catering.jpg" alt="Catering Services" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="bg-light py-5">
        <div class="container">
            <h2 class="text-center mb-4">Why Choose Us?</h2>
            <div class="row text-center">
                <div class="col-md-4">
                    <div class="p-4">
                        <h5>Fresh Ingredients</h5>
                        <p>We prioritize quality and freshness in every dish we prepare.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4">
                        <h5>Custom Menus</h5>
                        <p>Our menus are tailored to meet your unique preferences and dietary needs.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4">
                        <h5>Professional Service</h5>
                        <p>Our team is committed to providing exceptional service from start to finish.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p>&copy; 2025 Tasteful Touch. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>