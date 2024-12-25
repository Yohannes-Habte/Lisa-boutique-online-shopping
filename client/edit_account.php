<?php
// Include database connection
include "../backend/connect.php";

// Start session to access user data
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['edit_account'])) {
    $user_id = $_GET['edit_account'];

    // Fetch current user's email from session
    $user_email = $_SESSION['user_email'];

    // Fetch user details from the database
    $select_user = "SELECT * FROM users WHERE user_email = '$user_email'";
    $result_user = mysqli_query($connection, $select_user);
    $user_info = mysqli_fetch_assoc($result_user);

    if ($user_info) {
        $user_id = $user_info['user_id'];
        $user_name = $user_info['user_name'];
        $user_mobile = $user_info['user_mobile'];
        $user_address = $user_info['user_address'];
        $user_image = $user_info['user_image'];
    } else {
        // Handle case when user info is not found
        echo "<script>alert('User data not found.'); window.location.href = 'login.php';</script>";
        exit();
    }

    // Check if the form is submitted to update user data
    if (isset($_POST['update_user_account'])) {
        $user_name = $_POST['user_name'];
        $user_mobile = $_POST['user_mobile'];
        $user_address = $_POST['user_address'];

        // Handle file upload (if new image is uploaded)
        if (isset($_FILES['user_image']) && $_FILES['user_image']['error'] === 0) {
            $user_image = $_FILES['user_image']['name'];
            $user_image_tmp = $_FILES['user_image']['tmp_name'];
            $upload_dir = "uploads/";

            // Ensure the directory exists
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Move the uploaded file to the target directory
            if (move_uploaded_file($user_image_tmp, $upload_dir . $user_image)) {
                // Image uploaded successfully
            } else {
                echo "<script>alert('Error uploading the image.');</script>";
            }
        } else {
            // If no new image, keep the existing image
            $user_image = $user_info['user_image'];
        }

        // Update user data in the database
        $update_user = "UPDATE users SET 
                        user_name = '$user_name',  
                        user_mobile = '$user_mobile', 
                        user_address = '$user_address', 
                        user_image = '$user_image' 
                    WHERE user_email = '$user_email'";

        $result_update = mysqli_query($connection, $update_user);

        // Check if update was successful
        if ($result_update) {
            echo "<script>alert('Account updated successfully!');</script>";
            echo "<script>window.location.href = 'profile.php';</script>";
        } else {
            echo "<script>alert('Error updating account. Please try again.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Account</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4p889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">


    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container py-2">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <!-- Card Container -->
                <div class="card shadow">
                    <div class="card-header bg-secondary-subtle text-black text-center">
                        <h4>Update Your Account</h4>
                    </div>
                    <div class="card-body">
                        <form action="" method="post" enctype="multipart/form-data">
                            <!-- User Name Input -->
                            <div class="input-group mb-3">
                                <span class="input-group-text bg-secondary-subtle"><i class="fas fa-user"></i></span>
                                <input type="text" id="user_name" name="user_name" value="<?php echo $user_name; ?>" class="form-control" placeholder="Enter your full name">
                            </div>

                            <!-- User Mobile Input -->
                            <div class="input-group mb-3">
                                <span class="input-group-text bg-secondary-subtle"><i class="fas fa-phone"></i></span>
                                <input type="tel" id="user_mobile" name="user_mobile" value="<?php echo $user_mobile; ?>" class="form-control" placeholder="Enter phone number">
                            </div>

                            <!-- User Address Input -->
                            <div class="input-group mb-3">
                                <span class="input-group-text bg-secondary-subtle"><i class="fas fa-home"></i></span>
                                <input id="user_address" name="user_address" value="<?php echo $user_address; ?>" class="form-control" placeholder="Enter your address">
                            </div>

                            <!-- User Image Input -->
                            <div class="mb-3">
                                <label for="user_image" class="form-label">Upload Profile Photo</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-secondary-subtle"><i class="fas fa-image"></i></span>
                                    <input type="file" id="user_image" name="user_image" class="form-control" accept="image/*">
                                </div>
                            </div>

                            <!-- Update Button -->
                            <div class="d-grid gap-2 mt-4">
                                <input type="submit" class="btn btn-info btn-lg text-white" name="update_user_account" value="Update Account">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-bqwE7N4UVfeuvWpf4tb5UD9xqhNUq4lB3VFYfZjUgpD8k4+IQLXPt8XgUCBzVk0A" crossorigin="anonymous"></script>
</body>

</html>