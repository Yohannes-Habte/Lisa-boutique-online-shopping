<?php

if (isset($_GET['payment'])) {

    // Sanitize and validate the payment ID from GET request
    $delete_payment_id = (int) $_GET['payment'];

    if ($delete_payment_id > 0) {
        // Prepare the query to delete the payment
        $delete_payment_query = "DELETE FROM payments WHERE payment_id = ?";

        // Initialize the prepared statement
        if ($stmt = $connection->prepare($delete_payment_query)) {
            
            // Bind the payment ID to the prepared statement
            if ($stmt->bind_param('i', $delete_payment_id)) {

                // Execute the statement
                if ($stmt->execute()) {
                    // Success: Payment deleted
                    echo "<script>alert('Success: The payment has been successfully deleted.');</script>";
                    echo "<script>window.open('index.php?payments', '_self');</script>"; // Redirect to payments page
                    exit; // Ensure no further code is executed
                } else {
                    // Error during execution
                    echo "<script>alert('Error: An error occurred while deleting the payment. " . $stmt->error . "');</script>";
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
        // Invalid payment ID
        echo "<script>alert('Error: Invalid payment ID.');</script>";
    }
}

?>
