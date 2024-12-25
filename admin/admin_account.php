<?php
// Start session
session_start();

include('../backend/connect.php');
include('../functions/global_function.php');

// Message for feedback
$message = '';

/**
 * Sanitize input data to ensure security and prevent SQL injection.
 *
 * @param string $data The input data to sanitize.
 * @return string Sanitized data.
 */
function sanitize_input($data)
{
    global $connection;  // Use the global connection for database-related sanitization
    $data = trim($data);  // Remove extra spaces
    $data = stripslashes($data);  // Remove slashes
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');  // Convert special characters to HTML entities
    if ($connection) {
        $data = mysqli_real_escape_string($connection, $data);  // Escape special characters for SQL queries
    }
    return $data;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_admin'])) {
    try {
        // Sanitize inputs
        $admin_name = sanitize_input($_POST['admin_name']);
        $admin_email = sanitize_input($_POST['admin_email']);
        $admin_password = sanitize_input($_POST['admin_password']);
        $confirm_password = sanitize_input($_POST['confirm_password']);
        $user_status = "admin";

        // Validate inputs
        if (!filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email address.');
        }
        if ($admin_password !== $confirm_password) {
            throw new Exception('Passwords do not match.');
        }

        // Check if email already exists
        $check_email_query = "SELECT admin_email FROM admin_table WHERE admin_email = ?";
        if ($prepared_statement = $connection->prepare($check_email_query)) {
            $prepared_statement->bind_param("s", $admin_email);
            $prepared_statement->execute();
            $prepared_statement->store_result();

            if ($prepared_statement->num_rows > 0) {
                throw new Exception('This email is already registered. Please use a different email.');
            }

            $prepared_statement->close();
        }

        // Hash the password for security
        $hashed_password = password_hash($admin_password, PASSWORD_BCRYPT);

        // Insert user into the database
        $insert_user_query = "INSERT INTO admin_table (admin_name, admin_email, admin_password, user_status) VALUES (?, ?, ?, ?)";
        if ($prepared_statement = $connection->prepare($insert_user_query)) {
            $prepared_statement->bind_param("ssss", $admin_name, $admin_email, $hashed_password, $user_status);
            $prepared_statement->execute();
            $prepared_statement->close();

            // Set session and redirect
            $_SESSION['admin_email'] = $admin_email;
            $_SESSION['admin_name'] = $admin_name;
            $_SESSION['user_status'] = $user_status;

            $message = "<div class='alert alert-success'>Registration successful! Redirecting to login page...</div>";
             // Redirect to dashboard or products page
             header("Location: index.php");
            exit();
        } else {
            throw new Exception('Error in database query.');
        }
    } catch (Exception $e) {
        $message = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | Family Online Shopping</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4p889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>

<body class="d-flex flex-column min-vh-100 bg-light">

    <!-- Main Content -->
    <main class="container py-5 flex-grow-1">
        <div class="row justify-content-center align-items-center">
            <!-- Image Section -->
            <div class="col-md-6 mb-4 text-center">
                <img src="../assets/registration-image.jpg" alt="Account Registration" class="img-fluid rounded shadow-sm border border-secondary" style="max-width: 70%; height: auto;">
            </div>

            <!-- Form Section -->
            <div class="col-md-6 col-lg-5">
                <div class="border px-4 py-3 rounded shadow">
                    <h1 class="text-center mb-4">Sign Up</h1>
                    <form action="" method="post" enctype="multipart/form-data">
                        <!-- Admin Name Input -->
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-secondary-subtle"><i class="fas fa-user"></i></span>
                            <input type="text" id="admin_name" name="admin_name" class="form-control" placeholder="Enter your full name" required>
                        </div>

                        <!-- Admin Email Input -->
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-secondary-subtle"><i class="fas fa-envelope"></i></span>
                            <input type="email" id="admin_email" name="admin_email" class="form-control" placeholder="Enter your email" required>
                        </div>

                        <!-- Admin Password Input -->
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-secondary-subtle"><i class="fas fa-lock"></i></span>
                            <input type="password" id="admin_password" name="admin_password" class="form-control" placeholder="Create a password" required>
                        </div>

                        <!-- Confirm Password Input -->
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-secondary-subtle"><i class="fas fa-lock"></i></span>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm your password" required>
                        </div>

                        <!-- Signup Button -->
                        <div class="d-grid gap-2 mt-4">
                            <input type="submit" class="btn btn-info btn-lg" name="register_admin" value="Sign Up">
                        </div>

                        <!-- Login Link -->
                        <div class="text-center mt-3">
                            <p>Already have an account? <a href="admin_login.php" class="text-decoration-none">Login</a></p>
                        </div>

                        <!-- Display success or error message -->
                        <?php if (!empty($message)) { ?>
                            <div class="mt-3">
                                <?php echo $message; ?>
                            </div>
                        <?php } ?>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="text-center py-3 bg-dark text-light">
        <small>&copy; <span id="year"></span> Family Online Shopping. All rights reserved.</small>
    </footer>

    <!-- JavaScript -->
    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
