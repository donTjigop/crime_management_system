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

// Fetch today's cases from the database
try {
    $today_cases = $conn->query("SELECT COUNT(*) FROM complaints WHERE DATE(created_at) = CURDATE()")->fetchColumn();  // Count today's complaints
} catch (PDOException $e) {
    die("Error fetching today's cases: " . $e->getMessage());
}
?>

<!-- Display today's cases -->
<div class="container mt-5">
    <h1 class="text-center">Today's Cases</h1>
    
    <div class="text-center mt-4">
        <h2><?= htmlspecialchars($today_cases); ?> Case(s) Reported Today</h2>
    </div>

    <!-- Button to go back to the dashboard -->
    <div class="text-center mt-4">
        <a href="dashboard.php" class="btn btn-primary">Go to Dashboard</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>  <!-- Include footer -->
