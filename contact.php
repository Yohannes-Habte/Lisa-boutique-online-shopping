<?php

// Ensure the user is logged in
session_start();
include('backend/connect.php');  // Ensure the database connection
include('functions/global_function.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    $_SESSION['errorMessage'] = 'You must be logged in to send a message.';
    header('Location: ./client/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];  // Use the session user_id if logged in
$user_name = $_SESSION['user_name'];

$successMessage = '';
$errorMessage = '';

// Get current page
$current_page = basename($_SERVER['PHP_SELF']);

// Helper functions
function sanitize_input($data)
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function validate_field($data, $maxLength = 255, $minLength = 1)
{
    $data = sanitize_input($data);
    return (strlen($data) >= $minLength && strlen($data) <= $maxLength) ? $data : false;
}

function validate_email($email)
{
    $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : false;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $errors = [];
    $user_ip = getUserIPAddress();
    $name = validate_field($_POST['name'], 100);
    $email = validate_email($_POST['email']);
    $subject = validate_field($_POST['subject'], 100);
    $message = validate_field($_POST['message'], 255);

    if (!$name) $errors[] = 'Name must be between 1 and 100 characters.';
    if (!$email) $errors[] = 'Invalid email address.';
    if (!$subject) $errors[] = 'Subject must be between 1 and 100 characters.';
    if (!$message) $errors[] = 'Message must be between 1 and 255 characters.';

    if (empty($errors)) {
        try {
            // Insert comment into the database, make sure to use the correct user_id
            $stmt = $connection->prepare("INSERT INTO comments (user_id, user_ip, name, email, subject, message) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssss", $user_id, $user_ip, $name, $email, $subject, $message);

            if ($stmt->execute()) {
                $_SESSION['successMessage'] = 'Your message has been successfully sent.';
                header("Location: contact.php");  // Redirect to the same page or wherever needed
                exit();
            } else {
                throw new Exception('Database error: Failed to execute query.');
            }
            $stmt->close();
        } catch (Exception $e) {
            $_SESSION['errorMessage'] = $e->getMessage();
        }
    } else {
        $_SESSION['errorMessage'] = implode('<br>', $errors);
    }
}

$connection->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>


    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4p889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <!-- Header Section -->
    <header>
        <!-- Primary Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container p-0">
                <a class="navbar-brand fw-bold" href="index.php">Lisa Boutique</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page == 'about.php' ? 'active' : ''; ?>" href="about.php">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page == 'products.php' ? 'active' : ''; ?>" href="products.php">Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page == 'contact.php' ? 'active' : ''; ?>" href="contact.php">Contact</a>
                        </li>

                    </ul>

                    <!-- Search Form -->
                    <form class="d-flex" role="search" action="search_product.php" method="get">
                        <input class="form-control me-2" type="search" name="search_item" placeholder="Search" aria-label="Search Product Input">
                        <input type="submit" class="btn btn-outline-light" name="searched_product" value="Search">
                    </form>
                </div>
            </div>
        </nav>

        <!-- Secondary Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-secondary-subtle">
            <div class="container">
                <div class="d-flex justify-content-between w-100">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link text-dark" href="#">Welcome, <?php echo htmlspecialchars($user_name); ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark" href="./client/logout.php">Logout</a>
                        </li>
                    </ul>

                    <!-- Profile Dropdown -->
                    <div class="dropdown">
                        <button
                            class="btn btn-secondary dropdown-toggle"
                            type="button"
                            id="profileDropdown"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">
                            ☰
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                            <li>
                                <a class="dropdown-item" href="./client/profile.php">Profile</a>
                            </li>
                            <?php if ($user_status == 'admin'): ?>
                                <li>
                                    <a class="dropdown-item" href="./admin/index.php">Admin</a>
                                </li>
                            <?php endif; ?>
                            <li>
                                <a class="dropdown-item" href="./client/logout.php">Logout</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Section -->
    <main class="min-vh-100 py-5 bg-light">
        <div class="container mt-5">
            <h1 class="text-center mb-4 text-primary">Contact Us</h1>

            <!-- Display success or error message -->
            <?php if (isset($_SESSION['successMessage'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['successMessage']; ?></div>
                <?php unset($_SESSION['successMessage']); ?>
            <?php elseif (isset($_SESSION['errorMessage'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['errorMessage']; ?></div>
                <?php unset($_SESSION['errorMessage']); ?>
            <?php endif; ?>

            <div class="row gap-5">
                <!-- Contact Form Section -->
                <div class="col-md-7 mb-4 bordered shadow p-4">
                    <h3 class="mb-4 text-secondary">Get in Touch</h3>
                    <form action="contact.php" method="POST">
                        <!-- Name Input -->
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" name="name" placeholder="Your Name" required>
                        </div>
                        <!-- Email Input -->
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" name="email" placeholder="Your Email" required>
                        </div>
                        <!-- Subject Input -->
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-tag"></i></span>
                            <input type="text" class="form-control" name="subject" placeholder="Subject" required>
                        </div>
                        <!-- Message Input -->
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-comment"></i></span>
                            <textarea class="form-control" name="message" placeholder="Your Message" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-info w-100" name="submit">Send Message</button>
                    </form>
                </div>

                <!-- Contact Information Section -->
                <div class="col-md-4 shadow-sm p-4 mb-4 rounded bg-white">
                    <h3 class="mb-4 text-secondary"><i class="fas fa-info-circle"></i> Contact Information</h3>
                    <ul class="list-unstyled">
                        <li class="mb-3">
                            <i class="fas fa-phone-alt text-primary"></i>
                            <strong>Phone:</strong> +1 (555) 123-4567
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-envelope text-danger"></i>
                            <strong>Email:</strong> <a href="mailto:contact@lisaboutque.com" class="text-decoration-none">contact@lisaboutque.com</a>
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-map-marker-alt text-success"></i>
                            <strong>Address:</strong> 123 Lisaboutque St, Luxury City, ABC 12345
                        </li>
                    </ul>

                    <h3 class="mb-4 text-secondary"><i class="fas fa-share-alt"></i> Follow Us</h3>
                    <div class="d-grid gap-2">
                        <a href="https://www.facebook.com/" class="btn btn-outline-primary mb-2 w-100" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-facebook-f"></i> Facebook
                        </a>
                        <a href="https://x.com/" class="btn btn-outline-info mb-2 w-100" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-twitter"></i> Twitter
                        </a>
                        <a href="https://www.instagram.com/" class="btn btn-outline-danger w-100" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-instagram"></i> Instagram
                        </a>
                    </div>
                </div>

            </div>

            <!-- Google Maps Section -->

            <div class="container my-4">
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <h5 class="card-title text-center p-3">Map Location</h5>
                        <div class="embed-responsive embed-responsive-16by9">
                            <iframe class="embed-responsive-item w-100"
                                src="https://www.google.com/maps/embed?pb=https://g.co/kgs/k875BZp"
                                allowfullscreen=""
                                loading="lazy"
                                style="height: 500px;">
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </main>



    <!-- Footer Section -->
    <?php include('components/footer/footer.php'); ?>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>