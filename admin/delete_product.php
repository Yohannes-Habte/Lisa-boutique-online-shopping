<?php

if (isset($_GET['delete_product'])) {
    // Sanitize and validate the product ID from the GET request
    $delete_product_id = (int) $_GET['delete_product']; // Cast to integer for basic validation

    if ($delete_product_id > 0) {
        // Prepare the query to delete the product
        $delete_product_query = "DELETE FROM products WHERE product_id = ?";

        // Initialize the prepared statement
        if ($stmt = $connection->prepare($delete_product_query)) {
            
            // Bind the product ID to the prepared statement
            if ($stmt->bind_param('i', $delete_product_id)) {

                // Execute the statement
                if ($stmt->execute()) {
                    // Success: Product deleted
                    echo "<script>alert('Success: The product has been successfully deleted.');</script>";
                    echo "<script>window.open('index.php?products', '_self');</script>"; // Redirect to products page
                    exit; // Ensure no further code is executed
                } else {
                    // Error during execution
                    echo "<script>alert('Error: An error occurred while deleting the product. " . htmlspecialchars($stmt->error) . "');</script>";
                }
            } else {
                // Error during parameter binding
                echo "<script>alert('Error: Unable to bind parameters. " . htmlspecialchars($stmt->error) . "');</script>";
            }

            $stmt->close(); // Close the prepared statement
        } else {
            // Error during query preparation
            echo "<script>alert('Error: Unable to prepare the query. " . htmlspecialchars($connection->error) . "');</script>";
        }
    } else {
        // Invalid product ID
        echo "<script>alert('Error: Invalid product ID.');</script>";
    }
}

?>
