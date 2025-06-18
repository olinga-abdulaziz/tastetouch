<?php
// Start session
session_start();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Check credentials
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['loggedin'] = true;
        header('Location: admindashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #212529; /* Bootstrap dark background */
            font-family: 'Segoe UI', sans-serif;
            color: #f8f9fa; /* Light text for contrast */
        }
        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 2.5rem;
            background-color: #343a40; /* Dark gray for container */
            border-radius: 1rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            margin: 0 auto;
        }
        .logo {
            max-width: 120px;
            margin: 0 auto 1.5rem;
            display: block;
        }
        .form-control {
            background-color: #495057; /* Darker input background */
            color: #f8f9fa;
            border-color: #6c757d;
        }
        .form-control:focus {
            border-color: #ffc107; /* Warning yellow border on focus */
            box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
            background-color: #495057;
            color: #f8f9fa;
        }
        .form-control[readonly] {
            background-color: #6c757d; /* Slightly lighter for readonly inputs */
            opacity: 1;
        }
        .btn-primary {
            background-color: #ffc107; /* Warning yellow for button */
            border-color: #ffc107;
            color: #212529; /* Dark text for contrast */
        }
        .btn-primary:hover {
            background-color: #ffca2c; /* Lighter warning on hover */
            border-color: #ffca2c;
        }
        .forgot-password {
            font-size: 0.9rem;
            color: #ffc107; /* Warning yellow for link */
            text-decoration: none;
        }
        .forgot-password:hover {
            text-decoration: underline;
            color: #ffca2c; /* Lighter warning on hover */
        }
        .form-check-input {
            background-color: #495057;
            border-color: #6c757d;
        }
        .form-check-input:checked {
            background-color: #ffc107; /* Warning yellow when checked */
            border-color: #ffc107;
        }
        .form-check-label {
            color: #f8f9fa;
        }
        .alert-danger {
            background-color: #495057;
            color: #ffc107; /* Warning yellow text for errors */
            border-color: #ffc107;
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="login-container">
            <!-- Placeholder for logo -->
           
            <h2 class="text-center mb-4 fw-bold">Admin Dashboard Login</h2>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="admin" readonly required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" value="admin123" readonly required>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="rememberMe" name="rememberMe">
                        <label class="form-check-label" for="rememberMe">Remember Me</label>
                    </div>
                    <a href="/forgot-password" class="forgot-password">Forgot Password?</a>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2">Login</button>
            </form>
        </div>
    </div>
</body>
</html>