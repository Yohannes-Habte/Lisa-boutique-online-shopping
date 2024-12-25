<?php
// Query to fetch brands using a prepared statement
$brands_query = "SELECT * FROM brands";
$brands_result = mysqli_query($connection, $brands_query);

// Check if the query succeeded
if (!$brands_result) {
    die("Error: Unable to fetch brands. " . mysqli_error($connection)); // Improved error handling
}

// Count the number of rows fetched
$count_brands = mysqli_num_rows($brands_result);
?>

<section>
    <h3 class="mb-4 text-center text-primary">All Brands List</h3>
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Keywords</th>
                    <th scope="col">Description</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Display fetched brands
                if ($count_brands > 0) {
                    while ($row = mysqli_fetch_assoc($brands_result)) {
                        // Sanitize data to prevent XSS attacks
                        $brand_id = htmlspecialchars($row['brand_id'], ENT_QUOTES, 'UTF-8');
                        $brand_name = htmlspecialchars($row['brand_name'], ENT_QUOTES, 'UTF-8');
                        $brand_keywords = htmlspecialchars($row['brand_keywords'], ENT_QUOTES, 'UTF-8');
                        $brand_description = htmlspecialchars($row['brand_description'], ENT_QUOTES, 'UTF-8');

                        echo "
                        <tr>
                            <td class='text-start'>{$brand_id}</td>
                            <td class='text-start'>{$brand_name}</td>
                            <td class='text-start'>{$brand_keywords}</td>
                            <td class='text-start'>{$brand_description}</td>
                            <td>
                                <div class='d-flex justify-content-center gap-2'>
                                    <!-- Edit Brand -->
                                    <a href='index.php?edit_brand={$brand_id}' class='btn btn-sm btn-outline-success' title='Edit Brand'>
                                        <i class='bi bi-pencil'></i>
                                    </a>
                                    <!-- Delete Brand -->
                                    <a href='index.php?delete_brand={$brand_id}' class='btn btn-sm btn-outline-danger' title='Delete Brand' onclick='return confirm(\"Are you sure you want to delete this brand?\")'>
                                        <i class='bi bi-trash'></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        ";
                    }
                } else {
                    // Display message if no brands are found
                    echo "
                    <tr>
                        <td colspan='5' class='text-center text-secondary'>
                            No brands found.
                        </td>
                    </tr>
                    ";
                }
                ?>
            </tbody>
        </table>
    </div>
</section>