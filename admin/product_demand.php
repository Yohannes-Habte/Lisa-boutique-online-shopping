<?php

// Securely fetch orders using prepared statements
$query = "SELECT * FROM orders_pending";
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
                <th>Product ID</th>
                <th>Invoice Number</th>
                <th>Quantity</th>
                <th>Product Image</th>
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
                    $product_id = htmlspecialchars($row['product_id']);
                    $invoice_number = htmlspecialchars($row['invoice_number']);
                    $quantity = htmlspecialchars($row['quantity']);
                    $order_status = htmlspecialchars($row['order_status']);
                    $created_at = htmlspecialchars($row['created_at']);
                    $date = substr($created_at, 0, 10);

                    // Fetch product details
                    $get_product_query = "SELECT * FROM products WHERE product_id = ?";
                    $stmt_product = $connection->prepare($get_product_query);
                    $stmt_product->bind_param("i", $product_id);
                    $stmt_product->execute();
                    $result_product = $stmt_product->get_result();
                    $product = $result_product->fetch_assoc();

                    if (!$product) {
                        // Skip rendering this order if the product is not found
                        continue;
                    }

                    $product_image = htmlspecialchars($product['product_image1']);
                    $product_name = htmlspecialchars($product['product_name']);

                    echo "
                    <tr>
                        <td class='text-start'>$order_id</td>
                        <td class='text-start'>$user_id</td>
                        <td class='text-start'>$product_id</td>
                        <td class='text-start'>$invoice_number</td>
                        <td class='text-start'>$quantity</td>
                        <td>
                            <img src='uploads/products/$product_image' 
                                alt='$product_name' 
                                class='img-thumbnail rounded shadow-sm border border-secondary' 
                                style='width: 75px; height: 75px; object-fit: cover;'>  
                        </td>
                        <td class='text-start'>$order_status</td>
                        <td class='text-start'>$date</td>
                        <td>
                            <a href='index.php?product_demand={$order_id}' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Are you sure you want to delete this order?\")'>
                                <i class='bi bi-trash'></i>
                            </a>
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
