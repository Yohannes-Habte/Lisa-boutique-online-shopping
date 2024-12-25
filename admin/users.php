<?php
// Ensure connection is established and sanitize inputs globally if needed.
if (!isset($connection) || !$connection) {
    die("Database connection error.");
}

// Query to fetch users
$query = "SELECT * FROM users";
$result = mysqli_query($connection, $query);

if (!$result) {
    die("Query failed: " . htmlspecialchars(mysqli_error($connection)));
}

// Count the number of rows fetched
$user_count = mysqli_num_rows($result);
?>

<section class="container mt-5">
    <h3 class="text-center mb-4">All Users List</h3>

    <?php if ($user_count > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover align-middle">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">User Name</th>
                        <th scope="col">Photo </th>
                        <th scope="col">Mobile</th>
                        <th scope="col">Email</th>
                        <th scope="col">Address</th>
                        <th scope="col">IP Address</th>
                        <th scope="col">Created At</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                            <td><?php echo htmlspecialchars($user['user_name']); ?></td>
                            <td>
                                <?php if (!empty($user['user_image'])): ?>
                                    <img src="../client/uploads/<?php echo htmlspecialchars($user['user_image']); ?>" 
                                         alt="<?php echo htmlspecialchars($user['user_name']); ?>" 
                                         class="img-thumbnail" style="width: 75px; height: 75px;">
                                <?php else: ?>
                                    <span class="text-muted">No Image</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($user['user_mobile']); ?></td>
                            <td><?php echo htmlspecialchars($user['user_email']); ?></td>
                            <td><?php echo htmlspecialchars($user['user_address']); ?></td>
                            <td><?php echo htmlspecialchars($user['user_ip']); ?></td>
                            <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="index.php?edit_user=<?php echo urlencode($user['user_id']); ?>" 
                                       class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="index.php?delete_user=<?php echo urlencode($user['user_id']); ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Are you sure you want to delete this user?');">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center">No users found in the database.</div>
    <?php endif; ?>
</section>
