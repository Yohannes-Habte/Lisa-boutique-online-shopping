<?php
include('backend/connect.php');
include('functions/global_function.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Lisaboutque</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <!-- Header Section -->
    <?php include('components/header/header.php'); ?>

    <!-- Main Section -->
    <main class="min-vh-100 bg-light">
        <div class="container py-5">
            <!-- Title Section -->
            <div class="text-center mb-5">
                <h1 class="fw-bold display-4">About Lisaboutque</h1>
                <p class="lead text-muted">Discover our story, values, and mission.</p>
            </div>

            <!-- Mission Section -->
            <section class="mb-5">
                <div class="row align-items-center gy-4">
                    <div class="col-md-6">
                        <h2 class="fw-bold">Our Mission</h2>
                        <p class="text-muted">
                            At Lisaboutque, our mission is to create a truly exceptional experience that goes beyond mere transactions, offering a seamless blend of sophistication, expertise, and meaningful connections. We are devoted to curating and delivering only the finest quality products and services, each meticulously selected for its excellence, craftsmanship, and timeless appeal. Our commitment is not just to meet, but to exceed the expectations of those who seek unparalleled luxury and distinctive design.
                        </p>

                        <p class="text-muted">
                            Every interaction with Lisaboutque is an opportunity for us to provide insightful guidance, whether through personalized recommendations or expert advice, ensuring that each client’s individual tastes and needs are thoughtfully considered. We believe that true luxury is about more than just possession; it’s about creating a lasting experience that resonates deeply and enhances the lives of those we serve. At Lisaboutque, we’re not just offering products—we are cultivating a community of discerning individuals who appreciate the art of fine living, and we are honored to be a part of that journey.
                        </p>

                    </div>
                    <div class="col-md-6">
                        <div class="text-center">
                            <!-- Image with improved size and border-radius for professionalism -->
                            <img src="assets/mission.jpg" alt="Our Mission" class="img-fluid rounded-3 shadow-sm" style="max-width: 90%; height: auto;">
                        </div>
                    </div>
                </div>
            </section>

            <!-- History Section -->
            <section class="mb-5">
                <div class="row align-items-center gy-4 flex-md-row-reverse">
                    <div class="col-md-6">
                        <h2 class="fw-bold">Our History</h2>
                        <p class="text-muted">
                            Founded in 2024, Lisaboutque began with a singular vision: to merge luxury with functionality, creating an experience that elevates everyday living through exquisite craftsmanship and thoughtful design. What started as a small boutique, driven by a passion for quality and a commitment to exceptional service, has quickly grown into a recognized leader in the industry.
                        </p>

                        <p class="text-muted">
                            Over the years, we have remained steadfast in our dedication to offering products and services that not only embody elegance but also serve a practical purpose. This unique combination of beauty and utility has resonated with discerning clients around the world, allowing us to expand our reach while maintaining the personal touch that set us apart from the beginning.
                        </p>

                        <p class="text-muted">
                            Today, Lisaboutque stands as a symbol of excellence, constantly evolving to meet the ever-changing needs of our clients. As we continue to push the boundaries of innovation and sophistication, our core values remain unchanged—delivering unparalleled quality, service, and experiences that stand the test of time.
                        </p>
                    </div>
                    <div class="col-md-6">
                        <div class="text-center">
                            <img src="assets/history.jpg" alt="Our History" class="img-fluid rounded-3 shadow-sm" style="max-width: 90%; height: auto;">
                        </div>
                    </div>
                </div>
            </section>



            <!-- Values Section -->
            <section class="mb-5 text-center">
                <h2 class="fw-bold mb-4">Our Values</h2>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-gem fa-3x text-primary mb-3"></i>
                                <h5>Quality</h5>
                                <p class="text-muted">We ensure the highest standards in every product and service we offer.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-balance-scale fa-3x text-success mb-3"></i>
                                <h5>Integrity</h5>
                                <p class="text-muted">Transparency and trust are at the core of our operations.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-lightbulb fa-3x text-warning mb-3"></i>
                                <h5>Innovation</h5>
                                <p class="text-muted">We evolve and adapt to meet changing market demands.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-users fa-3x text-danger mb-3"></i>
                                <h5>Customer Focus</h5>
                                <p class="text-muted">Our customers are our priority in every decision we make.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Offerings Section -->
            <section class="mb-5">
                <h2 class="fw-bold text-center mb-4">What We Offer</h2>
                <p class="text-center text-muted">
                    Lisaboutque specializes in offering a diverse range of luxury products and services tailored for sophisticated clients.
                </p>
                <div class="text-center">
                    <a href="products.php" class="btn btn-info mt-3">Explore Our Products</a>
                </div>
            </section>

            <!-- Contact Section -->
            <section class="bg-secondary text-white rounded p-4 text-center">
                <h2>Get in Touch</h2>
                <p>We would love to hear from you!</p>
                <p><i class="fas fa-phone"></i> +4917601005050</p>
                <p><i class="fas fa-envelope"></i> lisaboutique@gmail.com</p>
                <p><i class="fas fa-map-marker-alt"></i> Hamberger Str. 1, 10115 Berlin, Germany</p>
            </section>
        </div>
    </main>

    <!-- Footer Section -->
    <?php include('components/footer/footer.php'); ?>

    <!-- JavaScript -->
    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>