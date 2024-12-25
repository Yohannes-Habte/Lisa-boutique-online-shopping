<?php

include('../backend/connect.php');

// Function to sanitize user input
function sanitize_input($data)
{
    return htmlspecialchars(trim($data)); // Sanitize the input to prevent XSS and trim extra spaces
}

// Check if the form is submitted
if (isset($_POST['add_product_category'])) {
    // Retrieve and sanitize input values
    $category_name = sanitize_input($_POST['category_name']);
    $category_description = sanitize_input($_POST['category_description']);
    $category_keywords = sanitize_input($_POST['category_keywords']);

    // Validate the category name
    if (empty($category_name)) {
        echo "<script>alert('Category name cannot be empty');</script>"; 
        exit();
    }

    // Allow empty description and keywords (set to null if empty)
    if (empty($category_description)) {
        $category_description = null; // Allow empty description by setting it to null
    }

    if (empty($category_keywords)) {
        $category_keywords = null; // Allow empty keywords by setting it to null
    }

    // Check if the category already exists using a prepared statement
    $select_category_stmt = $connection->prepare("SELECT * FROM categories WHERE category_name = ?"); 
    $select_category_stmt->bind_param("s", $category_name); // "s" denotes the type (string) for the category name
    $select_category_stmt->execute(); // Execute the SELECT query
    $result = $select_category_stmt->get_result(); // Get the result of the query
    $number_of_rows = $result->num_rows; // Count how many rows were returned (if any)

    if ($number_of_rows > 0) {
        echo "<script>alert('$category_name already exists');</script>"; 
        $select_category_stmt->close(); 
        exit();
    } else {
        // Prepare the INSERT statement using a prepared statement
        $insert_category_stmt = $connection->prepare("INSERT INTO categories (category_name, category_description, category_keywords) VALUES (?, ?, ?)"); 
        $insert_category_stmt->bind_param("sss", $category_name, $category_description, $category_keywords); // Bind category name, description, and keywords as string parameters

        // Execute the statement and check if successful
        if ($insert_category_stmt->execute()) {
            echo "<script>alert('Category added successfully');</script>"; 
        } else {
            echo "<script>alert('Failed to add category');</script>"; 
        }

        $insert_category_stmt->close(); // Close the INSERT statement
    }

    $select_category_stmt->close(); // Close the SELECT statement for checking category existence
}

// Close the MySQL connection only if it's open
if (isset($connection) && $connection !== null) {
    $connection->close(); // Close the database connection
}
?>




<!-- HTML Form to Add Category -->
<div class="container mt-5 w-50 m-auto">
    <form action="" method="post" class="p-4 border rounded shadow-sm bg-light">
        <h4 class="mb-3 text-center">Add Product Category</h4>

        <!-- Category Name Input Field -->
        <div class="mb-4">
            <label for="category_name" class="form-label text-muted">Category Name</label>
            <div class="input-group">
                <span class="input-group-text bg-secondary text-white">
                    <i class="bi bi-list-task"></i>
                </span>
                <input type="text" name="category_name" id="category_name" class="form-control" placeholder="Enter category name" required>
            </div>
        </div>

        <!-- Category Description Input Field -->
        <div class="mb-4">
            <label for="category_description" class="form-label text-muted">Category Description</label>
            <div class="input-group">
                <span class="input-group-text bg-secondary text-white">
                    <i class="bi bi-file-earmark-text"></i>
                </span>
                <textarea name="category_description" id="category_description" class="form-control" placeholder="Enter category description" rows="3"></textarea>
            </div>
        </div>

        <!-- Category Keywords Input Field -->
        <div class="mb-4">
            <label for="category_keywords" class="form-label text-muted">Category Keywords</label>
            <div class="input-group">
                <span class="input-group-text bg-secondary text-white">
                    <i class="bi bi-search"></i>
                </span>
                <textarea name="category_keywords" id="category_keywords" class="form-control" placeholder="Enter category keywords" rows="3"></textarea>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="input-group mb-1">
            <input type="submit" name="add_product_category" value="Add Category" class="btn btn-info w-100" aria-label="Add Product Category">
        </div>
    </form>
</div>