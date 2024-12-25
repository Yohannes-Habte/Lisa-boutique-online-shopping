<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    echo '<div class="alert alert-danger" role="alert">You must be logged in to view your orders.</div>';
    exit;
}

// Retrieve user email from session
$user_email = $_SESSION['user_email'];

// Validate user email format
if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
    echo '<div class="alert alert-danger" role="alert">Invalid session data. Please log in again.</div>';
    exit;
}

// Establish database connection
// Assumes $connection is properly instantiated elsewhere
if (!isset($connection) || !$connection instanceof mysqli) {
    echo '<div class="alert alert-danger" role="alert">Database connection error. Please try again later.</div>';
    exit;
}

// Use prepared statements to fetch user information
$query_user = $connection->prepare("SELECT * FROM users WHERE user_email = ?");
if (!$query_user) {
    echo '<div class="alert alert-danger" role="alert">Database query error.</div>';
    exit;
}

$query_user->bind_param('s', $user_email);
$query_user->execute();
$result_user = $query_user->get_result();

// Check if user exists
if ($result_user && $result_user->num_rows > 0) {
    $user_data = $result_user->fetch_assoc();
    $user_id = intval($user_data['user_id']);
    $user_name = htmlspecialchars($user_data['user_name'], ENT_QUOTES, 'UTF-8');
} else {
    echo '<div class="alert alert-warning" role="alert">User not found.</div>';
    $query_user->close();
    exit;
}

$query_user->close();

?>

<section>
    <h4 class="p-3"><?php echo $user_name ? htmlspecialchars("$user_name's Orders", ENT_QUOTES, 'UTF-8') : "User Orders"; ?></h4>

    <table class="table table-striped table-bordered table-hover" style="width:100%; text-align:left;">
        <thead class="thead-dark">
            <tr>
                <th>Order Date</th>
                <th>Order ID</th>
                <th>Amount Due</th>
                <th>Invoice Number</th>
                <th>Total Products</th>
                <th>Payment Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>

            <?php
            // Use prepared statements to fetch orders securely
            $query_orders = $connection->prepare("SELECT * FROM orders WHERE user_id = ?");
            if (!$query_orders) {
                echo '<tr><td colspan="7" class="text-center">Database query error.</td></tr>';
                exit;
            }

            $query_orders->bind_param('i', $user_id);
            $query_orders->execute();
            $query_orders_result = $query_orders->get_result();

            // Check if orders exist
            if ($query_orders_result->num_rows > 0) {
                while ($order = $query_orders_result->fetch_assoc()) {
                    // Sanitize and assign variables
                    $order_id = intval($order['order_id']);
                    $amount_due = number_format(floatval($order['amount_due']), 2, '.', ',');
                    $invoice_number = htmlspecialchars($order['invoice_number'], ENT_QUOTES, 'UTF-8');
                    $total_products = intval($order['total_products']);
                    $order_status_raw = $order['order_status'];
                    $order_date = htmlspecialchars(substr($order['order_date'], 0, 10), ENT_QUOTES, 'UTF-8');

                    // Display styled order status
                    $order_status = $order_status_raw === 'Pending'
                        ? "<span class='text-danger'>Pending</span>"
                        : "<span class='text-success'>Complete</span>";

                    // Generate action link or plain text for the payment
                    $action = $order_status_raw === 'Paid'
                        ? "<span class='text-success'>Paid</span>"
                        : "<a href='confirm_payment.php?order_id=$order_id' class='btn btn-primary btn-sm'>Pay</a>";

                    echo "
                        <tr>
                            <td>$order_date</td>
                            <td>$order_id</td>
                            <td>$$amount_due</td>
                            <td>$invoice_number</td>
                            <td>$total_products</td>
                            <td>$order_status</td>
                            <td>$action</td>
                        </tr>
                    ";
                }
            } else {
                echo "<tr><td colspan='7' class='text-center'>No orders found.</td></tr>";
            }

            $query_orders->close();
            ?>

        </tbody>
    </table>
</section>