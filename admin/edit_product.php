<?php

// Initialize a variable for feedback messages
$feedback_message = '';

// Function to sanitize inputs
function sanitize_input($data)
{
    global $connection;  // Ensure you're using the global $connection variable
    $data = trim($data);  // Remove extra spaces
    $data = stripslashes($data);  // Remove backslashes
    $data = mysqli_real_escape_string($connection, $data);  // Escape special characters
    return $data;
}

// Fetch product data when 'edit_product' is set
if (isset($_GET['edit_product'])) {
    $product_id = intval($_GET['edit_product']);

    // Fetch the product from the database
    $fetch_product_query = "SELECT * FROM products WHERE product_id = ?";
    $product_statement = $connection->prepare($fetch_product_query);
    $product_statement->bind_param("i", $product_id);
    $product_statement->execute();
    $product_result = $product_statement->get_result();
    $product_data = $product_result->fetch_assoc();

    // Fetch the category and brand for the product
    $category_id = $product_data['product_category'];
    $brand_id = $product_data['product_brand'];

    // Fetch category name
    $fetch_category_query = "SELECT category_name FROM categories WHERE category_id = ?";
    $category_statement = $connection->prepare($fetch_category_query);
    $category_statement->bind_param("i", $category_id);
    $category_statement->execute();
    $category_result = $category_statement->get_result();
    $category_data = $category_result->fetch_assoc();

    // Fetch brand name
    $fetch_brand_query = "SELECT brand_name FROM brands WHERE brand_id = ?";
    $brand_statement = $connection->prepare($fetch_brand_query);
    $brand_statement->bind_param("i", $brand_id);
    $brand_statement->execute();
    $brand_result = $brand_statement->get_result();
    $brand_data = $brand_result->fetch_assoc();

    $product_statement->close();
    $category_statement->close();
    $brand_statement->close();
}

// Update product logic
if (isset($_POST['update_product'])) {
    $product_id = intval($_POST['product_id']);

    // Sanitize all inputs using the sanitize_input function
    $product_name = sanitize_input($_POST['product_name']);
    $product_description = sanitize_input($_POST['product_description']);
    $product_keywords = sanitize_input($_POST['product_keywords']);
    $category_id = intval($_POST['product_category']);
    $brand_id = intval($_POST['product_brand']);
    $product_price = floatval($_POST['product_price']);

    // Handle images
    $product_image1 = $_FILES['product_image1']['name'] ? $_FILES['product_image1']['name'] : $product_data['product_image1'];
    $product_image2 = $_FILES['product_image2']['name'] ? $_FILES['product_image2']['name'] : $product_data['product_image2'];
    $product_image3 = $_FILES['product_image3']['name'] ? $_FILES['product_image3']['name'] : $product_data['product_image3'];

    // Move the uploaded files if any
    if ($_FILES['product_image1']['tmp_name']) {
        move_uploaded_file($_FILES['product_image1']['tmp_name'], "./uploads/products/$product_image1");
    }
    if ($_FILES['product_image2']['tmp_name']) {
        move_uploaded_file($_FILES['product_image2']['tmp_name'], "./uploads/products/$product_image2");
    }
    if ($_FILES['product_image3']['tmp_name']) {
        move_uploaded_file($_FILES['product_image3']['tmp_name'], "./uploads/products/$product_image3");
    }

    // Update product query using prepared statements
    $update_product_query = "
        UPDATE products SET
        product_name = ?, 
        product_description = ?, 
        product_keywords = ?, 
        product_category = ?,
        product_brand = ?, 
        product_price = ?, 
        product_image1 = ?, 
        product_image2 = ?, 
        product_image3 = ?
        WHERE product_id = ?
    ";

    $update_statement = $connection->prepare($update_product_query);
    $update_statement->bind_param(
        "sssiidssss",
        $product_name,
        $product_description,
        $product_keywords,
        $category_id,
        $brand_id,
        $product_price,
        $product_image1,
        $product_image2,
        $product_image3,
        $product_id
    );

    if ($update_statement->execute()) {
        $feedback_message = "<div class='alert alert-success'>Product updated successfully!</div>";
    } else {
        $feedback_message = "<div class='alert alert-danger'>Error updating product: " . mysqli_error($connection) . "</div>";
    }
    $update_statement->close();
}
?>

<!-- HTML Form to Edit Product -->
<div class="container mt-5 w-50 m-auto">
    <h3 class="text-center text-primary mb-4">Edit Product</h3>

    <!-- Display feedback message -->
    <?php echo $feedback_message; ?>

    <form action="" method="post" class="p-4 border rounded shadow-sm bg-light" enctype="multipart/form-data">
        <!-- Hidden input to store product ID -->
        <input type="hidden" name="product_id" value="<?php echo $product_data['product_id']; ?>">

        <!-- Product Name Input Field -->
        <div class="mb-4">
            <label for="product_name" class="form-label text-muted">Product Name</label>
            <div class="input-group">
                <span class="input-group-text bg-secondary text-white">
                    <i class="bi bi-box-seam"></i>
                </span>
                <input type="text" name="product_name" id="product_name" class="form-control" value="<?php echo htmlspecialchars($product_data['product_name']); ?>" required>
            </div>
        </div>

        <!-- Product Description Input Field -->
        <div class="mb-4">
            <label for="product_description" class="form-label text-muted">Product Description</label>
            <div class="input-group">
                <span class="input-group-text bg-secondary text-white">
                    <i class="bi bi-card-text"></i>
                </span>
                <input type="text" name="product_description" id="product_description" class="form-control" value="<?php echo htmlspecialchars($product_data['product_description']); ?>" required>
            </div>
        </div>

        <!-- Product Keywords Input Field -->
        <div class="mb-4">
            <label for="product_keywords" class="form-label text-muted">Product Keywords</label>
            <div class="input-group">
                <span class="input-group-text bg-secondary text-white">
                    <i class="bi bi-tags-fill"></i>
                </span>
                <input type="text" name="product_keywords" id="product_keywords" class="form-control" value="<?php echo htmlspecialchars($product_data['product_keywords']); ?>" required>
            </div>
        </div>

        <!-- Product Category Input Field -->
        <div class="mb-4">
            <label for="product_category" class="form-label text-muted">Product Category</label>
            <div class="input-group">
                <span class="input-group-text bg-secondary text-white">
                    <i class="bi bi-list-task"></i>
                </span>
                <select name="product_category" id="product_category" class="form-select" required>
                    <option value="<?php echo $category_data['category_name']; ?>" selected><?php echo $category_data['category_name']; ?></option>
                    <?php
                    $get_all_categories_query = "SELECT * FROM categories";
                    $all_categories_result = mysqli_query($connection, $get_all_categories_query);
                    while ($category_row = mysqli_fetch_assoc($all_categories_result)) {
                        $category_id = $category_row['category_id'];
                        $category_name = $category_row['category_name'];
                        $selected = $category_id == $product_data['product_category'] ? "selected" : "";
                        echo "<option value='$category_id' $selected>$category_name</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <!-- Product Brand Input Field -->
        <div class="mb-4">
            <label for="product_brand" class="form-label text-muted">Product Brand</label>
            <div class="input-group">
                <span class="input-group-text bg-secondary text-white">
                    <i class="bi bi-shop"></i>
                </span>
                <select name="product_brand" id="product_brand" class="form-select" required>
                    <option value="<?php echo $brand_data['brand_name']; ?>" selected><?php echo $brand_data['brand_name']; ?></option>
                    <?php
                    $get_all_brands_query = "SELECT * FROM brands";
                    $all_brands_result = mysqli_query($connection, $get_all_brands_query);
                    while ($brand_row = mysqli_fetch_assoc($all_brands_result)) {
                        $brand_id = $brand_row['brand_id'];
                        $brand_name = $brand_row['brand_name'];
                        $selected = $brand_id == $product_data['product_brand'] ? "selected" : "";
                        echo "<option value='$brand_id' $selected>$brand_name</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <!-- Product Price Input Field -->
        <div class="mb-4">
            <label for="product_price" class="form-label text-muted">Product Price</label>
            <div class="input-group">
                <span class="input-group-text bg-secondary text-white">
                    <i class="bi bi-currency-exchange"></i>
                </span>
                <input type="number" name="product_price" id="product_price" class="form-control" value="<?php echo $product_data['product_price']; ?>" required step="0.01">
            </div>
        </div>

        <!-- Image Upload Fields -->
        <div class="mb-4">
            <label for="product_image1" class="form-label text-muted">Product Image 1</label>
            <input type="file" name="product_image1" id="product_image1" class="form-control">
            <?php if ($product_data['product_image1']) : ?>
                <img src="uploads/products/<?php echo htmlspecialchars($product_data['product_image1']); ?>" alt="Product Image 1" class="img-thumbnail img-fluid" width="150">
            <?php endif; ?>
        </div>

        <div class="mb-4">
            <label for="product_image2" class="form-label text-muted">Product Image 2</label>
            <input type="file" name="product_image2" id="product_image2" class="form-control">
            <?php if ($product_data['product_image2']) : ?>
                <img src="uploads/products/<?php echo htmlspecialchars($product_data['product_image2']); ?>" alt="Product Image 2" class="img-thumbnail img-fluid" width="150">
            <?php endif; ?>
        </div>

        <div class="mb-4">
            <label for="product_image3" class="form-label text-muted">Product Image 3</label>
            <input type="file" name="product_image3" id="product_image3" class="form-control">
            <?php if ($product_data['product_image3']) : ?>
                <img src="uploads/products/<?php echo htmlspecialchars($product_data['product_image3']); ?>" alt="Product Image 3" class="img-thumbnail img-fluid" width="150">
            <?php endif; ?>
        </div>

        <!-- Submit Button -->
        <div class="input-group mb-1">
            <input type="submit" name="update_product" value="Update Product" class="btn btn-info w-100">
        </div>
    </form>
</div>