<?php

// Initialize a feedback message variable
$alert_message = '';

// Function to sanitize inputs
function sanitize_input($data)
{
    global $connection;
    $data = trim($data); // Remove extra spaces
    $data = stripslashes($data); // Remove backslashes
    $data = mysqli_real_escape_string($connection, $data); // Escape special characters
    return $data;
}

// Check if the brand ID is being edited
if (isset($_GET['edit_brand'])) {
    $brand_id = (int) $_GET['edit_brand'];  // Ensure brand_id is an integer

    // Fetch brand details from the database
    $selectBrandQuery = "SELECT * FROM brands WHERE brand_id = ?";
    $selectBrandStatement = $connection->prepare($selectBrandQuery);
    $selectBrandStatement->bind_param('i', $brand_id);  // Bind brand_id as an integer
    $selectBrandStatement->execute();
    $brand_result = $selectBrandStatement->get_result();

    if ($brand_result->num_rows > 0) {
        // Fetch the brand data
        $brand = $brand_result->fetch_assoc();
        $brand_name =  htmlspecialchars($brand['brand_name']);
        $brand_description = htmlspecialchars($brand['brand_description']);
        $brand_keywords = htmlspecialchars($brand['brand_keywords']);
    } else {
        $alert_message = "Brand not found!";
    }
    $selectBrandStatement->close();
}

// Handle form submission for updating the brand
if (isset($_POST['update_brand'])) {
    $brand_name = sanitize_input($_POST['brand_name']);
    $brand_description = sanitize_input($_POST['brand_description']);
    $brand_keywords = sanitize_input($_POST['brand_keywords']);

    // Validate required fields
    if (empty($brand_name) || empty($brand_description) || empty($brand_keywords)) {
        $alert_message = "Please fill in all fields!";
    } else {
        // Prepare SQL update query
        $updateBrandQuery = "UPDATE brands SET brand_name = ?, brand_description = ?, brand_keywords = ? WHERE brand_id = ?";
        $updateBrandStatement = $connection->prepare($updateBrandQuery);
        $updateBrandStatement->bind_param('sssi', $brand_name, $brand_description, $brand_keywords, $brand_id);

        if ($updateBrandStatement->execute()) {
            $alert_message = "Brand updated successfully!";
        } else {
            $alert_message = "Failed to update brand. Please try again.";
        }
        $updateBrandStatement->close();
    }
}

// Close the database connection
if (isset($connection) && $connection !== null) {
    $connection->close();
}
?>

<!-- HTML Form to Update Brand -->
<div class="container mt-5 w-50 m-auto">

    <!-- <?php if (!empty($alert_message)) : ?>
        <script>
            alert("<?php echo $alert_message; ?>");
        </script>
    <?php endif; ?> -->

    <!-- Display Alert Message -->
    <?php if (!empty($alert_message)) : ?>
        <div class="alert alert-info"><?= htmlspecialchars($alert_message) ?></div>
    <?php endif; ?>

    <form action="" method="post" class="p-4 border rounded shadow-sm bg-light">
        <h4 class="mb-3 text-center">Update Product Brand</h4>

        <!-- Brand Name Input Field -->
        <div class="mb-4">
            <label for="brand_name" class="form-label text-muted">Brand Name</label>
            <div class="input-group">
                <span class="input-group-text bg-secondary text-white">
                    <i class="bi bi-list-task"></i>
                </span>
                <input type="text" name="brand_name" id="brand_name" class="form-control" placeholder="Enter brand name" value="<?php echo  $brand_name ?> ">
            </div>
        </div>

        <!-- Brand Description Input Field -->
        <div class="mb-4">
            <label for="brand_description" class="form-label text-muted">Brand Description</label>
            <div class="input-group">
                <span class="input-group-text bg-secondary text-white">
                    <i class="bi bi-file-earmark-text"></i>
                </span>
                <textarea name="brand_description" id="brand_description" class="form-control" placeholder="Enter brand description" rows="3"><?php echo  $brand_description ?></textarea>
            </div>
        </div>

        <!-- Brand Keywords Input Field -->
        <div class="mb-4">
            <label for="brand_keywords" class="form-label text-muted">Brand Keywords</label>
            <div class="input-group">
                <span class="input-group-text bg-secondary text-white">
                    <i class="bi bi-search"></i>
                </span>
                <textarea name="brand_keywords" id="brand_keywords" class="form-control" placeholder="Enter brand keywords" rows="3"><?php echo  $brand_keywords ?></textarea>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="input-group mb-1">
            <input type="submit" name="update_brand" value="Update Brand" class="btn btn-info w-100" aria-label="Update Brand">
        </div>
    </form>
</div>