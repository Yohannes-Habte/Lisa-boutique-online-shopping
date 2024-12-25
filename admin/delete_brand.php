<?php

if (isset($_GET['delete_brand'])) {

    // Sanitize and validate the brand ID from GET request
    $delete_brand_id = (int) $_GET['delete_brand'];

    if ($delete_brand_id > 0) {
        // Prepare the query to delete the brand
        $delete_brand_query = "DELETE FROM brands WHERE brand_id = ?";

        // Initialize the prepared statement
        if ($stmt = $connection->prepare($delete_brand_query)) {
            
            // Bind the brand ID to the prepared statement
            if ($stmt->bind_param('i', $delete_brand_id)) {

                // Execute the statement
                if ($stmt->execute()) {
                    // Success: Brand deleted
                    echo "<script>alert('Success: The brand has been successfully deleted.');</script>";
                    echo "<script>window.open('index.php?brands', '_self');</script>"; // Redirect to brands page
                    exit; // Ensure no further code is executed
                } else {
                    // Error during execution
                    echo "<script>alert('Error: An error occurred while deleting the brand. " . $stmt->error . "');</script>";
                }
            } else {
                // Error during parameter binding
                echo "<script>alert('Error: Unable to bind parameters. " . $stmt->error . "');</script>";
            }

            $stmt->close(); // Close the prepared statement
        } else {
            // Error during query preparation
            echo "<script>alert('Error: Unable to prepare the query. " . $connection->error . "');</script>";
        }
    } else {
        // Invalid brand ID
        echo "<script>alert('Error: Invalid brand ID.');</script>";
    }
}

?>
