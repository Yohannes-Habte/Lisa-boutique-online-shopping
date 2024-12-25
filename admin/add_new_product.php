<?php
include('../backend/connect.php');


function sanitize_input($data)
{
    global $connection;  // Ensure you're using the global $connection variable
    $data = trim($data);  // Remove extra spaces
    $data = stripslashes($data);  // Remove backslashes
    $data = mysqli_real_escape_string($connection, $data);  // Escape special characters
    return $data;
}

// Fetch categories from the database
$categories_query = "SELECT category_id, category_name FROM categories";
$categories_result = $connection->query($categories_query);

// Fetch brands from the database
$brands_query = "SELECT brand_id, brand_name FROM brands";
$brands_result = $connection->query($brands_query);

// Function to handle file uploads (as in your original code)
function upload_image($file, $destination_dir)
{
    $allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_file_size = 2 * 1024 * 1024; // 2MB

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [false, 'File upload error.'];
    }

    if (!in_array(mime_content_type($file['tmp_name']), $allowed_mime_types)) {
        return [false, 'Invalid file type. Only JPG, PNG, and GIF are allowed.'];
    }

    if ($file['size'] > $max_file_size) {
        return [false, 'File size exceeds 2MB limit.'];
    }

    $file_name = uniqid() . '_' . basename($file['name']);
    $target_path = $destination_dir . '/' . $file_name;

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return [true, $file_name];
    } else {
        return [false, 'Failed to upload image.'];
    }
}

$alert_message = ''; // To store alert messages

// Check if the form is submitted
if (isset($_POST['add_new_product_only'])) {
    // Retrieve and sanitize input values
    $product_name = sanitize_input($_POST['product_name']);
    $product_description = sanitize_input($_POST['product_description']);
    $product_keywords = sanitize_input($_POST['product_keywords']);
    $product_category = sanitize_input($_POST['product_category']);
    $product_brand = sanitize_input($_POST['product_brand']);
    $product_price = filter_var($_POST['product_price'], FILTER_VALIDATE_FLOAT);

    // Validate required fields
    if (empty($product_name) || empty($product_category) || empty($product_brand) || $product_price === false) {
        $alert_message = 'Please fill all required fields with valid values.';
    } else {
        // Handle file uploads (as in your original code)
        $upload_dir = 'uploads/products';
        $images = [];
        foreach (['product_image1', 'product_image2', 'product_image3'] as $image_field) {
            if (!empty($_FILES[$image_field]['name'])) {
                [$success, $result] = upload_image($_FILES[$image_field], $upload_dir);
                if ($success) {
                    $images[] = $result;
                } else {
                    $alert_message = $result;
                    break;
                }
            } else {
                $images[] = null;
            }
        }

        if (!$alert_message) {
            // Insert product into the database
            $insert_product_query = "INSERT INTO products (product_name, product_description, product_keywords, product_category, product_brand, product_price, product_image1, product_image2, product_image3) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $connection->prepare($insert_product_query);
            $stmt->bind_param(
                "sssiidsss",
                $product_name,
                $product_description,
                $product_keywords,
                $product_category,
                $product_brand,
                $product_price,
                $images[0],
                $images[1],
                $images[2]
            );

            if ($stmt->execute()) {
                $alert_message = 'Product added successfully.';
            } else {
                error_log("Database Error: " . $stmt->error);
                $alert_message = 'Failed to add product. Please try again.';
            }

            $stmt->close();
        }
    }
}

// Close the database connection
if (isset($connection) && $connection !== null) {
    $connection->close();
}
?>

<!-- HTML Form to Add Product -->
<div class="container mt-5 w-50 m-auto">
    <!-- Display Alert Message -->
    <?php if (!empty($alert_message)) : ?>
        <div class="alert alert-info"><?= htmlspecialchars($alert_message) ?></div>
    <?php endif; ?>

    <form action="" method="post" class="p-4 border rounded shadow-sm bg-light" enctype="multipart/form-data">
        <h4 class="mb-3 text-center">Add Product</h4>

        <!-- Product Name Input Field -->
        <div class="mb-4">
            <label for="product_name" class="form-label text-muted">Product Name</label>
            <div class="input-group">
                <span class="input-group-text bg-secondary text-white">
                    <i class="bi bi-box-seam"></i>
                </span>
                <input type="text" name="product_name" id="product_name" class="form-control" placeholder="Enter product name">
            </div>
        </div>

        <!-- Product Description Input Field -->
        <div class="mb-4">
            <label for="product_description" class="form-label text-muted">Product Description</label>
            <div class="input-group">
                <span class="input-group-text bg-secondary text-white">
                    <i class="bi bi-card-text"></i>
                </span>
                <input type="text" name="product_description" id="product_description" class="form-control" placeholder="Enter product description">
            </div>
        </div>

        <!-- Product Keywords Input Field -->
        <div class="mb-4">
            <label for="product_keywords" class="form-label text-muted">Product Keywords</label>
            <div class="input-group">
                <span class="input-group-text bg-secondary text-white">
                    <i class="bi bi-tags-fill"></i>
                </span>
                <input type="text" name="product_keywords" id="product_keywords" class="form-control" placeholder="Enter product keywords">
            </div>
        </div>

        <!-- Product Category Input Field -->
        <div class="mb-4">
            <label for="product_category" class="form-label text-muted">Product Category</label>
            <div class="input-group">
                <span class="input-group-text bg-secondary text-white">
                    <i class="bi bi-list-task"></i>
                </span>
                <select name="product_category" id="product_category" class="form-select">
                    <option value="">Select Category</option>
                    <?php while ($category = $categories_result->fetch_assoc()) : ?>
                        <option value="<?= $category['category_id'] ?>"><?= htmlspecialchars($category['category_name']) ?></option>
                    <?php endwhile; ?>
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
                <select name="product_brand" id="product_brand" class="form-select">
                    <option value="">Select Brand</option>
                    <?php while ($brand = $brands_result->fetch_assoc()) : ?>
                        <option value="<?= $brand['brand_id'] ?>"><?= htmlspecialchars($brand['brand_name']) ?></option>
                    <?php endwhile; ?>
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
                <input type="number" name="product_price" id="product_price" class="form-control" placeholder="Enter product price" step="0.01">
            </div>
        </div>

        <!-- Image Upload Fields -->
        <?php for ($i = 1; $i <= 3; $i++) : ?>
            <div class="mb-4">
                <label for="product_image<?= $i ?>" class="form-label text-muted">Product Image <?= $i ?></label>
                <div class="input-group">
                    <span class="input-group-text bg-secondary text-white">
                        <i class="bi bi-file-image"></i>
                    </span>
                    <input type="file" name="product_image<?= $i ?>" id="product_image<?= $i ?>" class="form-control">
                </div>
            </div>
        <?php endfor; ?>

        <!-- Submit Button -->
        <div class="input-group mb-1">
            <input type="submit" name="add_new_product_only" value="Add New Product" class="btn btn-info w-100">
        </div>
    </form>
</div>
