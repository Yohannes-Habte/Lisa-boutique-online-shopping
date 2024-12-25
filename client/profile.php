<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

// Include database connection and global functions
include('../backend/connect.php');
include('../functions/global_function.php');

// Start output buffering
ob_start();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4p889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>

<body class="d-flex flex-column min-vh-100 bg-light">

    <header class="container py-3">
        <a href="../index.php" class="btn btn-link text-blue fs-5 fw-semibold">
            <i class="bi bi-house-door"></i> Go to Home
        </a>
    </header>

    <!-- Main Content -->
    <main class="mx-5 p-5 flex-grow-1">

        <div class="row mx-5">
            <!-- Sidebar -->
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <?php
                        $user_email = $_SESSION['user_email'];

                        // Select user details
                        $select_user = "SELECT * FROM users WHERE user_email = '$user_email'";
                        $result_of_selected_user = mysqli_query($connection, $select_user);
                        $user = mysqli_fetch_array($result_of_selected_user);
                        $user_name = $user['user_name'] ?? 'Guest';
                        $user_image = $user['user_image'] ?? 'default.png';
                        ?>
                        <img src="uploads/<?php echo $user_image ?>" alt="Profile Picture" class="img-thumbnail rounded-circle mb-3" style="width: 150px; height: 150px;">
                        <h4 class="text-dark"><?php echo $user_name ?></h4>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <a href="profile.php?my_pending_orders" class="text-decoration-none text-dark"><i class="fas fa-box me-2"></i> Pending Orders</a>
                        </li>

                        <li class="list-group-item">
                            <a href="profile.php?my_orders" class="text-decoration-none text-dark"><i class="fas fa-shopping-bag me-2"></i> My Orders</a>
                        </li>

                        <li class="list-group-item">
                            <a href="profile.php?edit_account" class="text-decoration-none text-dark"><i class="fas fa-edit me-2"></i> Edit Account</a>
                        </li>

                        <li class="list-group-item">
                            <a href="profile.php?delete_account" class="text-decoration-none text-dark"><i class="fas fa-shopping-bag me-2"></i> Delete Account </a>
                        </li>

                        <li class="list-group-item">
                            <a href="logout.php" class="text-decoration-none text-dark"><i class="fas fa-shopping-bag me-2"></i> Logout </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="col-md-9">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="text-center text-info">Welcome to Your Profile</h2>
                        <p class="text-center">Manage your account, view your orders, and update your profile information here.</p>
                        <div class="row text-center mt-4">

                            <!-- Display pending orders -->
                            <?php
                            get_user_pending_orders();



                            if (isset($_GET['edit_account'])) {
                                include('edit_account.php');
                            }

                            if (isset($_GET['my_orders'])) {
                                include('user_orders.php');
                            }

                            if (isset($_GET['delete_account'])) {
                                include('delete_account.php');
                            }
                            ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </main>

    <!-- Include footer -->
    <?php include('../components/footer/footer.php'); ?>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>