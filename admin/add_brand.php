<?php
// Ensure this file contains a valid $connection object
include('../backend/connect.php');

// Initialize an alert message variable
$alert_message = "";

// Function to sanitize user input
function sanitize_input($data)
{
    return htmlspecialchars(trim($data));
}

// Check if the form is submitted for adding a brand
if (isset($_POST['add_product_brand'])) {
    // Retrieve and sanitize input
    $brand_name = sanitize_input($_POST['brand_name']);
    $brand_description = sanitize_input($_POST['brand_description']);
    $brand_keywords = sanitize_input($_POST['brand_keywords']);

    // Validate input
    if (empty($brand_name)) {
        $alert_message = "Brand name cannot be empty.";
    } else {
        $brand_description = empty($brand_description) ? null : $brand_description;
        $brand_keywords = empty($brand_keywords) ? null : $brand_keywords;

        // Prevent SQL Injection by using prepared statements
        $select_brand_stmt = $connection->prepare("SELECT * FROM brands WHERE brand_name = ?");
        $select_brand_stmt->bind_param("s", $brand_name); // "s" denotes the type (string) for the brand name
        $select_brand_stmt->execute(); // Execute the SELECT query
        $result = $select_brand_stmt->get_result(); // Get the result of the query
        $number_of_rows = $result->num_rows; // Count how many rows were returned (if any)

        if ($number_of_rows > 0) {
            $alert_message = "$brand_name already exists.";
        } else {
            // Prepare the insert statement using a prepared statement
            $insert_brand_stmt = $connection->prepare("INSERT INTO brands (brand_name, brand_description, brand_keywords) VALUES (?, ?, ?)");
            $insert_brand_stmt->bind_param("sss", $brand_name, $brand_description, $brand_keywords);

            // Execute the statement and check if successful
            if ($insert_brand_stmt->execute()) {
                $alert_message = "Brand added successfully.";
            } else {
                $alert_message = "Failed to add brand: " . $insert_brand_stmt->error;
            }

            $insert_brand_stmt->close(); // Close the INSERT statement
        }

        $select_brand_stmt->close(); // Close the SELECT statement
    }
}

// Close the MySQL connection only if it's open
if (isset($connection) && $connection !== null) {
    $connection->close(); // Close the database connection
}
?>


<!-- HTML Form to Add Brand -->
<div class="container mt-5 w-50 m-auto">

    <?php if (!empty($alert_message)) : ?>
        <script>
            alert("<?php echo $alert_message; ?>");
        </script>
    <?php endif; ?>

    <form action="" method="post" class="p-4 border rounded shadow-sm bg-light">
        <h4 class="mb-3 text-center">Add Product Brand</h4>

        <!-- Brand Name Input Field -->
        <div class="mb-4">
            <label for="brand_name" class="form-label text-muted">Brand Name</label>
            <div class="input-group">
                <span class="input-group-text bg-secondary text-white">
                    <i class="bi bi-list-task"></i>
                </span>
                <input type="text" name="brand_name" id="brand_name" class="form-control" placeholder="Enter brand name" required>
            </div>
        </div>

        <!-- Brand Description Input Field -->
        <div class="mb-4">
            <label for="brand_description" class="form-label text-muted">Brand Description</label>
            <div class="input-group">
                <span class="input-group-text bg-secondary text-white">
                    <i class="bi bi-file-earmark-text"></i>
                </span>
                <textarea name="brand_description" id="brand_description" class="form-control" placeholder="Enter brand description" rows="3"></textarea>
            </div>
        </div>

        <!-- Brand Keywords Input Field -->
        <div class="mb-4">
            <label for="brand_keywords" class="form-label text-muted">Brand Keywords</label>
            <div class="input-group">
                <span class="input-group-text bg-secondary text-white">
                    <i class="bi bi-search"></i>
                </span>
                <textarea name="brand_keywords" id="brand_keywords" class="form-control" placeholder="Enter brand keywords" rows="3"></textarea>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="input-group mb-1">
            <input type="submit" name="add_product_brand" value="Add Brand" class="btn btn-info w-100" aria-label="Add Brand Category">
        </div>
    </form>
</div>