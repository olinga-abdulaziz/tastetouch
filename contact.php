<!-- filepath: d:\xampp\htdocs\projects\Tasteful Touch\tastetouch\contact.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Tasteful Touch</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Include Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Contact Section -->
    <section class="py-5">
        <div class="container">
            <h1 class="text-center mb-4">Contact Us</h1>
            <p class="text-center mb-5">
                Have questions or need more information about our catering services? Feel free to reach out to us!
            </p>
            <div class="row">
                <div class="col-md-6">
                    <h4>Contact Information</h4>
                    <p><strong>Phone:</strong> +1 (123) 456-7890</p>
                    <p><strong>Email:</strong> info@tastefultouch.com</p>
                    <p><strong>Address:</strong> 123 Catering Lane, Food City, FC 45678</p>
                </div>
                <div class="col-md-6">
                    <h4>Send Us a Message</h4>
                    <form action="process_contact.php" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </form>
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