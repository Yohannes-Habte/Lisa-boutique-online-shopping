<section class="container mt-5">
    <h3 class="mb-4 text-center text-primary">All Products List</h3>
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th scope="col">Product ID</th>
                    <th scope="col">Product Name</th>
                    <th scope="col">Product Image</th>
                    <th scope="col">Product Price</th>
                    <th scope="col">Total Sold</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>

                <?php
                // Secure connection using prepared statements
                $query_products = "SELECT product_id, product_name, product_image1, product_price FROM products";
                $stmt = $connection->prepare($query_products);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    // Sanitize variables
                    $product_id = htmlspecialchars($row['product_id']);
                    $product_name = htmlspecialchars($row['product_name']);
                    $product_image1 = htmlspecialchars($row['product_image1']);
                    $product_price = htmlspecialchars($row['product_price']);

                    // Count total products sold using prepared statements
                    $query_total_products = "SELECT COUNT(*) AS total FROM orders_pending WHERE product_id = ?";
                    $stmt_sold = $connection->prepare($query_total_products);
                    $stmt_sold->bind_param("i", $product_id);
                    $stmt_sold->execute();
                    $result_sold = $stmt_sold->get_result();
                    $total_products = $result_sold->fetch_assoc()['total'];

                    echo "
                        <tr>
                            <td>{$product_id}</td>
                            <td>{$product_name}</td>
                            <td>
                                <img src='uploads/products/{$product_image1}' alt='Image of {$product_name}' class='img-thumbnail' style='width: 75px; height: 75px;'>
                            </td>
                            <td>\${$product_price}</td>
                            <td>{$total_products}</td>
                            <td>
                                <div class='d-flex gap-2'>
                                    <a class='btn btn-sm btn-outline-success' href='index.php?edit_product={$product_id}' aria-label='Edit {$product_name}'>
                                        <i class='bi bi-pencil'></i>
                                    </a>
                                    <a class='btn btn-sm btn-outline-danger' href='index.php?delete_product={$product_id}' aria-label='Delete {$product_name}' onclick='return confirm(\"Are you sure you want to delete this product?\")'>
                                        <i class='bi bi-trash'></i> 
                                    </a>
                                </div>
                            </td>
                        </tr>
                    ";
                }

                // Close statements
                $stmt->close();
                $stmt_sold->close();
                ?>

            </tbody>
        </table>
    </div>
</section>