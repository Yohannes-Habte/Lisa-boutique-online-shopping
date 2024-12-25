<?php

include('../backend/connect.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$order_id = '';
$amount_due = '';

// ========================================================================================================================
// Get order details
// ========================================================================================================================

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Fetch order details securely
    $select_order = "SELECT * FROM orders WHERE order_id = ?";
    $stmt = $connection->prepare($select_order);
    $stmt->bind_param("i", $order_id); // Bind the order_id as an integer
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
        $amount_due = $order['amount_due'];
    }
    $stmt->close();
}

// ========================================================================================================================
// Confirm Order Payment and Update Order Status
// ========================================================================================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_order_payment'])) {
    $order_id = $_POST['order_id'];
    $payment_method = $_POST['payment_method'];
    $amount = $_POST['amount'];

    // Begin transaction
    $connection->begin_transaction();

    try {
        // Insert payment details securely
        $insert_payment = "INSERT INTO payments (order_id, amount, payment_mode, payment_date) VALUES (?, ?, ?, NOW())";
        $stmt = $connection->prepare($insert_payment);
        $stmt->bind_param("ids", $order_id, $amount, $payment_method);
        $stmt->execute();

        // Update order status to Paid
        $update_order = "UPDATE orders SET order_status = 'Paid' WHERE order_id = ?";
        $update_stmt = $connection->prepare($update_order);
        $update_stmt->bind_param("i", $order_id);
        $update_stmt->execute();

        // Commit the transaction
        $connection->commit();

        // Redirect to profile page
        header('Location: profile.php?my_orders');
        exit();
    } catch (Exception $e) {
        // Rollback transaction on error
        $connection->rollback();
        echo "<script>alert('Failed to Confirm Payment and Update Order');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Family Online Shopping</title>

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
    <main class="container d-flex justify-content-center align-items-center py-5 flex-grow-1">

        <section class="col-md-6 col-lg-4 shadow-sm p-4 bg-white rounded">
            <h4 class="mb-4 text-center">Confirm Order Payment</h4>
            <form action="" method="post">
                <!-- Order ID -->
                <div class="mb-3">
                    <label for="order_id" class="form-label">Order ID</label>
                    <div class="input-group">
                        <span class="input-group-text bg-secondary-subtle"><i class="fas fa-file-invoice"></i></span>
                        <input type="text" id="order_id" name="order_id" class="form-control" value="<?php echo htmlspecialchars($order_id); ?>" readonly>
                    </div>
                </div>

                <!-- Amount to be paid -->
                <div class="mb-3">
                    <label for="amount" class="form-label">Amount Due</label>
                    <div class="input-group">
                        <span class="input-group-text bg-secondary-subtle"><i class="fas fa-dollar-sign"></i></span>
                        <input type="text" id="amount" name="amount" class="form-control" value="<?php echo htmlspecialchars($amount_due); ?>" readonly>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="mb-3">
                    <label for="payment_method" class="form-label">Payment Method</label>
                    <select name="payment_method" id="payment_method" class="form-select" required>
                        <option value="" disabled selected>Select Payment Mode</option>
                        <option value="Paypal">PayPal</option>
                        <option value="Stripe">Stripe</option>
                        <option value="Cash">Cash</option>
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="d-grid">
                    <input type="submit" name="confirm_order_payment" class="btn btn-info" value="Confirm Payment">
                </div>
            </form>
        </section>
    </main>

    <!-- JavaScript -->
    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>