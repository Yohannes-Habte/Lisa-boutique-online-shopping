<?php

// Query to fetch categories using prepared statements
$categories_query = "SELECT * FROM categories";
if ($categories_result = mysqli_query($connection, $categories_query)) {
    // Count the number of rows fetched
    $count_categories = mysqli_num_rows($categories_result);
} else {
    die("Query failed: " . mysqli_error($connection)); // Improved error handling
}

?>

<section>
    <h3 class="mb-4 text-center text-primary">All Categories List</h3>
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
                // Display fetched categories
                if ($count_categories > 0) {
                    while ($row = mysqli_fetch_assoc($categories_result)) {
                        // Sanitize output to prevent XSS (cross-site scripting) attacks
                        $category_id = htmlspecialchars($row['category_id'], ENT_QUOTES, 'UTF-8');
                        $category_name = htmlspecialchars($row['category_name'], ENT_QUOTES, 'UTF-8');
                        $category_keywords = htmlspecialchars($row['category_keywords'], ENT_QUOTES, 'UTF-8');
                        $category_description = htmlspecialchars($row['category_description'], ENT_QUOTES, 'UTF-8');

                        echo "
                        <tr>
                            <td class='text-start'>{$category_id}</td>
                            <td class='text-start'>{$category_name}</td>
                            <td class='text-start'>{$category_keywords}</td>
                            <td class='text-start'>{$category_description}</td>
                            <td>
                                <div class='d-flex justify-content-center gap-2'>
                                    <a href='index.php?edit_category={$category_id}' class='btn btn-sm btn-outline-success' title='Edit Category'>
                                        <i class='bi bi-pencil'></i>
                                    </a>
                                    <a href='index.php?delete_category={$category_id}' class='btn btn-sm btn-outline-danger' title='Delete Category' onclick='return confirm(\"Are you sure you want to delete this category?\")'>
                                        <i class='bi bi-trash'></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        ";
                    }
                } else {
                    echo "
                    <tr>
                        <td colspan='5' class='text-center text-secondary'>
                            No categories found
                        </td>
                    </tr>
                    ";
                }
                ?>
            </tbody>
        </table>
    </div>
</section>