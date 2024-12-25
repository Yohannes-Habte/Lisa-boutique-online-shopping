
<?php


if (isset($_GET['delete_category'])) {

  $delete_category_id = (int) $_GET['delete_category'];

  // Check if the ID is valid
  if ($delete_category_id > 0) {

    // Prepare the query to delete the category
    $delete_category_query = "DELETE FROM categories WHERE category_id = ?";

    // Initialize the prepared statement
    if ($stmt = $connection->prepare($delete_category_query)) {
      echo "Prepared statement successful."; // Debugging message

      // Bind the category ID to the prepared statement
      if ($stmt->bind_param('i', $delete_category_id)) {
        echo "Binding successful."; // Debugging message

        // Execute the statement
        if ($stmt->execute()) {
          echo "<script>alert('Success: The category has been successfully deleted.');</script>";
          echo "<script>window.open('index.php?categories', '_self');</script>"; // Redirect
          exit;
        } else {
          echo "<script>alert('Error: An error occurred while executing the query: " . $stmt->error . "');</script>";
        }
      } else {
        echo "<script>alert('Error: An error occurred while binding parameters: " . $stmt->error . "');</script>";
      }
      $stmt->close();
    } else {
      echo "<script>alert('Error: An error occurred while preparing the query: " . $connection->error . "');</script>";
    }
  } else {
    echo "<script>alert('Error: Invalid category ID.');</script>";
  }
}

?>