<?php

include('backend/connect.php');
include('functions/global_function.php');

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart Details</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4p889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>

<body class="d-flex flex-column min-vh-100 bg-light">

    <!-- Header section -->
    <?php include('components/header/header.php') ?>

    <!-- Main Content -->
    <main class="container-fluid py-5 flex-grow-1">
        <h1 class="text-center mb-5">Shopping Cart</h1>
        <div class="container">
            <?php
            $userIP = getUserIP();
            $total_price = 0;
            $select_cart = "SELECT * FROM cart_table WHERE ip_address = '$userIP'";
            $result = mysqli_query($connection, $select_cart);
            $result_count = mysqli_num_rows($result);

            if ($result_count > 0) {
            ?>
                <form action="" method="post">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">Product Name</th>
                                    <th scope="col">Product Image</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Remove</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $product_id = $row['product_id'];
                                    $quantity = $row['quantity'];
                                    $select_product = "SELECT * FROM products WHERE product_id = $product_id";
                                    $product_result = mysqli_query($connection, $select_product);

                                    while ($product_data = mysqli_fetch_assoc($product_result)) {
                                        $product_name = $product_data['product_name'];
                                        $product_image1 = $product_data['product_image1'];
                                        $product_price = $product_data['product_price'];

                                        $product_total_price = $product_price * $quantity;
                                        $total_price += $product_total_price;
                                ?>
                                        <tr>
                                            <td class='text-start'>
                                                <span class="fw-bold"><?php echo $product_name ?></span>
                                            </td>
                                            <td>
                                                <img src='admin/uploads/products/<?php echo $product_image1 ?>'
                                                    alt='<?php echo $product_image1 ?>'
                                                    class='img-fluid rounded border'
                                                    style='width: 80px; height: 70px;'>
                                            </td>
                                            <td class='text-center'>
                                                <input type='number' name='qty[<?php echo $product_id; ?>]'
                                                    value='<?php echo $quantity; ?>'
                                                    class='form-control text-center w-50 mx-auto'
                                                    min='1'>
                                            </td>
                                            <td class="fw-bold text-success">
                                                $<?php echo $product_total_price ?>
                                            </td>
                                            <td>
                                                <input type='checkbox' name='remove_items[]' value="<?php echo $product_id; ?>">
                                            </td>
                                            <td class="d-flex justify-content-center align-items-center gap-2 py-4">
                                                <!-- Update button -->
                                                <button type='submit' name='update_cart' class='btn btn-outline-success btn-sm'>
                                                    <i class="fa fa-sync-alt"></i> Update
                                                </button>
                                                <!-- Delete button -->
                                                <button type='submit' name='remove_from_cart' class='btn btn-outline-danger btn-sm'>
                                                    <i class="fa fa-trash"></i> Delete
                                                </button>
                                            </td>
                                        </tr>


                                <?php }
                                } ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Total:</td>
                                    <td colspan="2" class="fw-bold text-primary">$<?php echo $total_price; ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="products.php" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Continue Shopping
                        </a>
                        <a href="client/checkout.php" class="btn btn-info text-white">
                            Proceed to Checkout <i class="fa fa-arrow-right"></i>
                        </a>
                    </div>
                </form>

            <?php
            } else {
            ?>
                <h3 class='text-center'>No items in the cart</h3>
                <div class="text-center">
                    <a href="products.php" class="btn btn-secondary">Continue Shopping</a>
                </div>
            <?php
            }
            ?>
        </div>
    </main>


    <!-- Footer section -->
    <?php include('components/footer/footer.php') ?>

    <!-- JavaScript -->
    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>

<?php
// ====================================================================================================
// Update cart quantities
// ====================================================================================================

if (isset($_POST['update_cart'])) {
    foreach ($_POST['qty'] as $product_id => $qty) {
        $qty = intval($qty);
        if ($qty > 0) {
            $update_cart_query = "UPDATE cart_table 
                                  SET quantity = '$qty' 
                                  WHERE ip_address = '$userIP' AND product_id = '$product_id'";
            mysqli_query($connection, $update_cart_query);
        }
    }
}

// ====================================================================================================
// Remove Cart Item/s
// ====================================================================================================
if (isset($_POST['remove_from_cart'])) {
    foreach ($_POST['remove_items'] as $product_id) {
        $delete_query = "DELETE FROM cart_table 
                         WHERE ip_address = '$userIP' AND product_id = '$product_id'";
        $run_delete = mysqli_query($connection, $delete_query);

        if ($run_delete) {
            echo "<script>alert('Product has been removed from cart')</script>";
            echo "<script>window.open('cart.php', '_self')</script>";
        }
    }
}
?>