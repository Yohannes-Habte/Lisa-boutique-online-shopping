<?php

// Securely fetch orders using prepared statements
$query = "SELECT * FROM orders";
$stmt = $connection->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Error executing query: " . htmlspecialchars($stmt->error));
}
?>

<section>

    <h3 class="text-center mb-4">All Orders List</h3>
    <table class="table table-striped table-bordered table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>Order ID</th>
                <th>User ID</th>
                <th>amount_due</th>
                <th>Invoice Number</th>
                <th>total_products</th>
                <th>Order Status</th>
                <th>Order Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $order_id = htmlspecialchars($row['order_id']);
                    $user_id = htmlspecialchars($row['user_id']);
                    $amount_due = htmlspecialchars($row['amount_due']);
                    $invoice_number = htmlspecialchars($row['invoice_number']);
                    $total_products = htmlspecialchars($row['total_products']);
                    $order_status = htmlspecialchars($row['order_status']);
                    $order_date =  htmlspecialchars($row['order_date']);
                    $date = substr($order_date, 0, 10);

                    // Set the status class based on the order status
                    $status_class = $order_status == "Pending" ? "text-warning fw-bold" : "text-success fw-bold";

                    echo "
                    <tr>
                        <td class='text-start'>$order_id</td>
                        <td class='text-start'>$user_id</td>
                        <td class='text-start'>$amount_due</td>
                        <td class='text-start'>$invoice_number</td>
                        <td class='text-start'>$total_products</td>
                        <td class='text-start $status_class'>$order_status</td>
                        <td class='text-start'>$date</td>

                        <td>
                            <div class='d-flex justify-content-center gap-2'>
                                <a href='index.php?view_order={$order_id}' class='btn btn-sm btn-outline-success'>
                                    View
                                </a>
                                <a href='index.php?delete_order={$order_id}' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Are you sure you want to delete this order?\")'>
                                   Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                    ";
                }
            } else {
                echo "<tr><td colspan='9' class='text-center'>No orders found</td></tr>";
            }
            ?>
        </tbody>
    </table>

</section>

<?php
// Close the statement
$stmt->close();
?>