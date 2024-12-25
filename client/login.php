<?php
// Start session securely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection and global functions
include('../backend/connect.php');
include('../functions/global_function.php');

// Initialize message variable for feedback
$message = '';

// Prevent CSRF attacks: Generate a CSRF token and store it in the session
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Generate CSRF token
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_login'])) {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = "<div class='alert alert-danger'>Invalid CSRF token.</div>";
    } else {
        // Sanitize and validate input
        $user_email = filter_var(trim($_POST['user_email']), FILTER_SANITIZE_EMAIL);
        $user_password = trim($_POST['user_password']);

        if (empty($user_email) || empty($user_password)) {
            $message = "<div class='alert alert-danger'>Please fill in all fields.</div>";
        } else {
            // Prepare SQL query to fetch the user securely using prepared statements
            $sql_user = "SELECT * FROM users WHERE user_email = ?";
            $stmt = mysqli_prepare($connection, $sql_user);
            mysqli_stmt_bind_param($stmt, "s", $user_email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            $user_ip = getUserIP();

            // Check if user exists
            if (mysqli_num_rows($result) > 0) {
                $user = mysqli_fetch_assoc($result);

                // Verify password securely
                if (password_verify($user_password, $user['user_password'])) {
                    // Regenerate session ID to prevent session fixation attacks
                    session_regenerate_id(true);

                    // Set session variables
                    $_SESSION['user_email'] = $user['user_email'];
                    $_SESSION['user_name'] = $user['user_name'];
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['user_status'] = $user['user_status'];

                    // Check if the user has items in the cart
                    $cart_query = "SELECT * FROM cart_table WHERE ip_address = '$user_ip'";
                    $cart_result = mysqli_query($connection, $cart_query);
                    $cart_count = mysqli_num_rows($cart_result);

                    // Redirect based on cart status
                    if ($cart_count == 0) {
                        $message = "<div class='alert alert-success'>Login successful! Welcome back.</div>";

                        header("Location: profile.php");
                        exit();
                    } else {

                        header("Location: payment.php");
                        exit();
                    }
                } else {
                    // Incorrect password
                    $message = "<div class='alert alert-danger'>Invalid password.</div>";
                }
            } else {
                // User not found
                $message = "<div class='alert alert-danger'>No user found with this email.</div>";
            }

            // Close the statement to prevent resource leak
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Family Online Shopping</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4p889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">

</head>

<body class="d-flex flex-column justify-content-center align-items-center min-vh-100 bg-light">
    <!-- Main Content -->
    <main class="container py-5 flex-grow-1">
        <div class="row justify-content-center">
            <div class="col-md-6 border px-3">
                <h1 class="text-center mb-4">Login User</h1>
                <form action="" method="post">
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                    <!-- Email Input -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-secondary-subtle"><i class="fas fa-envelope"></i></span>
                            <input type="email" id="user_email" name="user_email" class="form-control" placeholder="Enter your email" autocomplete="off" required>
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-secondary-subtle"><i class="fas fa-lock"></i></span>
                            <input type="password" id="user_password" name="user_password" class="form-control" placeholder="Enter your password" required>
                        </div>
                    </div>

                    <!-- Forgot Password -->
                    <div class="d-flex justify-content-between">
                        <div>
                            <input type="checkbox" id="remember" name="remember" class="form-check-input">
                            <label for="remember" class="form-check-label">Remember Me</label>
                        </div>
                        <a href="forgot_password.php" class="text-decoration-none">Forgot Password?</a>
                    </div>

                    <!-- Login Button -->
                    <div class="d-grid gap-2 mt-4">
                        <input type="submit" class="btn btn-info btn-lg" name="user_login" value="Login">
                    </div>

                    <!-- Register Link -->
                    <div class="text-center mt-3">
                        <p>Don't have an account? <a href="register.php" class="text-decoration-none">Sign Up</a></p>
                    </div>
                </form>

                <!-- Display success or error message -->
                <?php if (!empty($message)) { ?>
                    <div class="mt-3">
                        <?php echo $message; ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </main>

    <!-- JavaScript -->
    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>