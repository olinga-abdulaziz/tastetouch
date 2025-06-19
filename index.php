<?php
// Start session
session_start();

// Initialize variables for messages
$error = '';
$success = '';

// Database configuration
$host = 'localhost';
$username = 'root'; // Change to your MySQL username
$password = ''; // Change to your MySQL password
$dbname = 'cateringdb';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Create database connection
    $conn = new mysqli($host, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        $error = "Connection failed: " . $conn->connect_error;
    } else {
        $loginInput = trim($_POST['username']);
        $password = $_POST['password'];

        // Validate inputs
        if (empty($loginInput) || empty($password)) {
            $error = "All fields are required";
        } else {
            // Prepare statement to check for username or email
            $stmt = $conn->prepare("SELECT id, username, email, password FROM users WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $loginInput, $loginInput);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $success = "Login successful!";
                    // Redirect to a dashboard or home page
                    header("Location: home.php"); // Change to your desired page
                    exit();
                } else {
                    $error = "Invalid password";
                }
            } else {
                $error = "User not found";
            }
            $stmt->close();
        }
        $conn->close();
    }
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
    body {
      background: url('uploads/bg.jpg') no-repeat center center fixed;
      background-size: cover;
    }
    .card img {height: 200px; object-fit: cover;}
    .service-card {cursor: pointer; transition: transform 0.2s;}
    .service-card:hover {transform: scale(1.03);}
    .modal-content, .container, .bg-white {
      background: rgba(255,255,255,0.95) !important;
      backdrop-filter: blur(2px);
    }
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            max-width: 400px;
            width: 100%;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .error-message {
            color: red;
            text-align: center;
        }
        .success-message {
            color: green;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-card bg-white">
        <h2 class="text-center mb-4">TastefulTouch Login</h2>

        <?php
        if (!empty($error)) {
            echo "<p class='error-message'>$error</p>";
        }
        if (!empty($success)) {
            echo "<p class='success-message'>$success</p>";
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username or Email</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Enter username or email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
        </form>

        <div class="text-center mt-3">
            <a href="forgot-password.php" class="text-decoration-none">Forgot Password?</a>
            <p class="mt-2">Don't have an account? <a href="register.php" class="text-decoration-none">Sign Up</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>
</html>