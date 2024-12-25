<?php


// Include database connection
include('../backend/connect.php');
include('../functions/global_function.php');

// Query to retrieve user information based on their IP address
$user_ip = getUserIPAddress();
$get_user = "SELECT * FROM users WHERE user_ip = '$user_ip'";
$run_user = mysqli_query($connection, $get_user);
$row_user = mysqli_fetch_array($run_user);
$user_id = $row_user['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_payment'])) {
    // Sanitize and validate input data
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars(trim($_POST['phone']));
    $card_number = htmlspecialchars(trim($_POST['card_number']));
    $expiry_date = htmlspecialchars(trim($_POST['expiry_date']));
    $cvv = htmlspecialchars(trim($_POST['cvv']));

    $errors = [];

    // Validate inputs
    if (empty($name)) {
        $errors[] = 'Full Name is required.';
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid Email is required.';
    }

    if (empty($phone) || !preg_match('/^\+?\d{10,15}$/', $phone)) {
        $errors[] = 'A valid Phone number is required.';
    }

    if (empty($card_number) || !preg_match('/^\d{16}$/', $card_number)) {
        $errors[] = 'A valid 16-digit Card Number is required.';
    }

    if (empty($expiry_date) || !preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $expiry_date)) {
        $errors[] = 'A valid Expiry Date (MM/YY) is required.';
    }

    if (empty($cvv) || !preg_match('/^\d{3}$/', $cvv)) {
        $errors[] = 'A valid 3-digit CVV is required.';
    }

    if (empty($errors)) {
        try {
            // Placeholder for actual payment gateway integration
            // You would use a payment processing API like Stripe or PayPal here

            // Redirect on successful payment
            header("Location: order.php?user=$user_id");
            exit();
        } catch (Exception $e) {
            echo '<div class="alert alert-danger text-center mt-4">
                    <h4>Payment failed</h4>
                    <p>There was an error processing your payment. Please try again later.</p>
                  </div>';
        }
    } else {
        // Display errors
        echo '<div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">Payment Failed</h4>
                <ul>';
        foreach ($errors as $error) {
            echo '<li class="">' . htmlspecialchars($error) . '</li>';
        }
        echo '</ul>
              </div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">

    <style>
        .form-control::placeholder {
            font-size: 0.75rem;
        }
    </style>
</head>

<body class="bg-light">

    <main class="container">
        <section class="py-3">
            <?php
            $userIP = getUserIP();
            $total_price = 0;
            $select_cart = "SELECT * FROM cart_table WHERE ip_address = '$userIP'";
            $result = mysqli_query($connection, $select_cart);
            $result_cart_count = mysqli_num_rows($result);

            if ($result_cart_count > 0) {

                while ($row = mysqli_fetch_assoc($result)) {
                    $product_id = $row['product_id'];
                    $quantity = $row['quantity']; // Fetch the current quantity
                    $select_product = "SELECT * FROM products WHERE product_id = $product_id";
                    $product_result = mysqli_query($connection, $select_product);

                    while ($product_data = mysqli_fetch_assoc($product_result)) {
                        $product_name = $product_data['product_name'];
                        $product_image1 = $product_data['product_image1'];
                        $product_price = $product_data['product_price'];

                        $product_total_price = $product_price * $quantity; // Calculate total price for the product
                        $total_price += $product_total_price;
                    }
                }
            }
            ?>


            <h3 class="text-center bg-light text-dark py-3 mt-5 shadow-sm rounded">
                Total Items Price: <strong class="text-primary"> $<?php echo $total_price; ?></strong>
            </h3>



        </section>

        <section class="py-3">
            <h3 class="text-center mb-4">Payment Methods</h3>
            <form action="" method="post" class="bg-white p-4 rounded shadow-sm col-md-6 m-auto">
                <!-- Full Name -->
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <div class="input-group">
                        <span class="input-group-text bg-secondary-subtle"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter your full name">
                    </div>
                </div>
                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-secondary-subtle"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email">
                    </div>
                </div>
                <!-- Phone -->
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <div class="input-group">
                        <span class="input-group-text bg-secondary-subtle"><i class="fas fa-phone"></i></span>
                        <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter your phone number">
                    </div>
                </div>
                <!-- Card Details Row -->
                <div class="row g-3 fs-6">
                    <!-- Card Number -->
                    <div class="col-md-6">
                        <label for="card_number" class="form-label">Card Number</label>
                        <div class="input-group">
                            <span class="input-group-text bg-secondary-subtle"><i class="fas fa-credit-card"></i></span>
                            <input type="text" class="form-control" id="card_number" name="card_number" placeholder="Card number">
                        </div>
                    </div>
                    <!-- Expiry Date -->
                    <div class="col-md-3">
                        <label for="expiry_date" class="form-label">Expiry Date</label>
                        <div class="input-group">
                            <span class="input-group-text bg-secondary-subtle"><i class="fas fa-calendar-alt"></i></span>
                            <input type="text" class="form-control" id="expiry_date" name="expiry_date" placeholder="MM/YY">
                        </div>
                    </div>
                    <!-- CVV -->
                    <div class="col-md-3">
                        <label for="cvv" class="form-label">CVV</label>
                        <div class="input-group">
                            <span class="input-group-text bg-secondary-subtle"><i class="fas fa-lock"></i></span>
                            <input type="text" class="form-control" id="cvv" name="cvv" placeholder="CVV">
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <input type="submit" name="submit_payment" value="Pay Now" class="btn btn-info w-100 mt-4">
            </form>
        </section>



    </main>







    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>