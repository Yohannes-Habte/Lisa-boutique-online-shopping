<?php
// Start session
session_start();

include('../backend/connect.php');
include('../functions/global_function.php');

// Function to handle file upload securely
function handleFileUpload($file)
{
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 2 * 1024 * 1024; // 2 MB
    $upload_dir = 'uploads/';

    if ($file['error'] === UPLOAD_ERR_OK) {
        if (in_array($file['type'], $allowed_types) && $file['size'] <= $max_size) {
            $file_name = time() . '_' . basename($file['name']);
            $target_file = $upload_dir . $file_name;
            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                return $file_name;
            } else {
                throw new Exception('Error moving uploaded file.');
            }
        } else {
            throw new Exception('Invalid file type or size exceeds 2 MB.');
        }
    }
    return null; // No file uploaded
}

$message = ''; // Variable to store success or error messages

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_user'])) {
    try {
        // Sanitize inputs
        $user_name = htmlspecialchars(trim($_POST['user_name']));
        $user_email = filter_var($_POST['user_email'], FILTER_SANITIZE_EMAIL);
        $user_password = trim($_POST['user_password']);
        $confirm_password = trim($_POST['confirm_password']);
        $user_address = htmlspecialchars(trim($_POST['user_address']));
        $user_mobile = htmlspecialchars(trim($_POST['user_mobile']));
        $user_status = "customer";
        $user_ip = getUserIP();

        // Check if consent is provided
        if (!isset($_POST['consent'])) {
            throw new Exception('Please agree to the Terms and Conditions.');
        }

        // Validate other inputs
        if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email address.');
        }
        if ($user_password !== $confirm_password) {
            throw new Exception('Passwords do not match.');
        }

        // Check if email already exists in the database
        $check_email_query = "SELECT user_email FROM users WHERE user_email = ?";
        if ($prepared_statement = $connection->prepare($check_email_query)) {
            $prepared_statement->bind_param("s", $user_email);
            $prepared_statement->execute();
            $prepared_statement->store_result();

            if ($prepared_statement->num_rows > 0) {
                throw new Exception('This email is already registered. Please use a different email.');
            }

            $prepared_statement->close();
        }

        // Hash password
        $hashed_password = password_hash($user_password, PASSWORD_BCRYPT);

        // Handle file upload
        $user_image = null;
        if (!empty($_FILES['user_image']['name'])) {
            $user_image = handleFileUpload($_FILES['user_image']);
        }

        // Prepare SQL statement
        $insert_user_query = "INSERT INTO users (user_name, user_email, user_password, user_image, user_ip, user_address, user_mobile, user_status, consent)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Use prepared statements to avoid SQL injection
        if ($prepared_statement = $connection->prepare($insert_user_query)) {
            $consent = 1; // Assuming checkbox is required
            $prepared_statement->bind_param("sssssssss", $user_name, $user_email, $hashed_password, $user_image, $user_ip, $user_address, $user_mobile, $user_status, $consent);
            $prepared_statement->execute();
            $prepared_statement->close();
            $message = "<div class='alert alert-success'>Registration successful!</div>";

            header("Location: login.php");
            exit();
        } else {
            throw new Exception('Error in database query.');
        }
    } catch (Exception $e) {
        $message = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }

    // Redirect the user to `products.php` after registration
    $_SESSION['user_email'] = $user_email;
    $_SESSION['user_name'] = $user_name;
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['user_status'] = $user_status;
    echo "<script>window.open('products.php', '_self')</script>";
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
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 border px-4">
                <h1 class="text-center mb-4">Sign Up</h1>
                <form action="" method="post" enctype="multipart/form-data">
                    <!-- User Name Input -->

                    <div class="input-group mb-3">
                        <span class="input-group-text bg-secondary-subtle"><i class="fas fa-user"></i></span>
                        <input type="text" id="user_name" name="user_name" class="form-control" placeholder="Enter your full name">
                    </div>

                    <!-- User Email Input -->
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-secondary-subtle"><i class="fas fa-envelope"></i></span>
                        <input type="email" id="user_email" name="user_email" class="form-control" placeholder="Enter your email">
                    </div>

                    <!-- User Password Input -->
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-secondary-subtle"><i class="fas fa-lock"></i></span>
                        <input type="password" id="user_password" name="user_password" class="form-control" placeholder="Create a password">
                    </div>

                    <!-- Confirm Password Input -->
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-secondary-subtle"><i class="fas fa-lock"></i></span>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm your password">
                    </div>

                    <!-- User Mobile Input -->
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-secondary-subtle"><i class="fas fa-phone"></i></span>
                        <input type="tel" id="user_mobile" name="user_mobile" class="form-control" placeholder="Enter phone number">
                    </div>

                    <!-- User Address Input -->
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text bg-secondary-subtle">
                                <i class="fas fa-home"></i>
                            </span>
                            <input id="user_address" name="user_address" class="form-control" placeholder="Enter your address">
                        </div>
                    </div>


                    <!-- User Image Input -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Upload Profile Photo</label>
                        <div class="input-group">
                            <span class="input-group-text bg-secondary-subtle"><i class="fas fa-image"></i></span>
                            <input type="file" id="user_image" name="user_image" class="form-control" accept="image/*">
                        </div>



                        <!-- Terms and Conditions -->
                        <div class="form-check mb-3 mt-4">
                            <input type="checkbox" id="consent" name="consent" class="form-check-input">
                            <label for="consent" class="form-check-label">
                                I agree to the <a href="terms.php" class="text-decoration-none">Terms and Conditions</a>.
                            </label>
                        </div>

                        <!-- Signup Button -->
                        <div class="d-grid gap-2 mt-4">
                            <input type="submit" class="btn btn-info btn-lg" name="register_user" value="Sign Up">
                        </div>

                        <!-- Login Link -->
                        <div class="text-center mt-3">
                            <p class="text-start"> Already have an account? <a href="login.php" class="text-decoration-none">Login</a></p>
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
    </main>



    <!-- JavaScript -->
    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>