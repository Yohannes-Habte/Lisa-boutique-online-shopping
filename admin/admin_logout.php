<?php
session_start(); // Start the session

// Unset all session variables
session_unset();

// Destroy the session
session_destroy();

// Echo JavaScript to redirect and display a logout success message
echo "<script>
        alert('You have successfully logged out.');
        window.location.href = 'admin_login.php';
      </script>";
exit();
?>