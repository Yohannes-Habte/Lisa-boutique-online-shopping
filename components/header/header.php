<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Restrict access: Redirect to login if user is not logged in
if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_name'])) {
    header("Location: ./client/login.php");
    exit();
}

// Display the user's name
$user_name = $_SESSION['user_name'];
$user_status = $_SESSION['user_status'];
$user_id = $_SESSION['user_id'];

// Get current page
$current_page = basename($_SERVER['PHP_SELF']);
?>

<header>
    <!-- Primary Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">Lisa Boutique</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
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
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'cart.php' ? 'active' : ''; ?>" href="cart.php">
                            <i class="fa-solid fa-cart-shopping"></i>
                            <sup><?php get_total_items_in_cart() ?></sup>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Total Price = $<?php get_total_price_in_cart() ?></a>
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
