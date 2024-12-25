<?php

if (isset($_GET['delete_user'])) {
    // Sanitize and validate the user ID from the GET request
    $delete_user_id = (int) $_GET['delete_user']; // Cast to integer for basic validation

    if ($delete_user_id > 0) {
        // Prepare the query to delete the user
        $delete_user_query = "DELETE FROM users WHERE user_id = ?";

        // Initialize the prepared statement
        if ($stmt = $connection->prepare($delete_user_query)) {

            // Bind the user ID to the prepared statement
            if ($stmt->bind_param('i', $delete_user_id)) {

                // Execute the statement
                if ($stmt->execute()) {
                    // Success: User deleted
                    echo "<script>alert('Success: The user has been successfully deleted.');</script>";
                    echo "<script>window.open('index.php?users', '_self');</script>"; // Redirect to users page
                    exit; // Ensure no further code is executed
                } else {
                    // Error during execution
                    echo "<script>alert('Error: An error occurred while deleting the user. " . htmlspecialchars($stmt->error) . "');</script>";
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
        // Invalid user ID
        echo "<script>alert('Error: Invalid user ID.');</script>";
    }
}
