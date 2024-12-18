<?php
include 'includes/header.php';  // Include necessary HTML, CSS, and JS
include 'config_db.php';        // Ensure this file defines $conn

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect if the user is not logged in
if (!isset($_SESSION['user_name'])) {
    header("Location: ../login.php");  // Redirect to login if not logged in
    exit();
}

// Fetch user accounts from the database
try {
    // Adjust the query to match your actual table structure
    $stmt = $conn->query("SELECT id, name, email FROM users");  
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);  // Store users in an associative array
} catch (PDOException $e) {
    die("Error fetching users: " . $e->getMessage());
}
?>

<!-- Displaying the user accounts -->
<div class="container mt-5">
    <h1 class="text-center">User Accounts</h1>
    
    <table class="table table-striped">
        <thead>
            <tr>
                <th>User ID</th>
                <th>Name</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']); ?></td>
                    <td><?= htmlspecialchars($user['name']); ?></td>
                    <td><?= htmlspecialchars($user['email']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Button to redirect to Dashboard -->
    <div class="text-center mt-4">
        <a href="dashboard.php" class="btn btn-primary">Go to Dashboard</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>  <!-- Include footer -->
