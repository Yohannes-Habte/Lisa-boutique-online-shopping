<?php

include('backend/connect.php');

include('functions/global_function.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Family Online Shopping</title>

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
        <section class="text-center mb-5 mx-5 px-5">
            <h1 class="display-5">Welcome to Family Online Shopping</h1>
            <p class="lead text-muted">Discover a wide range of high-quality products for your home, health, and lifestyle. Shop from the comfort of your home and enjoy a seamless shopping experience.</p>
        </section>

        <div class="row">
            <!-- Sidebar -->
            <aside class="col-md-2">
                <div class=" bg-secondary text-white p-3 rounded mb-4">
                    <h4 class="text-center">Brands</h4>
                    <ul class="list-group list-group-flush">
                        <?php

                        get_brands()

                        ?>
                    </ul>
                </div>

                <div class="bg-secondary text-white p-3 rounded">
                    <h4 class="text-center">Categories</h4>
                    <ul class="list-group list-group-flush">
                        <?php

                        get_categories()

                        ?>
                    </ul>
                </div>
            </aside>

            <!-- Products -->
            <div class="col-md-10">
                <div class="row g-4">

                    <?php

                    get_single_product_details_with_category_and_brand();

                    get_products_by_category();

                    get_products_by_brand();
                    ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer section  -->

    <?php include('components/footer/footer.php') ?>

    <!-- JavaScript -->
    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>