<?php
// Start output buffering
ob_start();

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

// Get the email from the session
$email = $_SESSION['user_email'];


// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_account'])) {
    $password = $_POST['password'];

    // Validate password field
    if (empty($password)) {
        $error_message = "Password is required.";
    } else {
        // Fetch user details from the database
        $query = "SELECT * FROM users WHERE user_email = ?";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            $hashed_password = $user['user_password'];

            // Verify password
            if (password_verify($password, $hashed_password)) {
                // Delete the account
                $delete_query = "DELETE FROM users WHERE user_email = ?";
                $delete_stmt = mysqli_prepare($connection, $delete_query);
                mysqli_stmt_bind_param($delete_stmt, 's', $email);

                if (mysqli_stmt_execute($delete_stmt)) {
                    // Log the user out
                    session_destroy();
                    header("Location: register.php");
                    exit();
                } else {
                    $error_message = "Error deleting account. Please try again.";
                }
            } else {
                $error_message = "Incorrect password.";
            }
        } else {
            $error_message = "User not found.";
        }
    }
}
ob_end_flush(); // Flush output buffer
?>


<div class="container">
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card border-danger mt-5">
                <div class="card-header bg-secondary-subtle">
                    <h4 class="text-danger"> Delete Account</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    <form action="" method="post">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-secondary-subtle"><i class="fas fa-envelope"></i></span>
                                <input type="email" id="email" name="email" class="form-control"
                                    value="<?php echo htmlspecialchars($email); ?>" readonly>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-secondary-subtle"><i class="fas fa-key"></i></span>
                                <input type="password" id="password" name="password" class="form-control" required>
                            </div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="delete_account" class="btn btn-danger">
                                <i class="fas fa-trash-alt me-2"></i> Delete Account
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>