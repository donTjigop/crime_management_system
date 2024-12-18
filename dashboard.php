<?php
include 'includes/header.php';
include 'config_db.php';  // Ensure this file defines $conn

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect if the user is not logged in
if (!isset($_SESSION['user_name'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch statistics
try {
    $total_complaints = $conn->query("SELECT COUNT(*) FROM complaints")->fetchColumn();
    $total_users = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $today_cases = $conn->query("SELECT COUNT(*) FROM complaints WHERE DATE(created_at) = CURDATE()")->fetchColumn();
} catch (PDOException $e) {
    die("Error fetching statistics: " . $e->getMessage());
}
?>

<!-- HTML Structure -->
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 bg-dark text-white vh-100 p-4">
            <div class="text-center mb-4">
                <img src="../assets/crime-icon-removebg-preview.png" alt="Logo" class="img-fluid rounded-circle" style="max-width: 120px;">
            </div>
            <h2 class="text-center text-uppercase mb-4">CRMS</h2>
            <ul class="nav flex-column">
                <li class="nav-item mb-2">
                    <a class="nav-link text-blue btn btn-outline-light" href="dashboard.php">Dashboard</a>
                </li>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                <li class="nav-item mb-2">
                    <a class="nav-link text-white btn btn-outline-light" href="users.php">Manage Users</a>
                </li>
                <?php endif; ?>
                <li class="nav-item mb-2">
                    <a class="nav-link text-white btn btn-outline-light" href="complaints.php">Complaints</a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link text-white btn btn-outline-light" href="reports.php">Reports</a>
                </li>
                <li class="nav-item mt-auto">
                    <a class="nav-link text-danger btn btn-outline-danger" href="/logout.php">Logout</a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="container mt-5">
                <!-- Banner image at the top -->
                <div class="text-center mb-4">
                    <img src="../assets/images.png" alt="Welcome Banner" class="img-fluid" style="max-width: 15%; height: auto;">
                </div>
                
                <h1>Welcome, <?= htmlspecialchars($_SESSION['user_name']); ?></h1>
                <div class="row">
                    <!-- Total Complaints Card (Clickable) -->
                    <div class="col-md-4 mb-4">
                        <a href="complaints.php" class="text-decoration-none">
                            <div class="card text-white bg-primary mb-3">
                                <div class="card-body text-center">
                                    <img src="../assets/complaint-removebg-preview.png" alt="Complaints Icon" class="mb-3" style="width: 50px;">
                                    <h5 class="card-title">Total Complaints</h5>
                                    <p class="card-text"><?= $total_complaints; ?></p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Today's Cases Card (Clickable) -->
                    <div class="col-md-4 mb-4">
                        <a href="today_cases.php" class="text-decoration-none">
                            <div class="card text-white bg-success mb-3">
                                <div class="card-body text-center">
                                    <img src="../assets/case-removebg-preview.png" alt="Today's Cases Icon" class="mb-3" style="width: 50px;">
                                    <h5 class="card-title">Today's Cases</h5>
                                    <p class="card-text"><?= $today_cases; ?></p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Total Users Card (Clickable) -->
                    <div class="col-md-4 mb-4">
                        <a href="users.php" class="text-decoration-none">
                            <div class="card text-white bg-warning mb-3">
                                <div class="card-body text-center">
                                    <img src="../assets/user-removebg-preview.png" alt="Users Icon" class="mb-3" style="width: 50px;">
                                    <h5 class="card-title">Total Users</h5>
                                    <p class="card-text"><?= $total_users; ?></p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

            </div>
        </div> <!-- End Main Content -->
    </div> <!-- End Row -->
</div> <!-- End Container-fluid -->

<?php include 'includes/footer.php'; ?>
