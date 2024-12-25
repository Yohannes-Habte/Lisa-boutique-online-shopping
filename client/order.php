<?php
// Include database connection
include('../backend/connect.php');
include('../functions/global_function.php');

// Enable error reporting for debugging during development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure user is authenticated (use session for authentication)
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['user'])) {
    $user_id = intval($_GET['user']);

    //==========================================================================================
    // Start transaction and retrieve user information
    //==========================================================================================
    $connection->begin_transaction();
    try {
        // Retrieve user info
        $get_user_stmt = $connection->prepare("SELECT * FROM users WHERE user_id = ?");
        $get_user_stmt->bind_param("i", $user_id);
        $get_user_stmt->execute();
        $result_user = $get_user_stmt->get_result();
        $row_user = $result_user->fetch_assoc();

        if (!$row_user) {
            throw new Exception("User not found!");
        }

        // Initialize variables
        $total_price = 0;
        $invoice_number = mt_rand();
        $status = 'Pending';

        // Retrieve the user's IP address
        $user_ip = getUserIPAddress();

        // Retrieve cart items
        $cart_query = "SELECT * FROM cart_table WHERE ip_address = ?";
        $cart_stmt = $connection->prepare($cart_query);
        $cart_stmt->bind_param("s", $user_ip);
        $cart_stmt->execute();
        $cart_result = $cart_stmt->get_result();
        $cart_count_products = $cart_result->num_rows;

        while ($cart_row = $cart_result->fetch_assoc()) {
            $product_id = $cart_row['product_id'];
            $quantity = $cart_row['quantity'];

            // Fetch the product price
            $product_query = "SELECT product_price FROM products WHERE product_id = ?";
            $product_stmt = $connection->prepare($product_query);
            $product_stmt->bind_param("i", $product_id);
            $product_stmt->execute();
            $product_result = $product_stmt->get_result();
            $product_row = $product_result->fetch_assoc();

            // Calculate the total price for the cart
            if ($product_row) {
                $product_price = $product_row['product_price'];
                $total_price += $product_price * $quantity;
            }
        }

        // Handle empty cart
        if ($cart_count_products == 0) {
            $total_price = 0; // No products, no price
        }

        $subtotal = $total_price;

        // ======================================================================================
        // Insert order details into the database
        // ======================================================================================
        $insert_order_stmt = $connection->prepare("INSERT INTO orders (user_id, amount_due, invoice_number, total_products, order_status, order_date) 
                                                   VALUES (?, ?, ?, ?, ?, NOW())");
        $insert_order_stmt->bind_param("idiss", $user_id, $subtotal, $invoice_number, $cart_count_products, $status);
        $insert_order_stmt->execute();

        // ======================================================================================
        // Insert pending order details
        // ======================================================================================
        $cart_stmt->execute(); // Re-run cart query
        $cart_result = $cart_stmt->get_result();

        while ($cart_row = $cart_result->fetch_assoc()) {
            $product_id = $cart_row['product_id'];
            $quantity = $cart_row['quantity'];

            $insert_pending_order_stmt = $connection->prepare("INSERT INTO orders_pending (user_id, product_id, invoice_number, quantity, order_status) 
                                                               VALUES (?, ?, ?, ?, ?)");
            if (!$insert_pending_order_stmt) {
                throw new Exception("Prepare failed: " . $connection->error);
            }

            $insert_pending_order_stmt->bind_param("iiiis", $user_id, $product_id, $invoice_number, $quantity, $status);

            if (!$insert_pending_order_stmt->execute()) {
                throw new Exception("Error inserting into orders_pending: " . $connection->error);
            }
        }

        // ======================================================================================
        // Clear the cart after placing the order
        // ======================================================================================
        $delete_cart_stmt = $connection->prepare("DELETE FROM cart_table WHERE ip_address = ?");
        $delete_cart_stmt->bind_param("s", $user_ip);
        $delete_cart_stmt->execute();

        // Commit transaction
        $connection->commit();

        // Redirect user
        // echo "<script>alert('Order has been placed successfully!'); window.location.href = 'profile.php';</script>";
        // exit();
        header("Location: profile.php");
        exit();
    } catch (Exception $e) {
        // Rollback transaction and handle error
        $connection->rollback();
        error_log("Order error: " . $e->getMessage());
        echo "<script>alert('Error: " . $e->getMessage() . "'); window.location.href = 'order.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('User ID is missing!'); window.location.href = 'order.php';</script>";
    exit();
}
