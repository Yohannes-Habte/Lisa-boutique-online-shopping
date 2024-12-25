<?php
/**
 * To display only the orders that have been paid, you need to perform a JOIN between the payments table and the orders table in your database. 
 * You'll filter the results based on the order_status column in the orders table to ensure only "paid" orders are displayed.
 */

$query = "
    SELECT 
        payments.payment_id,
        payments.order_id,
        payments.amount AS amount_paid,
        payments.payment_mode AS payment_method,
        payments.payment_date,
        orders.order_status
    FROM payments
    INNER JOIN orders ON payments.order_id = orders.order_id
    WHERE orders.order_status = 'paid'
";

$stmt = $connection->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Error executing query: " . htmlspecialchars($stmt->error));
}
?>

<section>
    <h3 class="text-center mb-4">All Paid Orders</h3>
    <table class="table table-striped table-bordered table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>Payment ID</th>
                <th>Order ID</th>
                <th>Amount Paid</th>
                <th>Payment Method</th>
                <th>Payment Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $payment_id = htmlspecialchars($row['payment_id']);
                    $order_id = htmlspecialchars($row['order_id']);
                    $amount_paid = htmlspecialchars($row['amount_paid']);
                    $payment_method = htmlspecialchars($row['payment_method']);
                    $payment_date = htmlspecialchars($row['payment_date']);
                    $date = substr($payment_date, 0, 10);

                    echo "
                    <tr>
                        <td class='text-start'>$payment_id</td>
                        <td class='text-start'>$order_id</td>
                        <td class='text-start'>$amount_paid</td>
                        <td class='text-start'>$payment_method</td>
                        <td class='text-start'>$date</td>
                        <td>
                            <a href='index.php?payment={$payment_id}' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Are you sure you want to delete this payment?\")'>
                                <i class='bi bi-trash'></i>
                            </a>
                        </td>
                    </tr>
                    ";
                }
            } else {
                echo "<tr><td colspan='6' class='text-center'>No paid orders found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</section>
