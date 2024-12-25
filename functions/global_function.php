
<?php

// Include database connection
// Relative path
// include('../backend/connect.php');

// A more reliable approach is to use __DIR__ to get the directory of the current script and construct the path based on that. This avoids issues with relative paths
include_once(__DIR__ . '/../backend/connect.php');



/**
 * Rules of fetching data from the database:
 * Fetch products from the database
        $query_selected_products = "SELECT * FROM products";

 * Order the results by product_name,
        $query_selected_products = "SELECT * FROM products ORDER BY product_name";

 * Order the results by product_name in descending order
        $query_selected_products = "SELECT * FROM products ORDER BY product_name DESC";

 * Order the products by random order
        $query_selected_products = "SELECT * FROM products ORDER BY RAND()";
                    
 * Limit the number of products to display to 9 products
        $query_selected_products = "SELECT * FROM products ORDER BY RAND() LIMIT 9";
                       

 */


// ==================================================================================================================
// Get All Products
// ==================================================================================================================
function get_products()
{
    // Access the $connection variable from the global scope
    global $connection;

    // Condition to check if isset or not for the category and/or brand
    if (!isset($_GET['category']) && !isset($_GET['brand'])) {

        // Secure query to select products
        $query_selected_products = "SELECT * FROM products ORDER BY RAND()";

        // Execute the query
        $result_selected_products = mysqli_query($connection, $query_selected_products);

        // Check if products are available
        if ($result_selected_products && mysqli_num_rows($result_selected_products) > 0) {
            while ($product = mysqli_fetch_assoc($result_selected_products)) {
                // Validate and sanitize product data
                $product_id = intval($product['product_id']);
                $product_name = htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8');
                $product_description = htmlspecialchars($product['product_description'], ENT_QUOTES, 'UTF-8');
                $product_image1 = htmlspecialchars($product['product_image1'], ENT_QUOTES, 'UTF-8');
                $product_price = number_format(floatval($product['product_price']), 2, '.', ',');


                // Output product card
                echo "
                    <div class='col-md-4'>
                        <div class='card h-100 shadow-sm'>
                            <img src='admin/uploads/products/$product_image1' class='card-img-top' alt='$product_name'>
                            <div class='card-body'>
                                <h5 class='card-title'>$product_name</h5>
                                <p class='card-text text-truncate'>$product_description</p>
                                <p class='card-text'><strong>Price:</strong> $ $product_price</p>
                                <a href='index.php?add_selected_product_to_cart=$product_id' class='btn btn-info btn-sm'>Add to Cart</a>
                                <a href='product_detail.php?product=$product_id' class='btn btn-secondary btn-sm'>View More</a>
                            </div>
                        </div>
                    </div>";
            }
        } else {
            // Display a warning if no products are available
            echo '<div class="alert alert-warning" role="alert">No products available at the moment.</div>';
        }
    }
}




// ==================================================================================================================
// Get Products by applying limit
// ==================================================================================================================
function get_limited_products()
{
    // Access the $connection variable from the global scope
    global $connection;

    // Check if category and brand filters are not set
    if (!isset($_GET['category']) && !isset($_GET['brand'])) {

        // Query to fetch a limited number of products, ordered by product name in descending order
        $query_selected_products = "
            SELECT product_id, product_name, product_description, product_image1, product_price, product_category, product_brand 
            FROM products 
            ORDER BY product_name DESC 
            LIMIT 9";

        // Execute the query
        $result_selected_products = mysqli_query($connection, $query_selected_products);

        // Check if the query executed successfully and returns rows
        if ($result_selected_products && mysqli_num_rows($result_selected_products) > 0) {
            // Iterate through the products and display them
            while ($product = mysqli_fetch_assoc($result_selected_products)) {
                // Sanitize and validate product data
                $product_id = (int) $product['product_id'];
                $product_name = htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8');
                $product_description = htmlspecialchars($product['product_description'], ENT_QUOTES, 'UTF-8');
                $product_image1 = htmlspecialchars($product['product_image1'], ENT_QUOTES, 'UTF-8');
                $product_price = number_format((float) $product['product_price'], 2, '.', ',');


                // Output a responsive product card
                echo "
                    <div class='col-md-4 mb-4'>
                        <div class='card h-100 shadow-sm'>
                            <img 
                                src='./admin/uploads/products/$product_image1' 
                                class='card-img-top' 
                                alt='" . htmlspecialchars($product_name, ENT_QUOTES, 'UTF-8') . "' 
                            />
                            <div class='card-body'>
                                <h5 class='card-title text-truncate'>$product_name</h5>
                                <p class='card-text text-truncate' title='$product_description'>$product_description</p>
                                <p class='card-text'>
                                    <strong>Price:</strong> $ $product_price
                                </p>
                                <div class='d-flex justify-content-between'>
                                    <a 
                                        href='index.php?add_selected_product_to_cart=$product_id' 
                                        class='btn btn-info btn-sm'
                                    >
                                        Add to Cart
                                    </a>
                                    <a 
                                        href='product_detail.php?product=$product_id' 
                                        class='btn btn-secondary btn-sm'
                                    >
                                        View More
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>";
            }
        } else {
            // Display a user-friendly message if no products are available
            echo '<div class="alert alert-warning text-center" role="alert">
                    No products are available at the moment. Please check back later.
                  </div>';
        }
    }
}




// ==================================================================================================================
// Get Products by categories
// ==================================================================================================================

function get_products_by_category()
{
    // Access the $connection variable from the global scope
    global $connection;

    // Check if a category is set in the URL
    if (isset($_GET['category'])) {

        // Sanitize and validate the category ID
        $category_id = intval($_GET['category']);
        if ($category_id <= 0) {
            echo '<div class="alert alert-warning" role="alert">Invalid category selected.</div>';
            return;
        }

        // Secure query to fetch products by category
        $query_selected_products_by_category = "SELECT * FROM products WHERE product_category = $category_id";
        $result_selected_products_by_category = mysqli_query($connection, $query_selected_products_by_category);

        // Check if products exist for the given category
        if ($result_selected_products_by_category && mysqli_num_rows($result_selected_products_by_category) > 0) {
            while ($product = mysqli_fetch_assoc($result_selected_products_by_category)) {
                // Validate and sanitize product data
                $product_id = intval($product['product_id']);
                $product_name = htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8');
                $product_description = htmlspecialchars($product['product_description'], ENT_QUOTES, 'UTF-8');
                $product_image1 = htmlspecialchars($product['product_image1'], ENT_QUOTES, 'UTF-8');
                $product_price = number_format(floatval($product['product_price']), 2, '.', ',');
                $category_id = intval($product['product_category']);
                $brand_id = intval($product['product_brand']);

                // Output product card
                echo "
                <div class='col-md-4'>
                    <div class='card h-100 shadow-sm'>
                        <img src='./admin/uploads/products/$product_image1' class='card-img-top' alt='$product_name'>
                        <div class='card-body'>
                            <h5 class='card-title'>$product_name</h5>
                            <p class='card-text text-truncate'>$product_description</p>
                            <p class='card-text'><strong>Price:</strong> $ $product_price</p>
                            <a href='index.php?add_selected_product_to_cart=$product_id' class='btn btn-info btn-sm'>Add to Cart</a>
                            <a href='product_detail.php?product=$product_id' class='btn btn-secondary btn-sm'>View More</a>
                        </div>
                    </div>
                </div>";
            }
        } else {
            // Display a message if no products are available for the category
            echo '<div class="alert alert-warning" role="alert">No products available in this category.</div>';
        }
    }
}



// ==================================================================================================================
// Get Products by brands
// ==================================================================================================================

function get_products_by_brand()
{
    // Access the $connection variable from the global scope
    global $connection;

    // Check if a brand is set in the URL
    if (isset($_GET['brand'])) {

        // Sanitize and validate the brand ID
        $brand_id = intval($_GET['brand']);
        if ($brand_id <= 0) {
            echo '<div class="alert alert-warning" role="alert">Invalid brand selected.</div>';
            return;
        }

        // Secure query to fetch products by brand
        $query_selected_products_by_brand = "SELECT * FROM products WHERE product_brand = $brand_id";
        $result_selected_products_by_brand = mysqli_query($connection, $query_selected_products_by_brand);

        // Check if products exist for the given brand
        if ($result_selected_products_by_brand && mysqli_num_rows($result_selected_products_by_brand) > 0) {
            while ($product = mysqli_fetch_assoc($result_selected_products_by_brand)) {
                // Validate and sanitize product data
                $product_id = intval($product['product_id']);
                $product_name = htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8');
                $product_description = htmlspecialchars($product['product_description'], ENT_QUOTES, 'UTF-8');
                $product_image1 = htmlspecialchars($product['product_image1'], ENT_QUOTES, 'UTF-8');
                $product_price = number_format(floatval($product['product_price']), 2, '.', ',');
                $category_id = intval($product['product_category']);
                $brand_id = intval($product['product_brand']);

                // Output product card
                echo "
                <div class='col-md-4'>
                    <div class='card h-100 shadow-sm'>
                        <img src='./admin/uploads/products/$product_image1' class='card-img-top' alt='$product_name'>
                        <div class='card-body'>
                            <h5 class='card-title'>$product_name</h5>
                            <p class='card-text text-truncate'>$product_description</p>
                            <p class='card-text'><strong>Price:</strong> $ $product_price</p>
                            <a href='index.php?add_selected_product_to_cart=$product_id' class='btn btn-info btn-sm'>Add to Cart</a>
                            <a href='product_detail.php?product=$product_id' class='btn btn-secondary btn-sm'>View More</a>
                        </div>
                    </div>
                </div>";
            }
        } else {
            // Display a message if no products are available for the brand
            echo '<div class="alert alert-warning" role="alert">No products available for this brand at the moment.</div>';
        }
    }
}


// ==================================================================================================================
// Get Products by search using product keywords, category keywords, and brand keywords
// ==================================================================================================================

function get_products_by_product_keywords()
{
    // Access the $connection variable from the global scope
    global $connection;

    // Check if a search term has been entered
    if (isset($_GET['searched_product'])) {
        // Sanitize and validate the search term
        $search_item_value = isset($_GET['search_item']) ? trim($_GET['search_item']) : '';

        // Check if the search term is empty
        if (empty($search_item_value)) {
            echo '<div class="alert alert-warning" role="alert">Please enter a search term to find products.</div>';
            return;
        }

        // Prepare the SQL query to prevent SQL injection
        if ($search_stmt = $connection->prepare("SELECT * FROM products WHERE product_keywords LIKE ?")) {
            // Add '%' wildcards for partial matching
            $search_param = '%' . $search_item_value . '%';
            $search_stmt->bind_param('s', $search_param);
            $search_stmt->execute();
            $result_search_query_products = $search_stmt->get_result();

            // Check if any products match the search criteria
            if ($result_search_query_products && $result_search_query_products->num_rows > 0) {
                // Fetch and display each matching product
                while ($product = $result_search_query_products->fetch_assoc()) {
                    // Sanitize the product data
                    $product_id = intval($product['product_id']);
                    $product_name = htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8');
                    $product_description = htmlspecialchars($product['product_description'], ENT_QUOTES, 'UTF-8');
                    $product_image1 = htmlspecialchars($product['product_image1'], ENT_QUOTES, 'UTF-8');
                    $product_price = number_format(floatval($product['product_price']), 2, '.', ',');


                    // Output the product card
                    echo "
                    <div class='col-md-4'>
                        <div class='card h-100 shadow-sm'>
                            <img src='./admin/uploads/products/$product_image1' class='card-img-top' alt='$product_name'>
                            <div class='card-body'>
                                <h5 class='card-title'>$product_name</h5>
                                <p class='card-text text-truncate'>$product_description</p>
                                <p class='card-text'><strong>Price:</strong> $$product_price</p>
                                <a href='index.php?add_selected_product_to_cart=$product_id' class='btn btn-info btn-sm'>Add to Cart</a>
                                <a href='product_detail.php?product=$product_id' class='btn btn-secondary btn-sm'>View More</a>
                            </div>
                        </div>
                    </div>";
                }
            } else {
                // Inform the user if no products are found
                echo '<div class="alert alert-warning" role="alert">No products found matching your search criteria.</div>';
            }

            // Close the prepared statement
            $search_stmt->close();
        } else {
            // If the SQL query preparation fails
            echo '<div class="alert alert-danger" role="alert">An error occurred while processing your request. Please try again later.</div>';
        }
    } else {
        // Inform the user if no search term was provided
        echo '<div class="alert alert-warning" role="alert">No search term provided. Please enter a search term to find products.</div>';
    }
}

/**
 * Search Products by Keywords of Products, Categories, and Brands
 * To search for products using a combination of product keywords, category keywords, and brand keywords, we can enhance your existing search logic by joining the relevant tables (products, categories, brands) and updating the SQL query. 
 */

function get_products_searched_by_product_keywords_brand_keywords_and_category_keywords()
{
    // Access the $connection variable from the global scope
    global $connection;

    // Check if a search term has been entered
    if (isset($_GET['searched_product'])) {
        // Sanitize and validate the search term
        $search_item_value = isset($_GET['search_item']) ? trim($_GET['search_item']) : '';

        // Check if the search term is empty
        if (empty($search_item_value)) {
            echo '<div class="alert alert-warning" role="alert">Please enter a search term to find products.</div>';
            return;
        }

        // Prepare the SQL query to include product, category, and brand keywords
        $query = "
            SELECT 
                p.product_id, 
                p.product_name, 
                p.product_description, 
                p.product_image1, 
                p.product_price, 
                c.category_name, 
                b.brand_name
            FROM products p
            LEFT JOIN categories c ON p.product_category = c.category_id
            LEFT JOIN brands b ON p.product_brand = b.brand_id
            WHERE 
                p.product_keywords LIKE ? OR 
                c.category_keywords LIKE ? OR 
                b.brand_keywords LIKE ?
        ";

        // Prepare the statement to prevent SQL injection
        if ($search_stmt = $connection->prepare($query)) {
            // Add '%' wildcards for partial matching
            $search_param = '%' . $search_item_value . '%';
            $search_stmt->bind_param('sss', $search_param, $search_param, $search_param);
            $search_stmt->execute();
            $result_search_query_products = $search_stmt->get_result();

            // Check if any products match the search criteria
            if ($result_search_query_products && $result_search_query_products->num_rows > 0) {
                // Fetch and display each matching product
                while ($product = $result_search_query_products->fetch_assoc()) {
                    // Sanitize the product data
                    $product_id = intval($product['product_id']);
                    $product_name = htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8');
                    $product_description = htmlspecialchars($product['product_description'], ENT_QUOTES, 'UTF-8');
                    $product_image1 = htmlspecialchars($product['product_image1'], ENT_QUOTES, 'UTF-8');
                    $product_price = number_format(floatval($product['product_price']), 2, '.', ',');
                    $category_name = htmlspecialchars($product['category_name'], ENT_QUOTES, 'UTF-8');
                    $brand_name = htmlspecialchars($product['brand_name'], ENT_QUOTES, 'UTF-8');

                    // Output the product card
                    echo "
                    <div class='col-md-4'>
                        <div class='card h-100 shadow-sm'>
                            <img src='./admin/uploads/products/$product_image1' class='card-img-top' alt='$product_name'>
                            <div class='card-body'>
                                <h5 class='card-title'>$product_name</h5>
                                <p class='card-text text-truncate'>$product_description</p>
                                <p class='card-text'><strong>Price:</strong> $$product_price</p>
                                <p class='card-text'><strong>Category:</strong> $category_name</p>
                                <p class='card-text'><strong>Brand:</strong> $brand_name</p>
                                <a href='index.php?add_selected_product_to_cart=$product_id' class='btn btn-info btn-sm'>Add to Cart</a>
                                <a href='product_detail.php?product=$product_id' class='btn btn-secondary btn-sm'>View More</a>
                            </div>
                        </div>
                    </div>";
                }
            } else {
                // Inform the user if no products are found
                echo '<div class="alert alert-warning" role="alert">No products found matching your search criteria.</div>';
            }

            // Close the prepared statement
            $search_stmt->close();
        } else {
            // If the SQL query preparation fails
            echo '<div class="alert alert-danger" role="alert">An error occurred while processing your request. Please try again later.</div>';
        }
    } else {
        // Inform the user if no search term was provided
        echo '<div class="alert alert-warning" role="alert">No search term provided. Please enter a search term to find products.</div>';
    }
}




// ==================================================================================================================
// Get Single Products details
// ==================================================================================================================

function get_single_product_details()
{
    // Access the $connection variable from the global scope
    global $connection;

    // Check if the product ID is provided in the URL
    if (isset($_GET['product'])) {

        // Ensure the product ID is sanitized and validated as an integer
        $product_id = intval($_GET['product']);

        // Check for valid product ID
        if ($product_id <= 0) {
            echo '<div class="alert alert-danger" role="alert">Invalid product ID.</div>';
            return;
        }

        // Prepare a secure SQL query using prepared statements to fetch product details
        if ($single_product_details_stmt = $connection->prepare("SELECT * FROM products WHERE product_id = ?")) {
            // Bind the product ID parameter to the prepared statement
            $single_product_details_stmt->bind_param('i', $product_id);

            // Execute the query
            $single_product_details_stmt->execute();
            $result = $single_product_details_stmt->get_result();

            // Check if the query returned a result
            if ($result && $result->num_rows > 0) {
                // Fetch the product data
                $product_data = $result->fetch_assoc();

                // Sanitize and extract product details
                $product_name = htmlspecialchars($product_data['product_name'], ENT_QUOTES, 'UTF-8');
                $product_description = htmlspecialchars($product_data['product_description'], ENT_QUOTES, 'UTF-8');
                $product_image1 = htmlspecialchars($product_data['product_image1'], ENT_QUOTES, 'UTF-8');
                $product_image2 = htmlspecialchars($product_data['product_image2'], ENT_QUOTES, 'UTF-8');
                $product_image3 = htmlspecialchars($product_data['product_image3'], ENT_QUOTES, 'UTF-8');
                $product_price = number_format(floatval($product_data['product_price']), 2, '.', ',');


                // Display the product details
                echo "
                <div class='row'>
                    <!-- Product Details -->
                    <div class='col-md-6'>
                        <div class='card h-100 shadow-sm'>
                            <img src='./admin/uploads/products/$product_image1' class='card-img-top mb-3' alt='$product_name'>
                            <div class='card-body'>
                                <h5 class='card-title'>$product_name</h5>
                                <p class='card-text'>$product_description</p>
                                <p class='card-text'><strong>Price:</strong> $$product_price</p>
                                <a href='index.php?add_selected_product_to_cart=$product_id' class='btn btn-info btn-sm'>Add to Cart</a>
                                <a href='index.php' class='btn btn-secondary btn-sm'>Go To Home</a>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Images -->
                    <div class='col-md-6'>
                        <div class='row'>
                            <div class='col-md-12'>
                                <h4 class='text-center mb-3'>Additional Images</h4>
                            </div>
                            <div class='col-md-6 mb-3'>
                                <img src='./admin/uploads/products/$product_image2' class='card-img-top img-fluid rounded' alt='$product_name'>
                            </div>
                            <div class='col-md-6'>
                                <img src='./admin/uploads/products/$product_image3' class='card-img-top img-fluid rounded' alt='$product_name'>
                            </div>
                        </div>
                    </div>
                </div>
                ";
            } else {
                // No product found
                echo '<div class="alert alert-warning" role="alert">No product found with the selected ID.</div>';
            }

            // Close the prepared statement
            $single_product_details_stmt->close();
        } else {
            // Handle query preparation failure
            echo '<div class="alert alert-danger" role="alert">Failed to fetch product details. Please try again later.</div>';
        }
    } else {
        // If no product ID is provided in the URL
        echo '<div class="alert alert-danger" role="alert">No product selected.</div>';
    }
}

function get_single_product_details_with_category_and_brand()
{
    // Access the $connection variable from the global scope
    global $connection;

    // Check if the product ID is provided in the URL
    if (isset($_GET['product'])) {

        // Ensure the product ID is sanitized and validated as an integer
        $product_id = intval($_GET['product']);

        // Check for valid product ID
        if ($product_id <= 0) {
            echo '<div class="alert alert-danger" role="alert">Invalid product ID.</div>';
            return;
        }

        // Prepare a secure SQL query using prepared statements to fetch product details
        $single_product_details_query = "
            SELECT 
                p.product_name, 
                p.product_description, 
                p.product_image1, 
                p.product_image2, 
                p.product_image3, 
                p.product_price, 
                b.brand_name, 
                c.category_name
            FROM products p
            LEFT JOIN brands b ON p.product_brand = b.brand_id
            LEFT JOIN categories c ON p.product_category = c.category_id
            WHERE p.product_id = ?";

        if ($single_product_details_stmt = $connection->prepare($single_product_details_query)) {
            // Bind the product ID parameter to the prepared statement
            $single_product_details_stmt->bind_param('i', $product_id);

            // Execute the query
            $single_product_details_stmt->execute();
            $result = $single_product_details_stmt->get_result();

            // Check if the query returned a result
            if ($result && $result->num_rows > 0) {
                // Fetch the product data
                $product_data = $result->fetch_assoc();

                // Sanitize and extract product details
                $product_name = htmlspecialchars($product_data['product_name'], ENT_QUOTES, 'UTF-8');
                $product_description = htmlspecialchars($product_data['product_description'], ENT_QUOTES, 'UTF-8');
                $product_image1 = htmlspecialchars($product_data['product_image1'], ENT_QUOTES, 'UTF-8');
                $product_image2 = htmlspecialchars($product_data['product_image2'], ENT_QUOTES, 'UTF-8');
                $product_image3 = htmlspecialchars($product_data['product_image3'], ENT_QUOTES, 'UTF-8');
                $product_price = number_format(floatval($product_data['product_price']), 2, '.', ',');
                $product_brand = htmlspecialchars($product_data['brand_name'], ENT_QUOTES, 'UTF-8');
                $product_category = htmlspecialchars($product_data['category_name'], ENT_QUOTES, 'UTF-8');

                // Display the product details
                echo "
                <div class='row'>
                    <!-- Product Details -->
                    <div class='col-md-6'>
                        <div class='card h-100 shadow-sm'>
                            <img src='./admin/uploads/products/$product_image1' class='card-img-top mb-3' alt='$product_name'>
                            <div class='card-body'>
                                <h5 class='card-title'>$product_name</h5>
                                <p class='card-text'>$product_description</p>
                                <p class='card-text'><strong>Brand:</strong> $product_brand</p>
                                <p class='card-text'><strong>Category:</strong> $product_category</p>
                                <p class='card-text'><strong>Price:</strong> $$product_price</p>
                                <a href='index.php?add_selected_product_to_cart=$product_id' class='btn btn-info btn-sm'>Add to Cart</a>
                                <a href='index.php' class='btn btn-secondary btn-sm'>Go To Home</a>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Images -->
                    <div class='col-md-6'>
                        <div class='row'>
                            <div class='col-md-12'>
                                <h4 class='text-center mb-3'>Related Products</h4>
                            </div>
                            <div class='col-md-6 mb-3'>
                                <img src='./admin/uploads/products/$product_image2' class='card-img-top img-fluid rounded' alt='$product_name'>
                            </div>
                            <div class='col-md-6'>
                                <img src='./admin/uploads/products/$product_image3' class='card-img-top img-fluid rounded' alt='$product_name'>
                            </div>
                        </div>
                    </div>
                </div>
                ";
            } else {
                // No product found
                echo '<div class="alert alert-warning" role="alert">No product found with the selected ID.</div>';
            }

            // Close the prepared statement
            $single_product_details_stmt->close();
        } else {
            // Handle query preparation failure
            echo '<div class="alert alert-danger" role="alert">Failed to fetch product details. Please try again later.</div>';
        }
    } else {
        // If no product ID is provided in the URL
        echo '<div class="alert alert-danger" role="alert">No product selected.</div>';
    }
}



// ==================================================================================================================
// Get all Categories 
// ==================================================================================================================

function get_categories()
{
    // Access the $connection variable from the global scope
    global $connection;

    // Prepare the query to fetch categories
    $query = "SELECT category_id, category_name FROM categories";

    if ($prepared_query_category = $connection->prepare($query)) {
        // Execute the query
        $prepared_query_category->execute();
        $result = $prepared_query_category->get_result();

        // Check if any categories are returned
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Safely extract and sanitize category data
                $category_name = htmlspecialchars($row['category_name'], ENT_QUOTES, 'UTF-8');
                $category_id = intval($row['category_id']);

                // Display the category as a list item
                echo "
                <li class='list-group-item bg-secondary text-white my-2'>
                    <a class='text-decoration-none text-light' href='index.php?category=$category_id'>$category_name</a>
                </li>";
            }
        } else {
            // If no categories are found, display a message
            echo "
            <li class='list-group-item bg-dark text-white'>
                No categories available
            </li>";
        }

        // Close the prepared query
        $prepared_query_category->close();
    } else {
        // Handle query preparation error
        echo "
        <li class='list-group-item bg-danger text-white'>
            Failed to retrieve categories. Please try again later.
        </li>";
    }
}



// ==================================================================================================================
// Get all brands
// ==================================================================================================================

function get_brands()
{
    // Access the $connection variable from the global scope
    global $connection;

    // Prepare the query to fetch brands
    $query_brands = "SELECT brand_id, brand_name FROM brands";

    if ($prepared_query_brands = $connection->prepare($query_brands)) {
        // Execute the query
        $prepared_query_brands->execute();
        $result = $prepared_query_brands->get_result();

        // Check if any brands are returned
        if ($result && $result->num_rows > 0) {
            while ($brand = $result->fetch_assoc()) {
                // Safely extract and sanitize brand data
                $brand_name = htmlspecialchars($brand['brand_name'], ENT_QUOTES, 'UTF-8');
                $brand_id = intval($brand['brand_id']);

                // Display the brand as a list item
                echo "
                <li class='list-group-item bg-secondary text-white my-2'>
                    <a class='text-decoration-none text-light' href='index.php?brand=$brand_id'>$brand_name</a>
                </li>";
            }
        } else {
            // If no brands are found, display a message
            echo "
            <li class='list-group-item bg-dark text-white'>
                No brands available
            </li>";
        }

        // Close the prepared statement
        $prepared_query_brands->close();
    } else {
        // Handle query preparation error
        echo "
        <li class='list-group-item bg-danger text-white'>
            Failed to retrieve brands. Please try again later.
        </li>";
    }
}



// ==================================================================================================================
// Get IP Address of a User in PHP
// ==================================================================================================================

// IP Address of a user in PHP is a unique identifier assigned to each device connected to a network. It is used to identify the device and communicate with it over the network. The IP address can be used to track the location of a user, block access to certain websites, and personalize the user experience based on their location. In this getUserIP function, we will show you how to get the IP address of a user in PHP.

// To get the IP address of a user in PHP, you can use the $_SERVER superglobal array. The IP address is stored in the $_SERVER['REMOTE_ADDR'] variable. Here is how you can get the IP address of a user in PHP:

function getUserIP()
{
    // Check if the IP is from a shared internet
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    // Check if the IP is passed from a proxy
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    // Otherwise, get the remote address
    else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

// Usage
// $userIP = getUserIP();
// echo "User IP Address: " . $userIP;


// More professional way to get the IP address of a user in PHP 

function getUserIPAddress()
{
    // List of headers that may contain the client IP
    $headers = [
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'REMOTE_ADDR'
    ];

    foreach ($headers as $header) {
        if (!empty($_SERVER[$header])) {
            $ip = $_SERVER[$header];

            // Handle the case of multiple IPs in HTTP_X_FORWARDED_FOR
            if ($header === 'HTTP_X_FORWARDED_FOR') {
                $ipList = explode(',', $ip);
                $ip = trim(current($ipList)); // Take the first IP
            }

            // Validate IP format
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6)) {
                // Optionally check for private IP addresses if needed:
                // You can log or further handle specific IP ranges.
                return $ip;
            }
        }
    }

    // Log when an invalid IP is detected or return an empty string
    error_log("Invalid or unknown IP detected.");
    return 'Unknown IP';  // or return '' or null based on your preference
}



// Usage
// $userIP = getUserIP();
// echo "User IP Address: " . $userIP;


// ==================================================================================================================
// Add to Cart Function
// ==================================================================================================================

function add_to_cart_function()
{
    global $connection;

    if (isset($_GET["add_selected_product_to_cart"])) {
        // Ensure that product_id is an integer and valid
        $product_id = intval($_GET['add_selected_product_to_cart']);

        // Validate product ID is positive and greater than zero
        if ($product_id <= 0) {
            echo "<script>alert('Invalid product ID');</script>";
            exit();
        }

        // Get the user IP address
        $userIP = getUserIPAddress();
        if ($userIP === 'Unknown IP') {
            echo "<script>alert('Unable to retrieve IP address. Please try again later.');</script>";
            exit();
        }

        // Log the user IP
        error_log("User IP: " . $userIP);
        error_log("Attempting to add product ID: " . $product_id);

        // Check if the product exists in the database
        $check_product_query = "SELECT * FROM products WHERE product_id = $product_id";
        $check_product_result = mysqli_query($connection, $check_product_query);

        if (mysqli_num_rows($check_product_result) == 0) {
            echo "<script>alert('This product is not available!');</script>";
            echo "<script>window.location.href = 'index.php';</script>";
            exit();
        }

        // Check if the product is already in the cart
        $select_cart_query = "SELECT * FROM cart_table WHERE ip_address = '" . mysqli_real_escape_string($connection, $userIP) . "' AND product_id = $product_id";
        $select_cart_query_result = mysqli_query($connection, $select_cart_query);
        $num_of_rows_of_cart = mysqli_num_rows($select_cart_query_result);

        if ($num_of_rows_of_cart > 0) {
            // If the product is already in the cart, show a message and redirect
            echo "<script>alert('This product is already in your cart!');</script>";
            echo "<script>window.location.href = 'index.php';</script>";
        } else {
            // Insert the product into the cart table
            $insert_cart_query = "INSERT INTO cart_table (product_id, ip_address, quantity) VALUES ($product_id, '" . mysqli_real_escape_string($connection, $userIP) . "', 1)";
            $insert_cart_query_result = mysqli_query($connection, $insert_cart_query);

            if ($insert_cart_query_result) {
                // If the product is added to the cart successfully, show a success message and redirect
                echo "<script>alert('Product added to cart successfully!');</script>";
                echo "<script>window.open('index.php', '_self');</script>";
            } else {
                // Log any SQL errors
                error_log('Insert cart query error: ' . mysqli_error($connection));
                echo "<script>alert('Error inserting into cart. Please try again later.');</script>";
            }
        }
    }
}







// ==================================================================================================================
// Function to get the total number of items in the cart
// ==================================================================================================================

function get_total_items_in_cart()
{
    // Use global scope for database connection
    global $connection;

    // Check if the 'add_selected_product_to_cart' parameter is set in the URL
    if (isset($_GET["add_selected_product_to_cart"])) {

        $userIP = getUserIP();

        $select_cart = "SELECT * FROM cart_table WHERE ip_address = '$userIP'";
        $result = mysqli_query($connection, $select_cart);
        $count_cart_items = mysqli_num_rows($result);
    } else {
        $userIP = getUserIP();

        $select_cart = "SELECT * FROM cart_table WHERE ip_address = '$userIP'";
        $result = mysqli_query($connection, $select_cart);
        $count_cart_items = mysqli_num_rows($result);
    }

    echo $count_cart_items;
}

// ==================================================================================================================
// Function to get the total price of items in the cart
// ==================================================================================================================

function get_total_price_in_cart()
{
    // Use global scope for database connection
    global $connection;

    $userIP = getUserIP();

    $select_cart = "SELECT * FROM cart_table WHERE ip_address = '$userIP'";
    $result = mysqli_query($connection, $select_cart);

    $total_price = 0;

    while ($row = mysqli_fetch_assoc($result)) {
        $product_id = $row['product_id'];
        $select_product = "SELECT * FROM products WHERE product_id = $product_id";
        $product_result = mysqli_query($connection, $select_product);

        while ($product_data = mysqli_fetch_assoc($product_result)) {
            $product_price = array($product_data['product_price']);
            $product_price_sum = array_sum($product_price);
            $total_price += $product_price_sum;
        }
    }
    echo $total_price;
}

// ==================================================================================================================
// Get user orders
// ==================================================================================================================

function get_user_pending_orders()
{
    global $connection;

    // Check if the session is active and the user is logged in
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email'])) {
        echo '<div class="alert alert-danger" role="alert">You must be logged in to view pending orders.</div>';
        return;
    }

    // Get the user ID and email from the session
    $user_id = intval($_SESSION['user_id']);
    $user_email = $_SESSION['user_email'];

    // Use a prepared statement to get the user data based on email
    $select_user_stmt = $connection->prepare("SELECT * FROM users WHERE user_email = ?");
    $select_user_stmt->bind_param('s', $user_email);
    $select_user_stmt->execute();
    $result_user = $select_user_stmt->get_result();

    // Check if a user is found
    if ($result_user && $result_user->num_rows > 0) {
        $user_data = $result_user->fetch_assoc();
        $user_id = $user_data['user_id'];  // Get the user ID from the result

        // Avoid redundant checks for unrelated GET parameters
        if (!isset($_GET['edit_account']) && !isset($_GET['my_orders']) && !isset($_GET['delete_account'])) {
            // Use a prepared statement to fetch pending orders
            $query_orders_stmt = $connection->prepare("SELECT * FROM orders WHERE user_id = ? AND order_status = 'Pending'");
            $query_orders_stmt->bind_param('i', $user_id);
            $query_orders_stmt->execute();
            $query_pending_orders_result = $query_orders_stmt->get_result();
            $count = $query_pending_orders_result->num_rows;

            // Display the result
            if ($count > 0) {
                echo "
                    <h4 class='text-center text-success py-4'>You have <span class='text-primary'>$count</span> pending orders</h4>
                    <a href='profile.php?my_orders' class='text-primary'>Order Details</a>
                ";
            } else {
                echo "<div class='alert alert-warning' role='alert'>
                        <h3 class='text-center py-4'>You do not have any pending orders.</h3>
                        <a href='../products.php' class='text-primary'>Explore Products</a>
                      </div>";
            }

            // Close the prepared statements
            $query_orders_stmt->close();
        }
    } else {
        // If no user is found based on the email
        echo '<div class="alert alert-danger" role="alert">User not found. Please log in again.</div>';
    }

    // Close the user query prepared statement
    $select_user_stmt->close();
}


// ==================================================================================================================
// Get user orders
// ==================================================================================================================

function get_user_orders()
{
    global $connection;

    // Ensure the session is active and the user is logged in
    if (!isset($_SESSION['user_email'])) {
        echo '<div class="alert alert-danger" role="alert">You must be logged in to view your orders.</div>';
        return;
    }

    // Get user email from session and sanitize it
    $user_email = $_SESSION['user_email'];

    // Use prepared statements to fetch user data securely
    $select_user_stmt = $connection->prepare("SELECT user_id FROM users WHERE user_email = ?");
    $select_user_stmt->bind_param('s', $user_email);
    $select_user_stmt->execute();
    $result_user = $select_user_stmt->get_result();

    if ($result_user && $result_user->num_rows > 0) {
        $user_data = $result_user->fetch_assoc();
        $user_id = $user_data['user_id'];

        // Avoid redundant checks for unrelated GET parameters
        if (!isset($_GET['edit_account']) && !isset($_GET['my_pending_orders']) && !isset($_GET['delete_account'])) {
            // Fetch orders using prepared statements to prevent SQL injection
            $query_orders_stmt = $connection->prepare("SELECT * FROM orders WHERE user_id = ?");
            $query_orders_stmt->bind_param('i', $user_id);
            $query_orders_stmt->execute();
            $query_orders_result = $query_orders_stmt->get_result();
            $count_orders = $query_orders_result->num_rows;

            // Check if there are orders and display them
            if ($count_orders > 0) {
                echo "<table class='table table-striped table-bordered table-hover' style='width:100%; text-align:left;'>
                        <thead class='thead-dark'>
                            <tr>
                                <th>Order ID</th>
                                <th>Amount Due</th>
                                <th>Invoice Number</th>
                                <th>Total Products</th>
                                <th>Order Status</th>
                                <th>Order Date</th>
                            </tr>
                        </thead>
                        <tbody>";

                // Loop through the orders and display them
                while ($orders = $query_orders_result->fetch_assoc()) {
                    $order_id = intval($orders['order_id']);
                    $amount_due = number_format(floatval($orders['amount_due']), 2, '.', ',');
                    $invoice_number = htmlspecialchars($orders['invoice_number'], ENT_QUOTES, 'UTF-8');
                    $total_products = intval($orders['total_products']);
                    $order_status = htmlspecialchars($orders['order_status'], ENT_QUOTES, 'UTF-8');
                    $order_date = substr($orders['order_date'], 0, 10);

                    echo "<tr>
                            <td>$order_id</td>
                            <td>$$amount_due</td>
                            <td>$invoice_number</td>
                            <td>$total_products</td>
                            <td>$order_status</td>
                            <td>$order_date</td>
                          </tr>";
                }

                echo "</tbody></table>";
            } else {
                echo "<p>No orders found.</p>";
            }

            // Close the prepared statements
            $query_orders_stmt->close();
        }
    } else {
        echo '<div class="alert alert-warning" role="alert">No user found with the provided email.</div>';
    }

    // Close the user query prepared statement
    $select_user_stmt->close();
}



?>