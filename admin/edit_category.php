<?php
// Initialize a feedback message variable
$alert_message = '';

// Function to sanitize inputs
function sanitize_input($data)
{
    global $connection;
    $data = trim($data);
    $data = stripslashes($data);
    $data = mysqli_real_escape_string($connection, $data);
    return $data;
}

// Check if category is being edited
if (isset($_GET['edit_category'])) {
    $category_id = (int) $_GET['edit_category'];

    // Fetch category details from the database
    $selectCategoryQuery = "SELECT * FROM categories WHERE category_id = ?";
    $selectCategoryStatement = $connection->prepare($selectCategoryQuery);
    $selectCategoryStatement->bind_param('i', $category_id);  // Bind category_id as an integer
    $selectCategoryStatement->execute();
    $category_result = $selectCategoryStatement->get_result();

    if ($category_result->num_rows > 0) {

        $category = $category_result->fetch_assoc();
    } else {
        $alert_message = "Category not found!";
    }
    $selectCategoryStatement->close();
}

// Handle form submission for updating category
if (isset($_POST['update_category'])) {
    $category_name = sanitize_input($_POST['category_name']);
    $category_description = sanitize_input($_POST['category_description']);
    $category_keywords = sanitize_input($_POST['category_keywords']);


    // Prepare SQL update query
    $updateCategoryQuery = "UPDATE categories SET category_name = ?, category_description = ?, category_keywords = ? WHERE category_id = ?";
    $updateCategoryStatement = $connection->prepare($updateCategoryQuery);
    $updateCategoryStatement->bind_param('sssi', $category_name, $category_description, $category_keywords, $category_id);

    if ($updateCategoryStatement->execute()) {
        $alert_message = "Category updated successfully!";
    } else {
        $alert_message = "Failed to update category. Please try again.";
    }

    $updateCategoryStatement->close();
}

// Close the database connection
if (isset($connection) && $connection !== null) {
    $connection->close();
}
?>

<section class="container mt-5 w-50 m-auto">
    <h4 class="mb-3 text-center">Update Category</h4>

    <!-- Display Alert Message -->
    <?php if (!empty($alert_message)) : ?>
        <div class="alert alert-info"><?= htmlspecialchars($alert_message) ?></div>
    <?php endif; ?>

    <!-- Category Update Form -->
    <form action="" method="post" class="p-4 border rounded shadow-sm bg-light">

        <!-- Category Name Input Field -->
        <div class="mb-4">
            <label for="category_name" class="form-label text-muted">Category Name</label>
            <div class="input-group">
                <span class="input-group-text bg-secondary text-white">
                    <i class="bi bi-list-task"></i>
                </span>
                <input type="text" name="category_name" id="category_name" class="form-control" placeholder="Enter category name" value="<?= htmlspecialchars($category['category_name'] ?? '') ?>">
            </div>
        </div>

        <!-- Category Description Input Field -->
        <div class="mb-4">
            <label for="category_description" class="form-label text-muted">Category Description</label>
            <div class="input-group">
                <span class="input-group-text bg-secondary text-white">
                    <i class="bi bi-file-earmark-text"></i>
                </span>
                <textarea name="category_description" id="category_description" class="form-control" placeholder="Enter category description" rows="3"><?= htmlspecialchars($category['category_description'] ?? '') ?></textarea>
            </div>
        </div>

        <!-- Category Keywords Input Field -->
        <div class="mb-4">
            <label for="category_keywords" class="form-label text-muted">Category Keywords</label>
            <div class="input-group">
                <span class="input-group-text bg-secondary text-white">
                    <i class="bi bi-search"></i>
                </span>
                <textarea name="category_keywords" id="category_keywords" class="form-control" placeholder="Enter category keywords" rows="3"><?= htmlspecialchars($category['category_keywords'] ?? '') ?></textarea>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="input-group mb-1">
            <input type="submit" name="update_category" value="Update Category" class="btn btn-info w-100" aria-label="Update Product Category">
        </div>
    </form>
</section>