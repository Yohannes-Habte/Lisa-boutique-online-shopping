<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../backend/connect.php');
include('../functions/global_function.php');



// If the user is not logged in, redirect to the login page. If the user is not an admin, redirect to the login page.
if (!isset($_SESSION['admin_email']) || !isset($_SESSION['admin_name']) || !isset($_SESSION['user_status'])) {
    header('location: ./admin_login.php');
}

// Display the user's name
$admin_name = $_SESSION['admin_name'];
$admin_email = $_SESSION['admin_email'];
$user_status = $_SESSION['user_status'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Manage Categories, Products, Orders, and More</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Font Awesome CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../style.css?v=1.0">

</head>

<body>

    <header class="container-fluid p-0">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand fw-bold" href="../index.php">FOS</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link text-light" href="#">Welcome <?php echo $admin_name ?></a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Second Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-secondary px-2">
            <div class="container-fluid d-flex align-items-center">
                <!-- Admin Profile Image -->
                <div class="d-flex align-items-center">
                    <img src="../assets/Habte.jpg" alt="Admin Name" class="admin-profile-photo img-fluid me-3" style="width: 50px; height: 50px; border-radius: 50%;">
                </div>

                <!-- Toggler Button -->
                <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav" aria-controls="adminNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navigation Links -->
                <div class="collapse navbar-collapse flex-grow-1" id="adminNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a href="index.php?add_category" class="nav-link text-light">Add Category</a></li>
                        <li class="nav-item"><a href="index.php?categories" class="nav-link text-light">Categories</a></li>

                        <li class="nav-item"><a href="index.php?add_brand" class="nav-link text-light">Add Brand</a></li>
                        <li class="nav-item"><a href="index.php?brands" class="nav-link text-light">Brands</a></li>

                        <li class="nav-item"><a href="index.php?add_product" class="nav-link text-light">Add Product</a></li>
                        <li class="nav-item"><a href="index.php?products" class="nav-link text-light">Products</a></li>

                        <li class="nav-item"><a href="index.php?orders" class="nav-link text-light">Orders</a></li>
                        <li class="nav-item"><a href="index.php?product_demand" class="nav-link text-light">Product Demand</a></li>

                        <li class="nav-item"><a href="index.php?payments" class="nav-link text-light">Payments</a></li>

                        <li class="nav-item"><a href="index.php?users" class="nav-link text-light">Users</a></li>

                        <li class="nav-item"><a href="index.php?logout_admin" class="nav-link text-light">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>

    </header>

    <div class="container py-3">
        <a href="../index.php" class="btn btn-link text-blue fs-5 fw-semibold">
            <i class="bi bi-house-door"></i> Go to Home
        </a>
    </div>

    <main class="container my-5 min-vh-100">


        <!-- Main Content -->


        <div class="col-md-12">

            <?php

            // Handle category related pages
            if (isset($_GET['add_category'])) {
                include('add_category.php');
            }

            if (isset($_GET['edit_category'])) {
                include('edit_category.php');
            }

            if (isset($_GET['delete_category'])) {
                include('delete_category.php');
            }

            if (isset($_GET['categories'])) {
                include('categories.php');
            }

            // Handle brand related pages
            if (isset($_GET['add_brand'])) {
                include('add_brand.php');
            }

            if (isset($_GET['edit_brand'])) {
                include('edit_brand.php');
            }

            if (isset($_GET['delete_brand'])) {
                include('delete_brand.php');
            }

            if (isset($_GET['brands'])) {
                include('brands.php');
            }

            // Handle product related pages
            if (isset($_GET['add_product'])) {
                include('add_new_product.php');
            }

            if (isset($_GET['edit_product'])) {
                include('edit_product.php');
            }

            if (isset($_GET['delete_product'])) {
                include('delete_product.php');
            }

            if (isset($_GET['products'])) {
                include('admin_products.php');
            }



            // Handle order related pages
            if (isset($_GET['orders'])) {
                include('orders.php');
            }

            if (isset($_GET['product_demand'])) {
                include('product_demand.php');
            }

            // Handle payment related pages
            if (isset($_GET['payments'])) {
                include('payments.php');
            }

            if (isset($_GET['payment'])) {
                include('delete_payment.php');
            }

            // Handle user related pages
            if (isset($_GET['users'])) {
                include('users.php');
            }

            if (isset($_GET['delete_user'])) {
                include('delete_user.php');
            }


            // Logout the admin
            if (isset($_GET['logout_admin'])) {
                include('admin_logout.php');
            }

            ?>


        </div>


    </main>

    <!-- Footer section include it from component -->
    <?php include('../components/footer/footer.php') ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>

</html>