<?php
// Start session
session_start();

include('../backend/connect.php');
include('../functions/global_function.php');

$message = ''; // Variable to store success or error messages

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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_admin'])) {
    try {
        // Sanitize inputs
        $admin_email = sanitize_input($_POST['admin_email']);
        $admin_password = trim($_POST['admin_password']);  // Passwords don't need sanitization, just trimming

        // Validate email and password inputs
        if (empty($admin_email) || empty($admin_password)) {
            throw new Exception('Please fill in both email and password.');
        }
        if (!filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email address.');
        }

        // Query to check email and fetch user data
        $query = "SELECT * FROM admin_table WHERE admin_email = ?";
        if ($prepared_statement = $connection->prepare($query)) {
            $prepared_statement->bind_param("s", $admin_email);
            $prepared_statement->execute();
            $result = $prepared_statement->get_result();

            if ($result->num_rows === 1) {
                $user_data = $result->fetch_assoc();

                // Verify password
                if (password_verify($admin_password, $user_data['admin_password'])) {
                    // Set session variables
                    $_SESSION['admin_name'] = $user_data['admin_name'];
                    $_SESSION['admin_email'] = $user_data['admin_email'];
                    $_SESSION['user_status'] = $user_data['user_status'];

                    // Redirect to dashboard or products page
                    header("Location: index.php");
                    exit();
                } else {
                    throw new Exception('Incorrect password.');
                }
            } else {
                throw new Exception('No account found with this email.');
            }

            $prepared_statement->close();
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
    <title>Login | Family Online Shopping</title>

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
                <img src="../assets/registration-image.jpg"
                    alt="Account Registration"
                    class="img-fluid rounded shadow-sm border border-secondary"
                    style="max-width: 65%; height: auto;">
            </div>

            <!-- Form Section -->
            <div class="col-md-6 col-lg-5">
                <div class="border px-4 py-3 rounded shadow">
                    <h1 class="text-center mb-4">Login Admin</h1>
                    <form action="" method="post">
                        <!-- Email Input -->
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-secondary-subtle"><i class="fas fa-envelope"></i></span>
                            <input type="email" id="admin_email" name="admin_email" class="form-control" placeholder="Enter your email" required>
                        </div>

                        <!-- Password Input -->
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-secondary-subtle"><i class="fas fa-lock"></i></span>
                            <input type="password" id="admin_password" name="admin_password" class="form-control" placeholder="Enter your password" required>
                        </div>

                        <!-- Login Button -->
                        <div class="d-grid gap-2 mt-4">
                            <input type="submit" class="btn btn-info btn-lg" name="login_admin" value="Login">
                        </div>

                        <!-- Signup Link -->
                        <div class="text-center mt-3">
                            <p class="text-start"> Don't have an account? <a href="admin_account.php" class="text-decoration-none">Sign Up</a></p>
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

    <!-- JavaScript -->
    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>